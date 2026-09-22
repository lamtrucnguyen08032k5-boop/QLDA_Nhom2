<?php

namespace Tests\Feature;

use App\Models\BaiThi;
use App\Models\DangKy;
use App\Models\DeThi;
use App\Models\Khoa;
use App\Models\LichThi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class KetQuaThiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $sinhVien1;
    private User $sinhVien2;
    private Khoa $khoa;
    private DeThi $deThi;
    private LichThi $lichThi;
    private DangKy $dangKy1;
    private DangKy $dangKy2;
    private BaiThi $baiThi1;
    private BaiThi $baiThi2;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();

        $this->admin = User::create([
            'role' => 'admin',
            'name' => 'Admin Khảo Thí',
            'email' => 'admin@hvnh.edu.vn',
            'password' => Hash::make('Admin@123'),
            'active' => true,
        ]);

        $this->khoa = Khoa::create([
            'ma_khoa' => 'CNTT',
            'ten_khoa' => 'Khoa Công nghệ thông tin',
            'email' => 'khoa.cntt@hvnh.edu.vn',
            'active' => true,
        ]);

        $this->sinhVien1 = User::create([
            'role' => 'sinhvien',
            'ma_so' => '25A4040001',
            'name' => 'Nguyễn Văn An',
            'email' => 'an.nv@hvnh.edu.vn',
            'password' => Hash::make('Password@123'),
            'active' => true,
        ]);

        $this->sinhVien2 = User::create([
            'role' => 'sinhvien',
            'ma_so' => '25A4040002',
            'name' => 'Trần Thị Bình',
            'email' => 'binh.tt@hvnh.edu.vn',
            'password' => Hash::make('Password@123'),
            'active' => true,
        ]);

        $this->deThi = DeThi::create([
            'ma_de' => 'DT-CNTT-01',
            'ten_de' => 'Đề thi Tin học chuẩn đầu ra',
            'khoa_id' => $this->khoa->id,
            'loai_chung_chi' => 'cntt',
        ]);

        $this->lichThi = LichThi::create([
            'ten_ky_thi' => 'Kỳ thi Chuẩn đầu ra CNTT Đợt 1',
            'loai_chung_chi' => 'cntt',
            'khoa_id' => $this->khoa->id,
            'ngay_thi' => now()->toDateString(),
            'gio_bat_dau' => '08:00',
            'thoi_gian_thi_phut' => 60,
            'phong_thi' => 'P.302',
            'so_luong_toi_da' => 40,
            'han_dang_ky' => now()->subDays(2),
            'ma_ca_thi' => 'CA01-P302',
            'trang_thai' => 'da_ket_thuc',
            'de_thi_id' => $this->deThi->id,
        ]);

        $this->dangKy1 = DangKy::create([
            'sinh_vien_id' => $this->sinhVien1->id,
            'lich_thi_id' => $this->lichThi->id,
            'trang_thai' => 'da_duyet',
            'so_cccd' => '001205001111',
        ]);

        $this->dangKy2 = DangKy::create([
            'sinh_vien_id' => $this->sinhVien2->id,
            'lich_thi_id' => $this->lichThi->id,
            'trang_thai' => 'da_duyet',
            'so_cccd' => '001205002222',
        ]);

        $this->baiThi1 = BaiThi::create([
            'dang_ky_id' => $this->dangKy1->id,
            'de_thi_id' => $this->deThi->id,
            'gio_bat_dau' => now()->subHours(2),
            'gio_nop' => now()->subHour(),
            'trang_thai' => 'dang_cham',
            'diem_tu_dong' => 40,
            'diem_cham_tay' => 0,
            'diem_tong' => null,
            'cham_xong' => false,
        ]);

        $this->baiThi2 = BaiThi::create([
            'dang_ky_id' => $this->dangKy2->id,
            'de_thi_id' => $this->deThi->id,
            'gio_bat_dau' => now()->subHours(2),
            'gio_nop' => now()->subHour(),
            'trang_thai' => 'da_cham',
            'diem_tu_dong' => 50,
            'diem_cham_tay' => 25,
            'diem_tong' => 75,
            'cham_xong' => true,
        ]);
    }

    public function test_admin_xem_danh_sach_lich_thi_ket_qua(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.ketqua.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.ketqua.index');
        $response->assertSee('Công bố kết quả thi');
        $response->assertSee($this->lichThi->ten_ky_thi);
        $response->assertSee($this->lichThi->phong_thi);
    }

    public function test_admin_xem_chi_tiet_phong_thi_chua_cham_xong(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.ketqua.show', $this->lichThi));
        $response->assertStatus(200);
        $response->assertViewIs('admin.ketqua.show');
        $response->assertSee('Chưa thể trả kết quả. Vẫn còn bài thi chưa hoàn thành chấm.');
        $response->assertSee($this->sinhVien1->name);
        $response->assertSee($this->sinhVien2->name);
    }

    public function test_admin_khong_the_cong_bo_khi_chua_cham_xong_tat_ca_bai(): void
    {
        // baiThi1 vẫn đang dang_cham / cham_xong = false
        $response = $this->actingAs($this->admin)->post(route('admin.ketqua.congbo', $this->lichThi));
        $response->assertSessionHasErrors('cong_bo');
        
        $this->baiThi1->refresh();
        $this->assertNotEquals('da_cong_bo', $this->baiThi1->trang_thai);
    }

    public function test_admin_cong_bo_dong_loat_khi_tat_ca_bai_da_cham_xong(): void
    {
        // Hoàn thành chấm bài 1
        $this->baiThi1->update([
            'diem_cham_tay' => 20,
            'diem_tong' => 60,
            'cham_xong' => true,
            'trang_thai' => 'da_cham',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.ketqua.congbo', $this->lichThi));
        $response->assertSessionHas('status');

        $this->baiThi1->refresh();
        $this->baiThi2->refresh();
        $this->lichThi->refresh();

        $this->assertEquals('da_cong_bo', $this->baiThi1->trang_thai);
        $this->assertEquals('da_cong_bo', $this->baiThi2->trang_thai);
        $this->assertNotNull($this->baiThi1->ngay_cong_bo);
        $this->assertNotNull($this->baiThi1->ma_bai_thi);
        $this->assertEquals('da_cong_bo', $this->lichThi->trang_thai_cong_bo);
        $this->assertEquals($this->admin->id, $this->lichThi->nguoi_cong_bo_id);
    }

    public function test_sinh_vien_khong_thay_bai_thi_chua_cong_bo(): void
    {
        // Chưa công bố
        $response = $this->actingAs($this->sinhVien2)->get(route('sinhvien.ketqua.index'));
        $response->assertStatus(200);
        $response->assertSee('Chưa có kết quả thi.');
        $response->assertDontSee($this->baiThi2->ma_bai_thi_hien_thi);
    }

    public function test_sinh_vien_tra_cuu_va_xem_chi_tiet_bai_thi_da_cong_bo(): void
    {
        // Công bố kết quả cho bài thi 2
        $this->baiThi2->update([
            'trang_thai' => 'da_cong_bo',
            'ngay_cong_bo' => now(),
            'nguoi_cong_bo_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->sinhVien2)->get(route('sinhvien.ketqua.index'));
        $response->assertStatus(200);
        $response->assertSee($this->lichThi->ten_ky_thi);
        $response->assertSee('75');
        $response->assertSee('Đạt');

        // Xem chi tiết
        $detailResponse = $this->actingAs($this->sinhVien2)->get(route('sinhvien.ketqua.show', $this->baiThi2));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee($this->sinhVien2->name);
        $detailResponse->assertSee($this->dangKy2->so_cccd);
        $detailResponse->assertSee('Yêu cầu phúc khảo'); // còn trong hạn 7 ngày
        $detailResponse->assertSee('Đăng ký nhận chứng nhận'); // điểm 75 >= 50
    }

    public function test_sinh_vien_khong_the_xem_ket_qua_cua_sinh_vien_khac(): void
    {
        $this->baiThi2->update([
            'trang_thai' => 'da_cong_bo',
            'ngay_cong_bo' => now(),
        ]);

        // Sinh viên 1 cố tình truy cập bài thi của Sinh viên 2
        $response = $this->actingAs($this->sinhVien1)->get(route('sinhvien.ketqua.show', $this->baiThi2));
        $response->assertStatus(403);
    }

    public function test_sinh_vien_khong_the_xem_ket_qua_chua_cong_bo(): void
    {
        // Bài thi chưa công bố
        $response = $this->actingAs($this->sinhVien1)->get(route('sinhvien.ketqua.show', $this->baiThi1));
        $response->assertStatus(404);
    }

    public function test_thong_bao_he_thong_gui_cho_sinh_vien_khi_cong_bo(): void
    {
        $this->baiThi1->update([
            'diem_cham_tay' => 20,
            'diem_tong' => 60,
            'cham_xong' => true,
            'trang_thai' => 'da_cham',
        ]);

        $this->actingAs($this->admin)->post(route('admin.ketqua.congbo', $this->lichThi));

        // Kiểm tra sinh viên 1 nhận được database notification
        $this->sinhVien1->refresh();
        $this->assertTrue($this->sinhVien1->notifications()->exists());
        $notif = $this->sinhVien1->notifications()->first();
        $this->assertStringContainsString('Đã có kết quả thi', $notif->data['tieu_de']);
    }
}
