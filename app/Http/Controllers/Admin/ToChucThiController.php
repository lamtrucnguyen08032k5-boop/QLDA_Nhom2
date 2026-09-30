<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BaiThi;
use App\Models\DeThi;
use App\Models\LichThi;
use Illuminate\Http\Request;

// M5 - Tổ chức thi (UC5.1 Admin bấm bắt đầu ca thi khi đến hạn)
class ToChucThiController extends Controller
{
    public function index()
    {
        $lichThis = LichThi::with('khoa')
            ->withCount(['dangKysDaDuyet as so_thi_sinh'])
            ->whereIn('trang_thai', ['da_dong_dang_ky', 'dang_thi'])
            ->orderBy('ngay_thi')
            ->paginate(15);

        return view('admin.tochuc.index', compact('lichThis'));
    }

    // Admin bấm "Bắt đầu ca thi": chọn đề thi áp dụng, mở trạng thái cho phép SV vào thi
    public function batDau(Request $request, LichThi $lichthi)
    {
        $data = $request->validate([
            'de_thi_id' => 'required|exists:de_this,id',
        ]);

        $deThi = DeThi::findOrFail($data['de_thi_id']);
        if ($deThi->cauHois()->count() === 0) {
            return back()->withErrors(['de_thi_id' => 'Đề thi chưa có câu hỏi, không thể sử dụng.']);
        }

        $lichthi->update(['trang_thai' => 'dang_thi', 'de_thi_id' => $deThi->id]);

        return back()->with('status', "Đã bắt đầu ca thi phòng {$lichthi->phong_thi} lúc " . \Carbon\Carbon::parse($lichthi->gio_bat_dau)->format('H:i') . ". Sinh viên có thể vào thi.");
    }

    public function ketThuc(LichThi $lichthi)
    {
        $lichthi->update(['trang_thai' => 'da_ket_thuc']);
        return back()->with('status', 'Đã kết thúc ca thi.');
    }
}
