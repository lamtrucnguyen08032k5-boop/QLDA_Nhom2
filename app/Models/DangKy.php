<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DangKy extends Model
{
    protected $table = 'dang_kys';

    protected $fillable = [
        'sinh_vien_id', 'lich_thi_id', 'trang_thai', 'ly_do_tu_choi', 'ngay_duyet', 'nguoi_duyet_id',
        'ma_dang_ky',
        'so_dien_thoai', 'ngay_sinh', 'gioi_tinh', 'dan_toc', 'noi_sinh',
        'tinh_thanh_pho_code', 'tinh_thanh_pho_ten', 'xa_phuong_code', 'xa_phuong_ten',
        'dia_chi_chi_tiet', 'email_lien_he',
        'so_cccd', 'anh_cccd_truoc', 'anh_cccd_sau', 'anh_ho_so', 'anh_the_sv',
        'truong_can_bo_sung', 'ly_do_bo_sung', 'ly_do_tung_truong', 'han_bo_sung', 'ngay_bo_sung', 'han_thanh_toan',
        'phuong_thuc_thanh_toan', 'trang_thai_thanh_toan', 'ma_giao_dich', 'so_tien', 'ngay_thanh_toan', 'ngay_nhac_thanh_toan',
    ];

    protected function casts(): array
    {
        return [
            'ngay_duyet' => 'datetime',
            'ngay_sinh' => 'date',
            'han_bo_sung' => 'datetime',
            'ngay_bo_sung' => 'datetime',
            'han_thanh_toan' => 'datetime',
            'ngay_thanh_toan' => 'datetime',
            'ngay_nhac_thanh_toan' => 'datetime',
            'truong_can_bo_sung' => 'array',
            'ly_do_tung_truong' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (DangKy $dangKy) {
            if (empty($dangKy->ma_dang_ky)) {
                $dangKy->ma_dang_ky = 'DK' . now()->format('ymd') . strtoupper(Str::random(5));
            }
            if (empty($dangKy->han_thanh_toan)) {
                $han2Ngay = now()->addDays(2);
                if ($dangKy->lichThi && $dangKy->lichThi->han_dang_ky && $dangKy->lichThi->han_dang_ky->lt($han2Ngay)) {
                    $dangKy->han_thanh_toan = $dangKy->lichThi->han_dang_ky;
                } else {
                    $dangKy->han_thanh_toan = $han2Ngay;
                }
            }
        });
    }

    public function sinhVien()
    {
        return $this->belongsTo(User::class, 'sinh_vien_id');
    }

    public function nguoiDuyet()
    {
        return $this->belongsTo(User::class, 'nguoi_duyet_id');
    }

    public function lichThi()
    {
        return $this->belongsTo(LichThi::class);
    }

    public function baiThi()
    {
        return $this->hasOne(BaiThi::class);
    }

    public function lichSuXuLy()
    {
        return $this->hasMany(LichSuXuLyHoSo::class, 'dang_ky_id')->orderByDesc('created_at');
    }

    public function diaChiDayDu(): ?string
    {
        $phan = array_filter([$this->dia_chi_chi_tiet, $this->xa_phuong_ten, $this->tinh_thanh_pho_ten]);
        return empty($phan) ? null : implode(', ', $phan);
    }

    public function isHetHanBoSungOnline(): bool
    {
        if ($this->trang_thai !== 'cho_bo_sung' || ! $this->han_bo_sung) {
            return false;
        }
        return now()->gt($this->han_bo_sung);
    }

    public function hanCuoiBoSungTrucTiep(): string
    {
        if (! $this->lichThi || ! $this->lichThi->ngay_thi) {
            return now()->addDays(3)->format('d/m/Y');
        }
        return $this->lichThi->ngay_thi->copy()->subDays(1)->format('d/m/Y');
    }

    /**
     * Hạn chót thanh toán lệ phí thi: muộn nhất là sau 2 ngày (48 giờ) kể từ khi tạo hồ sơ,
     * nhưng không vượt quá hạn đăng ký của ca thi.
     */
    public function hanThanhToan(): \Carbon\Carbon
    {
        if ($this->han_thanh_toan) {
            return $this->han_thanh_toan;
        }

        $han2Ngay = $this->created_at ? $this->created_at->copy()->addDays(2) : now()->addDays(2);
        if ($this->lichThi && $this->lichThi->han_dang_ky && $this->lichThi->han_dang_ky->lt($han2Ngay)) {
            return $this->lichThi->han_dang_ky;
        }
        return $han2Ngay;
    }

    /**
     * Kiểm tra hồ sơ đã quá hạn thanh toán chưa
     */
    public function isQuaHanThanhToan(): bool
    {
        if ($this->trang_thai_thanh_toan === 'da_thanh_toan' || $this->trang_thai === 'da_huy') {
            return false;
        }
        return now()->gt($this->hanThanhToan());
    }

    /**
     * Kiểm tra có đang trong vòng 12h trước khi hết hạn nộp tiền không
     */
    public function isSapHetHanThanhToan(): bool
    {
        if ($this->trang_thai_thanh_toan === 'da_thanh_toan' || $this->trang_thai === 'da_huy') {
            return false;
        }
        $han = $this->hanThanhToan();
        return now()->lt($han) && now()->gte($han->copy()->subHours(12));
    }

    /**
     * Tự động kiểm tra và hủy hồ sơ nếu đã quá hạn thanh toán (sau 2 ngày)
     */
    public function kiemTraVaCapNhatQuaHan(): bool
    {
        if ($this->isQuaHanThanhToan()) {
            $trangThaiTruoc = $this->trang_thai;
            $this->update([
                'trang_thai' => 'da_huy',
                'trang_thai_thanh_toan' => 'thanh_toan_that_bai',
            ]);

            LichSuXuLyHoSo::create([
                'dang_ky_id' => $this->id,
                'user_id' => null,
                'vai_tro' => 'admin',
                'hanh_dong' => 'huy',
                'trang_thai_truoc' => $trangThaiTruoc,
                'trang_thai_sau' => 'da_huy',
                'noi_dung' => 'Hệ thống tự động huỷ đăng ký do quá hạn thanh toán lệ phí thi (sau 2 ngày kể từ khi đăng ký).',
            ]);

            return true;
        }
        return false;
    }

    public function nhanTrangThaiLabel(): string
    {
        if ($this->trang_thai_thanh_toan === 'cho_thanh_toan' && $this->trang_thai !== 'da_huy') {
            return 'Chờ thanh toán';
        }

        return match ($this->trang_thai) {
            'cho_duyet' => 'Chờ duyệt',
            'cho_bo_sung' => 'Yêu cầu bổ sung',
            'da_bo_sung' => 'Đã bổ sung/Chờ duyệt lại',
            'da_duyet' => 'Đã duyệt',
            'tu_choi' => 'Đã từ chối',
            'da_huy' => 'Đã huỷ',
            default => $this->trang_thai,
        };
    }

    public function nhanTrangThaiThanhToanLabel(): string
    {
        return match ($this->trang_thai_thanh_toan) {
            'da_thanh_toan' => 'Đã thanh toán',
            'thanh_toan_that_bai' => 'Thanh toán thất bại',
            default => 'Chờ thanh toán',
        };
    }
}
