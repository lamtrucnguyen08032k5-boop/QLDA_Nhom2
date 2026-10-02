<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BaiThi;
use App\Models\ChungNhan;
use App\Models\DangKy;
use App\Models\Khoa;
use App\Models\LichThi;
use App\Models\PhucKhao;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ThongKeController extends Controller
{
    /**
     * Hiển thị trang Thống kê & Báo cáo Khảo thí toàn diện
     */
    public function index(Request $request)
    {
        // 1. Dữ liệu danh mục phục vụ Bộ lọc
        $danhSachKhoa = Khoa::orderBy('ten_khoa')->get();
        $danhSachLichThi = LichThi::orderByDesc('ngay_thi')->get();
        $danhSachKyThiTen = LichThi::select('ten_ky_thi')->distinct()->pluck('ten_ky_thi');

        // 2. Query cơ sở theo Bộ lọc
        $queryLichThi = LichThi::query();
        $queryDangKy = DangKy::query();
        $queryBaiThi = BaiThi::query();

        // Lọc theo Khoa
        if ($request->filled('khoa_id')) {
            $queryLichThi->where('khoa_id', $request->khoa_id);
            $queryDangKy->whereHas('sinhVien', function ($q) use ($request) {
                $q->where('khoa_id', $request->khoa_id);
            });
            $queryBaiThi->whereHas('dangKy.sinhVien', function ($q) use ($request) {
                $q->where('khoa_id', $request->khoa_id);
            });
        }

        // Lọc theo Môn thi / Loại chứng chỉ
        if ($request->filled('loai_chung_chi')) {
            $queryLichThi->where('loai_chung_chi', $request->loai_chung_chi);
            $queryDangKy->whereHas('lichThi', function ($q) use ($request) {
                $q->where('loai_chung_chi', $request->loai_chung_chi);
            });
            $queryBaiThi->whereHas('dangKy.lichThi', function ($q) use ($request) {
                $q->where('loai_chung_chi', $request->loai_chung_chi);
            });
        }

        // Lọc theo Kỳ thi cụ thể (tên hoặc id)
        if ($request->filled('ten_ky_thi')) {
            $queryLichThi->where('ten_ky_thi', $request->ten_ky_thi);
            $queryDangKy->whereHas('lichThi', function ($q) use ($request) {
                $q->where('ten_ky_thi', $request->ten_ky_thi);
            });
            $queryBaiThi->whereHas('dangKy.lichThi', function ($q) use ($request) {
                $q->where('ten_ky_thi', $request->ten_ky_thi);
            });
        }

        // Lọc theo Khoảng thời gian
        if ($request->filled('tu_ngay')) {
            $queryLichThi->whereDate('ngay_thi', '>=', $request->tu_ngay);
            $queryDangKy->whereDate('created_at', '>=', $request->tu_ngay);
            $queryBaiThi->whereDate('created_at', '>=', $request->tu_ngay);
        }
        if ($request->filled('den_ngay')) {
            $queryLichThi->whereDate('ngay_thi', '<=', $request->den_ngay);
            $queryDangKy->whereDate('created_at', '<=', $request->den_ngay);
            $queryBaiThi->whereDate('created_at', '<=', $request->den_ngay);
        }

        // 3. Chỉ số Tổng quan (KPIs Overview)
        $tongCaThi = (clone $queryLichThi)->count();
        $tongChoNgoi = (clone $queryLichThi)->sum('so_luong_toi_da');

        // Đăng ký & Hồ sơ
        $tongDangKy = (clone $queryDangKy)->count();
        $dangKyDaDuyet = (clone $queryDangKy)->where('trang_thai', 'da_duyet')->count();
        $dangKyChoDuyet = (clone $queryDangKy)->whereIn('trang_thai', ['cho_duyet', 'da_bo_sung'])->count();
        $dangKyBoSung = (clone $queryDangKy)->where('trang_thai', 'cho_bo_sung')->count();
        $dangKyTuChoi = (clone $queryDangKy)->where('trang_thai', 'tu_choi')->count();
        $dangKyDaHuy = (clone $queryDangKy)->where('trang_thai', 'da_huy')->count();

        // Tài chính & Doanh thu
        $doanhThuDaThu = (clone $queryDangKy)->where('trang_thai_thanh_toan', 'da_thanh_toan')->sum('so_tien');
        // Nếu số tiền trong dang_kys null, tính theo le_phi của lich_thi
        if ($doanhThuDaThu == 0 && $dangKyDaDuyet > 0) {
            $doanhThuDaThu = (clone $queryDangKy)
                ->join('lich_this', 'dang_kys.lich_thi_id', '=', 'lich_this.id')
                ->where('dang_kys.trang_thai_thanh_toan', 'da_thanh_toan')
                ->orWhere('dang_kys.trang_thai', 'da_duyet')
                ->sum('lich_this.le_phi');
        }
        $doanhThuChoThu = (clone $queryDangKy)
            ->where('trang_thai_thanh_toan', 'cho_thanh_toan')
            ->where('trang_thai', '!=', 'da_huy')
            ->sum('so_tien');

        // Tình hình Dự thi & Kết quả thi
        $tongThiSinhDuThi = (clone $queryBaiThi)->whereNotNull('gio_nop')->count();
        $tongBaiDaCham = (clone $queryBaiThi)->whereNotNull('diem_tong')->count();
        $tongDat = (clone $queryBaiThi)->where('diem_tong', '>=', 50)->count();
        $tongKhongDat = (clone $queryBaiThi)->whereNotNull('diem_tong')->where('diem_tong', '<', 50)->count();
        $tyLeDat = $tongBaiDaCham > 0 ? round(($tongDat / $tongBaiDaCham) * 100, 1) : 0;
        $diemTrungBinh = $tongBaiDaCham > 0 ? round((clone $queryBaiThi)->whereNotNull('diem_tong')->avg('diem_tong'), 2) : 0;
        $diemCaoNhat = (clone $queryBaiThi)->whereNotNull('diem_tong')->max('diem_tong') ?? 0;
        $diemThapNhat = (clone $queryBaiThi)->whereNotNull('diem_tong')->min('diem_tong') ?? 0;

        // Điểm TB trắc nghiệm vs tự luận
        $diemTbTracNghiem = (clone $queryBaiThi)->whereNotNull('diem_tong')->avg('diem_tu_dong') ?? 0;
        $diemTbTuLuan = (clone $queryBaiThi)->whereNotNull('diem_tong')->avg('diem_cham_tay') ?? 0;

        // Phúc khảo & Chứng nhận
        $tongPhucKhao = PhucKhao::count();
        $phucKhaoDaDuyet = PhucKhao::whereIn('trang_thai', ['da_duyet', 'da_xu_ly', 'hoan_thanh'])->count();
        $phucKhaoTangDiem = PhucKhao::whereIn('trang_thai', ['da_duyet', 'da_xu_ly', 'hoan_thanh'])->whereColumn('diem_sau', '>', 'diem_truoc')->count();
        
        $tongChungNhan = ChungNhan::count();
        $chungNhanDaCap = ChungNhan::where('trang_thai', 'da_cap')->count();

        // 4. Phân bố Phổ điểm (Histogram)
        $phoDiem = [
            'kem' => (clone $queryBaiThi)->whereNotNull('diem_tong')->where('diem_tong', '<', 50)->count(),
            'trung_binh' => (clone $queryBaiThi)->whereNotNull('diem_tong')->whereBetween('diem_tong', [50, 64.9])->count(),
            'kha' => (clone $queryBaiThi)->whereNotNull('diem_tong')->whereBetween('diem_tong', [65, 79.9])->count(),
            'gioi' => (clone $queryBaiThi)->whereNotNull('diem_tong')->where('diem_tong', '>=', 80)->count(),
        ];

        // 5. Thống kê theo Môn thi (CNTT vs Tiếng Anh)
        $thongKeMonThi = [
            'cntt' => [
                'ten' => 'Tin học chuẩn CNTT',
                'so_dang_ky' => DangKy::whereHas('lichThi', fn($q) => $q->where('loai_chung_chi', 'cntt'))->count(),
                'so_du_thi' => BaiThi::whereNotNull('gio_nop')->whereHas('dangKy.lichThi', fn($q) => $q->where('loai_chung_chi', 'cntt'))->count(),
                'so_dat' => BaiThi::where('diem_tong', '>=', 50)->whereHas('dangKy.lichThi', fn($q) => $q->where('loai_chung_chi', 'cntt'))->count(),
                'diem_tb' => round(BaiThi::whereNotNull('diem_tong')->whereHas('dangKy.lichThi', fn($q) => $q->where('loai_chung_chi', 'cntt'))->avg('diem_tong') ?? 0, 1),
            ],
            'tieng_anh' => [
                'ten' => 'Tiếng Anh chuẩn đầu ra',
                'so_dang_ky' => DangKy::whereHas('lichThi', fn($q) => $q->where('loai_chung_chi', 'tieng_anh'))->count(),
                'so_du_thi' => BaiThi::whereNotNull('gio_nop')->whereHas('dangKy.lichThi', fn($q) => $q->where('loai_chung_chi', 'tieng_anh'))->count(),
                'so_dat' => BaiThi::where('diem_tong', '>=', 50)->whereHas('dangKy.lichThi', fn($q) => $q->where('loai_chung_chi', 'tieng_anh'))->count(),
                'diem_tb' => round(BaiThi::whereNotNull('diem_tong')->whereHas('dangKy.lichThi', fn($q) => $q->where('loai_chung_chi', 'tieng_anh'))->avg('diem_tong') ?? 0, 1),
            ],
        ];
        foreach ($thongKeMonThi as $k => $v) {
            $thongKeMonThi[$k]['ty_le_dat'] = $v['so_du_thi'] > 0 ? round(($v['so_dat'] / $v['so_du_thi']) * 100, 1) : 0;
        }

        // 6. Thống kê theo Khoa (Top các Khoa tham gia)
        $thongKeTheoKhoa = Khoa::all()->map(function ($khoa) {
            $soDangKy = DangKy::whereHas('sinhVien', fn($q) => $q->where('khoa_id', $khoa->id))->count();
            $soBaiThi = BaiThi::whereNotNull('gio_nop')
                ->whereHas('dangKy.sinhVien', fn($q) => $q->where('khoa_id', $khoa->id))
                ->count();
            $soDat = BaiThi::where('diem_tong', '>=', 50)
                ->whereHas('dangKy.sinhVien', fn($q) => $q->where('khoa_id', $khoa->id))
                ->count();
            $diemTb = BaiThi::whereNotNull('diem_tong')
                ->whereHas('dangKy.sinhVien', fn($q) => $q->where('khoa_id', $khoa->id))
                ->avg('diem_tong') ?? 0;

            return [
                'id' => $khoa->id,
                'ma_khoa' => $khoa->ma_khoa,
                'ten_khoa' => $khoa->ten_khoa,
                'tong_dang_ky' => $soDangKy,
                'so_du_thi' => $soBaiThi,
                'so_dat' => $soDat,
                'ty_le_dat' => $soBaiThi > 0 ? round(($soDat / $soBaiThi) * 100, 1) : 0,
                'diem_tb' => round($diemTb, 1),
            ];
        })->sortByDesc('tong_dang_ky')->values();

        // 7. Doanh thu theo tháng (12 tháng gần nhất)
        $driver = DB::connection()->getDriverName();
        $dateFormat = $driver === 'sqlite'
            ? "strftime('%Y-%m', dang_kys.created_at)"
            : "DATE_FORMAT(dang_kys.created_at, '%Y-%m')";

        $doanhThuTheoThang = DangKy::query()
            ->join('lich_this', 'dang_kys.lich_thi_id', '=', 'lich_this.id')
            ->where('dang_kys.trang_thai', 'da_duyet')
            ->selectRaw("{$dateFormat} as thang, SUM(lich_this.le_phi) as tong, COUNT(dang_kys.id) as so_luong")
            ->groupBy('thang')
            ->orderBy('thang')
            ->take(12)
            ->get();

        // 8. Bảng chi tiết từng Ca thi
        $chiTietCaThi = (clone $queryLichThi)
            ->with(['khoa'])
            ->withCount([
                'dangKys as tong_dang_ky',
                'dangKys as dang_ky_hop_le' => fn($q) => $q->where('trang_thai', 'da_duyet'),
                'dangKys as da_nop_bai' => fn($q) => $q->whereHas('baiThi', fn($b) => $b->whereNotNull('gio_nop')),
                'dangKys as so_dat' => fn($q) => $q->whereHas('baiThi', fn($b) => $b->where('diem_tong', '>=', 50)),
            ])
            ->orderByDesc('ngay_thi')
            ->get()
            ->map(function ($ca) {
                $ca->diem_tb = round(BaiThi::whereHas('dangKy', fn($d) => $d->where('lich_thi_id', $ca->id))->whereNotNull('diem_tong')->avg('diem_tong') ?? 0, 1);
                $ca->ty_le_dat = $ca->da_nop_bai > 0 ? round(($ca->so_dat / $ca->da_nop_bai) * 100, 1) : 0;
                $ca->doanh_thu = $ca->dang_ky_hop_le * ($ca->le_phi ?? 0);
                return $ca;
            });

        return view('admin.thongke.index', compact(
            'danhSachKhoa', 'danhSachLichThi', 'danhSachKyThiTen',
            'tongCaThi', 'tongChoNgoi',
            'tongDangKy', 'dangKyDaDuyet', 'dangKyChoDuyet', 'dangKyBoSung', 'dangKyTuChoi', 'dangKyDaHuy',
            'doanhThuDaThu', 'doanhThuChoThu',
            'tongThiSinhDuThi', 'tongBaiDaCham', 'tongDat', 'tongKhongDat', 'tyLeDat',
            'diemTrungBinh', 'diemCaoNhat', 'diemThapNhat', 'diemTbTracNghiem', 'diemTbTuLuan',
            'tongPhucKhao', 'phucKhaoDaDuyet', 'phucKhaoTangDiem',
            'tongChungNhan', 'chungNhanDaCap',
            'phoDiem', 'thongKeMonThi', 'thongKeTheoKhoa', 'doanhThuTheoThang', 'chiTietCaThi'
        ));
    }
}
