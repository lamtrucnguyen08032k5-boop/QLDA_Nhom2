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
use App\Models\PhucKhao;
use App\Models\SvWhitelist;
use App\Models\User;
use App\Notifications\ThongBaoHeThong;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $defaultPassword = Hash::make('123456');

        // =========================================================================
        // 1. TÀI KHOẢN ADMIN
        // =========================================================================
        $admin = User::updateOrCreate(
            ['email' => 'admin@hvnh.edu.vn'],
            [
                'role' => 'admin',
                'ma_so' => 'ADMIN001',
                'name' => 'Quản trị hệ thống - Phòng Khảo thí',
                'password' => $defaultPassword,
                'email_verified_at' => now(),
                'active' => true,
            ]
        );

        // =========================================================================
        // 2. CÁC KHOA VÀ TÀI KHOẢN KHOA
        // =========================================================================
        $khoasData = [
            ['ma' => 'CNTT', 'ten' => 'Khoa Công nghệ thông tin', 'email' => 'khoa.cntt@hvnh.edu.vn'],
            ['ma' => 'NN', 'ten' => 'Khoa Ngoại ngữ', 'email' => 'khoa.nn@hvnh.edu.vn'],
            ['ma' => 'NH', 'ten' => 'Khoa Ngân hàng', 'email' => 'khoa.nh@hvnh.edu.vn'],
            ['ma' => 'TC', 'ten' => 'Khoa Tài chính', 'email' => 'khoa.tc@hvnh.edu.vn'],
            ['ma' => 'KDQT', 'ten' => 'Khoa Kinh doanh quốc tế', 'email' => 'khoa.kdqt@hvnh.edu.vn'],
            ['ma' => 'KTKT', 'ten' => 'Khoa Kế toán - Kiểm toán', 'email' => 'khoa.ktkt@hvnh.edu.vn'],
        ];

        $khoas = [];
        $khoaUsers = [];
        foreach ($khoasData as $kd) {
            $k = Khoa::updateOrCreate(
                ['ma_khoa' => $kd['ma']],
                ['ten_khoa' => $kd['ten'], 'email' => $kd['email'], 'active' => true]
            );
            $khoas[$kd['ma']] = $k;

            $ku = User::updateOrCreate(
                ['email' => $kd['email']],
                [
                    'role' => 'khoa',
                    'ma_so' => $kd['ma'],
                    'name' => 'Tài khoản ' . $kd['ten'],
                    'password' => $defaultPassword,
                    'khoa_id' => $k->id,
                    'email_verified_at' => now(),
                    'active' => true,
                ]
            );
            $khoaUsers[$kd['ma']] = $ku;
        }

        // =========================================================================
        // 3. GIẢNG VIÊN THUỘC CÁC KHOA
        // =========================================================================
        $gvsData = [
            ['email' => 'giangvien.cntt@hvnh.edu.vn', 'ma_so' => 'GV001', 'name' => 'Nguyễn Văn A', 'khoa' => 'CNTT'],
            ['email' => 'gv.nguyenvanan@hvnh.edu.vn', 'ma_so' => 'GV_CNTT01', 'name' => 'ThS. Nguyễn Văn An', 'khoa' => 'CNTT'],
            ['email' => 'gv.tranthibinh@hvnh.edu.vn', 'ma_so' => 'GV_NN01', 'name' => 'TS. Trần Thị Bình', 'khoa' => 'NN'],
            ['email' => 'gv.lequangcuong@hvnh.edu.vn', 'ma_so' => 'GV_NH01', 'name' => 'PGS.TS. Lê Quang Cường', 'khoa' => 'NH'],
            ['email' => 'gv.phamthidung@hvnh.edu.vn', 'ma_so' => 'GV_TC01', 'name' => 'ThS. Phạm Thị Dung', 'khoa' => 'TC'],
            ['email' => 'gv.hoangminhe@hvnh.edu.vn', 'ma_so' => 'GV_KDQT01', 'name' => 'TS. Hoàng Minh Em', 'khoa' => 'KDQT'],
        ];

        $giangViens = [];
        foreach ($gvsData as $gv) {
            $uGv = User::updateOrCreate(
                ['email' => $gv['email']],
                [
                    'role' => 'giangvien',
                    'ma_so' => $gv['ma_so'],
                    'name' => $gv['name'],
                    'password' => $defaultPassword,
                    'khoa_id' => $khoas[$gv['khoa']]->id,
                    'email_verified_at' => now(),
                    'active' => true,
                ]
            );
            $giangViens[$gv['ma_so']] = $uGv;
        }

        // =========================================================================
        // 4. KHO EMAIL SINH VIÊN (WHITELIST) & TÀI KHOẢN SINH VIÊN
        // =========================================================================
        $ho = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Huỳnh', 'Phan', 'Vũ', 'Võ', 'Đặng', 'Bùi', 'Đỗ', 'Hồ', 'Ngô', 'Dương'];
        $dem = ['Văn', 'Thị', 'Minh', 'Hải', 'Quang', 'Đức', 'Thu', 'Anh', 'Ngọc', 'Thanh', 'Hồng', 'Hữu', 'Tuấn'];
        $ten = ['Anh', 'Bình', 'Châu', 'Dũng', 'Em', 'Giang', 'Hà', 'Khánh', 'Linh', 'Minh', 'Nam', 'Phong', 'Quân', 'Sơn', 'Trang', 'Vy', 'Yến'];

        $khoaKeys = array_keys($khoas);
        $khoaHocs = [
            'K22' => ['prefix' => '22A', 'count' => 10],
            'K23' => ['prefix' => '23A', 'count' => 16],
            'K24' => ['prefix' => '24A', 'count' => 24],
            'K25' => ['prefix' => '25A', 'count' => 20],
            'K26' => ['prefix' => '26A', 'count' => 12],
        ];

        $allSinhViens = [];
        $stt = 1;

        foreach ($khoaHocs as $khoaHoc => $info) {
            for ($i = 1; $i <= $info['count']; $i++) {
                $maSv = $info['prefix'] . str_pad($stt, 6, '0', STR_PAD_LEFT);
                $h = $ho[array_rand($ho)];
                $d = $dem[array_rand($dem)];
                $t = $ten[array_rand($ten)];
                $fullName = "$h $d $t";
                $email = strtolower($maSv) . '@hvnh.edu.vn';
                $khoaCode = $khoaKeys[array_rand($khoaKeys)];
                $khoaObj = $khoas[$khoaCode];
                $lop = $khoaHoc . $khoaCode . rand(1, 4);

                SvWhitelist::updateOrCreate(
                    ['ma_sv' => $maSv],
                    [
                        'ho_ten' => $fullName,
                        'email' => $email,
                        'lop' => $lop,
                        'khoa_hoc' => $khoaHoc,
                        'khoa_id' => $khoaObj->id,
                        'da_dang_ky' => true,
                    ]
                );

                $user = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'role' => 'sinhvien',
                        'ma_so' => $maSv,
                        'name' => $fullName,
                        'lop' => $lop,
                        'khoa_hoc' => $khoaHoc,
                        'khoa_id' => $khoaObj->id,
                        'password' => $defaultPassword,
                        'email_verified_at' => now(),
                        'active' => true,
                    ]
                );

                $allSinhViens[] = $user;
                $stt++;
            }
        }

        // =========================================================================
        // 5. KHO ĐỀ THI & BỘ CÂU HỎI (TRẮC NGHIỆM + TỰ LUẬN)
        // =========================================================================
        $deThiCNTT = DeThi::updateOrCreate(
            ['ma_de' => 'DT-CNTT-01'],
            [
                'ten_de' => 'Đề thi Ứng dụng CNTT cơ bản chuẩn đầu ra HVNH',
                'khoa_id' => $khoas['CNTT']->id,
                'loai_chung_chi' => 'cntt',
                'active' => true,
            ]
        );

        $cauHoiCNTT = [
            [
                'noi_dung' => 'Trong Microsoft Word, tổ hợp phím nào dùng để căn đều hai bên đoạn văn bản?',
                'loai_cau' => 'tracnghiem',
                'dap_an_a' => 'Ctrl + L', 'dap_an_b' => 'Ctrl + R', 'dap_an_c' => 'Ctrl + J', 'dap_an_d' => 'Ctrl + E',
                'dap_an_dung' => 'C', 'diem' => 15, 'thu_tu' => 1,
            ],
            [
                'noi_dung' => 'Trong Microsoft Excel, hàm nào dùng để đếm các ô có điều kiện?',
                'loai_cau' => 'tracnghiem',
                'dap_an_a' => 'COUNT', 'dap_an_b' => 'COUNTA', 'dap_an_c' => 'COUNTIF', 'dap_an_d' => 'SUMIF',
                'dap_an_dung' => 'C', 'diem' => 15, 'thu_tu' => 2,
            ],
            [
                'noi_dung' => 'Trong hệ điều hành Windows, phím tắt nào dùng để chụp một vùng màn hình nhanh chóng?',
                'loai_cau' => 'tracnghiem',
                'dap_an_a' => 'Windows + Shift + S', 'dap_an_b' => 'Ctrl + Alt + Del', 'dap_an_c' => 'Alt + Tab', 'dap_an_d' => 'Ctrl + Shift + Esc',
                'dap_an_dung' => 'A', 'diem' => 20, 'thu_tu' => 3,
            ],
            [
                'noi_dung' => 'Trình bày các giải pháp đảm bảo an toàn thông tin khi thực hiện giao dịch tài chính số tại ngân hàng và phân tích biện pháp phòng ngừa rủi ro mã độc Ransomware.',
                'loai_cau' => 'tuluan',
                'dap_an_a' => null, 'dap_an_b' => null, 'dap_an_c' => null, 'dap_an_d' => null,
                'dap_an_dung' => null, 'diem' => 50, 'thu_tu' => 4,
            ],
        ];

        foreach ($cauHoiCNTT as $ch) {
            CauHoi::updateOrCreate(
                ['de_thi_id' => $deThiCNTT->id, 'thu_tu' => $ch['thu_tu']],
                $ch
            );
        }

        $deThiNN = DeThi::updateOrCreate(
            ['ma_de' => 'DT-TA-B1'],
            [
                'ten_de' => 'Đề thi Đánh giá năng lực Tiếng Anh chuẩn đầu ra B1 HVNH',
                'khoa_id' => $khoas['NN']->id,
                'loai_chung_chi' => 'tieng_anh',
                'active' => true,
            ]
        );

        $cauHoiNN = [
            [
                'noi_dung' => 'Choose the best word to complete: "The central bank decided to ______ the interest rates by 0.5%."',
                'loai_cau' => 'tracnghiem',
                'dap_an_a' => 'reduce', 'dap_an_b' => 'fall', 'dap_an_c' => 'drop down', 'dap_an_d' => 'low',
                'dap_an_dung' => 'A', 'diem' => 25, 'thu_tu' => 1,
            ],
            [
                'noi_dung' => 'Choose the correct sentence:',
                'loai_cau' => 'tracnghiem',
                'dap_an_a' => 'If customers deposit money today, they received bonuses.',
                'dap_an_b' => 'If customers deposit money today, they will receive bonuses.',
                'dap_an_c' => 'If customers deposited money today, they will receive bonuses.',
                'dap_an_d' => 'If customers deposit money today, they would have received bonuses.',
                'dap_an_dung' => 'B', 'diem' => 25, 'thu_tu' => 2,
            ],
            [
                'noi_dung' => 'Writing task: Write a formal email (120-150 words) to an international client explaining why a loan application has been approved and listing the next required procedures.',
                'loai_cau' => 'tuluan',
                'dap_an_a' => null, 'dap_an_b' => null, 'dap_an_c' => null, 'dap_an_d' => null,
                'dap_an_dung' => null, 'diem' => 50, 'thu_tu' => 3,
            ],
        ];

        foreach ($cauHoiNN as $ch) {
            CauHoi::updateOrCreate(
                ['de_thi_id' => $deThiNN->id, 'thu_tu' => $ch['thu_tu']],
                $ch
            );
        }

        // =========================================================================
        // 6. KỲ THI & LỊCH THI THEO CÁC THÁNG
        // =========================================================================
        $now = Carbon::now();
        $kyThis = [
            KyThi::updateOrCreate(
                ['ten_ky_thi' => 'Kỳ thi Chuẩn đầu ra Đợt 1 - Học kỳ 2'],
                ['nam_hoc' => '2025-2026', 'hoc_ky' => 'HK2', 'mo_ta' => 'Kỳ thi chuẩn đầu ra Tin học & Ngoại ngữ chính khóa', 'trang_thai' => 'da_ket_thuc']
            ),
            KyThi::updateOrCreate(
                ['ten_ky_thi' => 'Kỳ thi Chuẩn đầu ra Đợt 2 - Học kỳ Hè'],
                ['nam_hoc' => '2025-2026', 'hoc_ky' => 'He', 'mo_ta' => 'Kỳ thi đợt hè năm 2026 cho sinh viên các khóa', 'trang_thai' => 'da_ket_thuc']
            ),
            KyThi::updateOrCreate(
                ['ten_ky_thi' => 'Kỳ thi Chuẩn đầu ra Đợt 3 - Học kỳ 1'],
                ['nam_hoc' => '2026-2027', 'hoc_ky' => 'HK1', 'mo_ta' => 'Kỳ thi chính khóa đầu năm học 2026-2027', 'trang_thai' => 'dang_dien_ra']
            ),
        ];

        $monthsConfig = [
            ['month_offset' => 5, 'ca_count' => 2, 'le_phi' => 250000, 'type' => 'cntt', 'de' => $deThiCNTT, 'khoa' => 'CNTT'],
            ['month_offset' => 4, 'ca_count' => 2, 'le_phi' => 300000, 'type' => 'tieng_anh', 'de' => $deThiNN, 'khoa' => 'NN'],
            ['month_offset' => 3, 'ca_count' => 3, 'le_phi' => 250000, 'type' => 'cntt', 'de' => $deThiCNTT, 'khoa' => 'CNTT'],
            ['month_offset' => 2, 'ca_count' => 3, 'le_phi' => 300000, 'type' => 'tieng_anh', 'de' => $deThiNN, 'khoa' => 'NN'],
            ['month_offset' => 1, 'ca_count' => 4, 'le_phi' => 250000, 'type' => 'cntt', 'de' => $deThiCNTT, 'khoa' => 'CNTT'],
            ['month_offset' => 0, 'ca_count' => 3, 'le_phi' => 250000, 'type' => 'cntt', 'de' => $deThiCNTT, 'khoa' => 'CNTT'],
        ];

        $lichThis = [];
        $caIndex = 1;

        foreach ($monthsConfig as $cfg) {
            $monthDate = (clone $now)->subMonths($cfg['month_offset']);
            $kyThiItem = $cfg['month_offset'] >= 3 ? $kyThis[0] : ($cfg['month_offset'] >= 1 ? $kyThis[1] : $kyThis[2]);

            for ($c = 1; $c <= $cfg['ca_count']; $c++) {
                $ngayThi = (clone $monthDate)->startOfMonth()->addDays($c * 6 + rand(1, 4));
                $maCa = 'CA' . str_pad($caIndex, 2, '0', STR_PAD_LEFT) . '-T' . $monthDate->format('m');
                $phong = 'P.' . (200 + $c) . ' - Nhà D' . rand(1, 3);
                $isPast = $ngayThi->isPast();

                $lt = LichThi::updateOrCreate(
                    ['ma_ca_thi' => $maCa],
                    [
                        'ky_thi_id' => $kyThiItem->id,
                        'ten_ky_thi' => $kyThiItem->ten_ky_thi . ' - ' . ($cfg['type'] == 'cntt' ? 'CNTT' : 'Tiếng Anh'),
                        'loai_chung_chi' => $cfg['type'],
                        'khoa_id' => $khoas[$cfg['khoa']]->id,
                        'ngay_thi' => $ngayThi->toDateString(),
                        'gio_bat_dau' => ($c % 2 == 1 ? '08:00' : '14:00'),
                        'thoi_gian_thi_phut' => 60,
                        'phong_thi' => $phong,
                        'so_luong_toi_da' => 40,
                        'han_dang_ky' => (clone $ngayThi)->subDays(4),
                        'le_phi' => $cfg['le_phi'],
                        'trang_thai' => $isPast ? 'da_ket_thuc' : 'dang_mo',
                        'de_thi_id' => $cfg['de']->id,
                        'trang_thai_cong_bo' => $isPast ? 'da_cong_bo' : 'chua_cong_bo',
                        'ngay_cong_bo' => $isPast ? (clone $ngayThi)->addDays(2) : null,
                    ]
                );

                $lichThis[] = [
                    'model' => $lt,
                    'is_past' => $isPast,
                    'ngay_thi' => $ngayThi,
                    'de_thi' => $cfg['de'],
                    'le_phi' => $cfg['le_phi'],
                ];
                $caIndex++;
            }
        }

        // =========================================================================
        // 7. ĐĂNG KÝ THI, BÀI THI, CÂU TRẢ LỜI, PHÂN CÔNG CHẤM & PHÚC KHẢO
        // =========================================================================
        $btIndex = 1;
        $gvValues = array_values($giangViens);

        foreach ($lichThis as $ltData) {
            $lt = $ltData['model'];
            $isPast = $ltData['is_past'];
            $ngayThi = $ltData['ngay_thi'];
            $deThi = $ltData['de_thi'];
            $cauHois = $deThi->cauHois()->orderBy('thu_tu')->get();
            $soLuongSv = rand(8, 14);

            $shuffled = collect($allSinhViens)->shuffle()->take($soLuongSv);

            foreach ($shuffled as $sv) {
                $createdAt = (clone $ngayThi)->subDays(rand(5, 12))->setTime(rand(8, 17), rand(0, 59));
                $statuses = ['da_duyet', 'da_duyet', 'da_duyet', 'da_duyet', 'cho_duyet', 'cho_bo_sung'];
                $trangThai = $isPast ? 'da_duyet' : $statuses[array_rand($statuses)];
                $isPaid = ($trangThai === 'da_duyet') ? 'da_thanh_toan' : 'cho_thanh_toan';

                $dk = DangKy::updateOrCreate(
                    [
                        'sinh_vien_id' => $sv->id,
                        'lich_thi_id' => $lt->id,
                    ],
                    [
                        'trang_thai' => $trangThai,
                        'trang_thai_thanh_toan' => $isPaid,
                        'so_tien' => $ltData['le_phi'],
                        'so_cccd' => '00120' . rand(1000000, 9999999),
                        'so_dien_thoai' => '09' . rand(10000000, 99999999),
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]
                );

                if ($isPast && $trangThai === 'da_duyet') {
                    $gv1 = $gvValues[0] ?? null;
                    $gv2 = $gvValues[1] ?? null;

                    $diemType = rand(1, 10);
                    if ($diemType <= 3) {
                        $diemTN = rand(40, 50);
                        $diemTL1 = rand(40, 50);
                        $diemTL2 = $diemTL1 + rand(-2, 2);
                    } elseif ($diemType <= 6) {
                        $diemTN = rand(35, 45);
                        $diemTL1 = rand(30, 35);
                        $diemTL2 = $diemTL1 + rand(-2, 2);
                    } elseif ($diemType <= 8) {
                        $diemTN = rand(25, 35);
                        $diemTL1 = rand(25, 29);
                        $diemTL2 = $diemTL1 + rand(-1, 2);
                    } else {
                        $diemTN = rand(15, 25);
                        $diemTL1 = rand(10, 20);
                        $diemTL2 = $diemTL1 + rand(-2, 2);
                    }

                    $diemTL = round(($diemTL1 + $diemTL2) / 2, 2);
                    $diemTong = $diemTN + $diemTL;

                    $gioBatDau = (clone $ngayThi)->setTime(rand(8, 14), 0);
                    $gioNop = (clone $gioBatDau)->addMinutes(rand(45, 58));

                    $maBt = 'BT' . $ngayThi->format('ymd') . strtoupper(Str::random(6));
                    $bt = BaiThi::updateOrCreate(
                        ['dang_ky_id' => $dk->id],
                        [
                            'de_thi_id' => $deThi->id,
                            'ma_bai_thi' => $maBt,
                            'gio_bat_dau' => $gioBatDau,
                            'gio_nop' => $gioNop,
                            'trang_thai' => 'da_cham',
                            'diem_tu_dong' => $diemTN,
                            'diem_cham_tay' => $diemTL,
                            'diem_tong' => $diemTong,
                            'cham_xong' => true,
                            'giang_vien_1_id' => $gv1?->id,
                            'giang_vien_2_id' => $gv2?->id,
                            'nhan_xet_1' => 'Bài làm đầy đủ ý, trình bày mạch lạc.',
                            'nhan_xet_2' => 'Đồng ý với điểm chấm của GV1.',
                            'ngay_cham' => (clone $ngayThi)->addDay(),
                            'ngay_cham_1' => (clone $ngayThi)->addDay(),
                            'ngay_cham_2' => (clone $ngayThi)->addDay(),
                            'ngay_cong_bo' => (clone $ngayThi)->addDays(2),
                            'da_khoa' => true,
                            'created_at' => $ngayThi,
                            'updated_at' => (clone $ngayThi)->addDays(2),
                        ]
                    );

                    // Tạo chi tiết câu trả lời
                    foreach ($cauHois as $chItem) {
                        if ($chItem->loai_cau === 'tracnghiem') {
                            $isCorrect = rand(1, 10) <= 8;
                            CauTraLoi::updateOrCreate(
                                ['bai_thi_id' => $bt->id, 'cau_hoi_id' => $chItem->id],
                                [
                                    'dap_an_chon' => $isCorrect ? $chItem->dap_an_dung : 'D',
                                    'diem_dat' => $isCorrect ? $chItem->diem : 0,
                                    'da_cham' => true,
                                ]
                            );
                        } else {
                            CauTraLoi::updateOrCreate(
                                ['bai_thi_id' => $bt->id, 'cau_hoi_id' => $chItem->id],
                                [
                                    'bai_lam_tu_luan' => 'Thí sinh đã trình bày chi tiết các giải pháp an toàn bảo mật, các bước xử lý và tuân thủ quy trình bảo vệ thông tin ngân hàng.',
                                    'diem_dat' => $diemTL,
                                    'diem_gv1' => $diemTL1,
                                    'diem_gv2' => $diemTL2,
                                    'da_cham' => true,
                                ]
                            );
                        }
                    }

                    // Cấp chứng nhận nếu đạt
                    if ($diemTong >= 50 && rand(1, 10) <= 8) {
                        $soCn = 'CN-' . strtoupper($lt->loai_chung_chi) . '-' . $ngayThi->format('y') . '-' . str_pad($btIndex, 4, '0', STR_PAD_LEFT) . '-' . rand(10, 99);
                        ChungNhan::updateOrCreate(
                            ['bai_thi_id' => $bt->id],
                            [
                                'so_chung_nhan' => $soCn,
                                'sinh_vien_id' => $sv->id,
                                'dia_chi_nhan' => 'Phòng Quản lý Đào tạo - Học viện Ngân hàng',
                                'so_dien_thoai' => $dk->so_dien_thoai,
                                'ngay_cap' => (clone $ngayThi)->addDays(5),
                                'trang_thai' => 'da_cap',
                            ]
                        );
                    }

                    // Đơn phúc khảo
                    if ($diemTong < 70 && rand(1, 10) <= 4) {
                        $diemSau = $diemTong + rand(2, 5);
                        $pkStatuses = [
                            PhucKhao::TRANG_THAI_CHO_TIEP_NHAN,
                            PhucKhao::TRANG_THAI_CHO_PHAN_CONG,
                            PhucKhao::TRANG_THAI_DANG_XU_LY,
                            PhucKhao::TRANG_THAI_HOAN_TAT,
                        ];
                        $pkStatus = $pkStatuses[array_rand($pkStatuses)];

                        PhucKhao::updateOrCreate(
                            ['bai_thi_id' => $bt->id],
                            [
                                'sinh_vien_id' => $sv->id,
                                'giang_vien_id' => $gv1?->id,
                                'admin_tiep_nhan_id' => $admin->id,
                                'admin_duyet_id' => $pkStatus === PhucKhao::TRANG_THAI_HOAN_TAT ? $admin->id : null,
                                'ly_do' => 'Em xin chấm lại phần câu hỏi tự luận do có bổ sung đầy đủ dẫn chứng quy trình.',
                                'trang_thai' => $pkStatus,
                                'diem_truoc' => $diemTong,
                                'diem_sau' => $pkStatus === PhucKhao::TRANG_THAI_HOAN_TAT ? $diemSau : null,
                                'phan_hoi' => $pkStatus === PhucKhao::TRANG_THAI_HOAN_TAT ? 'Hội đồng đã chấm lại và cập nhật điểm theo barem.' : null,
                                'ngay_tiep_nhan' => (clone $ngayThi)->addDays(4),
                                'ngay_xu_ly' => (clone $ngayThi)->addDays(7),
                                'ngay_duyet' => $pkStatus === PhucKhao::TRANG_THAI_HOAN_TAT ? (clone $ngayThi)->addDays(8) : null,
                            ]
                        );
                    }

                    $btIndex++;
                }
            }
        }

        // =========================================================================
        // 8. TẠO THÔNG BÁO MẪU CHO CÁC ROLE
        // =========================================================================
        $admin->notify(new ThongBaoHeThong(
            'Báo cáo khảo thí Tháng ' . $now->format('m/Y'),
            'Tổng kết kỳ thi chuẩn đầu ra Tin học & Tiếng Anh đã hoàn tất chấm 100% bài thi.',
            route('admin.thongke.index'),
            'thong_tin',
            'bi-graph-up'
        ));

        foreach ($khoaUsers as $ku) {
            $ku->notify(new ThongBaoHeThong(
                'Phân công chấm thi kỳ mới',
                'Kỳ thi chuẩn đầu ra đã mở danh sách bài thi, vui lòng phân công giảng viên chấm bài.',
                route('khoa.phan-cong-cham.index'),
                'nhac_nho',
                'bi-pencil-square'
            ));
        }

        foreach ($giangViens as $gu) {
            $gu->notify(new ThongBaoHeThong(
                'Bạn có bài thi tự luận cần chấm',
                'Khoa đã phân công danh sách bài thi tự luận cho bạn. Hạn hoàn thành chấm trong 3 ngày.',
                route('giangvien.cham-thi.index'),
                'nhac_nho',
                'bi-check2-circle'
            ));
        }
    }
}
