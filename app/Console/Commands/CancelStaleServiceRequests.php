<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\UserServiceRequest;

use App\Enums\ServiceRequestStatus;

use App\Utilities;

class CancelStaleServiceRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'service-request:cancel-stale';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel pending service requests older than configured timeou';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $timeoutMinutes = (int) env('SERVICE_REQUEST_TIMEOUT', 60);
        $cutoffTime = Carbon::now()->subMinutes($timeoutMinutes);

        $maxCancellations = env('STALE_SERVICE_REQUEST_MAX_CANCELLATION', 1000);
        
        $cancelledCount = UserServiceRequest::where('status', ServiceRequestStatus::PENDING->value)
            ->where('created_at', '<=', $cutoffTime)
            ->limit($maxCancellations)
            ->update(['status' => 'cancelled']);

        if ($cancelledCount >= $maxCancellations) {
            Utilities::workerLog("Hit cancellation limit of {$maxCancellations}", [
                'timeout_minutes' => $timeoutMinutes
            ]);
        }
        
        $this->info("Cancelled {$cancelledCount} pending service requests.");
        Utilities::workerLog("Cancelled {$cancelledCount} pending requests", [
            'timeout_minutes' => $timeoutMinutes
        ]);
    }
}
