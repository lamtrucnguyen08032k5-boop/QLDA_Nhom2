<?php

namespace App\Http\Controllers\Khoa;

use App\Http\Controllers\Controller;
use App\Models\BaiThi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhanCongChamController extends Controller
{
    public function index(Request $request)
    {
        $khoaId = Auth::user()->khoa_id;

        $giangViens = User::where('role', 'giangvien')
            ->where('khoa_id', $khoaId)
            ->where('active', true)
            ->orderBy('name')
            ->get();

        $query = BaiThi::with(['dangKy.sinhVien', 'dangKy.lichThi', 'deThi', 'giangVien1', 'giangVien2'])
            ->whereHas('deThi', fn ($q) => $q->where('khoa_id', $khoaId))
            ->whereNotNull('gio_nop');

        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        $baiThis = $query->orderByDesc('gio_nop')->paginate(20)->withQueryString();

        return view('khoa.phan-cong-cham.index', compact('baiThis', 'giangViens'));
    }

    public function show(BaiThi $baithi)
    {
        $this->authorizeKhoaForBaiThi($baithi);

        $khoaId = Auth::user()->khoa_id;

        $giangViens = User::where('role', 'giangvien')
            ->where('khoa_id', $khoaId)
            ->where('active', true)
            ->orderBy('name')
            ->get();

        $baithi->load(['cauTraLois.cauHoi', 'dangKy.sinhVien', 'dangKy.lichThi', 'deThi', 'giangVien1', 'giangVien2']);

        return view('khoa.phan-cong-cham.show', compact('baithi', 'giangViens'));
    }

    public function phanCong(Request $request, BaiThi $baithi)
    {
        $this->authorizeKhoaForBaiThi($baithi);

        $request->validate([
            'giang_vien_1_id' => 'required|exists:users,id',
            'giang_vien_2_id' => 'required|exists:users,id',
        ], [
            'giang_vien_1_id.required' => 'Vui lòng chọn Giảng viên chấm lần 1.',
            'giang_vien_2_id.required' => 'Vui lòng chọn Giảng viên chấm lần 2.',
        ]);

        $khoaId = Auth::user()->khoa_id;

        $gv1 = User::where('id', $request->giang_vien_1_id)
            ->where('role', 'giangvien')
            ->where('khoa_id', $khoaId)
            ->firstOrFail();

        $gv2 = User::where('id', $request->giang_vien_2_id)
            ->where('role', 'giangvien')
            ->where('khoa_id', $khoaId)
            ->firstOrFail();

        $baithi->update([
            'giang_vien_1_id' => $gv1->id,
            'giang_vien_2_id' => $gv2->id,
            'trang_thai' => 'cho_cham_1',
        ]);

        return redirect()->route('khoa.phan-cong-cham.index')
            ->with('status', 'Đã phân công giảng viên chấm bài thi thành công.');
    }

    public function phanCongHangLoat(Request $request)
    {
        $request->validate([
            'bai_thi_ids' => 'required|array',
            'bai_thi_ids.*' => 'exists:bai_this,id',
            'giang_vien_1_id' => 'required|exists:users,id',
            'giang_vien_2_id' => 'required|exists:users,id',
        ]);

        $khoaId = Auth::user()->khoa_id;

        User::where('id', $request->giang_vien_1_id)
            ->where('role', 'giangvien')
            ->where('khoa_id', $khoaId)
            ->firstOrFail();

        User::where('id', $request->giang_vien_2_id)
            ->where('role', 'giangvien')
            ->where('khoa_id', $khoaId)
            ->firstOrFail();

        $baiThis = BaiThi::whereIn('id', $request->bai_thi_ids)
            ->whereHas('deThi', fn ($q) => $q->where('khoa_id', $khoaId))
            ->get();

        foreach ($baiThis as $baithi) {
            $baithi->update([
                'giang_vien_1_id' => $request->giang_vien_1_id,
                'giang_vien_2_id' => $request->giang_vien_2_id,
                'trang_thai' => 'cho_cham_1',
            ]);
        }

        return redirect()->back()->with('status', 'Đã phân công hàng loạt bài thi thành công.');
    }

    private function authorizeKhoaForBaiThi(BaiThi $baithi): void
    {
        $khoaId = Auth::user()->khoa_id;
        if ($baithi->deThi->khoa_id !== $khoaId) {
            abort(403, 'Bài thi không thuộc phạm vi quản lý của Khoa.');
        }
    }
}
