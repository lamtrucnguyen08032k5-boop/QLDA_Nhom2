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
        'trang_thai_cong_bo', 'ngay_cong_bo', 'nguoi_cong_bo_id',
    ];

    protected function casts(): array
    {
        return [
            'ngay_thi' => 'date',
            'han_dang_ky' => 'datetime',
            'ngay_cong_bo' => 'datetime',
        ];
    }

    public function kyThi()
    {
        return $this->belongsTo(KyThi::class, 'ky_thi_id');
    }

    public function nguoiCongBo()
    {
        return $this->belongsTo(User::class, 'nguoi_cong_bo_id');
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

    // Nghiệp vụ: Ngay sau khi sinh viên đăng ký, hệ thống ghi nhận lượt đăng ký và trừ số chỗ còn lại
    // (tất cả hồ sơ chưa bị hủy hoặc chưa bị từ chối đều được tính giữ chỗ)
    public function dangKysGiuCho()
    {
        return $this->dangKys()->whereNotIn('trang_thai', ['da_huy', 'tu_choi']);
    }

    public function getSoChoDaDangKyAttribute(): int
    {
        return $this->dangKysGiuCho()->count();
    }

    public function getSoChoConLaiAttribute(): int
    {
        $soLuong = $this->so_luong_toi_da ?? 0;
        return max(0, $soLuong - $this->so_cho_da_dang_ky);
    }

    public function daHetCho(): bool
    {
        return $this->so_cho_con_lai <= 0;
    }

    public function daHetHanDangKy(): bool
    {
        return now()->greaterThan($this->han_dang_ky);
    }
}
