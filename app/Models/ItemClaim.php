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
        'owner_submitted_at' => 'datetime',
        'owner_verified_at'  => 'datetime',
        'finder_verified_at' => 'datetime',
        'claimant_seen_at'   => 'datetime',
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

    public function isKehilangan(): bool
    {
        return $this->report?->item_kind === 'kehilangan';
    }

    /** Pihak yang memegang barang (penemu). */
    public function finderId(): int
    {
        return (int) ($this->isKehilangan() ? $this->user_id : $this->report->user_id);
    }

    /** Pemilik barang. */
    public function ownerId(): int
    {
        return (int) ($this->isKehilangan() ? $this->report->user_id : $this->user_id);
    }

    /** Kedua pihak sudah saling memverifikasi -> maju ke step 3. */
    public function advanceIfBothVerified(): void
    {
        if ($this->current_step == 2 && $this->owner_verified_at && $this->finder_verified_at && !$this->rejected_at) {
            $this->forceFill(['current_step' => 3, 'status' => 'verified'])->save();
        }
    }

    /** Nomor WA format internasional (input form memakai prefix +62). */
    public static function waNumber(?string $phone): string
    {
        $d = preg_replace('/\D/', '', (string) $phone);
        return str_starts_with($d, '62') ? $d : '62' . ltrim($d, '0');
    }

    public function stages(string $first = '1. Dikirim'): array
    {
        $tz = 'Asia/Jakarta';
        return [
            1 => [$first, $this->created_at?->timezone($tz)->format('d M H:i')],
            2 => ['2. Balasan', $this->owner_submitted_at?->timezone($tz)->format('d M H:i')],
            3 => ['3. Diverifikasi', null],
            4 => ['4. Kesepakatan', null],
            5 => ['5. QR Tiket Pengambilan', $this->pickup_date ? $this->pickup_date->format('d M') . ' ' . $this->pickup_time : null],
            6 => ['6. Selesai', null],
        ];
    }
}
