<?php

namespace Database\Seeders;

use App\Models\BaiThi;
use App\Models\ChungNhan;
use App\Models\DangKy;
use App\Models\DeThi;
use App\Models\Khoa;
use App\Models\KyThi;
use App\Models\LichThi;
use App\Models\PhucKhao;
use App\Models\SvWhitelist;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo các Khoa mẫu
        $khoasData = [
            ['ma' => 'CNTT', 'ten' => 'Khoa Công nghệ thông tin', 'email' => 'khoa.cntt@hvnh.edu.vn'],
            ['ma' => 'NN', 'ten' => 'Khoa Ngoại ngữ', 'email' => 'khoa.nn@hvnh.edu.vn'],
            ['ma' => 'NH', 'ten' => 'Khoa Ngân hàng', 'email' => 'khoa.nh@hvnh.edu.vn'],
            ['ma' => 'TC', 'ten' => 'Khoa Tài chính', 'email' => 'khoa.tc@hvnh.edu.vn'],
            ['ma' => 'KDQT', 'ten' => 'Khoa Kinh doanh quốc tế', 'email' => 'khoa.kdqt@hvnh.edu.vn'],
            ['ma' => 'KTKT', 'ten' => 'Khoa Kế toán - Kiểm toán', 'email' => 'khoa.ktkt@hvnh.edu.vn'],
        ];

        $khoas = [];
        foreach ($khoasData as $kd) {
            $k = Khoa::updateOrCreate(
                ['ma_khoa' => $kd['ma']],
                ['ten_khoa' => $kd['ten'], 'email' => $kd['email'], 'active' => true]
            );
            $khoas[$kd['ma']] = $k;

            // Tài khoản Khoa
            User::updateOrCreate(
                ['email' => $kd['email']],
                [
                    'role' => 'khoa',
                    'ma_so' => $kd['ma'],
                    'name' => 'Tài khoản ' . $kd['ten'],
                    'password' => Hash::make('Khoa@123'),
                    'khoa_id' => $k->id,
                    'email_verified_at' => now(),
                    'active' => true,
                ]
            );
        }

        // 2. Tạo Giảng viên mẫu
        $gvs = [
            ['email' => 'gv.nguyenvanan@hvnh.edu.vn', 'ma_so' => 'GV_CNTT01', 'name' => 'ThS. Nguyễn Văn An', 'khoa' => 'CNTT'],
            ['email' => 'gv.tranthibinh@hvnh.edu.vn', 'ma_so' => 'GV_NN01', 'name' => 'TS. Trần Thị Bình', 'khoa' => 'NN'],
            ['email' => 'gv.lequangcuong@hvnh.edu.vn', 'ma_so' => 'GV_NH01', 'name' => 'PGS.TS. Lê Quang Cường', 'khoa' => 'NH'],
            ['email' => 'gv.phamthidung@hvnh.edu.vn', 'ma_so' => 'GV_TC01', 'name' => 'ThS. Phạm Thị Dung', 'khoa' => 'TC'],
        ];

        foreach ($gvs as $gv) {
            User::updateOrCreate(
                ['email' => $gv['email']],
                [
                    'role' => 'giangvien',
                    'ma_so' => $gv['ma_so'],
                    'name' => $gv['name'],
                    'password' => Hash::make('GiangVien@123'),
                    'khoa_id' => $khoas[$gv['khoa']]->id,
                    'email_verified_at' => now(),
                    'active' => true,
                ]
            );
        }

        // 3. Tạo Sinh viên mẫu theo các Khóa: K22, K23, K24, K25, K26
        $ho = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Huỳnh', 'Phan', 'Vũ', 'Võ', 'Đặng', 'Bùi', 'Đỗ', 'Hồ', 'Ngô', 'Dương'];
        $dem = ['Văn', 'Thị', 'Minh', 'Hải', 'Quang', 'Đức', 'Thu', 'Anh', 'Ngọc', 'Thanh', 'Hồng', 'Hữu', 'Tuấn'];
        $ten = ['Anh', 'Bình', 'Châu', 'Dũng', 'Em', 'Giang', 'Hà', 'Khánh', 'Linh', 'Minh', 'Nam', 'Phong', 'Quân', 'Sơn', 'Trang', 'Vy', 'Yến'];

        $khoaKeys = array_keys($khoas);
        $khoaHocs = [
            'K22' => ['prefix' => '22A', 'count' => 8, 'year' => 2022],
            'K23' => ['prefix' => '23A', 'count' => 14, 'year' => 2023],
            'K24' => ['prefix' => '24A', 'count' => 22, 'year' => 2024],
            'K25' => ['prefix' => '25A', 'count' => 18, 'year' => 2025],
            'K26' => ['prefix' => '26A', 'count' => 10, 'year' => 2026],
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

                // Lưu vào SvWhitelist
                $whitelist = SvWhitelist::updateOrCreate(
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

                // Tạo User sinh viên
                $user = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'role' => 'sinhvien',
                        'ma_so' => $maSv,
                        'name' => $fullName,
                        'lop' => $lop,
                        'khoa_hoc' => $khoaHoc,
                        'khoa_id' => $khoaObj->id,
                        'password' => Hash::make('SinhVien@123'),
                        'email_verified_at' => now(),
                        'active' => true,
                    ]
                );

                $allSinhViens[] = $user;
                $stt++;
            }
        }

        // 4. Tạo Đề thi mẫu
        $deThiCNTT = DeThi::firstOrCreate(
            ['ma_de' => 'DT-CNTT-01'],
            [
                'ten_de' => 'Đề thi Ứng dụng CNTT chuẩn đầu ra HVNH',
                'khoa_id' => $khoas['CNTT']->id,
                'loai_chung_chi' => 'cntt',
                'active' => true,
            ]
        );

        $deThiNN = DeThi::firstOrCreate(
            ['ma_de' => 'DT-TA-01'],
            [
                'ten_de' => 'Đề thi Tiếng Anh chuẩn đầu ra B1 HVNH',
                'khoa_id' => $khoas['NN']->id,
                'loai_chung_chi' => 'tieng_anh',
                'active' => true,
            ]
        );

        // 5. Tạo Kỳ thi & Lịch thi trải dài các tháng
        $now = Carbon::now();
        $kyThis = [
            KyThi::firstOrCreate(
                ['ten_ky_thi' => 'Kỳ thi Chuẩn đầu ra Đợt 1 - Học kỳ 2'],
                ['nam_hoc' => '2025-2026', 'hoc_ky' => 'HK2', 'mo_ta' => 'Kỳ thi chuẩn đầu ra Tin học & Ngoại ngữ', 'trang_thai' => 'da_ket_thuc']
            ),
            KyThi::firstOrCreate(
                ['ten_ky_thi' => 'Kỳ thi Chuẩn đầu ra Đợt 2 - Học kỳ Hè'],
                ['nam_hoc' => '2025-2026', 'hoc_ky' => 'He', 'mo_ta' => 'Kỳ thi đợt hè năm 2026', 'trang_thai' => 'da_ket_thuc']
            ),
            KyThi::firstOrCreate(
                ['ten_ky_thi' => 'Kỳ thi Chuẩn đầu ra Đợt 3 - Học kỳ 1'],
                ['nam_hoc' => '2026-2027', 'hoc_ky' => 'HK1', 'mo_ta' => 'Kỳ thi chính khóa đầu năm học 2026-2027', 'trang_thai' => 'dang_dien_ra']
            ),
        ];

        // Lịch thi theo từng tháng (Tháng 5, 6, 7, 8, 9, 10)
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
                        'so_luong_toi_da' => 35,
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

        // 6. Gán Đăng ký thi & Bài thi & Phúc khảo & Chứng nhận
        $btIndex = 1;

        foreach ($lichThis as $ltData) {
            $lt = $ltData['model'];
            $isPast = $ltData['is_past'];
            $ngayThi = $ltData['ngay_thi'];
            $soLuongSv = rand(6, 12);

            // Chọn ngẫu nhiên sinh viên
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

                // Nếu là ca thi trong quá khứ và đã duyệt -> Tạo Bài thi
                if ($isPast && $trangThai === 'da_duyet') {
                    // Phổ điểm đa dạng
                    $diemType = rand(1, 10);
                    if ($diemType <= 3) {
                        // Giỏi (80 - 100)
                        $diemTN = rand(40, 50);
                        $diemTL = rand(40, 50);
                    } elseif ($diemType <= 6) {
                        // Khá (65 - 79)
                        $diemTN = rand(35, 45);
                        $diemTL = rand(30, 34);
                    } elseif ($diemType <= 8) {
                        // Trung bình (50 - 64)
                        $diemTN = rand(25, 35);
                        $diemTL = rand(25, 29);
                    } else {
                        // Kém (< 50)
                        $diemTN = rand(15, 25);
                        $diemTL = rand(10, 20);
                    }

                    $diemTong = $diemTN + $diemTL;
                    $gioBatDau = (clone $ngayThi)->setTime(rand(8, 14), 0);
                    $gioNop = (clone $gioBatDau)->addMinutes(rand(45, 58));

                    $bt = BaiThi::updateOrCreate(
                        ['dang_ky_id' => $dk->id],
                        [
                            'de_thi_id' => $ltData['de_thi']->id,
                            'ma_bai_thi' => 'BT' . $ngayThi->format('ymd') . str_pad($btIndex, 4, '0', STR_PAD_LEFT),
                            'gio_bat_dau' => $gioBatDau,
                            'gio_nop' => $gioNop,
                            'trang_thai' => 'da_cham',
                            'diem_tu_dong' => $diemTN,
                            'diem_cham_tay' => $diemTL,
                            'diem_tong' => $diemTong,
                            'cham_xong' => true,
                            'ngay_cham' => (clone $ngayThi)->addDay(),
                            'ngay_cong_bo' => (clone $ngayThi)->addDays(2),
                            'created_at' => $ngayThi,
                            'updated_at' => (clone $ngayThi)->addDays(2),
                        ]
                    );

                    // Cấp Chứng nhận nếu điểm >= 50
                    if ($diemTong >= 50 && rand(1, 10) <= 8) {
                        ChungNhan::updateOrCreate(
                            ['bai_thi_id' => $bt->id],
                            [
                                'so_chung_nhan' => 'CN-' . strtoupper($lt->loai_chung_chi) . '-' . $ngayThi->format('y') . '-' . str_pad($btIndex, 4, '0', STR_PAD_LEFT),
                                'sinh_vien_id' => $sv->id,
                                'ngay_cap' => (clone $ngayThi)->addDays(5),
                                'trang_thai' => 'da_cap',
                            ]
                        );
                    }

                    // Thêm 1 vài đơn Phúc khảo mẫu
                    if ($diemTong < 65 && rand(1, 10) <= 4) {
                        $diemSau = $diemTong + rand(2, 6);
                        PhucKhao::updateOrCreate(
                            ['bai_thi_id' => $bt->id],
                            [
                                'sinh_vien_id' => $sv->id,
                                'ly_do' => 'Em xin chấm lại phần tự luận câu 3 do em đã giải thích đầy đủ các bước.',
                                'trang_thai' => 'da_xu_ly',
                                'diem_truoc' => $diemTong,
                                'diem_sau' => $diemSau,
                                'phan_hoi' => 'Đã chấm lại: Thêm điểm câu 3 (+ ' . ($diemSau - $diemTong) . 'đ)',
                                'ngay_xu_ly' => (clone $ngayThi)->addDays(10),
                            ]
                        );
                    }

                    $btIndex++;
                }
            }
        }
    }
}
