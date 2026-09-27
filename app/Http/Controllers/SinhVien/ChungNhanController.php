<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\BaiThi;
use App\Models\ChungNhan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// M9 - UC2.3.9 Sinh viên đăng ký & xem chứng nhận
class ChungNhanController extends Controller
{
    // TODO [BUSINESS RULE TBD]: Ngưỡng điểm đạt nhận chứng nhận (PC-05) chưa có quy định chính thức.
    // Giữ giá trị 50/100 điểm cho scaffold dev/test.
    public const DIEM_DAT = 50;

    public function index()
    {
        $chungNhans = ChungNhan::with('baiThi.deThi')
            ->where('sinh_vien_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return view('sinhvien.chung-nhan.index', compact('chungNhans'));
    }

    // HĐ2-3: Sinh viên xem chứng nhận điện tử trực tuyến
    public function show(BaiThi $baithi)
    {
        $this->authorizeSinhVienForBaiThi($baithi);
        abort_unless($baithi->trang_thai === 'da_cong_bo', 404, 'Kết quả bài thi chưa được công bố.');
        abort_unless($baithi->diem_tong >= self::DIEM_DAT, 403, 'Bài thi chưa đạt ngưỡng điểm nhận chứng nhận.');

        $chungNhan = $baithi->chungNhan;

        return view('sinhvien.chung-nhan.show', compact('baithi', 'chungNhan'));
    }

    // HĐ4: Đăng ký nhận bản cứng
    public function create(BaiThi $baithi)
    {
        $this->authorizeSinhVienForBaiThi($baithi);
        abort_unless($baithi->trang_thai === 'da_cong_bo', 404);
        abort_unless($baithi->diem_tong >= self::DIEM_DAT, 403, 'Bài thi chưa đạt điểm yêu cầu để đăng ký nhận chứng nhận.');

        return view('sinhvien.chung-nhan.create', compact('baithi'));
    }

    public function store(Request $request, BaiThi $baithi)
    {
        $this->authorizeSinhVienForBaiThi($baithi);
        abort_unless($baithi->diem_tong >= self::DIEM_DAT, 403);

        if ($baithi->chungNhan()->exists()) {
            return back()->withErrors(['chungnhan' => 'Bạn đã đăng ký nhận chứng nhận cho bài thi này rồi.']);
        }

        $data = $request->validate([
            'dia_chi_nhan' => 'required|string|max:255',
            'so_dien_thoai' => 'required|string|max:20',
        ], [
            'dia_chi_nhan.required' => 'Vui lòng nhập địa chỉ nhận bản cứng.',
            'so_dien_thoai.required' => 'Vui lòng nhập số điện thoại liên hệ.',
        ]);

        // HĐ4: SV gửi đăng ký bản cứng -> trang_thai = 'cho_xu_ly'
        ChungNhan::create([
            'bai_thi_id' => $baithi->id,
            'sinh_vien_id' => Auth::id(),
            'dia_chi_nhan' => $data['dia_chi_nhan'],
            'so_dien_thoai' => $data['so_dien_thoai'],
            'trang_thai' => ChungNhan::TRANG_THAI_CHO_XU_LY,
        ]);

        return redirect()->route('sinhvien.chung-nhan.index')->with('status', 'Đăng ký nhận chứng nhận bản cứng thành công.');
    }

    private function authorizeSinhVienForBaiThi(BaiThi $baithi): void
    {
        if ($baithi->dangKy->sinh_vien_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền thao tác trên bài thi này.');
        }
    }
}
