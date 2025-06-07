<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Agent;
use App\Models\AgentLinkClick;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AgentLinkClickSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some test agents if they don't exist
        $agents = $this->createTestAgents();

        // Create test link clicks for each agent
        foreach ($agents as $agent) {
            $this->createTestClicksForAgent($agent);
        }
    }

    private function createTestAgents(): array
    {
        $agents = [];

        $testAgentsData = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.agent@example.com',
                'phone' => '081234567890',
                'agent_code' => 'AGENT_001',
                'referral_code' => 'JOHN001',
            ],
            [
                'name' => 'Jane Smith',
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.agent@example.com',
                'phone' => '081234567891',
                'agent_code' => 'AGENT_002',
                'referral_code' => 'JANE002',
            ],
            [
                'name' => 'Michael Johnson',
                'first_name' => 'Michael',
                'last_name' => 'Johnson',
                'email' => 'michael.agent@example.com',
                'phone' => '081234567892',
                'agent_code' => 'AGENT_003',
                'referral_code' => 'MICH003',
            ],
        ];

        foreach ($testAgentsData as $agentData) {
            // Check if user exists
            $user = User::where('email', $agentData['email'])->first();

            if (!$user) {
                $user = User::create([
                    'first_name' => $agentData['first_name'],
                    'last_name' => $agentData['last_name'],
                    'email' => $agentData['email'],
                    'password' => Hash::make('password'),
                    'phone' => $agentData['phone'],
                    'role' => 'agent',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]);
            }

            // Check if agent exists
            $agent = Agent::where('user_id', $user->id)->first();

            if (!$agent) {
                $agent = Agent::create([
                    'user_id' => $user->id,
                    'agent_code' => $agentData['agent_code'],
                    'referral_code' => $agentData['referral_code'],
                    'total_referrals' => rand(5, 50),
                    'successful_placements' => rand(1, 20),
                    'total_commission' => rand(1000000, 10000000),
                    'total_points' => rand(100, 2000),
                    'level' => collect(['bronze', 'silver', 'gold', 'platinum'])->random(),
                    'success_rate' => rand(60, 95),
                    'status' => 'active',
                ]);
            }

            $agents[] = $agent;
        }

        return $agents;
    }

    private function createTestClicksForAgent(Agent $agent): void
    {
        $sources = ['facebook', 'instagram', 'whatsapp', 'email', 'direct', 'linkedin', 'twitter'];
        $mediums = ['social', 'email', 'referral', 'organic', 'paid', 'direct'];
        $campaigns = ['june_recruitment', 'summer_jobs', 'tech_hiring', 'remote_work', 'fresh_graduate'];
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Safari/605.1.15',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Linux; Android 11; SM-G991B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.120 Mobile Safari/537.36',
        ];

        // Generate clicks for the last 30 days
        for ($i = 0; $i < rand(20, 100); $i++) {
            $clickedAt = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            $source = collect($sources)->random();
            $medium = $this->getMediumForSource($source, $mediums);
            $campaign = collect($campaigns)->random();

            // Generate unique session and IP combinations
            $sessionId = 'sess_' . uniqid() . '_' . $agent->id;
            $ipAddress = $this->generateRandomIP();

            $click = AgentLinkClick::create([
                'agent_id' => $agent->id,
                'referral_code' => $agent->referral_code,
                'utm_source' => $source,
                'utm_medium' => $medium,
                'utm_campaign' => $campaign,
                'user_agent' => collect($userAgents)->random(),
                'ip_address' => $ipAddress,
                'session_id' => $sessionId,
                'browser_fingerprint' => 'fp_' . md5($sessionId . $ipAddress),
                'clicked_at' => $clickedAt,
                'created_at' => $clickedAt,
                'updated_at' => $clickedAt,
            ]);

            // Random chance for conversion (10-20% conversion rate)
            if (rand(1, 100) <= 15) {
                $convertedAt = $clickedAt->copy()->addMinutes(rand(5, 120));
                $click->update(['converted_at' => $convertedAt]);
            }
        }

        $this->command->info("Created test clicks for agent: {$agent->user->name}");
    }

    private function getMediumForSource(string $source, array $mediums): string
    {
        $sourceMediumMap = [
            'facebook' => 'social',
            'instagram' => 'social',
            'linkedin' => 'social',
            'twitter' => 'social',
            'whatsapp' => 'referral',
            'email' => 'email',
            'direct' => 'direct',
        ];

        return $sourceMediumMap[$source] ?? collect($mediums)->random();
    }

    private function generateRandomIP(): string
    {
        return rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255);
    }
}
