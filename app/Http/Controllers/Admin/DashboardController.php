<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BaiThi;
use App\Models\DangKy;
use App\Models\LichThi;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // Thống kê: doanh thu, số thí sinh thi, số thí sinh theo khóa
    public function index()
    {
        $tongLichThi = LichThi::count();
        $tongDangKyDaDuyet = DangKy::where('trang_thai', 'da_duyet')->count();
        $tongDaThi = BaiThi::whereNotNull('gio_nop')->count();

        $dateFormat = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', dang_kys.created_at)"
            : "DATE_FORMAT(dang_kys.created_at, '%Y-%m')";

        $doanhThuTheoThang = DangKy::query()
            ->join('lich_this', 'dang_kys.lich_thi_id', '=', 'lich_this.id')
            ->where('dang_kys.trang_thai', 'da_duyet')
            ->selectRaw("{$dateFormat} as thang, SUM(lich_this.le_phi) as tong")
            ->groupBy('thang')
            ->orderBy('thang')
            ->get();

        $tongDoanhThu = $doanhThuTheoThang->sum('tong');

        // Số thí sinh theo khóa học (VD: K23, K24...)
        $sinhVienTheoKhoaHoc = User::query()
            ->where('role', 'sinhvien')
            ->selectRaw('khoa_hoc, COUNT(*) as so_luong')
            ->groupBy('khoa_hoc')
            ->orderBy('khoa_hoc')
            ->get();

        // Số thí sinh thi theo ca thi
        $thiSinhTheoCa = LichThi::withCount(['dangKys as so_thi_sinh' => function ($q) {
            $q->where('trang_thai', 'da_duyet');
        }])->orderByDesc('ngay_thi')->take(10)->get();

        return view('admin.dashboard', compact(
            'tongLichThi', 'tongDangKyDaDuyet', 'tongDaThi',
            'doanhThuTheoThang', 'tongDoanhThu', 'sinhVienTheoKhoaHoc', 'thiSinhTheoCa'
        ));
    }
}
