<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BaiThi;
use App\Models\LichThi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

// M7 - Kết quả thi (UC7.1 Admin theo dõi & công bố kết quả thi)
class KetQuaController extends Controller
{
    /**
     * Danh sách lịch thi theo từng ngày, ca thi và phòng thi
     */
    public function index(Request $request)
    {
        $q = LichThi::with(['khoa', 'deThi', 'dangKys'])
            ->withCount([
                // Số lượng sinh viên dự thi (đã có bài làm hoặc đăng ký hợp lệ)
                'dangKys as tong_sinh_vien' => function ($query) {
                    $query->whereIn('trang_thai', ['da_duyet']);
                },
                'dangKys as tong_bai_thi' => function ($query) {
                    $query->whereHas('baiThi');
                },
                'dangKys as tong_bai_da_cham' => function ($query) {
                    $query->whereHas('baiThi', function ($b) {
                        $b->where('cham_xong', true)->orWhere('trang_thai', 'da_cong_bo');
                    });
                },
            ]);

        // Bộ lọc ngày thi
        if ($request->filled('ngay_thi')) {
            $q->whereDate('ngay_thi', $request->ngay_thi);
        }

        // Bộ lọc ca thi
        if ($request->filled('ma_ca_thi')) {
            $q->where('ma_ca_thi', 'like', '%' . trim($request->ma_ca_thi) . '%');
        }

        // Bộ lọc phòng thi
        if ($request->filled('phong_thi')) {
            $q->where('phong_thi', 'like', '%' . trim($request->phong_thi) . '%');
        }

        // Tìm kiếm theo từ khóa bài thi / kỳ thi
        if ($request->filled('q')) {
            $keyword = '%' . trim($request->q) . '%';
            $q->where(function ($query) use ($keyword) {
                $query->where('ten_ky_thi', 'like', $keyword)
                    ->orWhere('ma_ca_thi', 'like', $keyword)
                    ->orWhere('phong_thi', 'like', $keyword)
                    ->orWhereHas('deThi', fn ($d) => $d->where('ten_de', 'like', $keyword));
            });
        }

        // Bộ lọc trạng thái công bố
        if ($request->filled('trang_thai_cong_bo')) {
            if ($request->trang_thai_cong_bo === 'da_cong_bo') {
                $q->where('trang_thai_cong_bo', 'da_cong_bo');
            } elseif ($request->trang_thai_cong_bo === 'chua_cong_bo') {
                $q->where('trang_thai_cong_bo', '!=', 'da_cong_bo');
            }
        }

        $lichThis = $q->orderByDesc('ngay_thi')
            ->orderByDesc('gio_bat_dau')
            ->paginate(12)
            ->withQueryString();

        // Đếm số phòng thi đã chấm hoàn thành 100% bài thi nhưng chưa công bố kết quả
        $soPhongSanSangCongBo = LichThi::where('trang_thai_cong_bo', '!=', 'da_cong_bo')
            ->whereHas('dangKys.baiThi')
            ->get()
            ->filter(function ($lt) {
                $tong = $lt->dangKys()->whereHas('baiThi')->count();
                if ($tong === 0) return false;
                $daCham = $lt->dangKys()->whereHas('baiThi', function ($b) {
                    $b->where('cham_xong', true)->orWhere('trang_thai', 'da_cong_bo');
                })->count();
                return $tong === $daCham;
            })
            ->count();

        return view('admin.ketqua.index', compact('lichThis', 'soPhongSanSangCongBo'));
    }

