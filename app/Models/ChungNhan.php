<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChungNhan extends Model
{
    protected $table = 'chung_nhans';

    // Status constants theo Implementation Plan v6
    public const TRANG_THAI_CHO_XU_LY = 'cho_xu_ly';   // HĐ4: SV đăng ký
    public const TRANG_THAI_DANG_XU_LY = 'dang_xu_ly'; // HĐ7: Admin tiếp nhận/xử lý
    public const TRANG_THAI_DA_CAP = 'da_cap';         // HĐ9: Admin cấp chứng nhận
    public const TRANG_THAI_TU_CHOI = 'tu_choi';       // Admin từ chối

    protected $fillable = [
        'bai_thi_id', 'sinh_vien_id', 'nguoi_cap_id', 'so_chung_nhan', 'trang_thai',
        'dia_chi_nhan', 'so_dien_thoai', 'file_chung_nhan', 'ngay_cap', 'ghi_chu',
    ];

    protected function casts(): array
    {
        return [
            'ngay_cap' => 'datetime',
        ];
    }

    public function baiThi()
    {
        return $this->belongsTo(BaiThi::class);
    }

    public function sinhVien()
    {
        return $this->belongsTo(User::class, 'sinh_vien_id');
    }

    public function nguoiCap()
    {
        return $this->belongsTo(User::class, 'nguoi_cap_id');
    }
}
