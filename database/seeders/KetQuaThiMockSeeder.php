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
use App\Models\LichThi;
use App\Models\User;
use App\Notifications\ThongBaoHeThong;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KetQuaThiMockSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Lấy hoặc tạo Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@hvnh.edu.vn'],
            [
                'role' => 'admin',
                'ma_so' => 'ADMIN001',
                'name' => 'Quản trị hệ thống - Phòng Khảo thí',
                'password' => Hash::make('Admin@123'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Khoa CNTT
        $khoaCNTT = Khoa::firstOrCreate(
            ['ma_khoa' => 'CNTT'],
            ['ten_khoa' => 'Khoa Công nghệ thông tin', 'email' => 'khoa.cntt@hvnh.edu.vn', 'active' => true]
        );

        // 3. Tạo danh sách Sinh viên mẫu
        $sinhViensData = [
            [
                'email' => 'sv.nguyenvanan@hvnh.edu.vn',
                'ma_so' => '22A4010001',
                'name' => 'Nguyễn Văn An',
                'lop' => 'K22CLC1',
                'khoa_hoc' => 'K22',
                'cccd' => '001202001111',
            ],
            [
                'email' => 'sv.tranthibinh@hvnh.edu.vn',
                'ma_so' => '22A4010002',
                'name' => 'Trần Thị Bình',
                'lop' => 'K22CLC1',
                'khoa_hoc' => 'K22',
                'cccd' => '001202002222',
            ],
            [
                'email' => 'sv.lequangcuong@hvnh.edu.vn',
                'ma_so' => '22A4010003',
                'name' => 'Lê Quang Cường',
                'lop' => 'K22CLC2',
                'khoa_hoc' => 'K22',
                'cccd' => '001202003333',
            ],
            [
                'email' => 'sv.phamthidung@hvnh.edu.vn',
                'ma_so' => '22A4010004',
                'name' => 'Phạm Thị Dung',
                'lop' => 'K22CLC2',
                'khoa_hoc' => 'K22',
                'cccd' => '001202004444',
            ],
            [
                'email' => 'sv.hoangminhe@hvnh.edu.vn',
                'ma_so' => '22A4010005',
                'name' => 'Hoàng Minh Em',
                'lop' => 'K22CLC3',
                'khoa_hoc' => 'K22',
                'cccd' => '001202005555',
            ],
        ];

        $sinhViens = [];
        foreach ($sinhViensData as $data) {
            $sv = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'role' => 'sinhvien',
                    'ma_so' => $data['ma_so'],
                    'name' => $data['name'],
                    'lop' => $data['lop'],
                    'khoa_hoc' => $data['khoa_hoc'],
                    'password' => Hash::make('SinhVien@123'),
                    'email_verified_at' => now(),
                    'active' => true,
                ]
            );
            $sinhViens[$data['ma_so']] = [
                'user' => $sv,
                'cccd' => $data['cccd'],
            ];
        }

        // 4. Kỳ thi & Đề thi mẫu
        $kyThi = KyThi::firstOrCreate(
            ['ten_ky_thi' => 'Kỳ thi Chuẩn đầu ra CNTT Khóa 22 - Học kỳ 1'],
            [
                'nam_hoc' => '2025-2026',
                'hoc_ky' => 'HK1',
                'mo_ta' => 'Kỳ thi đánh giá kỹ năng công nghệ thông tin cơ bản cho sinh viên K22',
                'trang_thai' => 'da_ket_thuc',
            ]
        );

        $deThi = DeThi::firstOrCreate(
            ['ma_de' => 'DT-CNTT-K22-01'],
            [
                'ten_de' => 'Đề thi Ứng dụng CNTT cơ bản (Word, Excel, An toàn thông tin)',
                'khoa_id' => $khoaCNTT->id,
                'loai_chung_chi' => 'cntt',
                'active' => true,
            ]
        );

        // Tạo câu hỏi mẫu nếu chưa có
        if ($deThi->cauHois()->count() === 0) {
            CauHoi::create([
                'de_thi_id' => $deThi->id,
                'noi_dung' => 'Trong Microsoft Word, tổ hợp phím nào dùng để căn đều 2 bên đoạn văn bản?',
                'loai_cau' => 'tracnghiem',
                'dap_an_a' => 'Ctrl + L',
                'dap_an_b' => 'Ctrl + R',
                'dap_an_c' => 'Ctrl + J',
                'dap_an_d' => 'Ctrl + E',
                'dap_an_dung' => 'C',
                'diem' => 25,
                'thu_tu' => 1,
            ]);

            CauHoi::create([
                'de_thi_id' => $deThi->id,
                'noi_dung' => 'Trong Microsoft Excel, hàm nào dùng để đếm các ô thỏa mãn một điều kiện cho trước?',
                'loai_cau' => 'tracnghiem',
                'dap_an_a' => 'COUNT',
                'dap_an_b' => 'COUNTA',
                'dap_an_c' => 'COUNTIF',
                'dap_an_d' => 'COUNTIFS',
                'dap_an_dung' => 'C',
                'diem' => 25,
                'thu_tu' => 2,
            ]);

            CauHoi::create([
                'de_thi_id' => $deThi->id,
                'noi_dung' => 'Trình bày các biện pháp phòng chống mã độc và bảo vệ dữ liệu cá nhân khi sử dụng Internet trong môi trường ngân hàng.',
                'loai_cau' => 'tuluan',
                'diem' => 50,
                'thu_tu' => 3,
            ]);
        }

        // =========================================================================
        // PHÒNG THI 1: P.201 - ĐÃ CHẤM XONG 100% BÀI THI, CHƯA CÔNG BỐ
        // =========================================================================
        $phong1 = LichThi::updateOrCreate(
            ['ma_ca_thi' => 'CA01-P201'],
            [
                'ky_thi_id' => $kyThi->id,
                'ten_ky_thi' => $kyThi->ten_ky_thi,
                'loai_chung_chi' => 'cntt',
                'khoa_id' => $khoaCNTT->id,
                'ngay_thi' => now()->subDays(2)->toDateString(),
                'gio_bat_dau' => '08:00',
                'thoi_gian_thi_phut' => 60,
                'phong_thi' => 'P.201 - Nhà D1',
                'so_luong_toi_da' => 40,
                'han_dang_ky' => now()->subDays(5),
                'le_phi' => 200000,
                'trang_thai' => 'da_ket_thuc',
                'de_thi_id' => $deThi->id,
                'trang_thai_cong_bo' => 'chua_cong_bo',
                'ngay_cong_bo' => null,
                'nguoi_cong_bo_id' => null,
            ]
        );

        // 3 sinh viên An, Bình, Cường trong phòng 1: cả 3 đều đã chấm xong
        $p1Candidates = [
            ['sv' => $sinhViens['22A4010001'], 'diem_tn' => 50, 'diem_tl' => 40, 'diem_tong' => 90],
            ['sv' => $sinhViens['22A4010002'], 'diem_tn' => 50, 'diem_tl' => 25, 'diem_tong' => 75],
            ['sv' => $sinhViens['22A4010003'], 'diem_tn' => 25, 'diem_tl' => 35, 'diem_tong' => 60],
        ];

        foreach ($p1Candidates as $idx => $item) {
            $dk = DangKy::updateOrCreate(
                [
                    'sinh_vien_id' => $item['sv']['user']->id,
                    'lich_thi_id' => $phong1->id,
                ],
                [
                    'trang_thai' => 'da_duyet',
                    'so_cccd' => $item['sv']['cccd'],
                    'trang_thai_thanh_toan' => 'da_thanh_toan',
                    'so_dien_thoai' => '098800100' . ($idx + 1),
                ]
            );

            $bt = BaiThi::updateOrCreate(
                ['dang_ky_id' => $dk->id],
                [
                    'de_thi_id' => $deThi->id,
                    'ma_bai_thi' => 'BT260901' . str_pad($idx + 1, 3, '0', STR_PAD_LEFT),
                    'gio_bat_dau' => now()->subDays(2)->setTime(8, 5),
                    'gio_nop' => now()->subDays(2)->setTime(8, 55),
                    'trang_thai' => 'da_cham',
                    'diem_tu_dong' => $item['diem_tn'],
                    'diem_cham_tay' => $item['diem_tl'],
                    'diem_tong' => $item['diem_tong'],
                    'cham_xong' => true,
                    'ngay_cham' => now()->subDay(),
                    'ngay_cong_bo' => null,
                    'nguoi_cong_bo_id' => null,
                ]
            );
        }

        // Tạo thông báo cho Admin về phòng thi 1 đã hoàn thành chấm
        $admin->notify(new ThongBaoHeThong(
            'Phòng thi P.201 - Nhà D1 đã chấm xong 100%!',
            'Toàn bộ 3 bài làm trong phòng thi P.201 - Nhà D1 (Ca CA01-P201) đã chấm xong. Bạn có thể thực hiện Trả kết quả thi cho sinh viên.',
            route('admin.ketqua.show', $phong1),
            'thanh_cong',
            'bi-check-circle-fill'
        ));

        // =========================================================================
        // PHÒNG THI 2: P.202 - CÓ BÀI ĐANG CHẤM, CHƯA CHẤM (CHƯA ĐỦ ĐIỀU KIỆN TRẢ KQ)
        // =========================================================================
        $phong2 = LichThi::updateOrCreate(
            ['ma_ca_thi' => 'CA02-P202'],
            [
                'ky_thi_id' => $kyThi->id,
                'ten_ky_thi' => $kyThi->ten_ky_thi,
                'loai_chung_chi' => 'cntt',
                'khoa_id' => $khoaCNTT->id,
                'ngay_thi' => now()->subDays(2)->toDateString(),
                'gio_bat_dau' => '09:30',
                'thoi_gian_thi_phut' => 60,
                'phong_thi' => 'P.202 - Nhà D1',
                'so_luong_toi_da' => 40,
                'han_dang_ky' => now()->subDays(5),
                'le_phi' => 200000,
                'trang_thai' => 'da_ket_thuc',
                'de_thi_id' => $deThi->id,
                'trang_thai_cong_bo' => 'chua_cong_bo',
            ]
        );

        $p2Candidates = [
            // Bài 1: Đã chấm xong
            ['sv' => $sinhViens['22A4010002'], 'status' => 'da_cham', 'cham_xong' => true, 'tn' => 50, 'tl' => 30, 'tong' => 80],
            // Bài 2: Đang chấm phần tự luận
            ['sv' => $sinhViens['22A4010004'], 'status' => 'dang_cham', 'cham_xong' => false, 'tn' => 50, 'tl' => 0, 'tong' => null],
            // Bài 3: Chưa chấm
            ['sv' => $sinhViens['22A4010005'], 'status' => 'dang_thi', 'cham_xong' => false, 'tn' => 25, 'tl' => 0, 'tong' => null],
        ];

        foreach ($p2Candidates as $idx => $item) {
            $dk = DangKy::updateOrCreate(
                [
                    'sinh_vien_id' => $item['sv']['user']->id,
                    'lich_thi_id' => $phong2->id,
                ],
                [
                    'trang_thai' => 'da_duyet',
                    'so_cccd' => $item['sv']['cccd'],
                    'trang_thai_thanh_toan' => 'da_thanh_toan',
                    'so_dien_thoai' => '098800200' . ($idx + 1),
                ]
            );

            BaiThi::updateOrCreate(
                ['dang_ky_id' => $dk->id],
                [
                    'de_thi_id' => $deThi->id,
                    'ma_bai_thi' => 'BT260902' . str_pad($idx + 1, 3, '0', STR_PAD_LEFT),
                    'gio_bat_dau' => now()->subDays(2)->setTime(9, 35),
                    'gio_nop' => now()->subDays(2)->setTime(10, 30),
                    'trang_thai' => $item['status'],
                    'diem_tu_dong' => $item['tn'],
                    'diem_cham_tay' => $item['tl'],
                    'diem_tong' => $item['tong'],
                    'cham_xong' => $item['cham_xong'],
                    'ngay_cham' => $item['cham_xong'] ? now()->subDay() : null,
                ]
            );
        }

        // =========================================================================
        // PHÒNG THI 3: P.305 - ĐÃ CÔNG BỐ KẾT QUẢ CHO SINH VIÊN
        // =========================================================================
        $phong3 = LichThi::updateOrCreate(
            ['ma_ca_thi' => 'CA03-P305'],
            [
                'ky_thi_id' => $kyThi->id,
                'ten_ky_thi' => 'Kỳ thi Chuẩn đầu ra CNTT Đợt tháng 9',
                'loai_chung_chi' => 'cntt',
                'khoa_id' => $khoaCNTT->id,
                'ngay_thi' => now()->subDays(3)->toDateString(),
                'gio_bat_dau' => '13:30',
                'thoi_gian_thi_phut' => 60,
                'phong_thi' => 'P.305 - Giảng đường B',
                'so_luong_toi_da' => 30,
                'han_dang_ky' => now()->subDays(7),
                'le_phi' => 200000,
                'trang_thai' => 'da_ket_thuc',
                'de_thi_id' => $deThi->id,
                'trang_thai_cong_bo' => 'da_cong_bo',
                'ngay_cong_bo' => now()->subDays(1),
                'nguoi_cong_bo_id' => $admin->id,
            ]
        );

        $p3Candidates = [
            // Bài 1: Đạt (85 điểm) -> có chứng chỉ
            ['sv' => $sinhViens['22A4010001'], 'tn' => 50, 'tl' => 35, 'tong' => 85, 'dat' => true],
            // Bài 2: Không đạt (40 điểm) -> trong hạn phúc khảo
            ['sv' => $sinhViens['22A4010003'], 'tn' => 25, 'tl' => 15, 'tong' => 40, 'dat' => false],
            // Bài 3: Đạt (70 điểm)
            ['sv' => $sinhViens['22A4010005'], 'tn' => 50, 'tl' => 20, 'tong' => 70, 'dat' => true],
        ];

        foreach ($p3Candidates as $idx => $item) {
            $dk = DangKy::updateOrCreate(
                [
                    'sinh_vien_id' => $item['sv']['user']->id,
                    'lich_thi_id' => $phong3->id,
                ],
                [
                    'trang_thai' => 'da_duyet',
                    'so_cccd' => $item['sv']['cccd'],
                    'trang_thai_thanh_toan' => 'da_thanh_toan',
                    'so_dien_thoai' => '098800300' . ($idx + 1),
                ]
            );

            $bt = BaiThi::updateOrCreate(
                ['dang_ky_id' => $dk->id],
                [
                    'de_thi_id' => $deThi->id,
                    'ma_bai_thi' => 'BT260903' . str_pad($idx + 1, 3, '0', STR_PAD_LEFT),
                    'gio_bat_dau' => now()->subDays(3)->setTime(13, 35),
                    'gio_nop' => now()->subDays(3)->setTime(14, 30),
                    'trang_thai' => 'da_cong_bo',
                    'diem_tu_dong' => $item['tn'],
                    'diem_cham_tay' => $item['tl'],
                    'diem_tong' => $item['tong'],
                    'cham_xong' => true,
                    'ngay_cham' => now()->subDays(2),
                    'ngay_cong_bo' => now()->subDays(1),
                    'nguoi_cong_bo_id' => $admin->id,
                ]
            );

            // Nếu đạt và là thí sinh 1, tạo chứng chỉ mẫu
            if ($idx === 0) {
                ChungNhan::updateOrCreate(
                    ['bai_thi_id' => $bt->id],
                    [
                        'sinh_vien_id' => $item['sv']['user']->id,
                        'so_chung_nhan' => 'CC-HVNH-2026-00088',
                        'trang_thai' => 'da_cap',
                        'dia_chi_nhan' => '12 Chùa Bộc, Đống Đa, Hà Nội',
                        'so_dien_thoai' => '0988001001',
                        'ngay_cap' => now()->subHours(12),
                    ]
                );
            }

            // Gửi thông báo cho từng sinh viên trong phòng thi 3
            $item['sv']['user']->notify(new ThongBaoHeThong(
                "Đã có kết quả thi: {$phong3->ten_ky_thi}",
                "Kết quả thi phòng {$phong3->phong_thi} (Ca {$phong3->ma_ca_thi}) đã được công bố. Điểm của bạn: {$item['tong']} (" . ($item['dat'] ? 'Đạt' : 'Không đạt') . ').',
                route('sinhvien.ketqua.show', $bt),
                $item['dat'] ? 'thanh_cong' : 'canh_bao',
                'bi-award'
            ));
        }

        $this->command->info('Đã tạo thành công dữ liệu mock:');
        $this->command->info('- Phòng 1 (P.201 - Nhà D1): Đã chấm xong 100%, sẵn sàng trả kết quả.');
        $this->command->info('- Phòng 2 (P.202 - Nhà D1): Có bài đang chấm / chưa chấm.');
        $this->command->info('- Phòng 3 (P.305 - Giảng đường B): Đã công bố kết quả cho sinh viên.');
        $this->command->info('- Đã tạo thông báo chuông cho Admin và các Sinh viên.');
    }
}
