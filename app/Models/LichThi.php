<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LichThi extends Model
{
    protected $table = 'lich_this';

    protected $fillable = [
        'ky_thi_id', 'ten_ky_thi', 'loai_chung_chi', 'khoa_id', 'ngay_thi', 'gio_bat_dau',
        'thoi_gian_thi_phut', 'phong_thi', 'so_luong_toi_da', 'han_dang_ky',
        'le_phi', 'ma_ca_thi', 'trang_thai', 'de_thi_id',
    ];

    protected function casts(): array
    {
        return [
            'ngay_thi' => 'date',
            'han_dang_ky' => 'datetime',
        ];
    }

    public function kyThi()
    {
        return $this->belongsTo(KyThi::class, 'ky_thi_id');
    }

    public function getGioKetThucAttribute(): string
    {
        if (! $this->gio_bat_dau) {
            return '';
        }
        return \Carbon\Carbon::parse($this->gio_bat_dau)->addMinutes($this->thoi_gian_thi_phut ?? 60)->format('H:i');
    }

    public function khoa()
    {
        return $this->belongsTo(Khoa::class);
    }

    public function deThi()
    {
        return $this->belongsTo(DeThi::class, 'de_thi_id');
    }

    public function dangKys()
    {
        return $this->hasMany(DangKy::class);
    }

    public function dangKysDaDuyet()
    {
        return $this->dangKys()->where('trang_thai', 'da_duyet');
    }

    public function daHetHanDangKy(): bool
    {
        return now()->greaterThan($this->han_dang_ky);
    }
}
