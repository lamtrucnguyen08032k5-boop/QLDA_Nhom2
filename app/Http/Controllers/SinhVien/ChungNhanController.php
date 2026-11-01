<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\BaiThi;
use App\Models\ChungNhan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

// M9 - UC2.3.9 Sinh viên đăng ký & xem chứng nhận
class ChungNhanController extends Controller
{
    // Ngưỡng điểm đạt nhận chứng nhận (PC-05): 50/100 điểm
    public const DIEM_DAT = 50;

    /**
     * Danh sách bài thi ĐẠT đủ điều kiện cấp chứng chỉ của sinh viên
     */
    public function index()
    {
        $studentId = Auth::id();

        // Lấy tất cả bài thi ĐẠT (>= 50 điểm) đã công bố của sinh viên
        $baiThisDat = BaiThi::with(['deThi.khoa', 'dangKy.lichThi.khoa', 'chungNhan'])
            ->whereHas('dangKy', fn ($q) => $q->where('sinh_vien_id', $studentId))
            ->where('trang_thai', 'da_cong_bo')
            ->where('diem_tong', '>=', self::DIEM_DAT)
            ->orderByDesc('ngay_cong_bo')
            ->orderByDesc('created_at')
            ->get();

        return view('sinhvien.chung-nhan.index', compact('baiThisDat'));
    }

    /**
     * Sinh viên xem chứng nhận điện tử trực tuyến (kèm nút Tải xuống PDF)
     */
    public function show(BaiThi $baithi)
    {
        $this->authorizeSinhVienForBaiThi($baithi);
        abort_unless($baithi->trang_thai === 'da_cong_bo', 404, 'Kết quả bài thi chưa được công bố.');
        abort_unless($baithi->diem_tong >= self::DIEM_DAT, 403, 'Bài thi chưa đạt ngưỡng điểm nhận chứng nhận.');

        $baithi->load(['deThi.khoa', 'dangKy.lichThi', 'dangKy.sinhVien.khoa']);
        $chungNhan = $baithi->chungNhan;

        return view('sinhvien.chung-nhan.show', compact('baithi', 'chungNhan'));
    }

    /**
     * Mở form đăng ký nhận chứng chỉ bản cứng
     */
    public function create(BaiThi $baithi)
    {
        $this->authorizeSinhVienForBaiThi($baithi);
        abort_unless($baithi->trang_thai === 'da_cong_bo', 404, 'Bài thi chưa được công bố kết quả.');
        abort_unless($baithi->diem_tong >= self::DIEM_DAT, 403, 'Bài thi chưa đạt điểm yêu cầu để đăng ký nhận chứng nhận.');

        if ($baithi->chungNhan()->exists()) {
            return redirect()->route('sinhvien.chung-nhan.index')
                ->with('info', 'Bài thi này bạn đã gửi yêu cầu nhận chứng chỉ.');
        }

        $baithi->load(['deThi.khoa', 'dangKy.lichThi.khoa', 'dangKy.sinhVien.khoa']);
        $sinhVien = Auth::user();

        return view('sinhvien.chung-nhan.create', compact('baithi', 'sinhVien'));
    }

    /**
     * Gửi yêu cầu đăng ký nhận chứng chỉ bản cứng
     */
    public function store(Request $request, BaiThi $baithi)
    {
        $this->authorizeSinhVienForBaiThi($baithi);
        abort_unless($baithi->trang_thai === 'da_cong_bo', 404);
        abort_unless($baithi->diem_tong >= self::DIEM_DAT, 403);

        if ($baithi->chungNhan()->exists()) {
            return back()->withErrors(['chungnhan' => 'Bạn đã gửi yêu cầu nhận chứng chỉ cho bài thi này rồi.']);
        }

        $rules = [
            'hinh_thuc_nhan' => 'required|in:truc_tiep,buu_dien',
            'so_dien_thoai'  => 'required|string|max:20',
            'ghi_chu'        => 'nullable|string|max:500',
        ];

        if ($request->hinh_thuc_nhan === 'buu_dien') {
            $rules['dia_chi_nhan'] = 'required|string|max:255';
        }

        $messages = [
            'hinh_thuc_nhan.required' => 'Vui lòng chọn hình thức nhận chứng chỉ.',
            'dia_chi_nhan.required'   => 'Vui lòng nhập địa chỉ nhận chứng nhận khi chọn nhận qua bưu điện.',
            'so_dien_thoai.required'  => 'Vui lòng nhập số điện thoại liên hệ.',
        ];

        $data = $request->validate($rules, $messages);

        $diaChi = $request->hinh_thuc_nhan === 'buu_dien'
            ? $data['dia_chi_nhan']
            : 'Văn phòng Trung tâm Khảo thí - Phòng 102 Tòa A1, Học viện Ngân hàng (12 Chùa Bộc, Đống Đa, Hà Nội)';

        // Sinh mã yêu cầu tự động
        $maYeuCau = 'YCCN-' . now()->format('ymd') . '-' . strtoupper(Str::random(4));

        ChungNhan::create([
            'ma_yeu_cau'     => $maYeuCau,
            'bai_thi_id'     => $baithi->id,
            'sinh_vien_id'   => Auth::id(),
            'hinh_thuc_nhan' => $data['hinh_thuc_nhan'],
            'dia_chi_nhan'   => $diaChi,
            'so_dien_thoai'  => $data['so_dien_thoai'],
            'ghi_chu'        => $request->input('ghi_chu'),
            'trang_thai'     => ChungNhan::TRANG_THAI_CHO_XU_LY,
        ]);

        return redirect()->route('sinhvien.chung-nhan.index')
            ->with('status', "Đăng ký nhận chứng chỉ thành công! Mã yêu cầu của bạn là: {$maYeuCau}");
    }

    private function authorizeSinhVienForBaiThi(BaiThi $baithi): void
    {
        if ($baithi->dangKy->sinh_vien_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền thao tác trên bài thi này.');
        }
    }
}
