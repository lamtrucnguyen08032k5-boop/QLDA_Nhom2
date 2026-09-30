@extends('layouts.sinhvien')
@section('title', 'Xác nhận thông tin đăng ký dự thi')
@section('content')
@php $u = $u ?? auth()->user(); @endphp

<ul class="hvnh-steps">
    <li class="done">Bước 1: Cập nhật thông tin cá nhân</li>
    <li class="active">Bước 2: Chọn thông tin đăng ký thi</li>
    <li>Bước 3: Xác nhận và thanh toán</li>
    <li>Bước 4: Trạng thái thanh toán</li>
</ul>

<h4 class="fw-bold text-primary mb-3">XÁC NHẬN THÔNG TIN ĐĂNG KÝ DỰ THI</h4>

<div class="card mb-3">
    <div class="card-header-blue">Ảnh hồ sơ</div>
    <div class="card-body d-flex gap-3 flex-wrap">
        @if(!empty($draft['anh_ho_so']))
            <img src="{{ \Illuminate\Support\Facades\Storage::url($draft['anh_ho_so']) }}" style="height:140px;border-radius:.4rem;border:1px solid #dbe4f0;" alt="Ảnh hồ sơ">
        @endif
        @if(!empty($draft['anh_cccd_truoc']))
            <img src="{{ \Illuminate\Support\Facades\Storage::url($draft['anh_cccd_truoc']) }}" style="height:140px;border-radius:.4rem;border:1px solid #dbe4f0;" alt="CCCD mặt trước">
        @endif
        @if(!empty($draft['anh_cccd_sau']))
            <img src="{{ \Illuminate\Support\Facades\Storage::url($draft['anh_cccd_sau']) }}" style="height:140px;border-radius:.4rem;border:1px solid #dbe4f0;" alt="CCCD mặt sau">
        @endif
        @if(!empty($draft['anh_the_sv']))
            <img src="{{ \Illuminate\Support\Facades\Storage::url($draft['anh_the_sv']) }}" style="height:140px;border-radius:.4rem;border:1px solid #dbe4f0;" alt="Thẻ sinh viên">
        @endif
    </div>
</div>

<div class="card mb-3">
    <div class="card-header-blue">Thông tin cá nhân</div>
    <table class="table table-kv mb-0">
        <tr><th>Họ và tên thí sinh</th><td>{{ $u->name }}</td></tr>
        <tr><th>Mã sinh viên</th><td>{{ $u->ma_so }}</td></tr>
        <tr><th>Lớp</th><td>{{ $u->lop }}</td></tr>
        <tr><th>Khoa</th><td>{{ optional($u->khoa)->ten_khoa }}</td></tr>
        <tr><th>Ngày sinh</th><td>{{ \Illuminate\Support\Carbon::parse($draft['ngay_sinh'])->format('d/m/Y') }}</td></tr>
        <tr><th>Giới tính</th><td>{{ $draft['gioi_tinh'] === 'nam' ? 'Nam' : ($draft['gioi_tinh'] === 'nu' ? 'Nữ' : 'Khác') }}</td></tr>
        <tr><th>Dân tộc</th><td>{{ $draft['dan_toc'] }}</td></tr>
        <tr><th>Nơi sinh</th><td>{{ $draft['noi_sinh'] }}</td></tr>
        <tr><th>CCCD/Số CCCD</th><td>{{ $draft['so_cccd'] }}</td></tr>
        <tr><th>Số điện thoại liên hệ</th><td>{{ $draft['so_dien_thoai'] }}</td></tr>
        <tr><th>Địa chỉ liên hệ</th><td>{{ $draft['dia_chi_chi_tiet'] }}, {{ $draft['xa_phuong_ten'] }}, {{ $draft['tinh_thanh_pho_ten'] }}</td></tr>
        <tr><th>Email liên hệ</th><td>{{ $draft['email_lien_he'] }}</td></tr>
    </table>
</div>

<div class="card mb-4">
    <div class="card-header-blue">Thông tin đăng ký</div>
    <table class="table table-kv mb-0">
        <tr><th>Bài thi</th><td>{{ $lichthi->ten_ky_thi }}</td></tr>
        <tr><th>Thời gian thi</th><td>{{ $lichthi->ngay_thi->format('d/m/Y') }} — {{ $lichthi->gio_bat_dau ? \Carbon\Carbon::parse($lichthi->gio_bat_dau)->format('H:i') : '' }}</td></tr>
        <tr><th>Địa điểm/phòng thi</th><td>{{ $lichthi->phong_thi }}</td></tr>
        <tr><th>Lệ phí</th><td class="fw-bold text-primary">{{ number_format($lichthi->le_phi) }}đ</td></tr>
    </table>
</div>

<div class="d-flex justify-content-between mb-5">
    <a href="{{ route('sinhvien.dangky.buoc1', $lichthi) }}" class="btn btn-outline-secondary">← Quay lại chỉnh sửa</a>
    <form method="POST" action="{{ route('sinhvien.dangky.buoc2.xacnhan', $lichthi) }}">
        @csrf
        <button class="btn btn-primary px-4">Xác nhận & Tiếp tục thanh toán</button>
    </form>
</div>
@endsection
