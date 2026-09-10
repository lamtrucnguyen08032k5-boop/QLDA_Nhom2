<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LichSuXuLyHoSo extends Model
{
    protected $table = 'lich_su_xu_ly_ho_sos';

    protected $fillable = [
        'dang_ky_id',
        'user_id',
        'vai_tro',
        'hanh_dong',
        'trang_thai_truoc',
        'trang_thai_sau',
        'noi_dung',
    ];

    public function dangKy()
    {
        return $this->belongsTo(DangKy::class, 'dang_ky_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function nhanHanhDongLabel(): string
    {
        return match ($this->hanh_dong) {
            'tao_ho_so' => 'Khởi tạo hồ sơ',
            'duyet' => 'Duyệt hồ sơ',
            'yeu_cau_bo_sung' => 'Yêu cầu bổ sung',
            'bo_sung_ho_so' => 'Bổ sung thông tin',
            'bo_sung_ho_so_qua_han' => 'Admin hỗ trợ bổ sung quá hạn',
            'tu_choi' => 'Từ chối hồ sơ',
            'huy' => 'Hủy hồ sơ',
            default => $this->hanh_dong,
        };
    }

    public function nhanVaiTroLabel(): string
    {
        return match ($this->vai_tro) {
            'admin' => 'Admin / Phòng Khảo thí',
            'sinh_vien' => 'Sinh viên',
            'he_thong' => 'Hệ thống',
            default => $this->vai_tro,
        };
    }
}
