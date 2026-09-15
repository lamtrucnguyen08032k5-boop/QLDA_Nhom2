<?php

namespace Tests\Feature;

use App\Models\Khoa;
use App\Models\SvWhitelist;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthAndAccountManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_dang_nhap_thanh_cong_chuyen_huong_theo_vai_tro(): void
    {
        $admin = User::create([
            'role' => 'admin',
            'name' => 'Admin',
            'email' => 'admin@hvnh.edu.vn',
            'password' => Hash::make('password123'),
            'active' => true,
        ]);

        $response = $this->post('/dang-nhap', [
            'email' => 'admin@hvnh.edu.vn',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_dang_nhap_sai_mat_khau(): void
    {
        User::create([
            'role' => 'sinhvien',
            'name' => 'Sinh Vien',
            'email' => 'sv@hvnh.edu.vn',
            'password' => Hash::make('password123'),
            'active' => true,
        ]);

        $response = $this->post('/dang-nhap', [
            'email' => 'sv@hvnh.edu.vn',
            'password' => 'wrongpass',
        ]);

        $response->assertSessionHasErrors(['email' => 'Email hoặc mật khẩu không chính xác.']);
    }

    public function test_dang_nhap_tai_khoan_bi_khoa(): void
    {
        User::create([
            'role' => 'sinhvien',
            'name' => 'Sinh Vien Khoa',
            'email' => 'locked@hvnh.edu.vn',
            'password' => Hash::make('password123'),
            'active' => false,
        ]);

        $response = $this->post('/dang-nhap', [
            'email' => 'locked@hvnh.edu.vn',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email' => 'Tài khoản của bạn đã bị khóa hoặc vô hiệu hóa. Vui lòng liên hệ Quản trị viên.']);
    }

    public function test_dang_ky_va_xac_minh_email_thanh_cong(): void
    {
        SvWhitelist::create([
            'ma_sv' => '22A123456',
            'ho_ten' => 'Nguyễn Văn Nam',
            'email' => 'nam@hvnh.edu.vn',
            'lop' => 'K22',
            'khoa_hoc' => 'K22',
        ]);

        $res1 = $this->post('/dang-ky', [
            'email' => 'nam@hvnh.edu.vn',
        ]);

        $res1->assertStatus(200);
        $res1->assertViewIs('auth.register-sent');

        $tokenRow = DB::table('email_verification_tokens')->where('email', 'nam@hvnh.edu.vn')->first();
        $this->assertNotNull($tokenRow);

        $res2 = $this->get('/dang-ky/xac-minh/' . $tokenRow->token);
        $res2->assertStatus(200);
        $res2->assertViewIs('auth.set-password');

        $res3 = $this->post('/dang-ky/hoan-tat', [
            'token' => $tokenRow->token,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $res3->assertRedirect(route('login'));
        $res3->assertSessionHas('status');

        $this->assertDatabaseHas('users', [
            'email' => 'nam@hvnh.edu.vn',
            'role' => 'sinhvien',
            'ma_so' => '22A123456',
        ]);
    }

    public function test_quen_mat_khau_va_dat_lai_thanh_cong(): void
    {
        $user = User::create([
            'role' => 'sinhvien',
            'name' => 'Sinh Vien Reset',
            'email' => 'reset@hvnh.edu.vn',
            'password' => Hash::make('oldpassword'),
            'active' => true,
        ]);

        $res1 = $this->post('/quen-mat-khau', [
            'email' => 'reset@hvnh.edu.vn',
        ]);

        $res1->assertSessionHas('status');

        $tokenRow = DB::table('password_reset_tokens')->where('email', 'reset@hvnh.edu.vn')->first();
        $this->assertNotNull($tokenRow);

        $plainToken = 'test-token-string';
        DB::table('password_reset_tokens')->where('email', 'reset@hvnh.edu.vn')->update([
            'token' => Hash::make($plainToken),
        ]);

        $res2 = $this->get('/dat-lai-mat-khau/' . $plainToken . '?email=reset@hvnh.edu.vn');
        $res2->assertStatus(200);
        $res2->assertViewIs('auth.reset-password');

        $res3 = $this->post('/dat-lai-mat-khau', [
            'token' => $plainToken,
            'email' => 'reset@hvnh.edu.vn',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $res3->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    public function test_admin_tao_khoa_va_giang_vien(): void
    {
        $admin = User::create([
            'role' => 'admin',
            'name' => 'Admin',
            'email' => 'admin@hvnh.edu.vn',
            'password' => Hash::make('password123'),
            'active' => true,
        ]);

        $res1 = $this->actingAs($admin)->post('/admin/khoa', [
            'ma_khoa' => 'CNTT',
            'ten_khoa' => 'Công nghệ thông tin',
            'email' => 'cntt@hvnh.edu.vn',
            'password' => 'Khoa@123',
        ]);

        $res1->assertRedirect(route('admin.khoa.index'));
        $this->assertDatabaseHas('khoas', ['ma_khoa' => 'CNTT']);
        $this->assertDatabaseHas('users', ['email' => 'cntt@hvnh.edu.vn', 'role' => 'khoa']);

        $khoa = Khoa::where('ma_khoa', 'CNTT')->first();

        $res2 = $this->actingAs($admin)->post('/admin/khoa/' . $khoa->id . '/giang-vien', [
            'ma_giang_vien' => 'GV001',
            'ho_ten' => 'Giang Vien A',
            'email' => 'gva@hvnh.edu.vn',
            'password' => 'GiangVien@123',
        ]);

        $res2->assertSessionHas('status', 'Thêm Giảng viên thành công.');
        $this->assertDatabaseHas('users', [
            'role' => 'giangvien',
            'ma_so' => 'GV001',
            'khoa_id' => $khoa->id,
        ]);
    }

    public function test_admin_import_khoa_va_giang_vien(): void
    {
        $admin = User::create([
            'role' => 'admin',
            'name' => 'Admin',
            'email' => 'admin@hvnh.edu.vn',
            'password' => Hash::make('password123'),
            'active' => true,
        ]);

        $csvKhoaContent = "ma_khoa,ten_khoa,email,mo_ta\n"
            . "QTKD,Khoa Quản trị kinh doanh,khoa.qtkd@hvnh.edu.vn,Khoa QTKD\n";

        $fileKhoa = \Illuminate\Http\UploadedFile::fake()->createWithContent('khoas.csv', $csvKhoaContent);

        $res1 = $this->actingAs($admin)->post('/admin/khoa/import', [
            'file' => $fileKhoa,
            'password' => 'KhoaPass@123',
        ]);

        $res1->assertSessionHas('status');
        $this->assertDatabaseHas('khoas', ['ma_khoa' => 'QTKD']);
        $this->assertDatabaseHas('users', ['email' => 'khoa.qtkd@hvnh.edu.vn', 'role' => 'khoa']);

        $khoa = Khoa::where('ma_khoa', 'QTKD')->first();

        $csvGvContent = "ma_giang_vien,ho_ten,email\n"
            . "GV099,Trần Giảng Viên,gv.qtkd@hvnh.edu.vn\n";

        $fileGv = \Illuminate\Http\UploadedFile::fake()->createWithContent('giang_viens.csv', $csvGvContent);

        $res2 = $this->actingAs($admin)->post('/admin/khoa/' . $khoa->id . '/giang-vien/import', [
            'file' => $fileGv,
            'password' => 'GvPass@123',
        ]);

        $res2->assertSessionHas('status');
        $this->assertDatabaseHas('users', [
            'role' => 'giangvien',
            'ma_so' => 'GV099',
            'email' => 'gv.qtkd@hvnh.edu.vn',
            'khoa_id' => $khoa->id,
        ]);
    }

    public function test_dang_nhap_bang_ma_so(): void
    {
        $khoaUser = User::create([
            'role' => 'khoa',
            'name' => 'Tài khoản Khoa CNTT',
            'email' => 'cntt@hvnh.edu.vn',
            'ma_so' => 'CNTT',
            'password' => Hash::make('Khoa@123'),
            'active' => true,
        ]);

        $response = $this->post('/dang-nhap', [
            'email' => 'CNTT',
            'password' => 'Khoa@123',
        ]);

        $response->assertRedirect(route('khoa.dashboard'));
        $this->assertAuthenticatedAs($khoaUser);
    }
}
