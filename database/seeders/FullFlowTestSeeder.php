<?php

namespace Database\Seeders;

use App\Models\BaiThi;
use App\Models\CauHoi;
use App\Models\CauTraLoi;
use App\Models\ChungNhan;
use App\Models\DangKy;
use App\Models\DeThi;
use App\Models\Khoa;
use App\Models\KyThi;
use App\Models\LichSuXuLyHoSo;
use App\Models\LichThi;
use App\Models\PhucKhao;
use App\Models\SvWhitelist;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * FullFlowTestSeeder – Seeder toàn diện để test full luồng hệ thống
 *
 * Tạo dữ liệu cover tất cả roles & trạng thái:
 *   • Admin (1)  →  quản lý toàn hệ thống
 *   • Khoa  (3)  →  CNTT, Ngoại ngữ, Kế toán
 *   • Giảng viên (6)  →  mỗi khoa 2 GV
 *   • Sinh viên  (20) →  đủ case test
 *
 * Workflow được cover:
 *   1. Kho đề thi (DeThi + CauHoi) – trắc nghiệm & tự luận
 *   2. Kỳ thi (KyThi) – tất cả trạng thái
 *   3. Lịch thi (LichThi) – đang mở, đã đóng, đang diễn ra, đã kết thúc
 *   4. Đăng ký thi (DangKy) – tất cả trạng thái + thanh toán
 *   5. Bài thi (BaiThi + CauTraLoi) – chấm 1 lần, chấm 2 lần, chưa chấm
 *   6. Công bố kết quả
 *   7. Phúc khảo (PhucKhao) – tất cả trạng thái workflow
 *   8. Chứng nhận (ChungNhan) – tất cả trạng thái
 *   9. Lịch sử xử lý hồ sơ
 */
class FullFlowTestSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        // Truncate tất cả bảng dữ liệu (giữ nguyên schema)
        ChungNhan::truncate();
        PhucKhao::truncate();
        CauTraLoi::truncate();
        BaiThi::truncate();
        LichSuXuLyHoSo::truncate();
        DangKy::truncate();
        LichThi::truncate();
        KyThi::truncate();
        CauHoi::truncate();
        DeThi::truncate();
        SvWhitelist::truncate();
        User::truncate();
        Khoa::truncate();
        DB::table('notifications')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ================================================================
        // 1. KHOA (Departments)
        // ================================================================
        $khoaCNTT = Khoa::create([
            'ma_khoa'  => 'CNTT',
            'ten_khoa' => 'Khoa Công nghệ thông tin',
            'email'    => 'khoa.cntt@hvnh.edu.vn',
            'active'   => true,
        ]);

        $khoaNN = Khoa::create([
            'ma_khoa'  => 'NN',
            'ten_khoa' => 'Khoa Ngoại ngữ',
            'email'    => 'khoa.nn@hvnh.edu.vn',
            'active'   => true,
        ]);

        $khoaKT = Khoa::create([
            'ma_khoa'  => 'KT',
            'ten_khoa' => 'Khoa Kế toán - Kiểm toán',
            'email'    => 'khoa.kt@hvnh.edu.vn',
            'active'   => true,
        ]);

        $this->command->info('✅ Đã tạo 3 Khoa: CNTT, NN, KT');

        // ================================================================
        // 2. USERS – Admin
        // ================================================================
        $admin = User::create([
            'role'              => 'admin',
            'ma_so'             => 'ADMIN001',
            'name'              => 'Quản trị hệ thống - Phòng Khảo thí',
            'email'             => 'admin@hvnh.edu.vn',
            'password'          => Hash::make('Admin@123'),
            'email_verified_at' => now(),
            'active'            => true,
        ]);

        $this->command->info('✅ Admin: admin@hvnh.edu.vn / Admin@123');

        // ================================================================
        // 3. USERS – Tài khoản Khoa
        // ================================================================
        $tkKhoaCNTT = User::create([
            'role'              => 'khoa',
            'ma_so'             => 'CNTT',
            'name'              => 'Tài khoản Khoa Công nghệ thông tin',
            'email'             => 'khoa.cntt@hvnh.edu.vn',
            'password'          => Hash::make('Khoa@123'),
            'khoa_id'           => $khoaCNTT->id,
            'email_verified_at' => now(),
            'active'            => true,
        ]);

        $tkKhoaNN = User::create([
            'role'              => 'khoa',
            'ma_so'             => 'NN',
            'name'              => 'Tài khoản Khoa Ngoại ngữ',
            'email'             => 'khoa.nn@hvnh.edu.vn',
            'password'          => Hash::make('Khoa@123'),
            'khoa_id'           => $khoaNN->id,
            'email_verified_at' => now(),
            'active'            => true,
        ]);

        $tkKhoaKT = User::create([
            'role'              => 'khoa',
            'ma_so'             => 'KT',
            'name'              => 'Tài khoản Khoa Kế toán - Kiểm toán',
            'email'             => 'khoa.kt@hvnh.edu.vn',
            'password'          => Hash::make('Khoa@123'),
            'khoa_id'           => $khoaKT->id,
            'email_verified_at' => now(),
            'active'            => true,
        ]);

        $this->command->info('✅ 3 Tài khoản Khoa: khoa.cntt / khoa.nn / khoa.kt @hvnh.edu.vn / Khoa@123');

        // ================================================================
        // 4. USERS – Giảng viên (6 GV, mỗi khoa 2)
        // ================================================================
        $gvCNTT1 = User::create([
            'role' => 'giangvien', 'ma_so' => 'GV001', 'name' => 'Nguyễn Văn An',
            'email' => 'gv.an@hvnh.edu.vn', 'password' => Hash::make('GiangVien@123'),
            'khoa_id' => $khoaCNTT->id, 'email_verified_at' => now(), 'active' => true,
        ]);
        $gvCNTT2 = User::create([
            'role' => 'giangvien', 'ma_so' => 'GV002', 'name' => 'Trần Thị Bình',
            'email' => 'gv.binh@hvnh.edu.vn', 'password' => Hash::make('GiangVien@123'),
            'khoa_id' => $khoaCNTT->id, 'email_verified_at' => now(), 'active' => true,
        ]);

        $gvNN1 = User::create([
            'role' => 'giangvien', 'ma_so' => 'GV003', 'name' => 'Lê Hoàng Cường',
            'email' => 'gv.cuong@hvnh.edu.vn', 'password' => Hash::make('GiangVien@123'),
            'khoa_id' => $khoaNN->id, 'email_verified_at' => now(), 'active' => true,
        ]);
        $gvNN2 = User::create([
            'role' => 'giangvien', 'ma_so' => 'GV004', 'name' => 'Phạm Minh Duyên',
            'email' => 'gv.duyen@hvnh.edu.vn', 'password' => Hash::make('GiangVien@123'),
            'khoa_id' => $khoaNN->id, 'email_verified_at' => now(), 'active' => true,
        ]);

        $gvKT1 = User::create([
            'role' => 'giangvien', 'ma_so' => 'GV005', 'name' => 'Võ Thanh Em',
            'email' => 'gv.em@hvnh.edu.vn', 'password' => Hash::make('GiangVien@123'),
            'khoa_id' => $khoaKT->id, 'email_verified_at' => now(), 'active' => true,
        ]);
        $gvKT2 = User::create([
            'role' => 'giangvien', 'ma_so' => 'GV006', 'name' => 'Đặng Thị Phương',
            'email' => 'gv.phuong@hvnh.edu.vn', 'password' => Hash::make('GiangVien@123'),
            'khoa_id' => $khoaKT->id, 'email_verified_at' => now(), 'active' => true,
        ]);

        $this->command->info('✅ 6 Giảng viên: gv.an / gv.binh / gv.cuong / gv.duyen / gv.em / gv.phuong @hvnh.edu.vn / GiangVien@123');

        // ================================================================
        // 5. USERS – Sinh viên (20 SV) + SvWhitelist
        // ================================================================
        $sinhViens = [];
        $svData = [
            // Khoa CNTT - 8 SV
            ['22A4000001', 'Trần Thị Bảo', 'K22CLC1', 'K22', $khoaCNTT->id],
            ['22A4000002', 'Nguyễn Hoàng Dũng', 'K22CLC1', 'K22', $khoaCNTT->id],
            ['22A4000003', 'Lê Minh Hải', 'K22CLC2', 'K22', $khoaCNTT->id],
            ['22A4000004', 'Phạm Thị Hoa', 'K22CLC2', 'K22', $khoaCNTT->id],
            ['23A4000001', 'Vũ Đức Kiên', 'K23CLC1', 'K23', $khoaCNTT->id],
            ['23A4000002', 'Đỗ Thị Lan', 'K23CLC1', 'K23', $khoaCNTT->id],
            ['23A4000003', 'Trương Văn Minh', 'K23CLC2', 'K23', $khoaCNTT->id],
            ['23A4000004', 'Hoàng Thị Nga', 'K23CLC2', 'K23', $khoaCNTT->id],
            // Khoa Ngoại ngữ - 6 SV
            ['22B3000001', 'Bùi Văn Phong', 'K22NN1', 'K22', $khoaNN->id],
            ['22B3000002', 'Cao Thị Quỳnh', 'K22NN1', 'K22', $khoaNN->id],
            ['22B3000003', 'Đinh Văn Rơi', 'K22NN2', 'K22', $khoaNN->id],
            ['23B3000001', 'Ngô Thị Sương', 'K23NN1', 'K23', $khoaNN->id],
            ['23B3000002', 'Lý Văn Tài', 'K23NN1', 'K23', $khoaNN->id],
            ['23B3000003', 'Mai Thị Uyên', 'K23NN2', 'K23', $khoaNN->id],
            // Khoa Kế toán - 6 SV
            ['22C2000001', 'Phan Văn Vinh', 'K22KT1', 'K22', $khoaKT->id],
            ['22C2000002', 'Huỳnh Thị Xuân', 'K22KT1', 'K22', $khoaKT->id],
            ['22C2000003', 'Trịnh Văn Yên', 'K22KT2', 'K22', $khoaKT->id],
            ['23C2000001', 'Lương Thị Ánh', 'K23KT1', 'K23', $khoaKT->id],
            ['23C2000002', 'Tạ Văn Bằng', 'K23KT1', 'K23', $khoaKT->id],
            ['23C2000003', 'Châu Thị Cẩm', 'K23KT2', 'K23', $khoaKT->id],
        ];

        foreach ($svData as [$maSv, $hoTen, $lop, $khoaHoc, $khoaId]) {
            $email = 'sv' . strtolower($maSv) . '@hvnh.edu.vn';

            // Tạo whitelist
            SvWhitelist::create([
                'ma_sv'    => $maSv,
                'ho_ten'   => $hoTen,
                'email'    => $email,
                'lop'      => $lop,
                'khoa_hoc' => $khoaHoc,
                'khoa_id'  => $khoaId,
                'da_dang_ky' => true,
            ]);

            // Tạo user sinh viên
            $sv = User::create([
                'role'              => 'sinhvien',
                'ma_so'             => $maSv,
                'name'              => $hoTen,
                'email'             => $email,
                'password'          => Hash::make('SinhVien@123'),
                'khoa_id'           => $khoaId,
                'lop'               => $lop,
                'khoa_hoc'          => $khoaHoc,
                'email_verified_at' => now(),
                'active'            => true,
            ]);

            $sinhViens[$maSv] = $sv;
        }

        // Thêm 2 SV whitelist chưa đăng ký (để test đăng ký tài khoản)
        SvWhitelist::create([
            'ma_sv' => '24A4000001', 'ho_ten' => 'Nguyễn Thị Test',
            'email' => 'sv24a4000001@hvnh.edu.vn', 'lop' => 'K24CLC1', 'khoa_hoc' => 'K24',
            'khoa_id' => $khoaCNTT->id, 'da_dang_ky' => false,
        ]);
        SvWhitelist::create([
            'ma_sv' => '24B3000001', 'ho_ten' => 'Trần Văn Demo',
            'email' => 'sv24b3000001@hvnh.edu.vn', 'lop' => 'K24NN1', 'khoa_hoc' => 'K24',
            'khoa_id' => $khoaNN->id, 'da_dang_ky' => false,
        ]);

        $this->command->info('✅ 20 Sinh viên (pass: SinhVien@123) + 2 Whitelist chưa đăng ký');

        // ================================================================
        // 6. KHO ĐỀ THI (DeThi + CauHoi)
        // ================================================================
        // -- Đề thi CNTT (Tin học) --
        $deThiCNTT1 = DeThi::create([
            'ma_de' => 'DE-CNTT-001', 'loai_chung_chi' => 'cntt',
            'khoa_id' => $khoaCNTT->id, 'ten_de' => 'Đề thi Tin học cơ bản - Đề 1', 'active' => true,
        ]);
        $this->taoCauHoiTracNghiem($deThiCNTT1, 'cntt', 20);
        $this->taoCauHoiTuLuan($deThiCNTT1, 'cntt', 5);

        $deThiCNTT2 = DeThi::create([
            'ma_de' => 'DE-CNTT-002', 'loai_chung_chi' => 'cntt',
            'khoa_id' => $khoaCNTT->id, 'ten_de' => 'Đề thi Tin học cơ bản - Đề 2', 'active' => true,
        ]);
        $this->taoCauHoiTracNghiem($deThiCNTT2, 'cntt', 20);
        $this->taoCauHoiTuLuan($deThiCNTT2, 'cntt', 5);

        $deThiCNTT3 = DeThi::create([
            'ma_de' => 'DE-CNTT-003', 'loai_chung_chi' => 'cntt',
            'khoa_id' => $khoaCNTT->id, 'ten_de' => 'Đề thi Tin học nâng cao - Đề 1', 'active' => true,
        ]);
        $this->taoCauHoiTracNghiem($deThiCNTT3, 'cntt', 30);

        // Đề không active (đã ngưng sử dụng)
        $deThiCNTT_old = DeThi::create([
            'ma_de' => 'DE-CNTT-OLD', 'loai_chung_chi' => 'cntt',
            'khoa_id' => $khoaCNTT->id, 'ten_de' => 'Đề thi cũ (ngưng sử dụng)', 'active' => false,
        ]);
        $this->taoCauHoiTracNghiem($deThiCNTT_old, 'cntt', 10);

        // -- Đề thi Ngoại ngữ (Tiếng Anh) --
        $deThiNN1 = DeThi::create([
            'ma_de' => 'DE-NN-001', 'loai_chung_chi' => 'tienganh',
            'khoa_id' => $khoaNN->id, 'ten_de' => 'Đề thi Tiếng Anh B1 - Đề 1', 'active' => true,
        ]);
        $this->taoCauHoiTracNghiem($deThiNN1, 'tienganh', 25);
        $this->taoCauHoiTuLuan($deThiNN1, 'tienganh', 5);

        $deThiNN2 = DeThi::create([
            'ma_de' => 'DE-NN-002', 'loai_chung_chi' => 'tienganh',
            'khoa_id' => $khoaNN->id, 'ten_de' => 'Đề thi Tiếng Anh B1 - Đề 2', 'active' => true,
        ]);
        $this->taoCauHoiTracNghiem($deThiNN2, 'tienganh', 25);
        $this->taoCauHoiTuLuan($deThiNN2, 'tienganh', 5);

        // -- Đề thi Kế toán --
        $deThiKT1 = DeThi::create([
            'ma_de' => 'DE-KT-001', 'loai_chung_chi' => 'cntt',
            'khoa_id' => $khoaKT->id, 'ten_de' => 'Đề thi Tin học ứng dụng Kế toán', 'active' => true,
        ]);
        $this->taoCauHoiTracNghiem($deThiKT1, 'cntt', 20);
        $this->taoCauHoiTuLuan($deThiKT1, 'cntt', 5);

        $this->command->info('✅ 8 Đề thi (CNTT: 4, NN: 2, KT: 1 + 1 inactive) với câu hỏi trắc nghiệm + tự luận');

        // ================================================================
        // 7. KỲ THI (KyThi)
        // ================================================================
        // Kỳ thi 1: Đã kết thúc (đầy đủ dữ liệu để test xem kết quả)
        $kyThi1 = KyThi::create([
            'ten_ky_thi' => 'Kỳ thi Chứng chỉ CNTT & Ngoại ngữ - HK1 2025-2026',
            'nam_hoc'    => '2025-2026',
            'hoc_ky'     => 'Học kỳ 1',
            'mo_ta'      => 'Kỳ thi cấp chứng chỉ Tin học và Tiếng Anh đợt 1 năm học 2025-2026',
            'trang_thai' => 'da_ket_thuc',
        ]);

        // Kỳ thi 2: Đang diễn ra (có ca đang thi)
        $kyThi2 = KyThi::create([
            'ten_ky_thi' => 'Kỳ thi Chứng chỉ CNTT & Ngoại ngữ - HK2 2025-2026',
            'nam_hoc'    => '2025-2026',
            'hoc_ky'     => 'Học kỳ 2',
            'mo_ta'      => 'Kỳ thi cấp chứng chỉ đợt 2',
            'trang_thai' => 'dang_dien_ra',
        ]);

        // Kỳ thi 3: Đang mở đăng ký (SV có thể đăng ký)
        $kyThi3 = KyThi::create([
            'ten_ky_thi' => 'Kỳ thi bổ sung Chứng chỉ CNTT - Đợt 3',
            'nam_hoc'    => '2025-2026',
            'hoc_ky'     => 'Bổ sung',
            'mo_ta'      => 'Kỳ thi bổ sung dành cho SV chưa đạt hoặc chưa thi',
            'trang_thai' => 'dang_mo_dang_ky',
        ]);

        // Kỳ thi 4: Đã đóng đăng ký
        $kyThi4 = KyThi::create([
            'ten_ky_thi' => 'Kỳ thi Chứng chỉ Tiếng Anh bổ sung',
            'nam_hoc'    => '2025-2026',
            'hoc_ky'     => 'Bổ sung',
            'mo_ta'      => 'Đợt thi tiếng Anh bổ sung, đã đóng đăng ký',
            'trang_thai' => 'da_dong_dang_ky',
        ]);

        $this->command->info('✅ 4 Kỳ thi: đã kết thúc, đang diễn ra, đang mở đăng ký, đã đóng');

        // ================================================================
        // 8. LỊCH THI (LichThi)
        // ================================================================

        // --- Kỳ thi 1 (đã kết thúc): 3 ca thi ---
        $lt1_cntt = LichThi::create([
            'ky_thi_id' => $kyThi1->id,
            'ten_ky_thi' => $kyThi1->ten_ky_thi,
            'loai_chung_chi' => 'cntt',
            'khoa_id' => $khoaCNTT->id,
            'ngay_thi' => now()->subDays(30)->toDateString(),
            'gio_bat_dau' => '08:00',
            'thoi_gian_thi_phut' => 90,
            'phong_thi' => 'P.A301',
            'so_luong_toi_da' => 40,
            'han_dang_ky' => now()->subDays(35),
            'le_phi' => 200000,
            'ma_ca_thi' => 'CT-HK1-CNTT-01',
            'trang_thai' => 'da_ket_thuc',
            'de_thi_id' => $deThiCNTT1->id,
            'trang_thai_cong_bo' => 'da_cong_bo',
            'ngay_cong_bo' => now()->subDays(3),
            'nguoi_cong_bo_id' => $admin->id,
        ]);

        $lt1_nn = LichThi::create([
            'ky_thi_id' => $kyThi1->id,
            'ten_ky_thi' => $kyThi1->ten_ky_thi,
            'loai_chung_chi' => 'tienganh',
            'khoa_id' => $khoaNN->id,
            'ngay_thi' => now()->subDays(30)->toDateString(),
            'gio_bat_dau' => '13:30',
            'thoi_gian_thi_phut' => 90,
            'phong_thi' => 'P.B201',
            'so_luong_toi_da' => 35,
            'han_dang_ky' => now()->subDays(35),
            'le_phi' => 250000,
            'ma_ca_thi' => 'CT-HK1-NN-01',
            'trang_thai' => 'da_ket_thuc',
            'de_thi_id' => $deThiNN1->id,
            'trang_thai_cong_bo' => 'da_cong_bo',
            'ngay_cong_bo' => now()->subDays(2),
            'nguoi_cong_bo_id' => $admin->id,
        ]);

        $lt1_cntt2 = LichThi::create([
            'ky_thi_id' => $kyThi1->id,
            'ten_ky_thi' => $kyThi1->ten_ky_thi,
            'loai_chung_chi' => 'cntt',
            'khoa_id' => $khoaCNTT->id,
            'ngay_thi' => now()->subDays(29)->toDateString(),
            'gio_bat_dau' => '08:00',
            'thoi_gian_thi_phut' => 90,
            'phong_thi' => 'P.A302',
            'so_luong_toi_da' => 40,
            'han_dang_ky' => now()->subDays(35),
            'le_phi' => 200000,
            'ma_ca_thi' => 'CT-HK1-CNTT-02',
            'trang_thai' => 'da_ket_thuc',
            'de_thi_id' => $deThiCNTT2->id,
            'trang_thai_cong_bo' => 'chua_cong_bo', // Chưa công bố điểm
        ]);

        // --- Kỳ thi 2 (đang diễn ra): 2 ca thi ---
        $lt2_cntt = LichThi::create([
            'ky_thi_id' => $kyThi2->id,
            'ten_ky_thi' => $kyThi2->ten_ky_thi,
            'loai_chung_chi' => 'cntt',
            'khoa_id' => $khoaCNTT->id,
            'ngay_thi' => now()->toDateString(),
            'gio_bat_dau' => now()->subMinutes(15)->format('H:i'),
            'thoi_gian_thi_phut' => 90,
            'phong_thi' => 'P.A301',
            'so_luong_toi_da' => 40,
            'han_dang_ky' => now()->subDays(3),
            'le_phi' => 200000,
            'ma_ca_thi' => 'CT-HK2-CNTT-01',
            'trang_thai' => 'dang_thi',
            'de_thi_id' => $deThiCNTT1->id,
        ]);

        $lt2_nn = LichThi::create([
            'ky_thi_id' => $kyThi2->id,
            'ten_ky_thi' => $kyThi2->ten_ky_thi,
            'loai_chung_chi' => 'tienganh',
            'khoa_id' => $khoaNN->id,
            'ngay_thi' => now()->toDateString(),
            'gio_bat_dau' => now()->subMinutes(5)->format('H:i'),
            'thoi_gian_thi_phut' => 90,
            'phong_thi' => 'P.B201',
            'so_luong_toi_da' => 35,
            'han_dang_ky' => now()->subDays(2),
            'le_phi' => 250000,
            'ma_ca_thi' => 'CT-HK2-NN-01',
            'trang_thai' => 'dang_thi',
            'de_thi_id' => $deThiNN2->id,
        ]);

        // --- Kỳ thi 3 (đang mở đăng ký): 3 ca thi ---
        $lt3_cntt = LichThi::create([
            'ky_thi_id' => $kyThi3->id,
            'ten_ky_thi' => $kyThi3->ten_ky_thi,
            'loai_chung_chi' => 'cntt',
            'khoa_id' => $khoaCNTT->id,
            'ngay_thi' => now()->addDays(30)->toDateString(),
            'gio_bat_dau' => '08:00',
            'thoi_gian_thi_phut' => 90,
            'phong_thi' => 'P.A301',
            'so_luong_toi_da' => 50,
            'han_dang_ky' => now()->addDays(20),
            'le_phi' => 200000,
            'ma_ca_thi' => 'CT-BS-CNTT-01',
            'trang_thai' => 'dang_mo_dang_ky',
        ]);

        $lt3_cntt2 = LichThi::create([
            'ky_thi_id' => $kyThi3->id,
            'ten_ky_thi' => $kyThi3->ten_ky_thi,
            'loai_chung_chi' => 'cntt',
            'khoa_id' => $khoaCNTT->id,
            'ngay_thi' => now()->addDays(30)->toDateString(),
            'gio_bat_dau' => '13:30',
            'thoi_gian_thi_phut' => 90,
            'phong_thi' => 'P.A302',
            'so_luong_toi_da' => 50,
            'han_dang_ky' => now()->addDays(20),
            'le_phi' => 200000,
            'ma_ca_thi' => 'CT-BS-CNTT-02',
            'trang_thai' => 'dang_mo_dang_ky',
        ]);

        $lt3_kt = LichThi::create([
            'ky_thi_id' => $kyThi3->id,
            'ten_ky_thi' => $kyThi3->ten_ky_thi,
            'loai_chung_chi' => 'cntt',
            'khoa_id' => $khoaKT->id,
            'ngay_thi' => now()->addDays(31)->toDateString(),
            'gio_bat_dau' => '08:00',
            'thoi_gian_thi_phut' => 90,
            'phong_thi' => 'P.C101',
            'so_luong_toi_da' => 40,
            'han_dang_ky' => now()->addDays(20),
            'le_phi' => 200000,
            'ma_ca_thi' => 'CT-BS-KT-01',
            'trang_thai' => 'dang_mo_dang_ky',
        ]);

        // --- Kỳ thi 4 (đã đóng đăng ký) ---
        $lt4_nn = LichThi::create([
            'ky_thi_id' => $kyThi4->id,
            'ten_ky_thi' => $kyThi4->ten_ky_thi,
            'loai_chung_chi' => 'tienganh',
            'khoa_id' => $khoaNN->id,
            'ngay_thi' => now()->addDays(10)->toDateString(),
            'gio_bat_dau' => '08:00',
            'thoi_gian_thi_phut' => 90,
            'phong_thi' => 'P.B201',
            'so_luong_toi_da' => 30,
            'han_dang_ky' => now()->subDays(1),
            'le_phi' => 250000,
            'ma_ca_thi' => 'CT-BS-NN-01',
            'trang_thai' => 'da_dong_dang_ky',
        ]);

        $this->command->info('✅ 10 Lịch thi phân bổ trên 4 kỳ thi');

        // ================================================================
        // 9. ĐĂNG KÝ THI – Tất cả các trạng thái
        // ================================================================

        // ---- KỲ THI 1 (đã kết thúc) – 8 đăng ký đã duyệt + thanh toán ----
        $dk_completed = [];

        // SV1: Đăng ký CNTT ca 1 – đã duyệt, đã thanh toán, ĐẠT (88.5 điểm - Có Chứng nhận)
        $dk1 = $this->taoDangKyDaDuyet($sinhViens['22A4000001'], $lt1_cntt, $admin);
        $dk_completed[] = ['dk' => $dk1, 'sv' => $sinhViens['22A4000001'], 'de_thi' => $deThiCNTT1, 'diem' => 88.5, 'dat' => true];

        // SV1: Đăng ký NN ca 1 – đã duyệt, điểm 48.5 (chưa đạt, còn hạn phúc khảo, chưa gửi đơn)
        $dk1_pk = $this->taoDangKyDaDuyet($sinhViens['22A4000001'], $lt1_nn, $admin);
        $dk_completed[] = ['dk' => $dk1_pk, 'sv' => $sinhViens['22A4000001'], 'de_thi' => $deThiNN1, 'diem' => 48.5, 'dat' => false];

        // SV1: Đăng ký CNTT ca 1 (bài 2) – điểm 62.0 (Đã gửi phúc khảo đang xử lý)
        $dk1_pk_done = $this->taoDangKyDaDuyet($sinhViens['22A4000001'], $lt1_cntt, $admin);
        $dk_completed[] = ['dk' => $dk1_pk_done, 'sv' => $sinhViens['22A4000001'], 'de_thi' => $deThiCNTT2, 'diem' => 62.0, 'dat' => true];

        // SV2: Đăng ký CNTT ca 1 – đã duyệt, đã thanh toán, KHÔNG ĐẠT
        $dk2 = $this->taoDangKyDaDuyet($sinhViens['22A4000002'], $lt1_cntt, $admin);
        $dk_completed[] = ['dk' => $dk2, 'sv' => $sinhViens['22A4000002'], 'de_thi' => $deThiCNTT1, 'diem' => 38.0, 'dat' => false];

        // SV3: Đăng ký CNTT ca 2 – đã duyệt, đã thanh toán, ĐẠT
        $dk3 = $this->taoDangKyDaDuyet($sinhViens['22A4000003'], $lt1_cntt2, $admin);
        $dk_completed[] = ['dk' => $dk3, 'sv' => $sinhViens['22A4000003'], 'de_thi' => $deThiCNTT2, 'diem' => 62.0, 'dat' => true];

        // SV4: Đăng ký CNTT ca 2 – đã duyệt, đã thanh toán, ĐẠT (điểm cao)
        $dk4 = $this->taoDangKyDaDuyet($sinhViens['22A4000004'], $lt1_cntt2, $admin);
        $dk_completed[] = ['dk' => $dk4, 'sv' => $sinhViens['22A4000004'], 'de_thi' => $deThiCNTT2, 'diem' => 92.5, 'dat' => true];

        // SV9: Đăng ký NN ca 1 – đã duyệt, đã thanh toán, ĐẠT
        $dk5 = $this->taoDangKyDaDuyet($sinhViens['22B3000001'], $lt1_nn, $admin);
        $dk_completed[] = ['dk' => $dk5, 'sv' => $sinhViens['22B3000001'], 'de_thi' => $deThiNN1, 'diem' => 80.0, 'dat' => true];

        // SV10: Đăng ký NN ca 1 – KHÔNG ĐẠT
        $dk6 = $this->taoDangKyDaDuyet($sinhViens['22B3000002'], $lt1_nn, $admin);
        $dk_completed[] = ['dk' => $dk6, 'sv' => $sinhViens['22B3000002'], 'de_thi' => $deThiNN1, 'diem' => 42.0, 'dat' => false];

        // SV11: Đăng ký NN ca 1 – ĐẠT
        $dk7 = $this->taoDangKyDaDuyet($sinhViens['22B3000003'], $lt1_nn, $admin);
        $dk_completed[] = ['dk' => $dk7, 'sv' => $sinhViens['22B3000003'], 'de_thi' => $deThiNN1, 'diem' => 55.0, 'dat' => true];

        // SV5: Đăng ký CNTT ca 1 – ĐẠT (cũng test phúc khảo)
        $dk8 = $this->taoDangKyDaDuyet($sinhViens['23A4000001'], $lt1_cntt, $admin);
        $dk_completed[] = ['dk' => $dk8, 'sv' => $sinhViens['23A4000001'], 'de_thi' => $deThiCNTT1, 'diem' => 48.5, 'dat' => false];

        // ---- KỲ THI 2 (đang diễn ra) – đăng ký đã duyệt chờ thi ----
        $dk1_prep = $this->taoDangKyDaDuyet($sinhViens['22A4000001'], $lt2_nn, $admin); // SV1: ca chuẩn bị thi (phòng chờ)
        $dk1_live = $this->taoDangKyDaDuyet($sinhViens['22A4000001'], $lt2_cntt, $admin); // SV1: ca đang làm bài thi
        $dk9  = $this->taoDangKyDaDuyet($sinhViens['23A4000002'], $lt2_cntt, $admin);
        $dk10 = $this->taoDangKyDaDuyet($sinhViens['23A4000003'], $lt2_cntt, $admin);
        $dk11 = $this->taoDangKyDaDuyet($sinhViens['23B3000001'], $lt2_nn, $admin);
        $dk12 = $this->taoDangKyDaDuyet($sinhViens['23B3000002'], $lt2_nn, $admin);

        // ---- KỲ THI 3 (đang mở đăng ký) – tất cả trạng thái đăng ký ----

        // Trạng thái: cho_duyet (chờ duyệt) + đã thanh toán
        $dk_cho_duyet = DangKy::create([
            'sinh_vien_id' => $sinhViens['23A4000004']->id,
            'lich_thi_id'  => $lt3_cntt->id,
            'trang_thai'   => 'cho_duyet',
            'ma_dang_ky'   => 'DK' . now()->format('ymd') . 'CD001',
            'so_dien_thoai' => '0901234567',
            'ngay_sinh'    => '2004-03-15',
            'gioi_tinh'    => 'nu',
            'dan_toc'      => 'Kinh',
            'noi_sinh'     => 'TP. Hồ Chí Minh',
            'so_cccd'      => '079204000001',
            'tinh_thanh_pho_code' => '79', 'tinh_thanh_pho_ten' => 'TP. Hồ Chí Minh',
            'trang_thai_thanh_toan' => 'da_thanh_toan',
            'phuong_thuc_thanh_toan' => 'vnpay',
            'ma_giao_dich'  => 'VNP' . Str::random(10),
            'so_tien'       => 200000,
            'ngay_thanh_toan' => now()->subHours(3),
            'han_thanh_toan' => now()->addDays(2),
        ]);
        $this->taoLichSu($dk_cho_duyet, $sinhViens['23A4000004']->id, 'sinh_vien', 'tao_ho_so', null, 'cho_duyet');

        // Trạng thái: cho_duyet + chờ thanh toán (chưa thanh toán)
        $dk_cho_tt = DangKy::create([
            'sinh_vien_id' => $sinhViens['22C2000001']->id,
            'lich_thi_id'  => $lt3_kt->id,
            'trang_thai'   => 'cho_duyet',
            'ma_dang_ky'   => 'DK' . now()->format('ymd') . 'CT001',
            'so_dien_thoai' => '0912345678',
            'ngay_sinh'    => '2004-06-20',
            'gioi_tinh'    => 'nam',
            'dan_toc'      => 'Kinh',
            'noi_sinh'     => 'Hà Nội',
            'so_cccd'      => '001204000001',
            'tinh_thanh_pho_code' => '01', 'tinh_thanh_pho_ten' => 'Hà Nội',
            'trang_thai_thanh_toan' => 'cho_thanh_toan',
            'so_tien'       => 200000,
            'han_thanh_toan' => now()->addDays(2),
        ]);
        $this->taoLichSu($dk_cho_tt, $sinhViens['22C2000001']->id, 'sinh_vien', 'tao_ho_so', null, 'cho_duyet');

        // Trạng thái: cho_bo_sung (admin yêu cầu bổ sung hồ sơ)
        $dk_bo_sung = DangKy::create([
            'sinh_vien_id' => $sinhViens['22C2000002']->id,
            'lich_thi_id'  => $lt3_kt->id,
            'trang_thai'   => 'cho_bo_sung',
            'ma_dang_ky'   => 'DK' . now()->format('ymd') . 'BS001',
            'so_dien_thoai' => '0923456789',
            'ngay_sinh'    => '2004-01-10',
            'gioi_tinh'    => 'nu',
            'dan_toc'      => 'Kinh',
            'noi_sinh'     => 'Đà Nẵng',
            'so_cccd'      => '048204000002',
            'trang_thai_thanh_toan' => 'da_thanh_toan',
            'phuong_thuc_thanh_toan' => 'vnpay',
            'ma_giao_dich'  => 'VNP' . Str::random(10),
            'so_tien'       => 200000,
            'ngay_thanh_toan' => now()->subDays(1),
            'truong_can_bo_sung' => json_encode(['anh_cccd_truoc', 'anh_cccd_sau']),
            'ly_do_bo_sung' => 'Ảnh CCCD không rõ nét, vui lòng chụp lại',
            'han_bo_sung' => now()->addDays(3),
            'han_thanh_toan' => now()->addDays(2),
        ]);
        $this->taoLichSu($dk_bo_sung, $sinhViens['22C2000002']->id, 'sinh_vien', 'tao_ho_so', null, 'cho_duyet');
        $this->taoLichSu($dk_bo_sung, $admin->id, 'admin', 'yeu_cau_bo_sung', 'cho_duyet', 'cho_bo_sung');

        // Trạng thái: da_bo_sung (SV đã bổ sung, chờ duyệt lại)
        $dk_da_bs = DangKy::create([
            'sinh_vien_id' => $sinhViens['22C2000003']->id,
            'lich_thi_id'  => $lt3_kt->id,
            'trang_thai'   => 'da_bo_sung',
            'ma_dang_ky'   => 'DK' . now()->format('ymd') . 'DBS01',
            'so_dien_thoai' => '0934567890',
            'ngay_sinh'    => '2004-08-25',
            'gioi_tinh'    => 'nam',
            'dan_toc'      => 'Kinh',
            'noi_sinh'     => 'Cần Thơ',
            'so_cccd'      => '092204000003',
            'trang_thai_thanh_toan' => 'da_thanh_toan',
            'phuong_thuc_thanh_toan' => 'vnpay',
            'ma_giao_dich'  => 'VNP' . Str::random(10),
            'so_tien'       => 200000,
            'ngay_thanh_toan' => now()->subDays(2),
            'ngay_bo_sung'  => now()->subHours(5),
            'han_thanh_toan' => now()->addDays(2),
        ]);
        $this->taoLichSu($dk_da_bs, $sinhViens['22C2000003']->id, 'sinh_vien', 'tao_ho_so', null, 'cho_duyet');
        $this->taoLichSu($dk_da_bs, $admin->id, 'admin', 'yeu_cau_bo_sung', 'cho_duyet', 'cho_bo_sung');
        $this->taoLichSu($dk_da_bs, $sinhViens['22C2000003']->id, 'sinh_vien', 'bo_sung_ho_so', 'cho_bo_sung', 'da_bo_sung');

        // Trạng thái: tu_choi
        $dk_tu_choi = DangKy::create([
            'sinh_vien_id' => $sinhViens['23C2000001']->id,
            'lich_thi_id'  => $lt3_kt->id,
            'trang_thai'   => 'tu_choi',
            'ly_do_tu_choi' => 'Hồ sơ không hợp lệ: thiếu ảnh thẻ sinh viên và CCCD giả mạo',
            'ma_dang_ky'   => 'DK' . now()->format('ymd') . 'TC001',
            'so_dien_thoai' => '0945678901',
            'ngay_sinh'    => '2005-02-14',
            'gioi_tinh'    => 'nu',
            'dan_toc'      => 'Kinh',
            'noi_sinh'     => 'Huế',
            'so_cccd'      => '046205000001',
            'nguoi_duyet_id' => $admin->id,
            'ngay_duyet'   => now()->subDays(1),
            'trang_thai_thanh_toan' => 'da_thanh_toan',
            'phuong_thuc_thanh_toan' => 'vnpay',
            'ma_giao_dich'  => 'VNP' . Str::random(10),
            'so_tien'       => 200000,
            'ngay_thanh_toan' => now()->subDays(3),
            'han_thanh_toan' => now()->subDays(1),
        ]);
        $this->taoLichSu($dk_tu_choi, $sinhViens['23C2000001']->id, 'sinh_vien', 'tao_ho_so', null, 'cho_duyet');
        $this->taoLichSu($dk_tu_choi, $admin->id, 'admin', 'tu_choi', 'cho_duyet', 'tu_choi');

        // Trạng thái: da_huy (SV tự hủy hoặc hệ thống tự hủy do quá hạn thanh toán)
        $dk_da_huy = DangKy::create([
            'sinh_vien_id' => $sinhViens['23C2000002']->id,
            'lich_thi_id'  => $lt3_cntt2->id,
            'trang_thai'   => 'da_huy',
            'ma_dang_ky'   => 'DK' . now()->format('ymd') . 'HUY01',
            'so_dien_thoai' => '0956789012',
            'ngay_sinh'    => '2005-11-30',
            'gioi_tinh'    => 'nam',
            'dan_toc'      => 'Kinh',
            'noi_sinh'     => 'Hải Phòng',
            'so_cccd'      => '031205000002',
            'trang_thai_thanh_toan' => 'thanh_toan_that_bai',
            'han_thanh_toan' => now()->subDays(1),
        ]);
        $this->taoLichSu($dk_da_huy, $sinhViens['23C2000002']->id, 'sinh_vien', 'tao_ho_so', null, 'cho_duyet');
        $this->taoLichSu($dk_da_huy, null, 'admin', 'huy', 'cho_duyet', 'da_huy',
            'Hệ thống tự động huỷ đăng ký do quá hạn thanh toán lệ phí thi (sau 2 ngày kể từ khi đăng ký).');

        // Đăng ký đã duyệt cho kỳ 3 (để test SV chưa thi nhưng sẵn sàng)
        $dk_da_duyet_k3 = $this->taoDangKyDaDuyet($sinhViens['23C2000003'], $lt3_cntt->id === $lt3_cntt->id ? $lt3_cntt : $lt3_kt, $admin);

        // ---- KỲ THI 4 (đã đóng) ----
        $dk_k4_1 = $this->taoDangKyDaDuyet($sinhViens['23B3000003'], $lt4_nn, $admin);

        $this->command->info('✅ Đăng ký thi: cho_duyet, cho_thanh_toan, cho_bo_sung, da_bo_sung, da_duyet, tu_choi, da_huy');

        // ================================================================
        // 10. BÀI THI + CÂU TRẢ LỜI – Tất cả trạng thái chấm thi
        // ================================================================
        foreach ($dk_completed as $item) {
            $baiThi = $this->taoBaiThiVaCauTraLoi(
                $item['dk'], $item['de_thi'], $item['diem'],
                $item['dat'] ? 'da_cong_bo' : 'da_cong_bo',
                $gvCNTT1, $gvCNTT2, $admin,
                $item['de_thi']->loai_chung_chi === 'tienganh' ? $gvNN1 : $gvCNTT1,
                $item['de_thi']->loai_chung_chi === 'tienganh' ? $gvNN2 : $gvCNTT2,
            );
            $item['bai_thi'] = $baiThi;
            // Cập nhật lại mảng
            $dk_completed[array_search($item, $dk_completed)] = $item;
        }

        // Bài thi đang thi (kỳ 2)
        BaiThi::create([
            'dang_ky_id' => $dk1_live->id,
            'de_thi_id'  => $deThiCNTT1->id,
            'gio_bat_dau' => now()->subMinutes(15),
            'trang_thai'  => 'dang_thi',
        ]);

        BaiThi::create([
            'dang_ky_id' => $dk9->id,
            'de_thi_id'  => $deThiCNTT1->id,
            'gio_bat_dau' => now(),
            'trang_thai'  => 'dang_thi',
        ]);

        // Bài thi đã nộp chưa chấm
        $btChuaCham = BaiThi::create([
            'dang_ky_id' => $dk10->id,
            'de_thi_id'  => $deThiCNTT1->id,
            'gio_bat_dau' => now()->subMinutes(90),
            'gio_nop'     => now()->subMinutes(5),
            'trang_thai'  => 'da_nop',
        ]);
        // Tạo câu trả lời cho bài chưa chấm
        $cauHoisDe1 = CauHoi::where('de_thi_id', $deThiCNTT1->id)->get();
        foreach ($cauHoisDe1 as $ch) {
            CauTraLoi::create([
                'bai_thi_id'  => $btChuaCham->id,
                'cau_hoi_id'  => $ch->id,
                'dap_an_chon' => $ch->loai_cau === 'tracnghiem' ? collect(['A','B','C','D'])->random() : null,
                'bai_lam_tu_luan' => $ch->loai_cau === 'tuluan' ? 'Bài làm tự luận mẫu của sinh viên...' : null,
                'diem_dat'    => 0,
                'da_cham'     => false,
            ]);
        }

        $this->command->info('✅ Bài thi: dang_thi, da_nop(chưa chấm), da_cham, da_cong_bo');

        // ================================================================
        // 11. PHÚC KHẢO – Tất cả trạng thái workflow
        // ================================================================

        // Tìm bài thi của SV2 (không đạt) – phúc khảo chờ tiếp nhận
        $btSv2 = BaiThi::where('dang_ky_id', $dk2->id)->first();
        PhucKhao::create([
            'bai_thi_id'    => $btSv2->id,
            'sinh_vien_id'  => $sinhViens['22A4000002']->id,
            'ly_do'         => 'Em cho rằng phần tự luận câu 3 chưa được chấm đúng, xin phúc khảo.',
            'trang_thai'    => PhucKhao::TRANG_THAI_CHO_TIEP_NHAN,
            'diem_truoc'    => $btSv2->diem_tong,
        ]);

        // SV5 (không đạt 48.5) – phúc khảo đã tiếp nhận, chờ phân công
        $btSv5 = BaiThi::where('dang_ky_id', $dk8->id)->first();
        PhucKhao::create([
            'bai_thi_id'        => $btSv5->id,
            'sinh_vien_id'      => $sinhViens['23A4000001']->id,
            'ly_do'             => 'Em muốn xin phúc khảo lại bài thi vì điểm sát mức đạt.',
            'trang_thai'        => PhucKhao::TRANG_THAI_CHO_PHAN_CONG,
            'diem_truoc'        => $btSv5->diem_tong,
            'admin_tiep_nhan_id' => $admin->id,
            'ngay_tiep_nhan'    => now()->subDays(5),
        ]);

        // SV10 (NN, không đạt) – phúc khảo đang xử lý (GV được phân công)
        $btSv10 = BaiThi::where('dang_ky_id', $dk6->id)->first();
        PhucKhao::create([
            'bai_thi_id'        => $btSv10->id,
            'sinh_vien_id'      => $sinhViens['22B3000002']->id,
            'giang_vien_id'     => $gvNN1->id,
            'ly_do'             => 'Phần listening bị lỗi headphone, em không nghe rõ.',
            'trang_thai'        => PhucKhao::TRANG_THAI_DANG_XU_LY,
            'diem_truoc'        => $btSv10->diem_tong,
            'admin_tiep_nhan_id' => $admin->id,
            'ngay_tiep_nhan'    => now()->subDays(7),
        ]);

        // SV11 (NN, đạt 55) – phúc khảo GV đã xử lý xong, chờ admin duyệt
        $btSv11 = BaiThi::where('dang_ky_id', $dk7->id)->first();
        PhucKhao::create([
            'bai_thi_id'        => $btSv11->id,
            'sinh_vien_id'      => $sinhViens['22B3000003']->id,
            'giang_vien_id'     => $gvNN2->id,
            'ly_do'             => 'Câu tự luận phần Writing chưa được chấm hết.',
            'trang_thai'        => PhucKhao::TRANG_THAI_CHO_ADMIN_DUYET,
            'diem_truoc'        => 55.0,
            'diem_sau'          => 63.5,
            'phan_hoi'          => 'Đã chấm lại phần Writing, tăng 8.5 điểm do bài viết có nội dung tốt nhưng bị bỏ sót.',
            'admin_tiep_nhan_id' => $admin->id,
            'ngay_tiep_nhan'    => now()->subDays(10),
            'xu_ly_boi'         => $gvNN2->id,
            'ngay_xu_ly'        => now()->subDays(3),
        ]);

        // SV3 (CNTT, đạt 62) – phúc khảo hoàn tất (điểm tăng)
        $btSv3 = BaiThi::where('dang_ky_id', $dk3->id)->first();
        PhucKhao::create([
            'bai_thi_id'        => $btSv3->id,
            'sinh_vien_id'      => $sinhViens['22A4000003']->id,
            'giang_vien_id'     => $gvCNTT1->id,
            'ly_do'             => 'Câu tự luận số 2 bị chấm thiếu ý, xin phúc khảo.',
            'trang_thai'        => PhucKhao::TRANG_THAI_HOAN_TAT,
            'diem_truoc'        => 62.0,
            'diem_sau'          => 68.0,
            'phan_hoi'          => 'Sau khi chấm lại, phát hiện thiếu 1 ý trong câu tự luận 2. Điểm đã cập nhật.',
            'admin_tiep_nhan_id' => $admin->id,
            'ngay_tiep_nhan'    => now()->subDays(15),
            'xu_ly_boi'         => $gvCNTT1->id,
            'ngay_xu_ly'        => now()->subDays(10),
            'admin_duyet_id'    => $admin->id,
            'ngay_duyet'        => now()->subDays(8),
            'ly_do_duyet'       => 'Đã xác nhận kết quả phúc khảo, điểm tăng từ 62 lên 68. Phê duyệt hoàn tất.',
        ]);

        // SV4 (CNTT, đạt 92.5) – phúc khảo từ chối
        $btSv4 = BaiThi::where('dang_ky_id', $dk4->id)->first();
        PhucKhao::create([
            'bai_thi_id'        => $btSv4->id,
            'sinh_vien_id'      => $sinhViens['22A4000004']->id,
            'ly_do'             => 'Yêu cầu xem lại câu 5 phần trắc nghiệm.',
            'trang_thai'        => PhucKhao::TRANG_THAI_TU_CHOI,
            'diem_truoc'        => 92.5,
            'phan_hoi'          => 'Đã kiểm tra kỹ, đáp án đúng. Điểm giữ nguyên.',
            'admin_tiep_nhan_id' => $admin->id,
            'ngay_tiep_nhan'    => now()->subDays(12),
        ]);

        // SV1 (CNTT, đạt 62) – phúc khảo đang xử lý (để test danh sách phúc khảo SV1)
        $btSv1_pk = BaiThi::where('dang_ky_id', $dk1_pk_done->id)->first();
        PhucKhao::create([
            'bai_thi_id'        => $btSv1_pk->id,
            'sinh_vien_id'      => $sinhViens['22A4000001']->id,
            'giang_vien_id'     => $gvCNTT1->id,
            'ly_do'             => 'Em xin phúc khảo câu 2 tự luận phần Microsoft Excel, mong thầy cô xem xét lại điểm.',
            'trang_thai'        => PhucKhao::TRANG_THAI_DANG_XU_LY,
            'diem_truoc'        => 62.0,
            'admin_tiep_nhan_id' => $admin->id,
            'ngay_tiep_nhan'    => now()->subDays(4),
        ]);

        $this->command->info('✅ 7 Phúc khảo: cho_tiep_nhan, cho_phan_cong, dang_xu_ly, cho_admin_duyet, hoan_tat, tu_choi');

        // ================================================================
        // 12. CHỨNG NHẬN – Tất cả trạng thái
        // ================================================================

        // SV1 (đạt 75.5) – chứng nhận đã cấp
        $btSv1 = BaiThi::where('dang_ky_id', $dk1->id)->first();
        ChungNhan::create([
            'bai_thi_id'      => $btSv1->id,
            'sinh_vien_id'    => $sinhViens['22A4000001']->id,
            'nguoi_cap_id'    => $admin->id,
            'so_chung_nhan'   => 'CN-CNTT-2025-001',
            'trang_thai'      => ChungNhan::TRANG_THAI_DA_CAP,
            'dia_chi_nhan'    => '123 Nguyễn Văn Trỗi, Q.3, TP.HCM',
            'so_dien_thoai'   => '0901234567',
            'ngay_cap'        => now()->subDays(10),
            'ghi_chu'         => 'Chứng nhận Tin học cơ bản đã cấp thành công.',
        ]);

        // SV9 (NN, đạt 80) – chứng nhận đã cấp
        $btSv9 = BaiThi::where('dang_ky_id', $dk5->id)->first();
        ChungNhan::create([
            'bai_thi_id'      => $btSv9->id,
            'sinh_vien_id'    => $sinhViens['22B3000001']->id,
            'nguoi_cap_id'    => $admin->id,
            'so_chung_nhan'   => 'CN-NN-2025-001',
            'trang_thai'      => ChungNhan::TRANG_THAI_DA_CAP,
            'dia_chi_nhan'    => '456 Lê Lợi, Q.1, TP.HCM',
            'so_dien_thoai'   => '0903456789',
            'ngay_cap'        => now()->subDays(8),
            'ghi_chu'         => 'Chứng nhận Tiếng Anh B1 đã cấp.',
        ]);

        // SV3 (CNTT, đạt 62→68 sau phúc khảo) – chứng nhận chờ xử lý
        ChungNhan::create([
            'bai_thi_id'      => $btSv3->id,
            'sinh_vien_id'    => $sinhViens['22A4000003']->id,
            'trang_thai'      => ChungNhan::TRANG_THAI_CHO_XU_LY,
            'dia_chi_nhan'    => '789 Trần Hưng Đạo, Q.5, TP.HCM',
            'so_dien_thoai'   => '0905678901',
            'ghi_chu'         => 'SV yêu cầu cấp chứng nhận sau phúc khảo thành công.',
        ]);

        // SV4 (CNTT, đạt 92.5) – chứng nhận đang xử lý
        ChungNhan::create([
            'bai_thi_id'      => $btSv4->id,
            'sinh_vien_id'    => $sinhViens['22A4000004']->id,
            'nguoi_cap_id'    => $admin->id,
            'trang_thai'      => ChungNhan::TRANG_THAI_DANG_XU_LY,
            'dia_chi_nhan'    => '321 Hai Bà Trưng, Q.1, TP.HCM',
            'so_dien_thoai'   => '0907890123',
            'ghi_chu'         => 'Admin đang xử lý in chứng nhận.',
        ]);

        // SV11 (NN, đạt 55) – chứng nhận từ chối (điểm thấp, chờ phúc khảo)
        ChungNhan::create([
            'bai_thi_id'      => $btSv11->id,
            'sinh_vien_id'    => $sinhViens['22B3000003']->id,
            'nguoi_cap_id'    => $admin->id,
            'trang_thai'      => ChungNhan::TRANG_THAI_TU_CHOI,
            'dia_chi_nhan'    => '654 Võ Văn Tần, Q.3, TP.HCM',
            'so_dien_thoai'   => '0909012345',
            'ghi_chu'         => 'Từ chối cấp chứng nhận: đang trong quá trình phúc khảo, chờ kết quả cuối cùng.',
        ]);

        $this->command->info('✅ 5 Chứng nhận: cho_xu_ly, dang_xu_ly, da_cap(×2), tu_choi');

        // ================================================================
        // TỔNG KẾT
        // ================================================================
        $this->command->newLine();
        $this->command->info('╔══════════════════════════════════════════════════════════════╗');
        $this->command->info('║         🎉 SEED FULL FLOW TEST DATA HOÀN TẤT!              ║');
        $this->command->info('╠══════════════════════════════════════════════════════════════╣');
        $this->command->info('║                                                              ║');
        $this->command->info('║  📋 TÀI KHOẢN ĐĂNG NHẬP:                                   ║');
        $this->command->info('║  ─────────────────────────────────────────────────           ║');
        $this->command->info('║  👤 Admin:      admin@hvnh.edu.vn        / Admin@123        ║');
        $this->command->info('║  🏫 Khoa CNTT:  khoa.cntt@hvnh.edu.vn   / Khoa@123         ║');
        $this->command->info('║  🏫 Khoa NN:    khoa.nn@hvnh.edu.vn     / Khoa@123         ║');
        $this->command->info('║  🏫 Khoa KT:    khoa.kt@hvnh.edu.vn     / Khoa@123         ║');
        $this->command->info('║  👨‍🏫 Giảng viên: gv.an@hvnh.edu.vn      / GiangVien@123    ║');
        $this->command->info('║                 (6 GV: gv.an, gv.binh, gv.cuong, ...)      ║');
        $this->command->info('║  🎓 Sinh viên:  sv22a4000001@hvnh.edu.vn / SinhVien@123    ║');
        $this->command->info('║                 (20 SV có đủ dữ liệu)                      ║');
        $this->command->info('║                                                              ║');
        $this->command->info('║  📊 DỮ LIỆU:                                               ║');
        $this->command->info('║  ─────────────────────────────────────────────────           ║');
        $this->command->info('║  • 3 Khoa (CNTT, NN, KT)                                   ║');
        $this->command->info('║  • 8 Đề thi (có câu hỏi trắc nghiệm + tự luận)            ║');
        $this->command->info('║  • 4 Kỳ thi (mọi trạng thái)                               ║');
        $this->command->info('║  • 10 Lịch thi (ca thi)                                    ║');
        $this->command->info('║  • 16+ Đăng ký thi (mọi trạng thái + thanh toán)           ║');
        $this->command->info('║  • 10 Bài thi + Câu trả lời (chấm 2 lần)                  ║');
        $this->command->info('║  • 6 Phúc khảo (mọi trạng thái workflow)                   ║');
        $this->command->info('║  • 5 Chứng nhận (mọi trạng thái)                           ║');
        $this->command->info('║  • 2 SV Whitelist chưa đăng ký (test tạo tài khoản)        ║');
        $this->command->info('║                                                              ║');
        $this->command->info('╚══════════════════════════════════════════════════════════════╝');
    }

    // ================================================================
    // HELPER METHODS
    // ================================================================

    /**
     * Tạo đăng ký đã duyệt + đã thanh toán đầy đủ hồ sơ
     */
    private function taoDangKyDaDuyet(User $sv, LichThi $lichThi, User $admin): DangKy
    {
        $dk = DangKy::create([
            'sinh_vien_id' => $sv->id,
            'lich_thi_id'  => $lichThi->id,
            'trang_thai'   => 'da_duyet',
            'ma_dang_ky'   => 'DK' . now()->format('ymd') . strtoupper(Str::random(5)),
            'nguoi_duyet_id' => $admin->id,
            'ngay_duyet'   => now()->subDays(rand(5, 15)),
            // Thông tin cá nhân
            'so_dien_thoai' => '09' . rand(10000000, 99999999),
            'ngay_sinh'    => fake()->date('Y-m-d', '2005-12-31'),
            'gioi_tinh'    => fake()->randomElement(['nam', 'nu']),
            'dan_toc'      => 'Kinh',
            'noi_sinh'     => fake()->randomElement(['TP. Hồ Chí Minh', 'Hà Nội', 'Đà Nẵng', 'Cần Thơ', 'Huế', 'Hải Phòng']),
            'so_cccd'      => '0' . rand(10, 99) . rand(100000000, 999999999),
            'tinh_thanh_pho_code' => '79',
            'tinh_thanh_pho_ten'  => 'TP. Hồ Chí Minh',
            // Thanh toán
            'trang_thai_thanh_toan' => 'da_thanh_toan',
            'phuong_thuc_thanh_toan' => 'vnpay',
            'ma_giao_dich'  => 'VNP' . Str::random(10),
            'so_tien'       => $lichThi->le_phi,
            'ngay_thanh_toan' => now()->subDays(rand(10, 20)),
            'han_thanh_toan' => now()->subDays(rand(8, 18)),
        ]);

        $this->taoLichSu($dk, $sv->id, 'sinh_vien', 'tao_ho_so', null, 'cho_duyet');
        $this->taoLichSu($dk, $admin->id, 'admin', 'duyet', 'cho_duyet', 'da_duyet');

        return $dk;
    }

    /**
     * Tạo bài thi đã chấm 2 lần + câu trả lời + công bố
     */
    private function taoBaiThiVaCauTraLoi(
        DangKy $dk, DeThi $deThi, float $diemTong, string $trangThai,
        User $gvCham, User $gvCham2, User $admin,
        User $gv1, User $gv2,
    ): BaiThi {
        $ngayThi = $dk->lichThi->ngay_thi;

        $baiThi = BaiThi::create([
            'dang_ky_id'    => $dk->id,
            'de_thi_id'     => $deThi->id,
            'gio_bat_dau'   => $ngayThi->copy()->setTimeFromTimeString($dk->lichThi->gio_bat_dau),
            'gio_nop'       => $ngayThi->copy()->setTimeFromTimeString($dk->lichThi->gio_bat_dau)
                                ->addMinutes($dk->lichThi->thoi_gian_thi_phut - rand(0, 15)),
            'trang_thai'    => $trangThai,
            'diem_tu_dong'  => round($diemTong * 0.4, 2),  // 40% trắc nghiệm
            'diem_cham_tay' => round($diemTong * 0.6, 2),  // 60% tự luận
            'diem_tong'     => $diemTong,
            'cham_xong'     => true,
            'giang_vien_id' => $gvCham->id,
            'ngay_cham'     => $ngayThi->copy()->addDays(3),
            // Chấm 2 lần
            'giang_vien_1_id' => $gv1->id,
            'giang_vien_2_id' => $gv2->id,
            'nhan_xet_1'      => 'Bài làm ' . ($diemTong >= 50 ? 'đạt yêu cầu' : 'chưa đạt') . '. Phần trắc nghiệm ổn, phần tự luận cần cải thiện.',
            'nhan_xet_2'      => 'Đồng ý với nhận xét GV1. ' . ($diemTong >= 80 ? 'Bài làm xuất sắc.' : ($diemTong >= 50 ? 'Bài đạt mức trung bình khá.' : 'Bài chưa đạt yêu cầu tối thiểu.')),
            'ngay_cham_1'     => $ngayThi->copy()->addDays(2),
            'ngay_cham_2'     => $ngayThi->copy()->addDays(3),
            'diem_chot'       => $diemTong,
            'ly_do_thong_nhat' => 'Hai GV đồng ý với điểm tổng ' . $diemTong,
            'da_khoa'         => true,
            // Công bố
            'ngay_cong_bo'    => $trangThai === 'da_cong_bo' ? now()->subDays(2) : null,
            'nguoi_cong_bo_id' => $trangThai === 'da_cong_bo' ? $admin->id : null,
        ]);

        // Tạo câu trả lời
        $cauHois = CauHoi::where('de_thi_id', $deThi->id)->orderBy('thu_tu')->get();
        $tongCauHoi = $cauHois->count();
        $soCauDung = $tongCauHoi > 0 ? (int) round($diemTong / 100 * $tongCauHoi) : 0;

        foreach ($cauHois as $i => $ch) {
            $isDung = $i < $soCauDung;
            $diemCau = $isDung ? $ch->diem : ($ch->loai_cau === 'tuluan' ? round($ch->diem * rand(20, 60) / 100, 2) : 0);

            CauTraLoi::create([
                'bai_thi_id'       => $baiThi->id,
                'cau_hoi_id'       => $ch->id,
                'dap_an_chon'      => $ch->loai_cau === 'tracnghiem'
                    ? ($isDung ? $ch->dap_an_dung : collect(['A','B','C','D'])->reject(fn($v) => $v === $ch->dap_an_dung)->random())
                    : null,
                'bai_lam_tu_luan'  => $ch->loai_cau === 'tuluan'
                    ? ($isDung ? 'Bài làm đầy đủ, phân tích sâu...' : 'Bài làm sơ sài, thiếu ý chính...')
                    : null,
                'diem_dat'         => $diemCau,
                'da_cham'          => true,
                'diem_gv1'         => $diemCau,
                'diem_gv2'         => $diemCau + ($ch->loai_cau === 'tuluan' ? rand(-5, 5) / 10 : 0),
            ]);
        }

        return $baiThi;
    }

    /**
     * Tạo câu hỏi trắc nghiệm cho đề thi
     */
    private function taoCauHoiTracNghiem(DeThi $deThi, string $loai, int $soCau): void
    {
        $cauHoiMauCNTT = [
            'Trong hệ điều hành Windows, tổ hợp phím nào dùng để copy?',
            'RAM là viết tắt của từ nào?',
            'Đơn vị nhỏ nhất để đo lường dữ liệu là gì?',
            'CPU viết tắt của từ nào?',
            'Thiết bị nào sau đây là thiết bị nhập?',
            'Hệ điều hành nào do Microsoft phát triển?',
            'Trong Excel, hàm SUM dùng để làm gì?',
            'Phần mở rộng của file Word là gì?',
            'Trong PowerPoint, phím nào bắt đầu trình chiếu?',
            'Virus máy tính là gì?',
            'Ổ cứng SSD khác HDD ở điểm nào?',
            'Mạng LAN là gì?',
            'IP address dùng để làm gì?',
            'Phần mềm mã nguồn mở là gì?',
            'USB là viết tắt của từ nào?',
            'Trong Word, Ctrl+Z dùng để làm gì?',
            'Firewall dùng để làm gì?',
            'Cloud computing là gì?',
            'Đuôi file .pdf là định dạng của phần mềm nào?',
            'Phím tắt Ctrl+S dùng để làm gì?',
            'Router là thiết bị dùng để làm gì?',
            'GPU viết tắt của gì?',
            'Trong Excel, ô tham chiếu tuyệt đối sử dụng ký hiệu gì?',
            'HTML là ngôn ngữ gì?',
            'Bandwidth nghĩa là gì?',
            'Byte bằng bao nhiêu bit?',
            'DNS là viết tắt của gì?',
            'BIOS là gì?',
            'Hệ điều hành Linux có đặc điểm gì?',
            'Trojan horse là loại gì?',
        ];

        $cauHoiMauNN = [
            'Choose the correct answer: She ___ to school every day.',
            'What is the past tense of "go"?',
            'Which word is a noun?',
            'Choose the synonym of "happy".',
            'Fill in the blank: I have ___ finished my homework.',
            'Which sentence is correct?',
            'What does "enormous" mean?',
            'Choose the correct preposition: She arrived ___ the station.',
            'Which word is an adjective?',
            'What is the opposite of "expensive"?',
            'Choose the correct tense: By next year, I ___ graduated.',
            'Which sentence uses present perfect?',
            'What does "reluctant" mean?',
            'Fill in: If I ___ rich, I would travel the world.',
            'Choose the correct article: ___ apple a day keeps the doctor away.',
            'What is the plural of "child"?',
            'Which word means "to start"?',
            'Choose the correct form: She enjoys ___.',
            'What does "inevitable" mean?',
            'Fill in the blank: He is ___ tallest boy in class.',
            'Which word is an adverb?',
            'What does "ambiguous" mean?',
            'Choose the correct answer: Neither Tom ___ Jerry was there.',
            'What is a conditional sentence?',
            'Fill in: She asked me ___ I had been.',
        ];

        $cauHoiMau = $loai === 'tienganh' ? $cauHoiMauNN : $cauHoiMauCNTT;

        for ($i = 1; $i <= $soCau; $i++) {
            $noiDung = $cauHoiMau[($i - 1) % count($cauHoiMau)] ?? "Câu hỏi trắc nghiệm số $i";
            $dapAnDung = collect(['A', 'B', 'C', 'D'])->random();

            CauHoi::create([
                'de_thi_id'  => $deThi->id,
                'noi_dung'   => "[Câu $i] $noiDung",
                'loai_cau'   => 'tracnghiem',
                'dap_an_a'   => 'Đáp án A',
                'dap_an_b'   => 'Đáp án B',
                'dap_an_c'   => 'Đáp án C',
                'dap_an_d'   => 'Đáp án D',
                'dap_an_dung' => $dapAnDung,
                'diem'       => round(100 / ($soCau + 5), 2), // chia đều điểm, chừa cho tự luận
                'thu_tu'     => $i,
            ]);
        }
    }

    /**
     * Tạo câu hỏi tự luận cho đề thi
     */
    private function taoCauHoiTuLuan(DeThi $deThi, string $loai, int $soCau): void
    {
        $thuTuHienTai = CauHoi::where('de_thi_id', $deThi->id)->max('thu_tu') ?? 0;

        $cauTuLuanCNTT = [
            'Trình bày các bước tạo bảng trong Microsoft Word.',
            'Giải thích sự khác biệt giữa phần cứng và phần mềm.',
            'Mô tả quy trình gửi email bằng Gmail.',
            'Trình bày các biện pháp bảo mật thông tin cá nhân khi sử dụng Internet.',
            'So sánh ưu nhược điểm của ổ cứng SSD và HDD.',
        ];

        $cauTuLuanNN = [
            'Write a paragraph about your favorite hobby (100-150 words).',
            'Describe your daily routine using present simple tense.',
            'Write a letter to your friend about your recent vacation.',
            'Compare living in the city and living in the countryside.',
            'Write about the advantages and disadvantages of social media.',
        ];

        $cauMau = $loai === 'tienganh' ? $cauTuLuanNN : $cauTuLuanCNTT;
        $tongTracNghiem = CauHoi::where('de_thi_id', $deThi->id)->where('loai_cau', 'tracnghiem')->count();
        $diemConLai = 100 - CauHoi::where('de_thi_id', $deThi->id)->sum('diem');

        for ($i = 1; $i <= $soCau; $i++) {
            CauHoi::create([
                'de_thi_id' => $deThi->id,
                'noi_dung'  => '[Tự luận ' . $i . '] ' . ($cauMau[$i - 1] ?? "Câu tự luận số $i"),
                'loai_cau'  => 'tuluan',
                'diem'      => round($diemConLai / $soCau, 2),
                'thu_tu'    => $thuTuHienTai + $i,
            ]);
        }
    }

    /**
     * Tạo lịch sử xử lý hồ sơ
     */
    private function taoLichSu(DangKy $dk, ?int $userId, string $vaiTro, string $hanhDong, ?string $ttTruoc, string $ttSau, ?string $noiDung = null): void
    {
        $noiDungDefault = match ($hanhDong) {
            'tao_ho_so' => 'Sinh viên khởi tạo hồ sơ đăng ký thi.',
            'duyet' => 'Admin duyệt hồ sơ đăng ký thi.',
            'yeu_cau_bo_sung' => 'Admin yêu cầu bổ sung hồ sơ.',
            'bo_sung_ho_so' => 'Sinh viên đã bổ sung hồ sơ.',
            'tu_choi' => 'Admin từ chối hồ sơ đăng ký.',
            'huy' => 'Hủy hồ sơ đăng ký.',
            default => 'Cập nhật trạng thái hồ sơ.',
        };

        LichSuXuLyHoSo::create([
            'dang_ky_id'    => $dk->id,
            'user_id'       => $userId,
            'vai_tro'       => $vaiTro,
            'hanh_dong'     => $hanhDong,
            'trang_thai_truoc' => $ttTruoc,
            'trang_thai_sau'   => $ttSau,
            'noi_dung'      => $noiDung ?? $noiDungDefault,
        ]);
    }
}
