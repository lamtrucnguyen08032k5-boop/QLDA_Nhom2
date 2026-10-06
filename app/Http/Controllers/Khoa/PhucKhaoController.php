<?php

namespace App\Http\Controllers\Khoa;

use App\Http\Controllers\Controller;
use App\Models\PhucKhao;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// M8 - UC2.3.8 Khoa tiếp nhận & phân công Giảng viên chấm phúc khảo (HĐ10-11)
class PhucKhaoController extends Controller
{
    public function index(Request $request)
    {
        $khoaId = Auth::user()->khoa_id;

        $giangViens = User::where('role', 'giangvien')
            ->where('khoa_id', $khoaId)
            ->where('active', true)
            ->orderBy('name')
            ->get();

        $query = PhucKhao::with(['baiThi.dangKy.sinhVien', 'baiThi.deThi', 'giangVien'])
            ->whereHas('baiThi.deThi', fn ($q) => $q->where('khoa_id', $khoaId));

        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        $phucKhaos = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('khoa.phuc-khao.index', compact('phucKhaos', 'giangViens'));
    }

    public function phanCong(Request $request, PhucKhao $phuckhao)
    {
        $this->authorizeKhoaForPhucKhao($phuckhao);

        if ($phuckhao->trang_thai !== PhucKhao::TRANG_THAI_CHO_PHAN_CONG) {
            return redirect()->back()->withErrors(['msg' => 'Yêu cầu phúc khảo không ở trạng thái chờ phân công hoặc giảng viên đã tiến hành chấm/xử lý. Không thể thay đổi phân công nữa.']);
        }

        $request->validate([
            'giang_vien_id' => 'required|exists:users,id',
        ], [
            'giang_vien_id.required' => 'Vui lòng chọn Giảng viên chấm phúc khảo.',
        ]);

        $khoaId = Auth::user()->khoa_id;
        $gv = User::where('id', $request->giang_vien_id)
            ->where('role', 'giangvien')
            ->where('khoa_id', $khoaId)
            ->where('active', true)
            ->firstOrFail();

        // HĐ10-11: Khoa phân công GV -> trang_thai = 'dang_xu_ly'
        $phuckhao->update([
            'giang_vien_id' => $gv->id,
            'trang_thai' => PhucKhao::TRANG_THAI_DANG_XU_LY,
        ]);

        return redirect()->route('khoa.phuc-khao.index')
            ->with('status', 'Đã phân công Giảng viên chấm phúc khảo thành công.');
    }

    private function authorizeKhoaForPhucKhao(PhucKhao $phuckhao): void
    {
        $khoaId = Auth::user()->khoa_id;
        if ($phuckhao->baiThi->deThi->khoa_id !== $khoaId) {
            abort(403, 'Yêu cầu phúc khảo không thuộc phạm vi quản lý của Khoa.');
        }
    }
}
