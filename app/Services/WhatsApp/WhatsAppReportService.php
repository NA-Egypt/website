<?php

namespace App\Services\WhatsApp;

use App\Models\User;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppSubscriber;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WhatsAppReportService
{
    /**
     * Compute executive KPI summary for a given date range.
     */
    public function getKpis(Carbon $start, Carbon $end, bool $includeDev = false): array
    {
        $convQuery = WhatsAppConversation::whereBetween('created_at', [$start, $end]);
        $msgQuery = WhatsAppMessage::whereBetween('created_at', [$start, $end]);

        if (!$includeDev) {
            $convQuery->where('is_dev_test', false);
            $msgQuery->whereHas('conversation', function ($q) {
                $q->where('is_dev_test', false);
            });
        }

        $totalConversations = $convQuery->count();
        $totalInbound = (clone $msgQuery)->where('direction', 'incoming')->count();
        $totalOutboundBot = (clone $msgQuery)->where('direction', 'outgoing')->where('sender_type', 'bot')->count();
        $totalOutboundAgent = (clone $msgQuery)->where('direction', 'outgoing')->where('sender_type', 'agent')->count();
        $totalOutbound = $totalOutboundBot + $totalOutboundAgent;
        $totalMessages = $totalInbound + $totalOutbound;

        // Conversations handled exclusively by bot vs handed over to live agent
        $liveAgentConversations = (clone $convQuery)->where('is_live_agent_mode', true)->count();
        $automationRate = $totalConversations > 0
            ? round((($totalConversations - $liveAgentConversations) / $totalConversations) * 100, 1)
            : 100.0;

        // Subscriber Metrics
        $subQuery = WhatsAppSubscriber::query();
        if (!$includeDev) {
            $subQuery->where('is_dev_subscriber', false);
        }
        $totalSubscribers = (clone $subQuery)->count();
        $activeSubscribers = (clone $subQuery)->where('is_active', true)->count();
        $newSubscribersPeriod = (clone $subQuery)->whereBetween('subscribed_at', [$start, $end])->count();
        $unsubscribedPeriod = (clone $subQuery)->whereBetween('unsubscribed_at', [$start, $end])->count();

        // Calculate average volunteer response time in minutes
        $avgResponseMinutes = $this->calculateAvgVolunteerResponseTime($start, $end, $includeDev);

        return [
            'total_conversations' => $totalConversations,
            'total_messages' => $totalMessages,
            'inbound_messages' => $totalInbound,
            'outbound_messages' => $totalOutbound,
            'bot_replies' => $totalOutboundBot,
            'agent_replies' => $totalOutboundAgent,
            'automation_rate' => $automationRate,
            'live_agent_conversations' => $liveAgentConversations,
            'total_subscribers' => $totalSubscribers,
            'active_subscribers' => $activeSubscribers,
            'new_subscribers_period' => $newSubscribersPeriod,
            'unsubscribed_period' => $unsubscribedPeriod,
            'avg_response_minutes' => $avgResponseMinutes,
        ];
    }

    /**
     * Calculate average volunteer response time (in minutes) for live agent chats.
     */
    protected function calculateAvgVolunteerResponseTime(Carbon $start, Carbon $end, bool $includeDev): float
    {
        $agentMessages = WhatsAppMessage::whereBetween('created_at', [$start, $end])
            ->where('direction', 'outgoing')
            ->where('sender_type', 'agent')
            ->whereNotNull('user_id')
            ->get();

        if ($agentMessages->isEmpty()) {
            return 0.0;
        }

        $durations = [];
        foreach ($agentMessages as $msg) {
            // Find preceding incoming message in the same conversation
            $previousUserMsg = WhatsAppMessage::where('conversation_id', $msg->conversation_id)
                ->where('direction', 'incoming')
                ->where('created_at', '<=', $msg->created_at)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($previousUserMsg) {
                $diff = $previousUserMsg->created_at->diffInMinutes($msg->created_at);
                if ($diff < 1440) { // filter out stale messages older than 24h
                    $durations[] = $diff;
                }
            }
        }

        if (empty($durations)) {
            return 0.0;
        }

        return round(array_sum($durations) / count($durations), 1);
    }

    /**
     * Get daily time-series message volume trend.
     */
    public function getMessageVolumeTrend(Carbon $start, Carbon $end, bool $includeDev = false): array
    {
        $days = [];
        $current = $start->copy();
        while ($current->lte($end)) {
            $days[$current->format('Y-m-d')] = [
                'date' => $current->format('Y-m-d'),
                'label' => $current->translatedFormat('d M'),
                'inbound' => 0,
                'bot' => 0,
                'agent' => 0,
            ];
            $current->addDay();
        }

        $query = WhatsAppMessage::select([
            DB::raw('DATE(created_at) as msg_date'),
            'direction',
            'sender_type',
            DB::raw('COUNT(*) as total'),
        ])
        ->whereBetween('created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()]);

        if (!$includeDev) {
            $query->whereHas('conversation', function ($q) {
                $q->where('is_dev_test', false);
            });
        }

        $records = $query->groupBy('msg_date', 'direction', 'sender_type')->get();

        foreach ($records as $rec) {
            $date = $rec->msg_date;
            if (isset($days[$date])) {
                if ($rec->direction === 'incoming') {
                    $days[$date]['inbound'] += (int) $rec->total;
                } elseif ($rec->sender_type === 'bot') {
                    $days[$date]['bot'] += (int) $rec->total;
                } elseif ($rec->sender_type === 'agent') {
                    $days[$date]['agent'] += (int) $rec->total;
                }
            }
        }

        return [
            'labels' => array_column($days, 'label'),
            'inbound' => array_column($days, 'inbound'),
            'bot' => array_column($days, 'bot'),
            'agent' => array_column($days, 'agent'),
        ];
    }

    /**
     * Get distribution of user service inquiries by category.
     */
    public function getServiceCategoryBreakdown(Carbon $start, Carbon $end, bool $includeDev = false): array
    {
        $query = WhatsAppMessage::where('direction', 'outgoing')
            ->whereNotNull('category')
            ->whereBetween('created_at', [$start, $end]);

        if (!$includeDev) {
            $query->whereHas('conversation', function ($q) {
                $q->where('is_dev_test', false);
            });
        }

        $counts = $query->select('category', DB::raw('COUNT(*) as total'))
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        $labelsMap = [
            'jft' => __('messages.whatsapp_cat_jft') ?? 'قراءة فقط لليوم (JFT)',
            'meetings' => __('messages.whatsapp_cat_meetings') ?? 'اجتماعات التعافي',
            'helpline' => __('messages.whatsapp_cat_helpline') ?? 'خط المساعدة',
            'events' => __('messages.whatsapp_cat_events') ?? 'الفعاليات والمؤتمرات',
            'forms' => __('messages.whatsapp_cat_forms') ?? 'النماذج والأدبيات',
            'social' => __('messages.whatsapp_cat_social') ?? 'وسائل التواصل الاجتماعي',
            'subscription' => __('messages.whatsapp_cat_subscription') ?? 'إدارة الاشتراكات',
            'menu' => __('messages.whatsapp_cat_menu') ?? 'القائمة الرئيسية والترحيب',
        ];

        $labels = [];
        $data = [];
        foreach ($labelsMap as $cat => $label) {
            $labels[] = $label;
            $data[] = $counts[$cat] ?? 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Get volunteer interaction summary.
     */
    public function getVolunteerActivitySummary(Carbon $start, Carbon $end): Collection
    {
        $volunteerCounts = WhatsAppMessage::where('direction', 'outgoing')
            ->where('sender_type', 'agent')
            ->whereNotNull('user_id')
            ->whereBetween('created_at', [$start, $end])
            ->select('user_id', DB::raw('COUNT(*) as total_count'))
            ->groupBy('user_id')
            ->get();

        if ($volunteerCounts->isEmpty()) {
            return collect();
        }

        $userIds = $volunteerCounts->pluck('user_id');
        $users = User::with('roles')->whereIn('id', $userIds)->get()->keyBy('id');

        return $volunteerCounts->map(function ($item) use ($users) {
            $user = $users->get($item->user_id);
            if ($user) {
                $user->whatsapp_replies_count = (int) $item->total_count;
                return $user;
            }
            return null;
        })->filter()->sortByDesc('whatsapp_replies_count')->values();
    }

    /**
     * Stream CSV export of WhatsApp messages and conversation telemetry.
     */
    public function exportToCsv(Carbon $start, Carbon $end, bool $includeDev = false): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="whatsapp-logs-' . now()->format('Y-m-d-His') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return new StreamedResponse(function () use ($start, $end, $includeDev) {
            $output = fopen('php://output', 'w');
            // Add UTF-8 BOM for proper Arabic text display in Excel
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($output, [
                'ID',
                'Date & Time',
                'Phone / JID',
                'Push Name',
                'Direction',
                'Sender Type',
                'Category',
                'Message Body',
                'Status',
                'Volunteer User',
            ]);

            $query = WhatsAppMessage::with(['conversation', 'user'])
                ->whereBetween('created_at', [$start, $end])
                ->orderBy('id', 'desc');

            if (!$includeDev) {
                $query->whereHas('conversation', function ($q) {
                    $q->where('is_dev_test', false);
                });
            }

            $query->chunk(200, function ($messages) use ($output) {
                foreach ($messages as $msg) {
                    fputcsv($output, [
                        $msg->id,
                        $msg->created_at->format('Y-m-d H:i:s'),
                        $msg->conversation ? $msg->conversation->phone : '',
                        $msg->conversation ? $msg->conversation->name : '',
                        $msg->direction,
                        $msg->sender_type,
                        $msg->category ?: 'general',
                        $msg->body,
                        $msg->status,
                        $msg->user ? $msg->user->name : '',
                    ]);
                }
            });

            fclose($output);
        }, 200, $headers);
    }
}
