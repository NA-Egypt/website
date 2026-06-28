<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\InventoryItem;

return new class extends Migration {
    public function up(): void {
        $ips = [
            'For the Newcomer',
            'Welcome to NA',
            'A resource in your community',
            'Accessibility for Those with Additional Needs',
            'Am I an Addict ?',
            'Another Look',
            'Behind the Walls',
            'By Young Addicts for Young Addicts',
            'Disruptive & Violent',
            'For the Parents or Guardians of Young People in NA',
            'For Those in Treatment',
            'Funding NA Services',
            'Group Business',
            'Hospitals and Institutions Service and the NA member',
            'In Time of Illness',
            'Introduction to NA Meetings',
            'Just For Today (IP)',
            'Living the program',
            'Money Matters Self-Support in NA',
            'NA Groups & Medication',
            'One Addict’s Experience with Acceptance, Faith, and Commitment',
            'PI and the NA Member',
            'Principles & Leadership in Na Service',
            'Recovery & Relapse',
            'Self-Acceptance',
            'Social Media & Our Guiding Principles',
            'Sponsorship',
            'Staying Clean on the Outside',
            'The Group',
            'The Group Booklet',
            'Twelve Concepts for Na Service',
            'The Loner - Staying Clean in Isolation',
            'The Triangle of Self-Obsession',
            'Treasurer\'s Handbook',
            'White Booklet',
            'Who, what, How & Why',
            'Working Step Four in NA',
            'Mental Health in Recovery',
        ];

        foreach ($ips as $ip) {
            InventoryItem::firstOrCreate([
                'name' => $ip,
            ], [
                'selling_price' => 0.00,
                'store_quantity' => 0,
                'lit_quantity' => 0,
                'category' => 'English IP',
            ]);
        }
    }

    public function down(): void {
        // Safe to leave empty
    }
};