    /**
     * Xem chi tiết danh sách bài làm của phòng thi
     */
    public function show(Request $request, LichThi $lichthi)
    {
        $lichthi->load(['khoa', 'deThi', 'nguoiCongBo']);

        // Truy vấn tất cả bài làm của sinh viên trong phòng thi này
        $query = BaiThi::with(['dangKy.sinhVien', 'deThi', 'nguoiCongBo'])
            ->whereHas('dangKy', fn ($q) => $q->where('lich_thi_id', $lichthi->id));

        // Thống kê toàn bộ bài làm trong phòng thi (không bị ảnh hưởng bởi bộ lọc tìm kiếm)
        $allBaiThis = (clone $query)->get();
        $tongSoBai = $allBaiThis->count();
        $soBaiChuaCham = $allBaiThis->where('cham_xong', false)->where('trang_thai', '!=', 'dang_cham')->count();
        $soBaiDangCham = $allBaiThis->where('trang_thai', 'dang_cham')->count();
        $soBaiDaCham = $allBaiThis->filter(function ($b) {
            return $b->cham_xong || in_array($b->trang_thai, ['da_cham', 'da_cong_bo']);
        })->count();

        // Kiểm tra xem tất cả bài đã chấm xong chưa
        $tatCaDaCham = ($tongSoBai > 0 && $soBaiDaCham === $tongSoBai);
        $daCongBo = ($lichthi->trang_thai_cong_bo === 'da_cong_bo');

        // Danh sách các bài chưa chấm hoặc đang chấm (để admin dễ theo dõi)
        $baiChuaHoanThanh = $allBaiThis->filter(function ($b) {
            return ! $b->cham_xong && ! in_array($b->trang_thai, ['da_cham', 'da_cong_bo']);
        });

        // Tra cứu bài làm theo: Họ và tên, Mã sinh viên, Số CCCD, Mã bài thi
        $hasSearch = false;
        if ($request->filled('search')) {
            $hasSearch = true;
            $search = trim($request->search);
            $query->where(function ($sub) use ($search) {
                $sub->where('ma_bai_thi', 'like', "%{$search}%")
                    ->orWhereHas('dangKy.sinhVien', function ($sv) use ($search) {
                        $sv->where('name', 'like', "%{$search}%")
                            ->orWhere('ma_so', 'like', "%{$search}%");
                    })
                    ->orWhereHas('dangKy', function ($dk) use ($search) {
                        $dk->where('so_cccd', 'like', "%{$search}%");
                    });
            });
        }

        // Lọc theo trạng thái bài làm
        if ($request->filled('trang_thai_bai')) {
            $hasSearch = true;
            $tt = $request->trang_thai_bai;
            if ($tt === 'chua_cham') {
                $query->where('cham_xong', false)->where('trang_thai', '!=', 'dang_cham');
            } elseif ($tt === 'dang_cham') {
                $query->where('trang_thai', 'dang_cham');
            } elseif ($tt === 'da_cham') {
                $query->where(function ($b) {
                    $b->where('cham_xong', true)->orWhereIn('trang_thai', ['da_cham', 'da_cong_bo']);
                });
            }
        }

        $baiThis = $query->orderBy('id')->get();

        return view('admin.ketqua.show', compact(
            'lichthi',
            'baiThis',
            'tongSoBai',
            'soBaiChuaCham',
            'soBaiDangCham',
            'soBaiDaCham',
            'tatCaDaCham',
            'daCongBo',
            'baiChuaHoanThanh',
            'hasSearch'
        ));
    }

