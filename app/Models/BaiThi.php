<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BaiThi extends Model
{
    protected $table = 'bai_this';
    protected $fillable = [
        'ma_bai_thi', 'dang_ky_id', 'de_thi_id', 'gio_bat_dau', 'gio_nop', 'trang_thai',
        'diem_tu_dong', 'diem_cham_tay', 'diem_tong', 'cham_xong',
        'giang_vien_id', 'ngay_cham', 'ngay_cong_bo', 'nguoi_cong_bo_id',
    ];

    protected function casts(): array
    {
        return [
            'gio_bat_dau' => 'datetime',
            'gio_nop' => 'datetime',
            'ngay_cham' => 'datetime',
            'ngay_cong_bo' => 'datetime',
            'cham_xong' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (BaiThi $baiThi) {
            if (empty($baiThi->ma_bai_thi)) {
                $baiThi->ma_bai_thi = 'BT' . date('ymd') . strtoupper(Str::random(5));
            }
        });
    }

    public function dangKy()
    {
        return $this->belongsTo(DangKy::class);
    }

    public function deThi()
    {
        return $this->belongsTo(DeThi::class);
    }

    public function giangVien()
    {
        return $this->belongsTo(User::class, 'giang_vien_id');
    }

    public function nguoiCongBo()
    {
        return $this->belongsTo(User::class, 'nguoi_cong_bo_id');
    }

    public function cauTraLois()
    {
        return $this->hasMany(CauTraLoi::class);
    }

    public function phucKhaos()
    {
        return $this->hasMany(PhucKhao::class);
    }

    public function chungNhan()
    {
        return $this->hasOne(ChungNhan::class);
    }

    // Sinh viên qua Đăng ký
    public function sinhVien()
    {
        return $this->dangKy?->sinhVien;
    }

    public function getMaBaiThiHienThiAttribute(): string
    {
        return $this->ma_bai_thi ?: ('BT' . str_pad((string) $this->id, 6, '0', STR_PAD_LEFT));
    }

    public function getIsDatAttribute(): bool
    {
        return ($this->diem_tong !== null && (float) $this->diem_tong >= 50);
    }

    public function getKetQuaTextAttribute(): string
    {
        if ($this->diem_tong === null) {
            return 'Chưa có điểm';
        }
        return $this->is_dat ? 'Đạt' : 'Không đạt';
    }

    public function getHanPhucKhaoAttribute(): \Carbon\Carbon
    {
        $base = $this->ngay_cong_bo ?: $this->updated_at ?: now();
        return $base->copy()->addDays(7);
    }

    public function getConHanPhucKhaoAttribute(): bool
    {
        return now()->lte($this->han_phuc_khao);
    }

    public function getTrangThaiBaiLamLabelAttribute(): string
    {
        if ($this->cham_xong || in_array($this->trang_thai, ['da_cham', 'da_cong_bo'])) {
            return 'Đã chấm';
        }
        if ($this->trang_thai === 'dang_cham') {
            return 'Đang chấm';
        }
        return 'Chưa chấm';
    }
}

