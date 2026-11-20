<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhucKhao;
use App\Notifications\ThongBaoHeThong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

// M8 - UC2.3.8 Admin tiếp nhận & duyệt kết quả phúc khảo
class PhucKhaoController extends Controller
{
    public function index(Request $request)
    {
        $q = PhucKhao::with(['baiThi.dangKy.sinhVien', 'baiThi.deThi.khoa', 'giangVien', 'adminTiepNhan', 'adminDuyet'])
            ->where('trang_thai', '!=', PhucKhao::TRANG_THAI_CHO_THANH_TOAN);

        if ($request->filled('trang_thai')) {
            $q->where('trang_thai', $request->trang_thai);
        }
        $phucKhaos = $q->orderByDesc('created_at')->paginate(20)->withQueryString();
        return view('admin.phuckhao.index', compact('phucKhaos'));
    }

    public function show(PhucKhao $phuckhao)
    {
        $phuckhao->load(['baiThi.cauTraLois.cauHoi', 'baiThi.dangKy.sinhVien', 'giangVien', 'adminTiepNhan', 'adminDuyet']);
        return view('admin.phuckhao.show', compact('phuckhao'));
    }

    // HĐ8-9: Admin tiếp nhận yêu cầu hợp lệ -> cho_phan_cong (chờ Khoa phân công GV)
    public function tiepNhan(PhucKhao $phuckhao)
    {
        if (!in_array($phuckhao->trang_thai, [PhucKhao::TRANG_THAI_CHO_TIEP_NHAN, 'cho_xu_ly'])) {
            return back()->withErrors(['msg' => 'Yêu cầu không ở trạng thái chờ tiếp nhận.']);
        }

        $phuckhao->update([
            'trang_thai' => PhucKhao::TRANG_THAI_CHO_PHAN_CONG,
            'admin_tiep_nhan_id' => Auth::id(),
            'ngay_tiep_nhan' => now(),
        ]);

        return back()->with('status', 'Đã tiếp nhận yêu cầu phúc khảo và chuyển cho Khoa phân công.');
    }

    // HĐ8: Admin từ chối yêu cầu không hợp lệ
    public function tuChoi(Request $request, PhucKhao $phuckhao)
    {
        $data = $request->validate([
            'ly_do_duyet' => 'required|string',
        ], [
            'ly_do_duyet.required' => 'Vui lòng nhập lý do từ chối.',
        ]);

        $phuckhao->update([
            'trang_thai' => PhucKhao::TRANG_THAI_TU_CHOI,
            'admin_duyet_id' => Auth::id(),
            'ngay_duyet' => now(),
            'ly_do_duyet' => $data['ly_do_duyet'],
        ]);

        // Thông báo cho sinh viên
        $sinhVien = $phuckhao->baiThi?->dangKy?->sinhVien;
        if ($sinhVien) {
            try {
                $sinhVien->notify(new ThongBaoHeThong(
                    "Yêu cầu phúc khảo bị từ chối: {$phuckhao->ma_phuc_khao_hien_thi}",
                    "Yêu cầu phúc khảo mã {$phuckhao->ma_phuc_khao_hien_thi} không được tiếp nhận. Lý do: {$data['ly_do_duyet']}",
                    route('sinhvien.phuc-khao.index'),
                    'canh_bao',
                    'bi-x-circle'
                ));
            } catch (\Throwable $e) {
                Log::warning("Lỗi gửi thông báo từ chối phúc khảo: " . $e->getMessage());
            }

            if (!empty($sinhVien->email)) {
                try {
                    Mail::raw(
                        "Kính gửi {$sinhVien->name},\n\n" .
                        "Yêu cầu phúc khảo mã [{$phuckhao->ma_phuc_khao_hien_thi}] của bạn không được tiếp nhận.\n" .
                        "Lý do: {$data['ly_do_duyet']}\n\n" .
                        "Mọi thắc mắc xin vui lòng liên hệ Phòng Khảo thí.\n\n" .
                        "Trân trọng,\nHệ thống Khảo thí Trực tuyến.",
                        function ($msg) use ($sinhVien) {
                            $msg->to($sinhVien->email)->subject('[Khảo thí] Kết quả tiếp nhận yêu cầu phúc khảo');
                        }
                    );
                } catch (\Throwable $e) {
                    Log::warning("Lỗi gửi email từ chối phúc khảo: " . $e->getMessage());
                }
            }
        }

        return back()->with('status', 'Đã từ chối yêu cầu phúc khảo.');
    }

