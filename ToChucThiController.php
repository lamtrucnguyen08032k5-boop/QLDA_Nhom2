<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeThi;
use App\Models\LichThi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

// M5 - Tổ chức thi
class ToChucThiController extends Controller
{
    public function index()
    {
        $lichThis = LichThi::with(['khoa', 'deThi'])
            ->withCount(['dangKysDaDuyet as so_thi_sinh'])
            ->whereIn('trang_thai', ['da_dong_dang_ky', 'dang_thi'])
            ->orderBy('ngay_thi')
            ->orderBy('gio_bat_dau')
            ->paginate(15);

        $lichThis->getCollection()->transform(function (LichThi $lichThi) {
            $lichThi->deThiHopLe = DeThi::query()
                ->where('khoa_id', $lichThi->khoa_id)
                ->where('loai_chung_chi', $lichThi->loai_chung_chi)
                ->where('active', true)
                ->withCount('cauHois')
                ->having('cau_hois_count', '>', 0)
                ->orderByDesc('id')
                ->get();

            return $lichThi;
        });

        return view('admin.tochuc.index', compact('lichThis'));
    }

    public function batDau(Request $request, LichThi $lichthi)
    {
        $data = $request->validate([
            'de_thi_id' => 'required|integer|exists:de_this,id',
        ]);

        if ($lichthi->trang_thai !== 'da_dong_dang_ky') {
            throw ValidationException::withMessages([
                'de_thi_id' => 'Ca thi không ở trạng thái sẵn sàng để bắt đầu.',
            ]);
        }

        $gioBatDau = Carbon::parse($lichthi->ngay_thi->format('Y-m-d') . ' ' . $lichthi->gio_bat_dau);

        if (now()->lt($gioBatDau)) {
            throw ValidationException::withMessages([
                'de_thi_id' => 'Chưa đến thời gian bắt đầu ca thi.',
            ]);
        }

        $deThi = DeThi::query()
            ->whereKey($data['de_thi_id'])
            ->where('khoa_id', $lichthi->khoa_id)
            ->where('loai_chung_chi', $lichthi->loai_chung_chi)
            ->where('active', true)
            ->withCount('cauHois')
            ->first();

        if (! $deThi) {
            throw ValidationException::withMessages([
                'de_thi_id' => 'Đề thi không phù hợp với Khoa hoặc loại chứng chỉ của ca thi.',
            ]);
        }

        if ($deThi->cau_hois_count < 1) {
            throw ValidationException::withMessages([
                'de_thi_id' => 'Đề thi chưa có câu hỏi, không thể sử dụng.',
            ]);
        }

        if ($lichthi->dangKysDaDuyet()->count() < 1) {
            throw ValidationException::withMessages([
                'de_thi_id' => 'Ca thi chưa có thí sinh được duyệt.',
            ]);
        }

        DB::transaction(function () use ($lichthi, $deThi) {
            $lichthi->update([
                'trang_thai' => 'dang_thi',
                'de_thi_id' => $deThi->id,
            ]);
        });

        return back()->with('status', "Đã bắt đầu ca thi {$lichthi->ma_ca_thi}. Sinh viên có thể vào thi.");
    }

    public function ketThuc(LichThi $lichthi)
    {
        if ($lichthi->trang_thai !== 'dang_thi') {
            return back()->withErrors(['general' => 'Ca thi chưa ở trạng thái đang thi.']);
        }

        // Không xóa bài làm. Các bài đã nộp vẫn được giữ để chấm và tra cứu.
        $lichthi->update(['trang_thai' => 'da_ket_thuc']);

        return back()->with('status', 'Đã kết thúc ca thi. Các bài làm đã nộp vẫn được giữ nguyên.');
    }
}
