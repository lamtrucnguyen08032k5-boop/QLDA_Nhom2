<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\BaiThi;
use App\Models\PhucKhao;
use App\Services\VnPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// M8 - Quy trình Đăng ký & Thanh toán phúc khảo (8 bước nghiệp vụ)
class PhucKhaoController extends Controller
{
    public const HAN_PHUC_KHAO_NGAY = 7;

    public function index()
    {
        $phucKhaos = PhucKhao::with(['baiThi.dangKy.lichThi', 'baiThi.deThi'])
            ->where('sinh_vien_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('sinhvien.phuc-khao.index', compact('phucKhaos'));
    }

    // Bước 4: Kiểm tra điều kiện & mở form đăng ký phúc khảo
    public function create(BaiThi $baithi)
    {
        $this->authorizeSinhVienForBaiThi($baithi);
        abort_unless($baithi->trang_thai === 'da_cong_bo', 404, 'Kết quả bài thi chưa được công bố.');

        if (! $baithi->con_han_phuc_khao) {
            return redirect()->route('sinhvien.ketqua.show', $baithi)
                ->with('error', 'Đã hết thời hạn đăng ký phúc khảo cho bài thi này (thời hạn 7 ngày kể từ ngày công bố).');
        }

        // Kiểm tra xem đã có đơn phúc khảo đang hoạt động hay chưa
        $existing = $baithi->phucKhaos()->where('sinh_vien_id', Auth::id())->latest()->first();
        if ($existing) {
            if ($existing->trang_thai_thanh_toan === PhucKhao::TT_THANH_TOAN_CHO) {
                return redirect()->route('sinhvien.phuc-khao.thanh-toan', $existing->id)
                    ->with('info', 'Bạn đã tạo yêu cầu phúc khảo cho bài thi này. Vui lòng thanh toán lệ phí để hoàn tất.');
            }
            if (in_array($existing->trang_thai, [
                PhucKhao::TRANG_THAI_CHO_TIEP_NHAN,
                PhucKhao::TRANG_THAI_CHO_PHAN_CONG,
                PhucKhao::TRANG_THAI_DANG_XU_LY,
                PhucKhao::TRANG_THAI_CHO_ADMIN_DUYET,
                PhucKhao::TRANG_THAI_HOAN_TAT,
            ])) {
                return redirect()->route('sinhvien.phuc-khao.index')
                    ->with('info', 'Bạn đã có yêu cầu phúc khảo cho bài thi này đang trong tiến trình xử lý.');
            }
        }

        $baithi->load(['dangKy.sinhVien', 'dangKy.lichThi.khoa', 'deThi', 'cauTraLois.cauHoi']);
        $sinhVien = Auth::user();

        return view('sinhvien.phuc-khao.create', compact('baithi', 'sinhVien'));
    }

    // Bước 5: Tiếp nhận lý do, minh chứng -> chuyển sang bước thanh toán lệ phí
    public function store(Request $request, BaiThi $baithi)
    {
        $this->authorizeSinhVienForBaiThi($baithi);
        abort_unless($baithi->trang_thai === 'da_cong_bo', 404);

        if (! $baithi->con_han_phuc_khao) {
            return back()->withErrors(['phuckhao' => 'Đã hết thời hạn đăng ký phúc khảo.']);
        }

        // Kiểm tra đơn đã thanh toán hoặc đang xử lý
        if ($baithi->phucKhaos()->where('sinh_vien_id', Auth::id())
            ->whereIn('trang_thai', [
                PhucKhao::TRANG_THAI_CHO_TIEP_NHAN,
                PhucKhao::TRANG_THAI_CHO_PHAN_CONG,
                PhucKhao::TRANG_THAI_DANG_XU_LY,
                PhucKhao::TRANG_THAI_CHO_ADMIN_DUYET,
                PhucKhao::TRANG_THAI_HOAN_TAT,
            ])->exists()) {
            return back()->withErrors(['phuckhao' => 'Bạn đã có yêu cầu phúc khảo đang được xử lý cho bài thi này.']);
        }

        $request->validate([
            'ly_do' => 'required|string|min:10|max:2000',
            'file_minh_chung' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ], [
            'ly_do.required' => 'Vui lòng nhập lý do đề nghị phúc khảo.',
            'ly_do.min' => 'Lý do phúc khảo phải có tối thiểu 10 ký tự.',
            'ly_do.max' => 'Lý do phúc khảo không được vượt quá 2000 ký tự.',
            'file_minh_chung.mimes' => 'Tệp minh chứng phải có định dạng JPG, PNG hoặc PDF.',
            'file_minh_chung.max' => 'Tệp minh chứng không được vượt quá 5MB dung lượng.',
        ]);

        $filePath = null;
        if ($request->hasFile('file_minh_chung')) {
            $file = $request->file('file_minh_chung');
            $fileName = 'minh_chung_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('minh_chung_phuc_khao', $fileName, 'public');
        }

        $maPhucKhao = 'PK' . now()->format('ymd') . strtoupper(Str::random(4));

        // Tạo hoặc cập nhật đơn chờ thanh toán
        $phuckhao = PhucKhao::updateOrCreate(
            [
                'bai_thi_id' => $baithi->id,
                'sinh_vien_id' => Auth::id(),
                'trang_thai' => PhucKhao::TRANG_THAI_CHO_THANH_TOAN,
            ],
            [
                'ma_phuc_khao' => $maPhucKhao,
                'ly_do' => $request->input('ly_do'),
                'file_minh_chung' => $filePath,
                'le_phi' => PhucKhao::LE_PHI_MAC_DINH,
                'trang_thai_thanh_toan' => PhucKhao::TT_THANH_TOAN_CHO,
                'diem_truoc' => $baithi->diem_tong,
            ]
        );

        // Bước 6: Chuyển sang trang thanh toán lệ phí
        return redirect()->route('sinhvien.phuc-khao.thanh-toan', $phuckhao->id)
            ->with('info', 'Đã lưu yêu cầu phúc khảo. Vui lòng thanh toán lệ phí phúc khảo để hoàn tất đăng ký.');
    }

    // Bước 6a: Giao diện chọn phương thức thanh toán lệ phí phúc khảo
    public function thanhToan(PhucKhao $phuckhao)
    {
        abort_unless($phuckhao->sinh_vien_id === Auth::id(), 403);

        if ($phuckhao->trang_thai_thanh_toan === PhucKhao::TT_THANH_TOAN_DA) {
            return redirect()->route('sinhvien.phuc-khao.index')
                ->with('status', "Yêu cầu phúc khảo {$phuckhao->ma_phuc_khao_hien_thi} đã được thanh toán thành công.");
        }

        $phuckhao->load(['baiThi.dangKy.lichThi', 'baiThi.deThi', 'sinhVien']);

        return view('sinhvien.phuc-khao.thanh-toan', compact('phuckhao'));
    }

    // Bước 6b: Khởi tạo giao dịch thanh toán (VNPAY / Mô phỏng)
    public function khoiTaoThanhToan(Request $request, PhucKhao $phuckhao, VnPayService $vnPay)
    {
        abort_unless($phuckhao->sinh_vien_id === Auth::id(), 403);

        $request->validate([
            'phuong_thuc_thanh_toan' => ['required', 'in:vnpay,napas'],
        ]);

        $maGiaoDich = 'GDPK' . now()->format('ymdHis') . strtoupper(Str::random(4));

        $phuckhao->update([
            'phuong_thuc_thanh_toan' => $request->input('phuong_thuc_thanh_toan'),
            'ma_giao_dich' => $maGiaoDich,
        ]);

        if ($vnPay->daCauHinh()) {
            $noiDung = 'Thanh toan le phi phuc khao ' . $phuckhao->ma_phuc_khao_hien_thi;
            $returnUrl = route('sinhvien.phuc-khao.thanhtoan.vnpay.return');
            $url = $vnPay->taoUrlThanhToan($maGiaoDich, (int) $phuckhao->le_phi, $noiDung, $request->input('phuong_thuc_thanh_toan'), $returnUrl);
            return redirect()->away($url);
        }

        // Môi trường demo / chưa có merchant thật: Chuyển sang trang thanh toán mô phỏng
        return redirect()->route('sinhvien.phuc-khao.thanh-toan.mophong', $phuckhao->id);
    }

    // Giao diện mô phỏng thanh toán
    public function moPhongThanhToan(PhucKhao $phuckhao)
    {
        abort_unless($phuckhao->sinh_vien_id === Auth::id(), 403);
        $phuckhao->load(['baiThi.dangKy.lichThi', 'sinhVien']);

        return view('sinhvien.phuc-khao.thanh-toan-mo-phong', compact('phuckhao'));
    }

    // Xử lý kết quả mô phỏng thanh toán
    public function xuLyMoPhongThanhToan(Request $request, PhucKhao $phuckhao)
    {
        abort_unless($phuckhao->sinh_vien_id === Auth::id(), 403);

        $ketQua = $request->input('ket_qua', 'thanh_cong');

        if ($ketQua === 'thanh_cong') {
            $this->hoanTatThanhToan($phuckhao, $phuckhao->phuong_thuc_thanh_toan ?: 'mophong');

            return redirect()->route('sinhvien.phuc-khao.index')
                ->with('status', "Thanh toán lệ phí thành công! Mã yêu cầu phúc khảo của bạn là {$phuckhao->ma_phuc_khao_hien_thi}. Yêu cầu đã được chuyển tới Admin tiếp nhận.");
        }

        $phuckhao->update(['trang_thai_thanh_toan' => PhucKhao::TT_THANH_TOAN_THAT_BAI]);

        return redirect()->route('sinhvien.phuc-khao.thanh-toan', $phuckhao->id)
            ->withErrors(['payment' => 'Giao dịch thanh toán không thành công hoặc đã bị hủy. Vui lòng thanh toán lại.']);
    }

    // Xử lý return URL từ VNPAY thật
    public function vnpayReturn(Request $request, VnPayService $vnPay)
    {
        $params = $request->all();
        $hopLe = $vnPay->xacThucChuKy($params);

        $phuckhao = PhucKhao::where('ma_giao_dich', $params['vnp_TxnRef'] ?? null)->first();

        if (! $phuckhao) {
            abort(404, 'Không tìm thấy giao dịch phúc khảo tương ứng.');
        }

        if ($hopLe && ($params['vnp_ResponseCode'] ?? null) === '00') {
            $this->hoanTatThanhToan($phuckhao, 'VNPAY');

            return redirect()->route('sinhvien.phuc-khao.index')
                ->with('status', "Thanh toán lệ phí qua VNPAY thành công! Mã yêu cầu: {$phuckhao->ma_phuc_khao_hien_thi}.");
        }

        $phuckhao->update(['trang_thai_thanh_toan' => PhucKhao::TT_THANH_TOAN_THAT_BAI]);

        return redirect()->route('sinhvien.phuc-khao.thanh-toan', $phuckhao->id)
            ->withErrors(['payment' => 'Giao dịch thanh toán qua VNPAY không thành công hoặc đã bị từ chối.']);
    }

    // Bước 7 & 8: Ghi nhận yêu cầu, cập nhật trạng thái, gửi thông báo/email cho SV
    private function hoanTatThanhToan(PhucKhao $phuckhao, string $phuongThuc): void
    {
        $maPk = $phuckhao->ma_phuc_khao ?: ('PK' . now()->format('ymd') . strtoupper(Str::random(4)));
        $maGiaoDich = $phuckhao->ma_giao_dich ?: ('TXN' . now()->format('ymdHis') . strtoupper(Str::random(4)));

        $phuckhao->update([
            'ma_phuc_khao' => $maPk,
            'ma_giao_dich' => $maGiaoDich,
            'phuong_thuc_thanh_toan' => $phuckhao->phuong_thuc_thanh_toan ?: $phuongThuc,
            'trang_thai' => PhucKhao::TRANG_THAI_CHO_TIEP_NHAN,
            'trang_thai_thanh_toan' => PhucKhao::TT_THANH_TOAN_DA,
            'ngay_thanh_toan' => now(),
            'diem_truoc' => $phuckhao->baiThi?->diem_tong,
        ]);

        $sinhVien = $phuckhao->sinhVien;
        $tenKyThi = $phuckhao->baiThi?->dangKy?->lichThi?->ten_ky_thi ?? ($phuckhao->baiThi?->deThi?->ten_de ?? 'Bài thi');

        // Gửi email xác nhận
        if ($sinhVien && $sinhVien->email) {
            try {
                $maPkHienThi = $phuckhao->ma_phuc_khao_hien_thi;
                $diemHienTai = $phuckhao->diem_truoc !== null ? $phuckhao->diem_truoc : '—';
                $lePhi = number_format($phuckhao->le_phi);

                Mail::raw(
                    "Kính gửi bạn {$sinhVien->name},\n\n" .
                    "Hệ thống khảo thí HVNH xác nhận đã nhận được yêu cầu phúc khảo và ghi nhận thanh toán lệ phí thành công.\n\n" .
                    "Thông tin chi tiết:\n" .
                    "- Mã yêu cầu phúc khảo: {$maPkHienThi}\n" .
                    "- Kỳ thi / Môn thi: {$tenKyThi}\n" .
                    "- Điểm hiện tại: {$diemHienTai} điểm\n" .
                    "- Lệ phí đã thanh toán: {$lePhi} VNĐ ({$phuongThuc})\n" .
                    "- Thời gian ghi nhận: " . now()->format('H:i d/m/Y') . "\n\n" .
                    "Yêu cầu của bạn đã được chuyển đến Phòng Khảo thí & Đảm bảo chất lượng để thẩm định và chuyển cho Khoa phân công giảng viên chấm lại. Bạn có thể theo dõi tiến độ xử lý trực tiếp tại mục \"Phúc khảo\" trên trang Sinh viên.\n\n" .
                    "Trân trọng,\nPhòng Khảo thí & Đảm bảo chất lượng - Học viện Ngân hàng",
                    function ($m) use ($sinhVien, $maPkHienThi) {
                        $m->to($sinhVien->email)
                          ->subject("[HVNH] Xác nhận tiếp nhận yêu cầu phúc khảo: {$maPkHienThi}");
                    }
                );
            } catch (\Throwable $e) {
                Log::warning("Không thể gửi email xác nhận phúc khảo cho {$sinhVien->email}: " . $e->getMessage());
            }

            // Gửi thông báo hệ thống (Database notification)
            try {
                $sinhVien->notify(new \App\Notifications\ThongBaoHeThong(
                    "Đã ghi nhận yêu cầu phúc khảo: " . $phuckhao->ma_phuc_khao_hien_thi,
                    "Yêu cầu phúc khảo cho bài thi \"{$tenKyThi}\" đã được thanh toán lệ phí thành công và chuyển tới Admin tiếp nhận.",
                    route('sinhvien.phuc-khao.index'),
                    'thanh_cong',
                    'bi-arrow-counterclockwise'
                ));
            } catch (\Throwable $e) {
                Log::warning("Không thể tạo notification phúc khảo: " . $e->getMessage());
            }
        }
    }

    private function authorizeSinhVienForBaiThi(BaiThi $baithi): void
    {
        if ($baithi->dangKy?->sinh_vien_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền truy cập bài thi này.');
        }
    }
}
