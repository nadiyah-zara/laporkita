<?php

namespace App\Http\Controllers;

use App\Models\ReportLog;

class NotifikasiController extends Controller
{
    public function logBaca(ReportLog $log)
    {
        $log->loadMissing('report');
        abort_unless($log->report && $log->report->user_id === auth()->id(), 403);

        if (!$log->seen_at) {
            $log->forceFill(['seen_at' => now()])->save();
        }

        return response()->noContent();
    }
}