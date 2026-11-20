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
        'giang_vien_1_id', 'giang_vien_2_id', 'nhan_xet_1', 'nhan_xet_2',
        'diem_tu_luan_gv1', 'diem_tu_luan_gv2',
        'ngay_cham_1', 'ngay_cham_2', 'diem_chot', 'ly_do_thong_nhat', 'da_khoa',
    ];

    protected function casts(): array
    {
        return [
            'gio_bat_dau' => 'datetime',
            'gio_nop' => 'datetime',
            'ngay_cham' => 'datetime',
            'ngay_cham_1' => 'datetime',
            'ngay_cham_2' => 'datetime',
            'ngay_cong_bo' => 'datetime',
            'cham_xong' => 'boolean',
            'da_khoa' => 'boolean',
            'diem_tu_luan_gv1' => 'decimal:2',
            'diem_tu_luan_gv2' => 'decimal:2',
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

    public function giangVien1()
    {
        return $this->belongsTo(User::class, 'giang_vien_1_id');
    }

    public function giangVien2()
    {
        return $this->belongsTo(User::class, 'giang_vien_2_id');
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

    public function phucKhaoHienTai()
    {
        return $this->hasOne(PhucKhao::class)->latestOfMany();
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

    public function getHanPhucKhaoAttribute(): ?\Carbon\Carbon
    {
        if (! $this->ngay_cong_bo) {
            return null;
        }
        return $this->ngay_cong_bo->copy()->addDays(7);
    }

    public function getConHanPhucKhaoAttribute(): bool
    {
        if ($this->trang_thai !== 'da_cong_bo' || ! $this->ngay_cong_bo) {
            return false;
        }
        return now()->lte($this->han_phuc_khao);
    }

    public function getDaPhucKhaoAttribute(): bool
    {
        return $this->phucKhaos()
            ->whereIn('trang_thai', [
                PhucKhao::TRANG_THAI_CHO_THANH_TOAN,
                PhucKhao::TRANG_THAI_CHO_TIEP_NHAN,
                PhucKhao::TRANG_THAI_CHO_PHAN_CONG,
                PhucKhao::TRANG_THAI_DANG_XU_LY,
                PhucKhao::TRANG_THAI_CHO_ADMIN_DUYET,
                PhucKhao::TRANG_THAI_HOAN_TAT,
            ])
            ->exists();
    }

    public function getTrangThaiBaiLamLabelAttribute(): string
    {
        if ($this->cham_xong || in_array($this->trang_thai, ['da_cham', 'da_chot', 'da_cong_bo'])) {
            return 'Đã chấm';
        }
        if (in_array($this->trang_thai, ['dang_cham', 'dang_cham_1', 'dang_cham_2', 'cho_cham_1', 'cho_cham_2', 'cho_thong_nhat'])) {
            return 'Đang chấm';
        }
        return 'Chưa chấm';
    }

    // Helper: Lấy tổng điểm tối đa phần tự luận
    public function getTongDiemToiDaTuLuanAttribute(): float
    {
        $fromDeThi = (float) ($this->deThi?->cauHois()
            ->whereIn('loai_cau', ['tu_luan', 'tuluan'])
            ->sum('diem') ?? 0);
        if ($fromDeThi > 0) {
            return $fromDeThi;
        }
        return (float) $this->cauTraLois()
            ->whereHas('cauHoi', fn($q) => $q->whereIn('loai_cau', ['tu_luan', 'tuluan']))
            ->with('cauHoi')
            ->get()
            ->sum(fn($ctl) => $ctl->cauHoi?->diem ?? 0);
    }

    // Helper: Tổng điểm tối đa phần trắc nghiệm
    public function getTongDiemToiDaTracNghiemAttribute(): float
    {
        return (float) ($this->deThi?->cauHois()
            ->where('loai_cau', 'tracnghiem')
            ->sum('diem') ?? 0);
    }

    // Helper: Số câu trắc nghiệm làm đúng
    public function getSoCauTracNghiemDungAttribute(): int
    {
        return $this->cauTraLois()
            ->whereHas('cauHoi', fn($q) => $q->where('loai_cau', 'tracnghiem'))
            ->where('diem_dat', '>', 0)
            ->count();
    }

    // Helper: Tổng số câu trắc nghiệm
    public function getTongSoCauTracNghiemAttribute(): int
    {
        return $this->deThi?->cauHois()
            ->where('loai_cau', 'tracnghiem')
            ->count() ?? 0;
    }

    // Helper: Tổng số câu tự luận
    public function getTongSoCauTuLuanAttribute(): int
    {
        return $this->deThi?->cauHois()
            ->whereIn('loai_cau', ['tuluan', 'tu_luan'])
            ->count() ?? 0;
    }

    // Helper: Kiểm tra bài thi có câu tự luận hay không
    public function getCoTuLuanAttribute(): bool
    {
        return $this->tong_so_cau_tu_luan > 0;
    }
}
