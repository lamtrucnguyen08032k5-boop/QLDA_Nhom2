<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KyThi extends Model
{
    protected $table = 'ky_this';

    protected $fillable = [
        'ten_ky_thi',
        'nam_hoc',
        'hoc_ky',
        'mo_ta',
        'trang_thai',
    ];

    public function lichThis()
    {
        return $this->hasMany(LichThi::class, 'ky_thi_id');
    }

    public function dangKys()
    {
        return $this->hasManyThrough(DangKy::class, LichThi::class, 'ky_thi_id', 'lich_thi_id');
    }

    public function getTongSoLuongToiDaAttribute(): int
    {
        return (int) $this->lichThis->sum('so_luong_toi_da');
    }

    public function getTongSoThiSinhAttribute(): int
    {
        return (int) $this->dangKys()->whereNotIn('dang_kys.trang_thai', ['da_huy', 'tu_choi'])->count();
    }

    public function coTheXoa(): bool
    {
        return ! $this->dangKys()->exists();
    }

    public function getTrangThaiBadgeAttribute(): string
    {
        return match ($this->trang_thai) {
            'dang_mo_dang_ky' => '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1"><i class="bi bi-door-open me-1"></i>Đang mở đăng ký</span>',
            'da_dong_dang_ky' => '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1"><i class="bi bi-door-closed me-1"></i>Đã đóng đăng ký</span>',
            'dang_dien_ra' => '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1"><i class="bi bi-play-circle me-1"></i>Đang diễn ra</span>',
            'da_ket_thuc' => '<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1"><i class="bi bi-check2-all me-1"></i>Đã kết thúc</span>',
            default => '<span class="badge bg-light text-dark border px-2 py-1">' . e($this->trang_thai) . '</span>',
        };
    }
}
