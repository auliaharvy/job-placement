<?php
/**
 * File Path: /backend/app/Services/WhatsAppService.php
 * Service untuk integrasi dengan WhatsApp Gateway
 */

namespace App\Services;

use App\Models\Applicant;
use App\Models\Application;
use App\Models\JobPosting;
use App\Models\Placement;
use App\Models\WhatsAppLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class WhatsAppService
{
    private $baseUrl;
    private $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('whatsapp.gateway_url', 'http://brevet.online:8005');
        $this->apiKey = config('whatsapp.api_key', ''); // Not needed for this gateway
    }

    /**
     * Kirim pesan selamat datang untuk pelamar baru
     */
    public function sendWelcomeMessage(Applicant $applicant): bool
    {
        $message = "Selamat datang {$applicant->full_name}! 🎉\n\n";
        $message .= "Pendaftaran Anda telah berhasil. Berikut detail akun Anda:\n";
        $message .= "📧 Email: {$applicant->user->email}\n";
        $message .= "🔑 Password: NIK Anda\n\n";
        $message .= "Anda akan mendapat notifikasi untuk lowongan yang sesuai dengan profil Anda.\n\n";
        $message .= "Terima kasih telah bergabung! 🙏";

        return $this->sendMessage($applicant->phone, $message, 'welcome');
    }

    /**
     * Broadcast lowongan kerja baru ke pelamar yang sesuai
     */
    public function broadcastJobOpening(JobPosting $job, Collection $applicants): array
    {
        $successCount = 0;
        $failedCount = 0;

        $message = "🚀 *LOWONGAN KERJA BARU* 🚀\n\n";
        $message .= "🏢 Perusahaan: {$job->company->name}\n";
        $message .= "💼 Posisi: {$job->title}\n";
        $message .= "📍 Lokasi: {$job->work_city}\n";
        $message .= "⏰ Deadline: " . $job->application_deadline->format('d M Y') . "\n";
        $message .= "👥 Kuota: {$job->total_positions} orang\n\n";
        $message .= "📋 Persyaratan:\n";

        if ($job->min_age || $job->max_age) {
            $ageRange = '';
            if ($job->min_age && $job->max_age) {
                $ageRange = "{$job->min_age}-{$job->max_age} tahun";
            } elseif ($job->min_age) {
                $ageRange = "minimal {$job->min_age} tahun";
            } else {
                $ageRange = "maksimal {$job->max_age} tahun";
            }
            $message .= "• Usia: {$ageRange}\n";
        }

        if ($job->required_education_levels && count($job->required_education_levels) > 0) {
            $message .= "• Pendidikan: " . implode(', ', array_map('strtoupper', $job->required_education_levels)) . "\n";
        }

        if ($job->min_experience_months) {
            $message .= "• Pengalaman: " . round($job->min_experience_months / 12, 1) . " tahun\n";
        }

        $message .= "\n🔗 Daftar sekarang melalui aplikasi atau hubungi kami!\n";
        $message .= "\n_Pesan ini dikirim karena profil Anda sesuai dengan persyaratan lowongan._";

        foreach ($applicants as $applicant) {
            try {
                if ($this->sendMessage($applicant->phone, $message, 'job_broadcast', $job->id)) {
                    $successCount++;
                } else {
                    $failedCount++;
                }
                // Add delay to prevent rate limiting
                usleep(500000); // 0.5 second delay
            } catch (\Exception $e) {
                $failedCount++;
                Log::error("Failed to send WhatsApp to {$applicant->phone}: " . $e->getMessage());
            }
        }

        return [
            'total_sent' => $successCount,
            'total_failed' => $failedCount,
            'total_recipients' => $applicants->count()
        ];
    }

    /**
     * Kirim konfirmasi aplikasi lamaran
     */
    public function sendApplicationConfirmation(Application $application): bool
    {
        $message = "✅ *KONFIRMASI LAMARAN* ✅\n\n";
        $message .= "Halo {$application->applicant->full_name},\n\n";
        $message .= "Lamaran Anda telah berhasil dikirim untuk posisi:\n";
        $message .= "💼 {$application->jobPosting->title}\n";
        $message .= "🏢 {$application->jobPosting->company->name}\n\n";
        $message .= "📋 Status: Dalam Review\n";
        $message .= "📅 Tanggal Apply: " . $application->applied_at->format('d M Y H:i') . "\n\n";
        $message .= "Kami akan menginformasikan perkembangan seleksi melalui WhatsApp ini.\n\n";
        $message .= "Terima kasih! 🙏";

        return $this->sendMessage(
            $application->applicant->phone,
            $message,
            'application_confirmation',
            $application->id
        );
    }

    /**
     * Kirim notifikasi update tahap seleksi
     */
    public function sendStageUpdateNotification(Application $application): bool
    {
        $stageName = $this->getStageDisplayName($application->selection_stage);

        $message = "📢 *UPDATE STATUS SELEKSI* 📢\n\n";
        $message .= "Halo {$application->applicant->full_name},\n\n";
        $message .= "Status seleksi Anda telah diperbarui:\n";
        $message .= "💼 Posisi: {$application->jobPosting->title}\n";
        $message .= "🏢 Perusahaan: {$application->jobPosting->company->name}\n";
        $message .= "📋 Status Terbaru: *{$stageName}*\n\n";

        switch ($application->selection_stage) {
            case Application::STAGE_PSYCOTEST:
                $message .= "🧠 Anda akan menjalani tes psikologi.\n";
                $message .= "📅 Jadwal akan diinformasikan segera.\n";
                break;
            case Application::STAGE_INTERVIEW:
                $message .= "🗣️ Anda akan menjalani wawancara.\n";
                $message .= "📅 Jadwal akan diinformasikan segera.\n";
                break;
            case Application::STAGE_MEDICAL:
                $message .= "🏥 Anda akan menjalani medical check-up.\n";
                $message .= "📅 Jadwal akan diinformasikan segera.\n";
                break;
        }

        $message .= "\nSelamat! Anda semakin dekat dengan kesempatan kerja ini! 🎉";

        return $this->sendMessage(
            $application->applicant->phone,
            $message,
            'stage_update',
            $application->id
        );
    }

    /**
     * Kirim notifikasi diterima kerja
     */
    public function sendAcceptanceNotification(Application $application, Placement $placement): bool
    {
        $message = "🎉 *SELAMAT! ANDA DITERIMA!* 🎉\n\n";
        $message .= "Halo {$application->applicant->full_name},\n\n";
        $message .= "Kami dengan senang hati menginformasikan bahwa Anda telah *DITERIMA* untuk posisi:\n\n";
        $message .= "💼 Posisi: {$placement->position}\n";
        $message .= "🏢 Perusahaan: {$placement->company->name}\n";
        $message .= "📍 Lokasi: {$placement->location}\n";
        $message .= "📅 Mulai Kerja: " . $placement->start_date->format('d M Y') . "\n";
        $message .= "💰 Gaji: Rp " . number_format($placement->salary, 0, ',', '.') . "\n\n";
        $message .= "📋 *LANGKAH SELANJUTNYA:*\n";
        $message .= "1. Konfirmasi kehadiran H-1 sebelum mulai kerja\n";
        $message .= "2. Siapkan dokumen yang diperlukan\n";
        $message .= "3. Datang tepat waktu pada hari pertama\n\n";
        $message .= "Tim kami akan menghubungi Anda untuk detail lebih lanjut.\n\n";
        $message .= "Selamat dan sukses untuk karier baru Anda! 🚀";

        return $this->sendMessage(
            $application->applicant->phone,
            $message,
            'acceptance',
            $placement->id
        );
    }

    /**
     * Kirim notifikasi penolakan
     */
    public function sendRejectionNotification(Application $application): bool
    {
        $message = "📝 *INFORMASI SELEKSI* 📝\n\n";
        $message .= "Halo {$application->applicant->full_name},\n\n";
        $message .= "Terima kasih atas minat dan partisipasi Anda dalam proses seleksi untuk posisi:\n";
        $message .= "💼 {$application->jobPosting->title}\n";
        $message .= "🏢 {$application->jobPosting->company->name}\n\n";
        $message .= "Setelah melalui pertimbangan yang matang, untuk saat ini kami belum dapat melanjutkan proses seleksi Anda.\n\n";
        $message .= "Namun, profil Anda tetap tersimpan dalam database kami dan akan dipertimbangkan untuk kesempatan lain yang sesuai.\n\n";
        $message .= "Jangan menyerah! Tetap semangat mencari peluang kerja yang tepat! 💪\n\n";
        $message .= "Terima kasih dan semoga sukses! 🙏";

        return $this->sendMessage(
            $application->applicant->phone,
            $message,
            'rejection',
            $application->id
        );
    }

    /**
     * Kirim pengingat kontrak akan berakhir
     */
    public function sendContractExpirationReminder(Placement $placement, int $daysLeft): bool
    {
        $message = "⏰ *PENGINGAT KONTRAK* ⏰\n\n";
        $message .= "Halo {$placement->applicant->full_name},\n\n";
        $message .= "Kontrak kerja Anda akan berakhir dalam *{$daysLeft} hari*:\n\n";
        $message .= "💼 Posisi: {$placement->position}\n";
        $message .= "🏢 Perusahaan: {$placement->company->name}\n";
        $message .= "📅 Berakhir: " . $placement->end_date->format('d M Y') . "\n\n";
        $message .= "Silakan hubungi tim kami untuk informasi mengenai:\n";
        $message .= "• Perpanjangan kontrak\n";
        $message .= "• Kesempatan kerja lainnya\n";
        $message .= "• Proses handover\n\n";
        $message .= "Terima kasih atas dedikasi Anda! 🙏";

        return $this->sendMessage(
            $placement->applicant->phone,
            $message,
            'contract_reminder',
            $placement->id
        );
    }

    /**
     * Kirim pengingat jadwal interview/test
     */
    public function sendScheduleReminder(Application $application, string $type, \DateTime $scheduleDate): bool
    {
        $typeName = [
            'psycotest' => 'Tes Psikologi',
            'interview' => 'Wawancara',
            'medical' => 'Medical Check-up'
        ][$type] ?? 'Jadwal';

        $message = "⏰ *PENGINGAT JADWAL* ⏰\n\n";
        $message .= "Halo {$application->applicant->full_name},\n\n";
        $message .= "Pengingat untuk jadwal {$typeName} Anda:\n\n";
        $message .= "💼 Posisi: {$application->jobPosting->title}\n";
        $message .= "🏢 Perusahaan: {$application->jobPosting->company->name}\n";
        $message .= "📅 Tanggal: " . $scheduleDate->format('d M Y') . "\n";
        $message .= "🕒 Waktu: " . $scheduleDate->format('H:i') . " WIB\n\n";
        $message .= "📋 *PERSIAPAN:*\n";

        switch ($type) {
            case 'psycotest':
                $message .= "• Bawa KTP asli\n";
                $message .= "• Istirahat yang cukup\n";
                $message .= "• Datang 15 menit sebelum waktu\n";
                break;
            case 'interview':
                $message .= "• Berpakaian rapi dan formal\n";
                $message .= "• Bawa CV dan dokumen pendukung\n";
                $message .= "• Datang 15 menit sebelum waktu\n";
                break;
            case 'medical':
                $message .= "• Puasa 8-12 jam sebelumnya\n";
                $message .= "• Bawa KTP asli\n";
                $message .= "• Istirahat yang cukup\n";
                break;
        }

        $message .= "\nSemoga sukses! 🍀";

        return $this->sendMessage(
            $application->applicant->phone,
            $message,
            'schedule_reminder',
            $application->id
        );
    }

    /**
     * Metode dasar untuk mengirim pesan
     */
    public function sendMessage(string $phone, string $message, string $type = 'general', $referenceId = null): bool
    {
        try {
            $response = Http::timeout(30)->post($this->baseUrl . '/message/send-text', [
                'session' => config('whatsapp.default_session', 'job-placement'),
                'to' => $this->formatPhoneNumber($phone),
                'text' => $message
            ]);

            $success = false;
            if ($response->successful()) {
                $data = $response->json();
                // Check if the response indicates success
                $success = isset($data['status']) ? $data['status'] === 'success' : true;
            } else {
                Log::error('WhatsApp API Error: ' . $response->body());
            }

            // Log the message attempt
            $this->logMessage($phone, $message, $type, $success, $referenceId);

            return $success;
        } catch (\Exception $e) {
            Log::error('WhatsApp Send Error: ' . $e->getMessage());
            $this->logMessage($phone, $message, $type, false, $referenceId, $e->getMessage());
            return false;
        }
    }

    /**
     * Format nomor telepon ke format internasional
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Remove any non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Convert Indonesian format to international
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Helper untuk nama tahap seleksi
     */
    private function getStageDisplayName(string $stage): string
    {
        $stages = [
            Application::STAGE_APPLICATION => 'Dokumen Diterima',
            Application::STAGE_PSYCOTEST => 'Tes Psikologi',
            Application::STAGE_INTERVIEW => 'Wawancara',
            Application::STAGE_MEDICAL => 'Medical Check-up',
            Application::STAGE_FINAL => 'Evaluasi Akhir',
        ];

        return $stages[$stage] ?? 'Tidak Diketahui';
    }

    /**
     * Kirim pesan massal dengan antrian
     */
    public function sendBulkMessages(array $recipients, string $message): array
    {
        $results = [
            'total_recipients' => count($recipients),
            'total_sent' => 0,
            'total_failed' => 0,
            'failed_numbers' => []
        ];

        foreach ($recipients as $recipient) {
            try {
                $phone = is_array($recipient) ? $recipient['phone'] : $recipient;
                $name = is_array($recipient) ? $recipient['name'] : '';

                // Personalize message if name is provided
                $personalizedMessage = $name ? str_replace('{name}', $name, $message) : $message;

                if ($this->sendMessage($phone, $personalizedMessage, 'bulk')) {
                    $results['total_sent']++;
                } else {
                    $results['total_failed']++;
                    $results['failed_numbers'][] = $phone;
                }

                // Add delay to prevent rate limiting
                usleep(750000); // 0.75 second delay
            } catch (\Exception $e) {
                $results['total_failed']++;
                $results['failed_numbers'][] = $phone ?? 'unknown';
                Log::error("Bulk WhatsApp send error: " . $e->getMessage());
            }
        }

        return $results;
    }

    /**
     * Mendapatkan template pesan
     */
    public function getMessageTemplate(string $templateName, array $variables = []): string
    {
        $templates = [
            'birthday_greeting' => "🎉 Selamat ulang tahun {name}! Semoga tahun ini membawa kesuksesan dan kebahagiaan. Tetap semangat mencari peluang kerja yang tepat! 🎂",
            'job_reminder' => "🔔 Halo {name}, jangan lupa untuk melengkapi profil Anda agar kami dapat mencarikan lowongan yang sesuai. Terima kasih!",
            'monthly_update' => "📊 Update bulanan: Bulan ini kami telah membantu {placement_count} orang mendapatkan pekerjaan. Tetap semangat!",
            'training_invitation' => "📚 Undangan pelatihan: Kami mengadakan pelatihan {training_name} pada {date}. Daftar sekarang untuk meningkatkan skill Anda!",
            'survey_request' => "📝 Bantuan Anda diperlukan! Mohon isi survei kepuasan layanan kami: {survey_link}. Terima kasih atas partisipasinya."
        ];

        $template = $templates[$templateName] ?? '';

        // Replace variables in template
        foreach ($variables as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }

        return $template;
    }

    /**
     * Log aktivitas pengiriman pesan
     */
    private function logMessage(string $phone, string $message, string $type, bool $success, $referenceId = null, string $errorMessage = null): void
    {
        try {
            WhatsAppLog::create([
                'phone_number' => $phone,
                'message_content' => $message,
                'message_type' => $type,
                'status' => $success ? 'sent' : 'failed',
                'context_type' => $this->getRefereceType($type),
                'context_id' => $referenceId,
                'error_message' => $errorMessage,
                'sent_at' => now(),
                'session_id' => config('whatsapp.default_session', 'job-placement')
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log WhatsApp message: ' . $e->getMessage());
        }
    }

    /**
     * Helper untuk menentukan reference type
     */
    private function getRefereceType(string $messageType): ?string
    {
        $referenceMap = [
            'job_broadcast' => 'job_posting',
            'application_confirmation' => 'application',
            'stage_update' => 'application',
            'acceptance' => 'placement',
            'rejection' => 'application',
            'contract_reminder' => 'placement',
            'schedule_reminder' => 'application'
        ];

        return $referenceMap[$messageType] ?? null;
    }

    /**
     * Mendapatkan statistik pengiriman pesan
     */
    public function getMessageStats(Carbon $startDate = null, Carbon $endDate = null): array
    {
        $query = WhatsAppLog::query();

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $totalMessages = $query->count();
        $sentMessages = $query->where('status', 'sent')->count();
        $failedMessages = $query->where('status', 'failed')->count();

        $messagesByType = $query->groupBy('message_type')
                               ->selectRaw('message_type, count(*) as count')
                               ->pluck('count', 'message_type')
                               ->toArray();

        return [
            'total_messages' => $totalMessages,
            'sent_messages' => $sentMessages,
            'failed_messages' => $failedMessages,
            'success_rate' => $totalMessages > 0 ? round(($sentMessages / $totalMessages) * 100, 2) : 0,
            'messages_by_type' => $messagesByType
        ];
    }

    /**
     * Cek status koneksi WhatsApp Gateway
     */
    public function checkGatewayStatus(): array
    {
        try {
            // Check all sessions to see if our session exists
            $response = Http::timeout(10)->get($this->baseUrl . '/session');
            if ($response->successful()) {
                $responseData = $response->json();
                $ourSession = config('whatsapp.default_session', 'job-placement');

                // Check if our session is in the list
                $sessionExists = false;
                $sessionsList = [];
                
                // Handle different response structures
                if (isset($responseData['success']) && $responseData['success'] && isset($responseData['data']['data'])) {
                    // Structure: {"success": true, "data": {"data": ["session1", "session2"]}}
                    $sessionsList = $responseData['data']['data'];
                    $sessionExists = in_array($ourSession, $sessionsList);
                } elseif (isset($responseData['data']) && is_array($responseData['data'])) {
                    // Structure: {"data": ["session1", "session2"]}
                    $sessionsList = $responseData['data'];
                    $sessionExists = in_array($ourSession, $sessionsList);
                } elseif (is_array($responseData)) {
                    // Direct array: ["session1", "session2"]
                    $sessionsList = $responseData;
                    $sessionExists = in_array($ourSession, $sessionsList);
                } elseif (isset($responseData['sessions']) && is_array($responseData['sessions'])) {
                    // Structure: {"sessions": ["session1", "session2"]}
                    $sessionsList = $responseData['sessions'];
                    $sessionExists = in_array($ourSession, $sessionsList);
                }

                return [
                    'status' => $sessionExists ? 'connected' : 'session_not_found',
                    'response_time' => $response->handlerStats()['total_time'] ?? 0,
                    'session' => $ourSession,
                    'all_sessions' => $responseData,
                    'session_list' => $sessionsList,
                    'session_exists' => $sessionExists
                ];
            }

            return [
                'status' => 'error',
                'message' => 'HTTP ' . $response->status(),
                'response_time' => null
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'disconnected',
                'message' => $e->getMessage(),
                'response_time' => null
            ];
        }
    }

    /**
     * Start WhatsApp session if not exists
     */
    public function startSession(): array
    {
        try {
            $session = config('whatsapp.default_session', 'job-placement');
            $response = Http::timeout(30)->get($this->baseUrl . '/session/start', [
                'session' => $session
            ]);

            if ($response->successful()) {
                return [
                    'status' => 'success',
                    'message' => 'Session started successfully',
                    'session' => $session,
                    'data' => $response->json()
                ];
            }

            return [
                'status' => 'error',
                'message' => 'Failed to start session: HTTP ' . $response->status(),
                'response' => $response->body()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Exception starting session: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send image message
     */
    public function sendImage(string $phone, string $imageUrl, string $caption = ''): bool
    {
        try {
            $response = Http::timeout(30)->post($this->baseUrl . '/message/send-image', [
                'session' => config('whatsapp.default_session', 'job-placement'),
                'to' => $this->formatPhoneNumber($phone),
                'text' => $caption,
                'image_url' => $imageUrl
            ]);

            $success = false;
            if ($response->successful()) {
                $data = $response->json();
                $success = isset($data['status']) ? $data['status'] === 'success' : true;
            } else {
                Log::error('WhatsApp Image API Error: ' . $response->body());
            }

            // Log the message attempt
            $this->logMessage($phone, "Image: {$caption} - {$imageUrl}", 'image', $success);

            return $success;
        } catch (\Exception $e) {
            Log::error('WhatsApp Send Image Error: ' . $e->getMessage());
            $this->logMessage($phone, "Image: {$caption} - {$imageUrl}", 'image', false, null, $e->getMessage());
            return false;
        }
    }

    /**
     * Send document message
     */
    public function sendDocument(string $phone, string $documentUrl, string $documentName, string $caption = ''): bool
    {
        try {
            $response = Http::timeout(30)->post($this->baseUrl . '/message/send-document', [
                'session' => config('whatsapp.default_session', 'job-placement'),
                'to' => $this->formatPhoneNumber($phone),
                'text' => $caption,
                'document_url' => $documentUrl,
                'document_name' => $documentName
            ]);

            $success = false;
            if ($response->successful()) {
                $data = $response->json();
                $success = isset($data['status']) ? $data['status'] === 'success' : true;
            } else {
                Log::error('WhatsApp Document API Error: ' . $response->body());
            }

            // Log the message attempt
            $this->logMessage($phone, "Document: {$documentName} - {$caption}", 'document', $success);

            return $success;
        } catch (\Exception $e) {
            Log::error('WhatsApp Send Document Error: ' . $e->getMessage());
            $this->logMessage($phone, "Document: {$documentName} - {$caption}", 'document', false, null, $e->getMessage());
            return false;
        }
    }

    /**
     * Stop WhatsApp session
     */
    public function stopSession(): array
    {
        try {
            $session = config('whatsapp.default_session', 'job-placement');
            $response = Http::timeout(30)->get($this->baseUrl . '/session/logout', [
                'session' => $session
            ]);

            if ($response->successful()) {
                return [
                    'status' => 'success',
                    'message' => 'Session stopped successfully',
                    'session' => $session,
                    'data' => $response->json()
                ];
            }

            return [
                'status' => 'error',
                'message' => 'Failed to stop session: HTTP ' . $response->status(),
                'response' => $response->body()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Exception stopping session: ' . $e->getMessage()
            ];
        }
    }
}