    /**
     * Admin thực hiện "Trả kết quả thi" (Công bố kết quả thi)
     */
    public function congBo(Request $request, LichThi $lichthi)
    {
        // Luồng phụ 3: Nếu phòng thi đã được công bố trước đó
        if ($lichthi->trang_thai_cong_bo === 'da_cong_bo') {
            return back()->withErrors(['cong_bo' => 'Phòng thi này đã được công bố kết quả trước đó.']);
        }

        // Lấy toàn bộ bài làm trong phòng thi
        $baiThis = BaiThi::with('dangKy.sinhVien')
            ->whereHas('dangKy', fn ($q) => $q->where('lich_thi_id', $lichthi->id))
            ->get();

        if ($baiThis->isEmpty()) {
            return back()->withErrors(['cong_bo' => 'Phòng thi này hiện chưa có bài làm nào để công bố.']);
        }

        // Luồng phụ 1: Kiểm tra xem còn bài ở trạng thái "Chưa chấm" hoặc "Đang chấm" hay không
        $conBaiChuaCham = $baiThis->contains(function ($bai) {
            return ! $bai->cham_xong && ! in_array($bai->trang_thai, ['da_cham', 'da_cong_bo']);
        });

        if ($conBaiChuaCham) {
            return back()->withErrors(['cong_bo' => 'Chưa thể trả kết quả. Vẫn còn bài thi chưa hoàn thành chấm.']);
        }

        try {
            DB::beginTransaction();

            $now = now();
            $adminId = Auth::id();

            // Cập nhật đồng loạt trạng thái bài làm thành "da_cong_bo"
            foreach ($baiThis as $bai) {
                if (empty($bai->ma_bai_thi)) {
                    $bai->ma_bai_thi = 'BT' . $now->format('ymd') . strtoupper(Str::random(5));
                }

                $bai->trang_thai = 'da_cong_bo';
                $bai->ngay_cong_bo = $now;
                $bai->nguoi_cong_bo_id = $adminId;
                $bai->save();
            }

            // Cập nhật trạng thái phòng thi thành "da_cong_bo"
            $lichthi->trang_thai_cong_bo = 'da_cong_bo';
            $lichthi->ngay_cong_bo = $now;
            $lichthi->nguoi_cong_bo_id = $adminId;
            $lichthi->save();

            DB::commit();

            // Gửi thông báo / email cho từng sinh viên trong phòng thi
            $soLuongSv = 0;
            foreach ($baiThis as $bai) {
                $sinhVien = $bai->dangKy?->sinhVien;
                if ($sinhVien && $sinhVien->email) {
                    $soLuongSv++;
                    try {
                        $tenKyThi = $lichthi->ten_ky_thi;
                        $diem = $bai->diem_tong !== null ? $bai->diem_tong : '—';
                        $ketQua = ($bai->diem_tong !== null && $bai->diem_tong >= 50) ? 'Đạt' : 'Không đạt';

                        Mail::raw(
                            "Kính gửi bạn {$sinhVien->name},\n\n" .
                            "Kết quả bài thi cho kỳ thi \"{$tenKyThi}\" (Phòng thi: {$lichthi->phong_thi}, Ca thi: {$lichthi->ma_ca_thi}) đã được công bố chính thức.\n\n" .
                            "- Tổng điểm: {$diem}\n" .
                            "- Kết quả: {$ketQua}\n" .
                            "- Thời gian công bố: {$now->format('H:i d/m/Y')}\n\n" .
                            "Vui lòng đăng nhập vào Hệ thống thi HVNH tại mục \"Kết quả thi\" để tra cứu chi tiết bài thi, phiếu điểm và gửi yêu cầu phúc khảo nếu có nguyện vọng.\n\n" .
                            "Trân trọng,\nPhòng Khảo thí & Đảm bảo chất lượng - Học viện Ngân hàng",
                            function ($m) use ($sinhVien, $tenKyThi) {
                                $m->to($sinhVien->email)
                                  ->subject("[HVNH] Thông báo công bố kết quả thi: {$tenKyThi}");
                            }
                        );

                        // Gửi thông báo hệ thống (Database notification)
                        $sinhVien->notify(new \App\Notifications\ThongBaoHeThong(
                            "Đã có kết quả thi: {$tenKyThi}",
                            "Phòng {$lichthi->phong_thi} ({$lichthi->ma_ca_thi}) đã công bố kết quả. Điểm của bạn: {$diem} ({$ketQua}).",
                            route('sinhvien.ketqua.show', $bai),
                            $bai->is_dat ? 'thanh_cong' : 'thong_tin',
                            'bi-award'
                        ));
                    } catch (\Throwable $mailErr) {
                        Log::warning("Không thể gửi thông báo công bố điểm cho {$sinhVien->email}: " . $mailErr->getMessage());
                    }
                }
            }

            return back()->with('status', "Đã công bố đồng loạt kết quả thi cho {$baiThis->count()} thí sinh trong phòng thi {$lichthi->phong_thi} thành công.");

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Lỗi khi công bố kết quả phòng thi {$lichthi->id}: " . $e->getMessage());

            // Luồng phụ 5: Lỗi hệ thống
            return back()->withErrors(['cong_bo' => 'Không thể trả kết quả thi. Vui lòng thử lại sau.']);
        }
    }

    /**
     * Admin xem chi tiết một bài làm: người chấm, điểm từng câu, thông tin sinh viên
     */
    public function xemBaiThi(BaiThi $baithi)
    {
        $baithi->load([
            'cauTraLois.cauHoi',
            'dangKy.sinhVien',
            'dangKy.lichThi.khoa',
            'deThi',
            'nguoiCongBo',
            'giangVien',   // người chấm (nếu có)
        ]);

        $lichThi = $baithi->dangKy?->lichThi;
        $sinhVien = $baithi->dangKy?->sinhVien;

        return view('admin.ketqua.baithi', compact('baithi', 'lichThi', 'sinhVien'));
    }
}
