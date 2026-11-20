@extends('layouts.sinhvien')
@section('title', 'Trạng thái đăng ký & thanh toán')
@section('content')

<ul class="hvnh-steps">
    <li class="done">Bước 1: Cập nhật thông tin cá nhân</li>
    <li class="done">Bước 2: Chọn thông tin đăng ký thi</li>
    <li class="done">Bước 3: Xác nhận và thanh toán</li>
    <li class="active">Bước 4: Trạng thái hồ sơ & thanh toán</li>
</ul>

<div class="row justify-content-center">
    <div class="col-lg-8">
        @if ($dangky->trang_thai === 'cho_bo_sung')
            @if ($dangky->isHetHanBoSungOnline())
                <!-- Luồng phụ: Hết hạn bổ sung trực tuyến -->
                <div class="alert alert-danger shadow-sm border-danger p-4 mb-4">
                    <h5 class="alert-heading fw-bold text-danger">⚠️ Đã hết hạn bổ sung hồ sơ trực tuyến</h5>
                    <p class="mb-2">
                        Đã hết hạn bổ sung hồ sơ trực tuyến. Vui lòng bổ sung hồ sơ trực tiếp tại <strong>Phòng Khảo thí – Phòng 101, Tòa nhà D1, Học viện Ngân hàng, 12 Chùa Bộc, Đống Đa, Hà Nội</strong>, chậm nhất trước ngày <strong>{{ $dangky->hanCuoiBoSungTrucTiep() }}</strong>.
                    </p>
                    <p class="mb-3 text-dark fw-semibold">
                        Nếu không hoàn tất bổ sung hồ sơ đúng thời hạn, hồ sơ đăng ký dự thi của bạn sẽ bị hủy và lệ phí thi đã thanh toán sẽ không được hoàn lại.
                    </p>
                    <hr>
                    <div class="row g-2 small text-muted">
                        <div class="col-md-6"><strong>Địa chỉ:</strong> Phòng 101, Tòa D1, Học viện Ngân hàng</div>
                        <div class="col-md-6"><strong>SĐT / Email:</strong> 024.3852.1852 | phongkhaothi@hvnh.edu.vn</div>
                        <div class="col-md-6"><strong>Hạn cuối bổ sung trực tiếp:</strong> {{ $dangky->hanCuoiBoSungTrucTiep() }}</div>
                        <div class="col-md-6"><strong>Trạng thái hồ sơ:</strong> <span class="badge bg-danger">Yêu cầu bổ sung (Quá hạn trực tuyến)</span></div>
                    </div>
                </div>
            @else
                <!-- Còn hạn bổ sung trực tuyến -->
                <div class="alert alert-warning shadow-sm border-warning p-4 mb-4">
                    <h5 class="alert-heading fw-bold text-dark">⚠️ Hồ sơ yêu cầu bổ sung thông tin</h5>
                    <p class="mb-2"><strong>Nội dung Admin yêu cầu:</strong> {{ $dangky->ly_do_bo_sung }}</p>
                    <p class="mb-3"><strong>Thời hạn bổ sung trực tuyến:</strong> {{ optional($dangky->han_bo_sung)->format('d/m/Y H:i') }}</p>
                    <a href="{{ route('sinhvien.dangky.bo-sung', $dangky) }}" class="btn btn-warning fw-bold px-4">
                        ✏️ Bổ sung hồ sơ ngay
                    </a>
                </div>
            @endif
        @elseif ($dangky->trang_thai === 'da_huy')
            <div class="alert alert-danger shadow-sm border-danger p-4 mb-4">
                <h5 class="alert-heading fw-bold text-danger">❌ Hồ sơ đăng ký đã bị huỷ</h5>
                <p class="mb-2">
                    Hồ sơ đăng ký <strong>{{ $dangky->ma_dang_ky }}</strong> đã bị huỷ
                    @if ($dangky->isQuaHanThanhToan() || $dangky->trang_thai_thanh_toan === 'thanh_toan_that_bai')
                        do quá thời hạn thanh toán lệ phí (sau 2 ngày kể từ lúc đăng ký).
                    @else
                        theo yêu cầu.
                    @endif
                </p>
                @php
                    $lichThi = $dangky->lichThi;
                    $coTheDangKyLai = $lichThi && !$lichThi->daHetHanDangKy() && $lichThi->trang_thai === 'dang_mo_dang_ky' && !$lichThi->daHetCho();
                @endphp
                @if ($coTheDangKyLai)
                    <p class="mb-3 text-success fw-semibold">
                        Ca thi này hiện vẫn còn chỗ và còn thời hạn đăng ký. Bạn có thể đăng ký lại ngay bây giờ.
                    </p>
                    <a href="{{ route('sinhvien.dangky.buoc1', $lichThi) }}" class="btn btn-primary fw-bold px-4">
                        🔄 Đăng ký lại ca thi này
                    </a>
                @else
                    <p class="mb-0 text-muted small">
                        Ca thi này hiện đã đóng đăng ký hoặc đã đủ chỉ tiêu thí sinh.
                    </p>
                @endif
            </div>
        @elseif ($dangky->trang_thai_thanh_toan === 'da_thanh_toan')
            <div class="alert alert-success shadow-sm mb-4">
                <strong>✅ Thanh toán thành công!</strong> Hồ sơ đăng ký dự thi của bạn đã được ghi nhận với trạng thái:
                <span class="badge bg-primary fs-6">{{ $dangky->nhanTrangThaiLabel() }}</span>.
            </div>
        @elseif ($dangky->trang_thai_thanh_toan === 'thanh_toan_that_bai')
            <div class="alert alert-danger d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                <span><strong>❌ Thanh toán thất bại.</strong> Hồ sơ chưa được gửi đi. Vui lòng thử lại.</span>
                <a href="{{ route('sinhvien.dangky.buoc3', $dangky) }}" class="btn btn-sm btn-primary">Thanh toán lại</a>
            </div>
        @elseif ($dangky->isSapHetHanThanhToan())
            <div class="alert alert-danger border-danger shadow-sm p-4 mb-4">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="fs-3">⚠️</span>
                    <h5 class="alert-heading fw-bold text-danger mb-0">CẢNH BÁO: SẮP HẾT HẠN NỘP LỆ PHÍ THI!</h5>
                </div>
                <p class="mb-2 text-dark">
                    Hồ sơ của bạn còn <strong>dưới 12 giờ</strong> trước khi hết hạn nộp tiền. Yêu cầu thanh toán muộn nhất trước:
                    <strong class="text-danger fs-6">{{ $dangky->hanThanhToan()->format('H:i \n\g\à\y d/m/Y') }}</strong>
                    (sau 2 ngày kể từ thời điểm đăng ký).
                </p>
                <p class="mb-3 small text-muted">
                    Sau thời điểm trên, hồ sơ sẽ tự động bị huỷ. Bạn vẫn có thể đăng ký lại nếu ca thi còn chỗ và còn thời gian đăng ký.
                </p>
                <a href="{{ route('sinhvien.dangky.buoc3', $dangky) }}" class="btn btn-danger fw-bold px-4">
                    💳 Thanh toán ngay bây giờ →
                </a>
            </div>
        @else
            <div class="alert alert-warning border-warning shadow-sm p-4 mb-4">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="fs-4">⏳</span>
                    <h5 class="alert-heading fw-bold text-dark mb-0">Hồ sơ đang chờ thanh toán lệ phí</h5>
                </div>
                <p class="mb-2 text-dark">
                    Yêu cầu bạn hoàn tất nộp lệ phí thi muộn nhất trước:
                    <strong class="text-primary fs-6">{{ $dangky->hanThanhToan()->format('H:i \n\g\à\y d/m/Y') }}</strong>
                    (sau 2 ngày kể từ thời điểm đăng ký).
                </p>
                <p class="mb-3 small text-muted">
                    Sau thời điểm trên, hồ sơ sẽ tự động bị huỷ. Bạn vẫn có thể đăng ký lại nếu ca thi còn chỗ và còn thời gian mở.
                </p>
                <a href="{{ route('sinhvien.dangky.buoc3', $dangky) }}" class="btn btn-primary fw-bold px-4">
                    Tiến hành thanh toán →
                </a>
            </div>
        @endif

        <div class="card mb-3 shadow-sm border-0">
            <div class="card-header-blue">Thông tin đăng ký</div>
            <table class="table table-kv mb-0">
                <tr><th>Mã đăng ký</th><td class="fw-bold text-primary font-monospace">{{ $dangky->ma_dang_ky }}</td></tr>
                <tr><th>Bài thi</th><td>{{ $dangky->lichThi->ten_ky_thi }}</td></tr>
                <tr><th>Thời gian thi</th><td>{{ optional($dangky->lichThi->ngay_thi)->format('d/m/Y') }} — {{ $dangky->lichThi->gio_bat_dau }}</td></tr>
                <tr><th>Địa điểm thi</th><td>{{ $dangky->lichThi->phong_thi }}</td></tr>
                <tr><th>Trạng thái hồ sơ</th><td><span class="badge bg-primary">{{ $dangky->nhanTrangThaiLabel() }}</span></td></tr>
            </table>
        </div>

        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header-blue">Thông tin thanh toán</div>
            <table class="table table-kv mb-0">
                <tr><th>Phương thức</th><td>{{ strtoupper($dangky->phuong_thuc_thanh_toan ?? 'N/A') }}</td></tr>
                <tr><th>Mã giao dịch</th><td>{{ $dangky->ma_giao_dich ?? '—' }}</td></tr>
                <tr><th>Số tiền</th><td class="fw-bold text-success">{{ number_format($dangky->so_tien) }}đ</td></tr>
                <tr><th>Thời gian thanh toán</th><td>{{ optional($dangky->ngay_thanh_toan)->format('d/m/Y H:i') ?? '—' }}</td></tr>
                <tr><th>Trạng thái thanh toán</th><td><span class="badge bg-success">{{ $dangky->nhanTrangThaiThanhToanLabel() }}</span></td></tr>
            </table>
        </div>

        <div class="d-flex justify-content-between mb-5">
            <a href="{{ route('sinhvien.dangky.cua-toi') }}" class="btn btn-outline-secondary">← Xem tất cả đăng ký của tôi</a>
            @if ($dangky->trang_thai === 'cho_bo_sung' && ! $dangky->isHetHanBoSungOnline())
                <a href="{{ route('sinhvien.dangky.bo-sung', $dangky) }}" class="btn btn-warning font-bold">Bổ sung hồ sơ ngay</a>
            @endif
        </div>
    </div>
</div>
@endsection
