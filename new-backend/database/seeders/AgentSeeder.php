<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agents = [
            [
                'user_id' => 5, // agent1@jobplacement.com
                'agent_code' => 'AGT001',
                'referral_code' => 'RINI001',
                'total_referrals' => 25,
                'successful_placements' => 18,
                'total_commission' => 4500000.00,
                'total_points' => 1800,
                'level' => 'gold',
                'success_rate' => 72.00,
                'performance_metrics' => json_encode([
                    'monthly_stats' => [
                        '2024-01' => ['referrals' => 8, 'placements' => 6],
                        '2024-02' => ['referrals' => 10, 'placements' => 7],
                        '2024-03' => ['referrals' => 7, 'placements' => 5],
                    ],
                    'ranking' => 2,
                    'top_industries' => ['Technology', 'Manufacturing'],
                    'avg_monthly_commission' => 1500000
                ]),
                'status' => 'active',
                'notes' => 'Agent performan sangat baik, konsisten memberikan referral berkualitas.',
                'created_at' => now()->subMonths(6),
                'updated_at' => now(),
            ],
            
            [
                'user_id' => 6, // agent2@jobplacement.com
                'agent_code' => 'AGT002',
                'referral_code' => 'DEDI002',
                'total_referrals' => 32,
                'successful_placements' => 22,
                'total_commission' => 5500000.00,
                'total_points' => 2200,
                'level' => 'platinum',
                'success_rate' => 68.75,
                'performance_metrics' => json_encode([
                    'monthly_stats' => [
                        '2024-01' => ['referrals' => 12, 'placements' => 8],
                        '2024-02' => ['referrals' => 11, 'placements' => 7],
                        '2024-03' => ['referrals' => 9, 'placements' => 7],
                    ],
                    'ranking' => 1,
                    'top_industries' => ['Retail', 'Hospitality', 'Logistics'],
                    'avg_monthly_commission' => 1833333
                ]),
                'status' => 'active',
                'notes' => 'Top performer, memiliki network yang luas di berbagai industri.',
                'created_at' => now()->subMonths(8),
                'updated_at' => now(),
            ],
        ];

        DB::table('agents')->insert($agents);
    }
}