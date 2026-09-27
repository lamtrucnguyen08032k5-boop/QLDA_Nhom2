<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhucKhao extends Model
{
    protected $table = 'phuc_khaos';

    // Status constants theo Implementation Plan v6
    public const TRANG_THAI_CHO_TIEP_NHAN = 'cho_tiep_nhan';   // HĐ6: SV gửi
    public const TRANG_THAI_CHO_PHAN_CONG = 'cho_phan_cong';   // HĐ9: Admin tiếp nhận, chờ Khoa
    public const TRANG_THAI_DANG_XU_LY = 'dang_xu_ly';         // HĐ11: Khoa phân công GV, GV đang xử lý
    public const TRANG_THAI_CHO_ADMIN_DUYET = 'cho_admin_duyet'; // HĐ16: GV gửi kết quả, chờ Admin duyệt
    public const TRANG_THAI_HOAN_TAT = 'hoan_tat';             // HĐ20: Admin duyệt hoàn tất
    public const TRANG_THAI_TU_CHOI = 'tu_choi';               // HĐ8: Admin từ chối

    protected $fillable = [
        'bai_thi_id', 'sinh_vien_id', 'giang_vien_id', 'admin_tiep_nhan_id',
        'admin_duyet_id', 'ly_do', 'trang_thai', 'phan_hoi', 'diem_truoc',
        'diem_sau', 'xu_ly_boi', 'ngay_tiep_nhan', 'ngay_xu_ly', 'ngay_duyet', 'ly_do_duyet',
    ];

    protected function casts(): array
    {
        return [
            'ngay_tiep_nhan' => 'datetime',
            'ngay_xu_ly' => 'datetime',
            'ngay_duyet' => 'datetime',
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

    public function giangVien()
    {
        return $this->belongsTo(User::class, 'giang_vien_id');
    }

    public function adminTiepNhan()
    {
        return $this->belongsTo(User::class, 'admin_tiep_nhan_id');
    }

    public function adminDuyet()
    {
        return $this->belongsTo(User::class, 'admin_duyet_id');
    }

    public function xuLyBoi()
    {
        return $this->belongsTo(User::class, 'xu_ly_boi');
    }
}
