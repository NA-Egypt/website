<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CalendarEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'start',
        'end',
        'description',
        'user_id',
        'color',
        'organizer',
        'location',
        'recurrence',
        'is_featured',
        'is_hero',
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
        'recurrence' => 'array',
        'is_featured' => 'boolean',
        'is_hero' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (CalendarEvent $event) {
            if ($event->is_hero) {
                $query = static::where('is_hero', true);
                if ($event->exists && $event->id) {
                    $query->where('id', '!=', $event->id);
                }
                $query->update(['is_hero' => false]);
            }
        });

        static::saved(function () {
            static::flushEventCaches();
        });

        static::deleted(function () {
            static::flushEventCaches();
        });
    }

    public static function flushEventCaches(): void
    {
        Cache::forget('frontend_events_data_ar');
        Cache::forget('frontend_events_data_en');
        Cache::forget('frontend_home_announcements');
        Cache::forget('frontend_home_hero_event');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor for Formatted Recurrence
    public function getFormattedRecurrenceAttribute()
    {
        if (empty($this->recurrence) || in_array('once', $this->recurrence)) {
            return __('messages.Once') ?? 'Once';
        }

        $weekday = $this->start ? \Carbon\Carbon::parse($this->start)->englishDayOfWeek : '';

        $formatted = array_map(function ($item) use ($weekday) {
            if ($item === 'every_two_months') return __('messages.Every Two Months');
            if ($item === 'monthly') return __('messages.Monthly (Same Date)');
            if (in_array($item, ['1st', '2nd', '3rd', '4th', '5th', 'last'])) {
                $translatedItem = __('messages.' . $item);
                $translatedWeekday = $weekday ? __('messages.' . strtolower($weekday)) : '';
                return $translatedItem . ($translatedWeekday ? ' ' . $translatedWeekday : '');
            }
            // For weekly or once:
            return __('messages.' . ucfirst($item));
        }, $this->recurrence);

        return implode(', ', $formatted);
    }

    /**
     * Classify location into Zoom link, Map link, generic URL, or plain text.
     */
    public function getLocationLinkInfoAttribute(): array
    {
        return static::classifyLocation($this->location);
    }

    public static function classifyLocation(?string $location): array
    {
        $loc = trim((string) $location);
        if ($loc === '') {
            return [
                'type' => 'empty',
                'is_link' => false,
                'url' => null,
                'label' => '',
            ];
        }

        $url = null;
        if (preg_match('/https?:\/\/[^\s"\'<>]+/i', $loc, $matches)) {
            $url = $matches[0];
        } elseif (preg_match('/^(?:www\.|(?:[\w-]+\.)*zoom\.us\/|maps\.app\.goo\.gl\/|(?:www\.)?google\.[a-z.]+\/maps|goo\.gl\/maps|maps\.google\.[a-z.]+\/|maps\.apple\.com\/|waze\.com\/)[^\s"\'<>]*/i', $loc, $matches)) {
            $url = 'https://' . $matches[0];
        }

        if ($url) {
            if (stripos($url, 'zoom.us') !== false) {
                return [
                    'type' => 'zoom',
                    'is_link' => true,
                    'url' => $url,
                    'label' => __('messages.join_zoom_meeting'),
                ];
            }

            if (preg_match('/(maps\.app\.goo\.gl|google\.[a-z.]+\/maps|maps\.google\.[a-z.]+|goo\.gl\/maps|maps\.apple\.com|waze\.com)/i', $url)) {
                return [
                    'type' => 'map',
                    'is_link' => true,
                    'url' => $url,
                    'label' => __('messages.view_on_map'),
                ];
            }

            return [
                'type' => 'link',
                'is_link' => true,
                'url' => $url,
                'label' => __('messages.open_location_link'),
            ];
        }

        return [
            'type' => 'text',
            'is_link' => false,
            'url' => null,
            'label' => $loc,
        ];
    }

    /**
     * Get all valid organizer names (both Arabic and English) from Committees, Workgroups, and Service Bodies.
     */
    public static function getValidOrganizerNames(): array
    {
        $names = [];

        $committeesAndWorkgroups = ServiceCommittee::query()
            ->select('ar_name', 'en_name', 'status')
            ->get();

        foreach ($committeesAndWorkgroups as $item) {
            if (!empty($item->ar_name)) {
                $names[] = trim($item->ar_name);
            }
            if (!empty($item->en_name)) {
                $names[] = trim($item->en_name);
            }
        }

        $serviceBodies = ServiceBody::query()
            ->select('ar_name', 'en_name')
            ->get();

        foreach ($serviceBodies as $sb) {
            if (!empty($sb->ar_name)) {
                $names[] = trim($sb->ar_name);
            }
            if (!empty($sb->en_name)) {
                $names[] = trim($sb->en_name);
            }
        }

        return array_values(array_unique(array_filter($names)));
    }

    /**
     * Get grouped organizer options for UI comboboxes and default organizer for user.
     */
    public static function getOrganizerCatalog(?User $user = null, ?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale() ?: 'ar';
        $isAr = $locale === 'ar';

        $formatItem = function ($item) use ($isAr) {
            $primary = $isAr
                ? (trim((string) $item->ar_name) ?: trim((string) $item->en_name))
                : (trim((string) $item->en_name) ?: trim((string) $item->ar_name));
            $secondary = $isAr
                ? trim((string) $item->en_name)
                : trim((string) $item->ar_name);

            return [
                'value' => $primary,
                'label' => $primary,
                'ar_name' => trim((string) $item->ar_name),
                'en_name' => trim((string) $item->en_name),
                'search_text' => mb_strtolower(trim($item->ar_name . ' ' . $item->en_name)),
                'secondary' => ($secondary && $secondary !== $primary) ? $secondary : '',
            ];
        };

        $committees = ServiceCommittee::committeesOnly()
            ->active()
            ->orderBy($isAr ? 'ar_name' : 'en_name')
            ->get()
            ->map($formatItem)
            ->filter(fn ($i) => !empty($i['value']))
            ->values()
            ->all();

        $workgroups = ServiceCommittee::workgroupsOnly()
            ->active()
            ->orderBy($isAr ? 'ar_name' : 'en_name')
            ->get()
            ->map($formatItem)
            ->filter(fn ($i) => !empty($i['value']))
            ->values()
            ->all();

        $serviceBodies = ServiceBody::query()
            ->orderBy($isAr ? 'ar_name' : 'en_name')
            ->get()
            ->map($formatItem)
            ->filter(fn ($i) => !empty($i['value']))
            ->values()
            ->all();

        $groups = [
            [
                'key' => 'committees',
                'label' => __('messages.organizer_committees_group'),
                'options' => $committees,
            ],
            [
                'key' => 'workgroups',
                'label' => __('messages.organizer_workgroups_group'),
                'options' => $workgroups,
            ],
            [
                'key' => 'service_bodies',
                'label' => __('messages.organizer_service_bodies_group'),
                'options' => $serviceBodies,
            ],
        ];

        $defaultOrganizer = '';
        if ($user) {
            $ownSc = ServiceCommittee::where('user_id', $user->id)->first();
            if ($ownSc) {
                $defaultOrganizer = $isAr
                    ? (trim((string) $ownSc->ar_name) ?: trim((string) $ownSc->en_name))
                    : (trim((string) $ownSc->en_name) ?: trim((string) $ownSc->ar_name));
            } elseif ($user->hasRole('rsc')) {
                $rsc = ServiceCommittee::find(83);
                if ($rsc) {
                    $defaultOrganizer = $isAr
                        ? (trim((string) $rsc->ar_name) ?: trim((string) $rsc->en_name))
                        : (trim((string) $rsc->en_name) ?: trim((string) $rsc->ar_name));
                }
            } elseif ($user->serviceBody) {
                $sb = $user->serviceBody;
                $defaultOrganizer = $isAr
                    ? (trim((string) $sb->ar_name) ?: trim((string) $sb->en_name))
                    : (trim((string) $sb->en_name) ?: trim((string) $sb->ar_name));
            }
        }

        return [
            'groups' => $groups,
            'default_organizer' => $defaultOrganizer,
        ];
    }
}
