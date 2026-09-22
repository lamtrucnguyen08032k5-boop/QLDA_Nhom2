<?php

namespace App\Http\Controllers\SinhVien;

use App\Http\Controllers\Controller;
use App\Models\BaiThi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

// M7 - UC7.2 Sinh viên tra cứu kết quả thi (chỉ xem bài đã công bố)
class KetQuaController extends Controller
{
    const DIEM_DAT = 50; // Thang điểm 100, đạt từ 50 điểm trở lên

    /**
     * Danh sách bài thi đã được công bố kết quả của sinh viên
     */
    public function index(Request $request)
    {
        try {
            $studentId = Auth::id();

            // Đếm tổng số bài thi đã công bố của sinh viên (để phân biệt Luồng phụ 1: Chưa có kết quả thi)
            $tongBaiDaCongBo = BaiThi::whereHas('dangKy', fn ($q) => $q->where('sinh_vien_id', $studentId))
                ->where('trang_thai', 'da_cong_bo')
                ->count();

            $query = BaiThi::with(['dangKy.lichThi.khoa', 'deThi'])
                ->whereHas('dangKy', fn ($q) => $q->where('sinh_vien_id', $studentId))
                ->where('trang_thai', 'da_cong_bo');

            $hasFilter = false;

            // Tra cứu theo tên bài thi / tên kỳ thi
            if ($request->filled('q')) {
                $hasFilter = true;
                $keyword = '%' . trim($request->q) . '%';
                $query->where(function ($sub) use ($keyword) {
                    $sub->whereHas('deThi', fn ($d) => $d->where('ten_de', 'like', $keyword))
                        ->orWhereHas('dangKy.lichThi', fn ($lt) => $lt->where('ten_ky_thi', 'like', $keyword));
                });
            }

            // Tra cứu theo mã bài thi
            if ($request->filled('ma_bai_thi')) {
                $hasFilter = true;
                $ma = trim($request->ma_bai_thi);
                $query->where('ma_bai_thi', 'like', "%{$ma}%");
            }

            // Tra cứu theo khoảng thời gian thi (từ ngày - đến ngày)
            if ($request->filled('tu_ngay')) {
                $hasFilter = true;
                $query->whereHas('dangKy.lichThi', function ($lt) use ($request) {
                    $lt->whereDate('ngay_thi', '>=', $request->tu_ngay);
                });
            }

            if ($request->filled('den_ngay')) {
                $hasFilter = true;
                $query->whereHas('dangKy.lichThi', function ($lt) use ($request) {
                    $lt->whereDate('ngay_thi', '<=', $request->den_ngay);
                });
            }

            // Tra cứu theo kết quả Đạt / Không đạt
            if ($request->filled('ket_qua')) {
                $hasFilter = true;
                if ($request->ket_qua === 'dat') {
                    $query->where('diem_tong', '>=', self::DIEM_DAT);
                } elseif ($request->ket_qua === 'khong_dat') {
                    $query->where(function ($sub) {
                        $sub->where('diem_tong', '<', self::DIEM_DAT)->orWhereNull('diem_tong');
                    });
                }
            }

            $baiThis = $query->orderByDesc('ngay_cong_bo')
                ->orderByDesc('gio_nop')
                ->paginate(10)
                ->withQueryString();

            return view('sinhvien.ketqua.index', compact('baiThis', 'tongBaiDaCongBo', 'hasFilter'));

        } catch (\Throwable $e) {
            Log::error('Lỗi khi tải danh sách kết quả thi SV: ' . $e->getMessage());
            // Luồng phụ 7: Lỗi hệ thống
            return view('sinhvien.ketqua.index', [
                'baiThis' => collect(),
                'tongBaiDaCongBo' => 0,
                'hasFilter' => false,
                'errorMessage' => 'Không thể tải kết quả thi. Vui lòng thử lại sau.',
            ]);
        }
    }

    /**
     * Xem chi tiết kết quả một bài thi của sinh viên
     */
    public function show(BaiThi $baithi)
    {
        try {
            // Luồng phụ 4: Sinh viên truy cập kết quả không thuộc tài khoản
            if ($baithi->dangKy->sinh_vien_id !== Auth::id()) {
                abort(403, 'Bạn không có quyền xem kết quả thi này.');
            }

            // Luồng phụ 3: Bài thi chưa được công bố kết quả
            if ($baithi->trang_thai !== 'da_cong_bo') {
                abort(404, 'Kết quả chưa được công bố.');
            }

            $baithi->load([
                'cauTraLois.cauHoi',
                'dangKy.lichThi.khoa',
                'dangKy.sinhVien',
                'deThi',
                'chungNhan',
                'phucKhaos',
                'nguoiCongBo',
            ]);

            $lichThi = $baithi->dangKy->lichThi;
            $isDat = ($baithi->diem_tong !== null && (float) $baithi->diem_tong >= self::DIEM_DAT);

            // Thời hạn phúc khảo: 7 ngày kể từ ngày công bố kết quả
            $hanPhucKhao = $baithi->han_phuc_khao;
            $conHanPhucKhao = $baithi->con_han_phuc_khao;
            $daPhucKhao = $baithi->phucKhaos->isNotEmpty();

            // Chứng nhận
            $chungNhan = $baithi->chungNhan;
            $daCoChungNhan = ! empty($chungNhan);

            return view('sinhvien.ketqua.show', compact(
                'baithi',
                'lichThi',
                'isDat',
                'hanPhucKhao',
                'conHanPhucKhao',
                'daPhucKhao',
                'chungNhan',
                'daCoChungNhan'
            ));

        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Lỗi khi xem chi tiết kết quả bài thi ' . $baithi->id . ': ' . $e->getMessage());
            // Luồng phụ 7: Lỗi hệ thống
            return back()->withErrors(['error' => 'Không thể tải kết quả thi. Vui lòng thử lại sau.']);
        }
    }
}
