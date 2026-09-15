<?php

namespace Tests\Feature;

use App\Models\DangKy;
use App\Models\Khoa;
use App\Models\KyThi;
use App\Models\LichThi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class KyThiManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Khoa $khoaCntt;
    private Khoa $khoaNn;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'role' => 'admin',
            'name' => 'Quản trị viên',
            'email' => 'admin@hvnh.edu.vn',
            'password' => Hash::make('Admin@123'),
            'active' => true,
        ]);

        $this->khoaCntt = Khoa::create([
            'ma_khoa' => 'CNTT',
            'ten_khoa' => 'Khoa Công nghệ thông tin',
            'email' => 'khoa.cntt@hvnh.edu.vn',
            'active' => true,
        ]);

        $this->khoaNn = Khoa::create([
            'ma_khoa' => 'NN',
            'ten_khoa' => 'Khoa Ngoại ngữ',
            'email' => 'khoa.nn@hvnh.edu.vn',
            'active' => true,
        ]);
    }

    public function test_admin_xem_danh_sach_ky_thi_thanh_cong(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.lichthi.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.lichthi.index');
        $response->assertViewHas('stats');
    }

    public function test_admin_tao_moi_ky_thi_voi_nhieu_lich_thi_va_ca_thi_thanh_cong(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.lichthi.store'), [
            'ten_ky_thi' => 'Kỳ thi Chuẩn đầu ra Đợt 1 - 2026',
            'nam_hoc' => '2025-2026',
            'hoc_ky' => 'Học kỳ 1',
            'mo_ta' => 'Kỳ thi CNTT và Tiếng Anh đầu ra',
            'trang_thai' => 'dang_mo_dang_ky',
            'lich_this' => [
                // Lịch thi 1: CNTT
                [
                    'loai_chung_chi' => 'cntt',
                    'khoa_id' => $this->khoaCntt->id,
                    'ngay_thi' => '2026-11-20',
                    'han_dang_ky' => '2026-11-15T23:59',
                    'le_phi' => 200000,
                    'ca_this' => [
                        [
                            'ma_ca_thi' => 'CNTT-CA1',
                            'gio_bat_dau' => '08:00',
                            'thoi_gian_thi_phut' => 60,
                            'phong_thi' => 'Phòng Máy 1',
                            'so_luong_toi_da' => 40,
                        ],
                        [
                            'ma_ca_thi' => 'CNTT-CA2',
                            'gio_bat_dau' => '09:30',
                            'thoi_gian_thi_phut' => 60,
                            'phong_thi' => 'Phòng Máy 1',
                            'so_luong_toi_da' => 40,
                        ],
                    ],
                ],
                // Lịch thi 2: Tiếng Anh
                [
                    'loai_chung_chi' => 'tienganh',
                    'khoa_id' => $this->khoaNn->id,
                    'ngay_thi' => '2026-11-21',
                    'han_dang_ky' => '2026-11-15T23:59',
                    'le_phi' => 250000,
                    'ca_this' => [
                        [
                            'ma_ca_thi' => 'TA-CA1',
                            'gio_bat_dau' => '08:00',
                            'thoi_gian_thi_phut' => 90,
                            'phong_thi' => 'Hội trường D',
                            'so_luong_toi_da' => 100,
                        ],
                    ],
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.lichthi.index'));
        $response->assertSessionHas('status', 'Thêm kỳ thi thành công.');

        $this->assertDatabaseHas('ky_this', [
            'ten_ky_thi' => 'Kỳ thi Chuẩn đầu ra Đợt 1 - 2026',
            'nam_hoc' => '2025-2026',
        ]);

        $kyThi = KyThi::where('ten_ky_thi', 'Kỳ thi Chuẩn đầu ra Đợt 1 - 2026')->first();
        $this->assertNotNull($kyThi);
        $this->assertCount(3, $kyThi->lichThis);
        $this->assertDatabaseHas('lich_this', ['ma_ca_thi' => 'CNTT-CA1', 'phong_thi' => 'Phòng Máy 1']);
        $this->assertDatabaseHas('lich_this', ['ma_ca_thi' => 'CNTT-CA2', 'phong_thi' => 'Phòng Máy 1']);
        $this->assertDatabaseHas('lich_this', ['ma_ca_thi' => 'TA-CA1', 'phong_thi' => 'Hội trường D']);
    }

    public function test_kiem_tra_xung_dot_trung_phong_thi_cung_thoi_gian(): void
    {
        // Thử thêm 2 ca thi trong cùng ngày 2026-11-20 ở cùng "Phòng Máy 1" nhưng trùng thời gian (08:00 - 09:30 và 08:30 - 09:30)
        $response = $this->actingAs($this->admin)->post(route('admin.lichthi.store'), [
            'ten_ky_thi' => 'Kỳ thi Trùng Phòng',
            'trang_thai' => 'dang_mo_dang_ky',
            'lich_this' => [
                [
                    'loai_chung_chi' => 'cntt',
                    'khoa_id' => $this->khoaCntt->id,
                    'ngay_thi' => '2026-11-20',
                    'han_dang_ky' => '2026-11-15T23:59',
                    'le_phi' => 200000,
                    'ca_this' => [
                        [
                            'gio_bat_dau' => '08:00',
                            'thoi_gian_thi_phut' => 60, // Kết thúc lúc 09:00
                            'phong_thi' => 'Phòng Máy 1',
                            'so_luong_toi_da' => 40,
                        ],
                        [
                            'gio_bat_dau' => '08:30', // Bắt đầu khi ca 1 chưa xong (trùng!)
                            'thoi_gian_thi_phut' => 60,
                            'phong_thi' => 'Phòng Máy 1',
                            'so_luong_toi_da' => 40,
                        ],
                    ],
                ],
            ],
        ]);

        $response->assertSessionHasErrors();
        $this->assertDatabaseMissing('ky_this', ['ten_ky_thi' => 'Kỳ thi Trùng Phòng']);
    }

    public function test_kiem_tra_han_dang_ky_phai_truoc_ngay_thi(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.lichthi.store'), [
            'ten_ky_thi' => 'Kỳ thi Lỗi Hạn ĐK',
            'trang_thai' => 'dang_mo_dang_ky',
            'lich_this' => [
                [
                    'loai_chung_chi' => 'cntt',
                    'khoa_id' => $this->khoaCntt->id,
                    'ngay_thi' => '2026-11-20',
                    'han_dang_ky' => '2026-11-21T23:59', // Hạn đăng ký sau ngày thi!
                    'le_phi' => 200000,
                    'ca_this' => [
                        [
                            'gio_bat_dau' => '08:00',
                            'thoi_gian_thi_phut' => 60,
                            'phong_thi' => 'Phòng Máy 1',
                            'so_luong_toi_da' => 40,
                        ],
                    ],
                ],
            ],
        ]);

        $response->assertSessionHasErrors();
    }

    public function test_admin_chinh_sua_ky_thi_thanh_cong(): void
    {
        $kyThi = KyThi::create([
            'ten_ky_thi' => 'Kỳ thi Cũ',
            'nam_hoc' => '2025-2026',
            'trang_thai' => 'dang_mo_dang_ky',
        ]);

        $lt = LichThi::create([
            'ky_thi_id' => $kyThi->id,
            'ten_ky_thi' => $kyThi->ten_ky_thi,
            'loai_chung_chi' => 'cntt',
            'khoa_id' => $this->khoaCntt->id,
            'ngay_thi' => '2026-12-01',
            'gio_bat_dau' => '08:00',
            'thoi_gian_thi_phut' => 60,
            'phong_thi' => 'P.101',
            'so_luong_toi_da' => 30,
            'han_dang_ky' => '2026-11-25 23:59:00',
            'le_phi' => 200000,
            'ma_ca_thi' => 'CA-OLD-01',
            'trang_thai' => 'dang_mo_dang_ky',
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.lichthi.update', $kyThi->id), [
            'ten_ky_thi' => 'Kỳ thi Đã Đổi Tên',
            'nam_hoc' => '2025-2026',
            'hoc_ky' => 'Học kỳ 2',
            'trang_thai' => 'da_dong_dang_ky',
            'lich_this' => [
                [
                    'loai_chung_chi' => 'cntt',
                    'khoa_id' => $this->khoaCntt->id,
                    'ngay_thi' => '2026-12-01',
                    'han_dang_ky' => '2026-11-25T23:59',
                    'le_phi' => 220000,
                    'ca_this' => [
                        [
                            'id' => $lt->id,
                            'ma_ca_thi' => 'CA-OLD-01',
                            'gio_bat_dau' => '08:00',
                            'thoi_gian_thi_phut' => 60,
                            'phong_thi' => 'P.101-NEW',
                            'so_luong_toi_da' => 35,
                        ],
                    ],
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.lichthi.index'));
        $response->assertSessionHas('status', 'Cập nhật kỳ thi thành công.');

        $this->assertDatabaseHas('ky_this', ['id' => $kyThi->id, 'ten_ky_thi' => 'Kỳ thi Đã Đổi Tên', 'trang_thai' => 'da_dong_dang_ky']);
        $this->assertDatabaseHas('lich_this', ['id' => $lt->id, 'phong_thi' => 'P.101-NEW', 'so_luong_toi_da' => 35]);
    }

    public function test_khong_the_xoa_ky_thi_khi_da_co_sinh_vien_dang_ky(): void
    {
        $kyThi = KyThi::create([
            'ten_ky_thi' => 'Kỳ thi Có Đăng Ký',
            'trang_thai' => 'dang_mo_dang_ky',
        ]);

        $lt = LichThi::create([
            'ky_thi_id' => $kyThi->id,
            'ten_ky_thi' => $kyThi->ten_ky_thi,
            'loai_chung_chi' => 'cntt',
            'khoa_id' => $this->khoaCntt->id,
            'ngay_thi' => '2026-12-01',
            'gio_bat_dau' => '08:00',
            'thoi_gian_thi_phut' => 60,
            'phong_thi' => 'P.101',
            'so_luong_toi_da' => 30,
            'han_dang_ky' => '2026-11-25 23:59:00',
            'le_phi' => 200000,
            'ma_ca_thi' => 'CA-DK-01',
            'trang_thai' => 'dang_mo_dang_ky',
        ]);

        $sinhVien = User::create([
            'role' => 'sinhvien',
            'name' => 'Sinh Vien Test',
            'email' => 'svtest@hvnh.edu.vn',
            'password' => Hash::make('password123'),
            'active' => true,
        ]);

        DangKy::create([
            'sinh_vien_id' => $sinhVien->id,
            'lich_thi_id' => $lt->id,
            'ma_dang_ky' => 'DK001',
            'trang_thai' => 'da_duyet',
            'thoi_gian_dang_ky' => now(),
        ]);

        // Thử xóa kỳ thi -> phải bị từ chối
        $res1 = $this->actingAs($this->admin)->delete(route('admin.lichthi.destroy', $kyThi->id));
        $res1->assertSessionHasErrors(['kythi']);
        $this->assertDatabaseHas('ky_this', ['id' => $kyThi->id]);

        // Thử xóa ca thi đơn lẻ -> phải bị từ chối
        $res2 = $this->actingAs($this->admin)->delete(route('admin.lichthi.cathi.destroy', $lt->id));
        $res2->assertSessionHasErrors(['lichthi']);
        $this->assertDatabaseHas('lich_this', ['id' => $lt->id]);
    }

    public function test_xoa_ky_thi_thanh_cong_khi_chua_co_dang_ky(): void
    {
        $kyThi = KyThi::create([
            'ten_ky_thi' => 'Kỳ thi Trống',
            'trang_thai' => 'dang_mo_dang_ky',
        ]);

        $lt = LichThi::create([
            'ky_thi_id' => $kyThi->id,
            'ten_ky_thi' => $kyThi->ten_ky_thi,
            'loai_chung_chi' => 'cntt',
            'khoa_id' => $this->khoaCntt->id,
            'ngay_thi' => '2026-12-01',
            'gio_bat_dau' => '08:00',
            'thoi_gian_thi_phut' => 60,
            'phong_thi' => 'P.101',
            'so_luong_toi_da' => 30,
            'han_dang_ky' => '2026-11-25 23:59:00',
            'le_phi' => 200000,
            'ma_ca_thi' => 'CA-EMPTY-01',
            'trang_thai' => 'dang_mo_dang_ky',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.lichthi.destroy', $kyThi->id));
        $response->assertRedirect(route('admin.lichthi.index'));
        $response->assertSessionHas('status', 'Xóa thành công.');

        $this->assertDatabaseMissing('ky_this', ['id' => $kyThi->id]);
        $this->assertDatabaseMissing('lich_this', ['id' => $lt->id]);
    }
}

