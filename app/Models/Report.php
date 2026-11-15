<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'ticket_code',
    'user_id',
    'category_id',
    'type',
    'item_kind',
    'title',
    'urgency',
    'description',
    'photo',
    'photos',
    'photo_after',
    'latitude',
    'longitude',
    'address',
    'reporter_name',
    'reporter_phone',
    'event_date',
    'event_time',
    'status',
    'progress_percentage',
    'current_step',
    'supervisor_name',
    'supervisor_notes',
    'completion_notes',
    'admin_note',
])]
/**
 * @property int|null $progress_percentage
 * @property string|null $ticket_code
 * @property string|null $urgency
 * @property array|null $photos
 * @property string|null $photo_after
 * @property string|null $reporter_name
 * @property string|null $reporter_phone
 * @property int|null $current_step
 * @property string|null $supervisor_name
 * @property string|null $supervisor_notes
 * @property string|null $completion_notes
 */
class Report extends Model
{
    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'photos' => 'array',
            'hidden_at' => 'datetime',
            'hidden_seen_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** Relasi ke semua klaim yang masuk untuk laporan ini. */
    public function claims(): HasMany
    {
        return $this->hasMany(ItemClaim::class, 'report_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ReportLog::class)->oldest();
    }

    /** Catat satu peristiwa ke timeline. $notify=false berarti tidak memicu notifikasi. */
    public function catat(string $title, ?string $note = null, ?string $photo = null, bool $notify = true): ReportLog
    {
        return $this->logs()->create([
            'status'  => $this->status,
            'step'    => $this->current_step,
            'title'   => $title,
            'note'    => $note,
            'photo'   => $photo,
            'seen_at' => $notify ? null : now(),
        ]);
    }

    /** Hanya laporan barang hilang / ditemukan. */
    public function scopeBarang(Builder $query): void
    {
        $query->where('type', 'barang_hilang');
    }

    /** Hanya laporan infrastruktur. */
    public function scopeInfrastruktur(Builder $query): void
    {
        $query->where('type', 'infrastruktur');
    }

    /**
     * Laporan yang tampil di peta publik:
     * - barang          : status verified
     * - infrastruktur   : verified (Perlu Diperbaiki) dan in_progress (Sedang Diperbaiki)
     */
    public function scopeTayang(Builder $query): void
{
    $query->whereNull('hidden_at')->where(function (Builder $w) {
        $w->where(fn (Builder $b) => $b->where('type', 'barang_hilang')->where('status', 'verified'))
            ->orWhere(fn (Builder $i) => $i->where('type', 'infrastruktur')->whereIn('status', ['verified', 'in_progress']));
    });
}

    /** Laporan dalam radius (km) dari titik pusat, memakai rumus Haversine. */
    public function scopeWithinRadius(Builder $query, float $lat, float $lng, float $km): void
    {
        $query->whereNotNull('latitude')->whereNotNull('longitude')->whereRaw(
            '(6371 * acos(least(1, cos(radians(?)) * cos(radians(latitude))
              * cos(radians(longitude) - radians(?))
              + sin(radians(?)) * sin(radians(latitude))))) <= ?',
            [$lat, $lng, $lat, $km]
        );
    }

    protected function photoUrl(): Attribute
    {
        return Attribute::get(
            fn() => $this->photo ? asset('storage/' . $this->photo) : null
        );
    }

    // Helper Generate Tiket Otomatis
    public static function boot()
    {
        parent::boot();
        static::creating(function ($report) {
            if (empty($report->ticket_code)) {
                if ($report->type === 'infrastruktur') {
                    $prefix = 'JL';
                } elseif ($report->item_kind === 'penemuan') {
                    $prefix = 'BRGT';
                } else {
                    $prefix = 'BRGH';
                }
                $report->ticket_code = '#' . $prefix . date('ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            }
        });
    }

    // Label Status untuk UI
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'     => 'Belum Diverifikasi',
            'verified'    => 'Menunggu Verifikasi & Jadwal',
            'in_progress' => 'Sedang Dikerjakan (' . ($this->progress_percentage ?? 0) . '% Selesai)',
            'done'        => 'Selesai',
            'resolved'    => 'Tuntas Dikembalikan',
            'rejected'    => 'Ditolak',
            default       => 'Perlu Diperbaiki',
        };
    }

    /** Laporan yang sudah selesai ditangani (aman ditampilkan ke publik). */
    public function scopeSelesai(Builder $query): void
{
    $query->whereIn('status', ['done', 'resolved'])->whereNull('hidden_at');
}

    /** Laporan yang tidak diturunkan admin. */
    public function scopeTampil(Builder $query): void
    {
        $query->whereNull('hidden_at');
    }
}
