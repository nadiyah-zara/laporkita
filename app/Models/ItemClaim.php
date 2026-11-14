<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'user_id',
        'claim_code',
        'secret_unique_feature',
        'secret_inner_color',
        'secret_full_detail',
        'proof_photo',
        'current_step',
        'status',
        'pickup_location',
        'pickup_date',
        'pickup_time',
    ];

    protected $casts = [
        'pickup_date' => 'date',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'report_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    /** Label status dari sudut pandang penemu (pemilik laporan). */
    public function getReporterLabelAttribute(): array
    {
        if ($this->rejected_at)         return ['Ditolak', 'bg-rose-50 text-rose-600'];
        if ($this->current_step >= 6)   return ['Selesai', 'bg-emerald-100 text-emerald-700'];
        if ($this->current_step >= 3)   return ['Diverifikasi', 'bg-emerald-50 text-emerald-600'];
        if ($this->reporter_seen_at)    return ['Sudah Diperiksa', 'bg-amber-50 text-amber-600'];
        return ['Baru', 'bg-blue-50 text-blue-600'];
    }
}
