<?php

namespace App\Http\Controllers;

use App\Models\ReportLog;
use App\Models\Report;

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
    public function laporanBaca(Report $report)
{
    abort_unless($report->user_id === auth()->id(), 403);

    if (!$report->hidden_seen_at) {
        $report->forceFill(['hidden_seen_at' => now()])->save();
    }

    return response()->noContent();
}
}