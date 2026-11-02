<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\DangKy;
use App\Models\LichThi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $q = LichThi::with('khoa')
            ->where('trang_thai', 'dang_mo_dang_ky')
            ->where('han_dang_ky', '>', now());

        // Kiểm tra hợp lệ khoảng ngày lọc
        if ($request->filled('tu_ngay') && $request->filled('den_ngay') && $request->tu_ngay > $request->den_ngay) {
            return redirect()->route('sinhvien.dashboard', $request->except(['den_ngay']))
                ->with('warning', 'Đến ngày thi phải lớn hơn hoặc bằng từ ngày thi.');
        }

        // Lọc theo loại chứng chỉ
        if ($request->filled('loai_chung_chi')) {
            $q->where('loai_chung_chi', $request->loai_chung_chi);
        }

        // Lọc theo từ ngày
        if ($request->filled('tu_ngay')) {
            $q->whereDate('ngay_thi', '>=', $request->tu_ngay);
        }

        // Lọc theo đến ngày
        if ($request->filled('den_ngay')) {
            $q->whereDate('ngay_thi', '<=', $request->den_ngay);
        }

        // Tìm kiếm theo tên bài thi hoặc phòng thi
        if ($request->filled('q')) {
            $keyword = '%' . $request->q . '%';
            $q->where(function ($sub) use ($keyword) {
                $sub->where('ten_ky_thi', 'like', $keyword)
                    ->orWhere('phong_thi', 'like', $keyword)
                    ->orWhere('ma_ca_thi', 'like', $keyword);
            });
        }

        $lichThis = $q->orderBy('ngay_thi')->paginate(9)->withQueryString();

        // Danh sách lịch thi mà sinh viên đã đăng ký (để hiển thị trạng thái)
        $daDangKyIds = DangKy::where('sinh_vien_id', Auth::id())
            ->whereNotIn('trang_thai', ['da_huy', 'tu_choi'])
            ->pluck('lich_thi_id')
            ->toArray();

        return view('sinhvien.dashboard', compact('lichThis', 'daDangKyIds'));
    }
}
