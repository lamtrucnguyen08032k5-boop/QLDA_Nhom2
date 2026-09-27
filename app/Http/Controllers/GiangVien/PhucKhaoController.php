<?php

namespace App\Http\Controllers\GiangVien;

use App\Http\Controllers\Controller;
use App\Models\PhucKhao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// M8 - UC2.3.8 Giảng viên chấm phúc khảo (HĐ12-16)
class PhucKhaoController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $q = PhucKhao::with(['baiThi.dangKy.sinhVien', 'baiThi.deThi'])
            ->where(function ($qq) use ($user) {
                $qq->where('giang_vien_id', $user->id)
                   ->orWhereHas('baiThi.deThi', fn ($q3) => $q3->where('khoa_id', $user->khoa_id));
            });

        if ($request->filled('trang_thai')) {
            $q->where('trang_thai', $request->trang_thai);
        }

        $phucKhaos = $q->orderByDesc('created_at')->paginate(20)->withQueryString();
        return view('giangvien.phuc-khao.index', compact('phucKhaos'));
    }

    public function show(PhucKhao $phuckhao)
    {
        $this->authorizeGiangVienForPhucKhao($phuckhao);
        $phuckhao->load(['baiThi.cauTraLois.cauHoi', 'baiThi.dangKy.sinhVien']);
        return view('giangvien.phuc-khao.show', compact('phuckhao'));
    }

    // HĐ12-16: GV nhập điểm sau phúc khảo & nhận xét, gửi kết quả -> cho_admin_duyet
    public function xuLy(Request $request, PhucKhao $phuckhao)
    {
        $this->authorizeGiangVienForPhucKhao($phuckhao);

        if ($phuckhao->trang_thai !== PhucKhao::TRANG_THAI_DANG_XU_LY) {
            return back()->withErrors(['msg' => 'Yêu cầu phúc khảo không ở trạng thái đang xử lý.']);
        }

        $data = $request->validate([
            'phan_hoi' => 'required|string',
            'diem_sau' => 'required|numeric|min:0',
        ], [
            'phan_hoi.required' => 'Vui lòng nhập nhận xét/phản hồi phúc khảo.',
            'diem_sau.required' => 'Vui lòng nhập điểm sau phúc khảo.',
        ]);

        $diemTruoc = $phuckhao->baiThi->diem_tong;

        // HĐ16: GV gửi kết quả -> trang_thai = 'cho_admin_duyet'
        // KHÔNG trực tiếp cập nhật baiThi.diem_tong tại đây (chờ Admin duyệt ở HĐ19)
        $phuckhao->update([
            'trang_thai' => PhucKhao::TRANG_THAI_CHO_ADMIN_DUYET,
            'phan_hoi' => $data['phan_hoi'],
            'diem_truoc' => $diemTruoc,
            'diem_sau' => $data['diem_sau'],
            'xu_ly_boi' => Auth::id(),
            'ngay_xu_ly' => now(),
        ]);

        return redirect()->route('giangvien.phuc-khao.index')
            ->with('status', 'Đã gửi kết quả chấm phúc khảo. Đang chờ Admin phê duyệt.');
    }

    private function authorizeGiangVienForPhucKhao(PhucKhao $phuckhao): void
    {
        $user = Auth::user();
        if ($phuckhao->giang_vien_id !== $user->id &&
            $phuckhao->baiThi->deThi->khoa_id !== $user->khoa_id) {
            abort(403, 'Bạn không được phân công chấm bài phúc khảo này.');
        }
    }
}
