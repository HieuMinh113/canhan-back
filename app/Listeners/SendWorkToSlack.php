<?php

namespace App\Listeners;

use App\Events\WorkCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendWorkToSlack implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(WorkCreated $event): void
    {   
        $workSchedules = $event->schedules; // mảng các WorkSchedule

        if (empty($workSchedules)) {
            return;
        }

        // Load user từ phần tử đầu tiên (vì cùng 1 user)
        $user = $workSchedules[0]->user;

        $workDates = [];
        $offDates  = [];

        foreach ($workSchedules as $schedule) {
            $formattedDate = Carbon::parse($schedule->date)->format('d/m/Y');

            if ($schedule->status === 'work') {
                $workDates[] = $formattedDate;
            } else {
                $offDates[]  = $formattedDate;
            }
        }

        $workText = count($workDates) ? "🟢 Làm: " . implode(", ", $workDates) . "\n" : "";
        $offText  = count($offDates)  ? "🔴 Nghỉ: " . implode(", ", $offDates) . "\n" : "";

        $message =
            "📅 Đăng ký lịch từ nhân viên\n" .
            "👤 Họ tên: {$user->name}\n" .
            "💼 Chức vụ: {$user->role}\n" .
            $workText .
            $offText;

        $webhookUrl = env('SLACK_WEBHOOK_URLL');

        try {
            $response = Http::post($webhookUrl, ["text" => $message]);

            if (!$response->successful()) {
                Log::error("❌ Slack gửi thất bại", [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error("❌ Lỗi khi gọi Slack API", [
                'message' => $e->getMessage(),
            ]);
        }
    }
}
