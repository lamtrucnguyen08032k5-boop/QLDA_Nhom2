<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhucKhao extends Model
{
    protected $table = 'phuc_khaos';

    // Status constants
    public const TRANG_THAI_CHO_THANH_TOAN = 'cho_thanh_toan'; // Đã tạo đơn, chờ thanh toán
    public const TRANG_THAI_CHO_TIEP_NHAN = 'cho_tiep_nhan';   // Đã thanh toán, chờ Admin tiếp nhận
    public const TRANG_THAI_CHO_PHAN_CONG = 'cho_phan_cong';   // Admin tiếp nhận, chờ Khoa phân công
    public const TRANG_THAI_DANG_XU_LY = 'dang_xu_ly';         // Khoa phân công GV, GV đang chấm lại
    public const TRANG_THAI_CHO_ADMIN_DUYET = 'cho_admin_duyet'; // GV gửi kết quả, chờ Admin duyệt
    public const TRANG_THAI_HOAN_TAT = 'hoan_tat';             // Admin duyệt hoàn tất
    public const TRANG_THAI_TU_CHOI = 'tu_choi';               // Admin từ chối

    // Trạng thái thanh toán
    public const TT_THANH_TOAN_CHO = 'cho_thanh_toan';
    public const TT_THANH_TOAN_DA = 'da_thanh_toan';
    public const TT_THANH_TOAN_THAT_BAI = 'thanh_toan_that_bai';

    // Lệ phí phúc khảo chuẩn (VNĐ)
    public const LE_PHI_MAC_DINH = 50000;

    protected $fillable = [
        'ma_phuc_khao', 'bai_thi_id', 'sinh_vien_id', 'giang_vien_id', 'admin_tiep_nhan_id',
        'admin_duyet_id', 'ly_do', 'file_minh_chung', 'le_phi', 'trang_thai_thanh_toan',
        'phuong_thuc_thanh_toan', 'ma_giao_dich', 'ngay_thanh_toan',
        'trang_thai', 'phan_hoi', 'diem_truoc', 'diem_sau', 'xu_ly_boi',
        'ngay_tiep_nhan', 'ngay_xu_ly', 'ngay_duyet', 'ly_do_duyet',
    ];

    protected function casts(): array
    {
        return [
            'ngay_thanh_toan' => 'datetime',
            'ngay_tiep_nhan' => 'datetime',
            'ngay_xu_ly' => 'datetime',
            'ngay_duyet' => 'datetime',
            'le_phi' => 'decimal:2',
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

    public function getMaPhucKhaoHienThiAttribute(): string
    {
        return $this->ma_phuc_khao ?: ('PK' . ($this->created_at ? $this->created_at->format('ymd') : date('ymd')) . str_pad((string) $this->id, 4, '0', STR_PAD_LEFT));
    }
}
