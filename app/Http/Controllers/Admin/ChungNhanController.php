<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChungNhan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// M9 - UC2.3.9 Admin xử lý & cấp chứng nhận
class ChungNhanController extends Controller
{
    public function index(Request $request)
    {
        $q = ChungNhan::with(['sinhVien', 'baiThi.deThi', 'nguoiCap']);
        if ($request->filled('trang_thai')) {
            $q->where('trang_thai', $request->trang_thai);
        }
        $chungNhans = $q->orderByDesc('created_at')->paginate(20)->withQueryString();
        return view('admin.chungnhan.index', compact('chungNhans'));
    }

    public function show(ChungNhan $chungnhan)
    {
        $chungnhan->load(['sinhVien', 'baiThi.deThi.khoa', 'nguoiCap']);
        return view('admin.chungnhan.show', compact('chungnhan'));
    }

    // HĐ7-8: Admin tiếp nhận / xử lý hồ sơ -> dang_xu_ly
    public function tiepNhan(ChungNhan $chungnhan)
    {
        if (!in_array($chungnhan->trang_thai, [ChungNhan::TRANG_THAI_CHO_XU_LY, 'cho_duyet'])) {
            return back()->withErrors(['msg' => 'Yêu cầu không ở trạng thái chờ xử lý.']);
        }

        $chungnhan->update([
            'trang_thai' => ChungNhan::TRANG_THAI_DANG_XU_LY,
        ]);

        return back()->with('status', 'Đã chuyển trạng thái yêu cầu sang Đang xử lý.');
    }

    // HĐ9-10: Admin cấp chứng nhận -> da_cap
    public function capNhan(Request $request, ChungNhan $chungnhan)
    {
        $soChungNhan = 'HVNH-' . now()->format('Y') . '-' . str_pad($chungnhan->id, 5, '0', STR_PAD_LEFT);

        $chungnhan->update([
            'trang_thai' => ChungNhan::TRANG_THAI_DA_CAP,
            'so_chung_nhan' => $soChungNhan,
            'nguoi_cap_id' => Auth::id(),
            'ngay_cap' => now(),
            'ghi_chu' => $request->input('ghi_chu'),
        ]);

        return redirect()->route('admin.chungnhan.index')->with('status', 'Đã cấp chứng nhận thành công. Số chứng nhận: ' . $soChungNhan);
    }

    public function tuChoi(Request $request, ChungNhan $chungnhan)
    {
        $chungnhan->update([
            'trang_thai' => ChungNhan::TRANG_THAI_TU_CHOI,
            'nguoi_cap_id' => Auth::id(),
            'ghi_chu' => $request->input('ghi_chu'),
        ]);

        return redirect()->route('admin.chungnhan.index')->with('status', 'Đã từ chối yêu cầu cấp chứng nhận.');
    }
}
