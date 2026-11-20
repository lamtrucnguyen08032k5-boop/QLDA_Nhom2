@extends('layouts.sinhvien')
@section('title', 'Thanh toán lệ phí phúc khảo')
@section('content')

<div class="mb-3">
    <a href="{{ route('sinhvien.phuc-khao.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Về danh sách phúc khảo của tôi
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        {{-- THÔNG BÁO LỖI / TRẠNG THÁI --}}
        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show d-flex align-items-center mb-4">
                <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                <div>{{ session('info') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                <i class="bi bi-credit-card-2-front text-primary fs-3"></i>
                <div>
                    <h5 class="fw-bold text-dark mb-0">Thanh toán lệ phí phúc khảo</h5>
                    <span class="text-muted small">Vui lòng chọn phương thức thanh toán để hệ thống ghi nhận yêu cầu chính thức</span>
                </div>
            </div>

            <div class="card-body p-4">
                {{-- BẢNG TÓM TẮT ĐƠN PHÚC KHẢO --}}
                <div class="bg-light p-3 rounded-3 mb-4 border">
                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-receipt me-2"></i>Thông tin đơn phúc khảo</h6>
                    <div class="row g-2 small">
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Mã yêu cầu tạm:</span>
                            <span class="font-monospace fw-bold text-primary fs-6">{{ $phuckhao->ma_phuc_khao_hien_thi }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Thí sinh:</span>
                            <strong class="text-dark">{{ $phuckhao->sinhVien->name }}</strong> ({{ $phuckhao->sinhVien->ma_so }})
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Kỳ thi / Bài thi:</span>
                            <span class="fw-semibold text-dark">{{ $phuckhao->baiThi?->dangKy?->lichThi?->ten_ky_thi ?? ($phuckhao->baiThi?->deThi?->ten_de ?? 'Bài thi') }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Điểm bài thi hiện tại:</span>
                            <span class="badge bg-secondary fs-6">{{ (float)$phuckhao->diem_truoc }} điểm</span>
                        </div>
                        <div class="col-12 mt-2 pt-2 border-top">
                            <span class="text-muted d-block">Lý do phúc khảo:</span>
                            <div class="p-2 bg-white rounded border mt-1 text-dark">{{ $phuckhao->ly_do }}</div>
                        </div>
                        @if ($phuckhao->file_minh_chung)
                            <div class="col-12 mt-2">
                                <span class="text-muted">Minh chứng đính kèm:</span>
                                <a href="{{ asset('storage/' . $phuckhao->file_minh_chung) }}" target="_blank" class="ms-1 text-primary text-decoration-none">
                                    <i class="bi bi-paperclip me-1"></i>Xem tệp đính kèm
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- SỐ TIỀN THANH TOÁN --}}
                <div class="d-flex justify-content-between align-items-center p-3 rounded-3 bg-primary bg-opacity-10 border border-primary border-opacity-25 mb-4">
                    <div>
                        <div class="text-uppercase small fw-bold text-primary">Tổng số tiền cần thanh toán</div>
                        <small class="text-muted">Lệ phí phúc khảo theo quy chế thi của Học viện Ngân hàng</small>
                    </div>
                    <div class="text-end">
                        <span class="display-6 fw-bold text-primary">{{ number_format($phuckhao->le_phi) }}đ</span>
                    </div>
                </div>

                {{-- FORM CHỌN PHƯƠNG THỨC THANH TOÁN --}}
                <form method="POST" action="{{ route('sinhvien.phuc-khao.thanh-toan.khoitao', $phuckhao->id) }}">
                    @csrf

                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-wallet2 me-2 text-primary"></i>Chọn phương thức thanh toán</h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="card h-100 p-3 border cursor-pointer payment-option" style="cursor: pointer;">
                                <div class="d-flex align-items-center gap-3">
                                    <input type="radio" name="phuong_thuc_thanh_toan" value="vnpay" class="form-check-input mt-0" checked>
                                    <div>
                                        <div class="fw-bold text-dark">VNPAY-QR / Ví điện tử</div>
                                        <small class="text-muted">Quét mã QR qua ứng dụng ngân hàng hoặc ví VNPAY</small>
                                    </div>
                                </div>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <label class="card h-100 p-3 border cursor-pointer payment-option" style="cursor: pointer;">
                                <div class="d-flex align-items-center gap-3">
                                    <input type="radio" name="phuong_thuc_thanh_toan" value="napas" class="form-check-input mt-0">
                                    <div>
                                        <div class="fw-bold text-dark">Thẻ ATM / Tài khoản nội địa (NAPAS)</div>
                                        <small class="text-muted">Hỗ trợ tất cả ngân hàng nội địa tại Việt Nam</small>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('sinhvien.phuc-khao.index') }}" class="btn btn-outline-secondary px-4 fw-semibold">
                            Để sau (Chưa thanh toán)
                        </a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                            <i class="bi bi-shield-lock-fill me-1"></i> Tiến hành thanh toán {{ number_format($phuckhao->le_phi) }}đ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