    // HĐ17-20: Admin duyệt kết quả chấm phúc khảo từ GV -> hoan_tat & cập nhật diem_tong (nếu thay đổi)
    public function duyet(Request $request, PhucKhao $phuckhao)
    {
        if ($phuckhao->trang_thai !== PhucKhao::TRANG_THAI_CHO_ADMIN_DUYET) {
            return back()->withErrors(['msg' => 'Yêu cầu không ở trạng thái chờ Admin duyệt.']);
        }

        $phuckhao->update([
            'trang_thai' => PhucKhao::TRANG_THAI_HOAN_TAT,
            'admin_duyet_id' => Auth::id(),
            'ngay_duyet' => now(),
            'ly_do_duyet' => $request->input('ly_do_duyet'),
        ]);

        // HĐ19: Nếu điểm thay đổi -> cập nhật diem_tong bài thi
        if ($phuckhao->diem_sau !== null && (float)$phuckhao->diem_sau !== (float)$phuckhao->diem_truoc) {
            $phuckhao->baiThi->update([
                'diem_tong' => $phuckhao->diem_sau,
                'cham_xong' => true,
                'da_khoa' => true,
            ]);
        }

        // Thông báo kết quả hoàn tất phúc khảo cho sinh viên
        $sinhVien = $phuckhao->baiThi?->dangKy?->sinhVien;
        if ($sinhVien) {
            $noiDungDiem = ($phuckhao->diem_sau !== null && (float)$phuckhao->diem_sau !== (float)$phuckhao->diem_truoc)
                ? "Điểm đã được điều chỉnh từ {$phuckhao->diem_truoc} thành {$phuckhao->diem_sau}."
                : "Điểm thi được giữ nguyên ({$phuckhao->diem_truoc} điểm).";

            try {
                $sinhVien->notify(new ThongBaoHeThong(
                    "Kết quả phúc khảo: {$phuckhao->ma_phuc_khao_hien_thi}",
                    "Yêu cầu phúc khảo mã {$phuckhao->ma_phuc_khao_hien_thi} đã hoàn tất. {$noiDungDiem}",
                    route('sinhvien.phuc-khao.index'),
                    'thanh_cong',
                    'bi-check-circle'
                ));
            } catch (\Throwable $e) {
                Log::warning("Lỗi gửi thông báo hoàn tất phúc khảo: " . $e->getMessage());
            }

            if (!empty($sinhVien->email)) {
                try {
                    Mail::raw(
                        "Kính gửi {$sinhVien->name},\n\n" .
                        "Yêu cầu phúc khảo mã [{$phuckhao->ma_phuc_khao_hien_thi}] đã được phê duyệt và hoàn tất.\n" .
                        "- Điểm ban đầu: {$phuckhao->diem_truoc}\n" .
                        "- Điểm sau phúc khảo: " . ($phuckhao->diem_sau ?? $phuckhao->diem_truoc) . "\n" .
                        "- Ý kiến cán bộ chấm phúc khảo: {$phuckhao->phan_hoi}\n\n" .
                        "Bạn có thể đăng nhập vào hệ thống để kiểm tra bảng điểm mới nhất.\n\n" .
                        "Trân trọng,\nHệ thống Khảo thí Trực tuyến.",
                        function ($msg) use ($sinhVien) {
                            $msg->to($sinhVien->email)->subject('[Khảo thí] Thông báo kết quả phúc khảo');
                        }
                    );
                } catch (\Throwable $e) {
                    Log::warning("Lỗi gửi email hoàn tất phúc khảo: " . $e->getMessage());
                }
            }
        }

        return back()->with('status', 'Đã duyệt hoàn tất kết quả phúc khảo.');
    }

    // Luồng phụ: Admin trả lại cho GV/Khoa xử lý lại
    public function traLai(Request $request, PhucKhao $phuckhao)
    {
        $data = $request->validate([
            'ly_do_duyet' => 'required|string',
        ]);

        $phuckhao->update([
            'trang_thai' => PhucKhao::TRANG_THAI_DANG_XU_LY,
            'ly_do_duyet' => $data['ly_do_duyet'],
        ]);

        return back()->with('status', 'Đã trả lại yêu cầu phúc khảo cho Khoa / Giảng viên.');
    }
}
