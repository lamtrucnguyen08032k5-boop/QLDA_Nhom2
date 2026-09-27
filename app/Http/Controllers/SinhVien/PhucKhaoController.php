<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\BaiThi;
use App\Models\PhucKhao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// M8 - UC2.3.8 Sinh viên gửi yêu cầu phúc khảo
class PhucKhaoController extends Controller
{
    // TODO [BUSINESS RULE TBD]: Thời hạn phúc khảo (PC-04) chưa có quy định chính thức trong PTTKHT.
    // Giữ giá trị tạm 7 ngày cho scaffold dev/test, không hard-code làm business rule cố định.
    public const HAN_PHUC_KHAO_NGAY = 7;

    public function index()
    {
        $phucKhaos = PhucKhao::with('baiThi.dangKy.lichThi')
            ->where('sinh_vien_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('sinhvien.phuc-khao.index', compact('phucKhaos'));
    }

    public function create(BaiThi $baithi)
    {
        $this->authorizeSinhVienForBaiThi($baithi);
        abort_unless($baithi->trang_thai === 'da_cong_bo', 404, 'Kết quả chưa công bố, chưa thể đăng ký phúc khảo.');

        // TODO [BUSINESS RULE TBD]: Kiểm tra điều kiện thời hạn phúc khảo PC-04 khi có quy định chính thức

        return view('sinhvien.phuc-khao.create', compact('baithi'));
    }

    public function store(Request $request, BaiThi $baithi)
    {
        $this->authorizeSinhVienForBaiThi($baithi);
        abort_unless($baithi->trang_thai === 'da_cong_bo', 404);

        if ($baithi->phucKhaos()->whereIn('trang_thai', ['cho_tiep_nhan', 'cho_phan_cong', 'dang_xu_ly', 'cho_admin_duyet', 'hoan_tat'])->exists()) {
            return back()->withErrors(['phuckhao' => 'Bạn đã có yêu cầu phúc khảo cho bài thi này.']);
        }

        $data = $request->validate([
            'ly_do' => 'required|string|min:10',
        ], [
            'ly_do.required' => 'Vui lòng nhập lý do phúc khảo.',
            'ly_do.min' => 'Lý do phúc khảo phải có tối thiểu 10 ký tự.',
        ]);

        // HĐ6: SV nộp yêu cầu -> trang_thai = 'cho_tiep_nhan'
        PhucKhao::create([
            'bai_thi_id' => $baithi->id,
            'sinh_vien_id' => Auth::id(),
            'ly_do' => $data['ly_do'],
            'trang_thai' => PhucKhao::TRANG_THAI_CHO_TIEP_NHAN,
        ]);

        return redirect()->route('sinhvien.phuc-khao.index')->with('status', 'Gửi yêu cầu phúc khảo thành công. Đang chờ Admin tiếp nhận.');
    }

    private function authorizeSinhVienForBaiThi(BaiThi $baithi): void
    {
        if ($baithi->dangKy->sinh_vien_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền thao tác trên bài thi này.');
        }
    }
}
