@extends('layouts.sinhvien')
@section('title', 'Cổng thanh toán mô phỏng')
@section('content')

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="alert alert-warning mb-4">
            <i class="bi bi-info-circle me-1"></i>
            Chưa cấu hình tài khoản merchant VNPAY thật trong file môi trường (<code>.env</code>).
            Đây là cổng thanh toán <strong>mô phỏng</strong> để kiểm thử và demo toàn diện quy trình thanh toán lệ phí phúc khảo ({{ strtoupper($phuckhao->phuong_thuc_thanh_toan) }}).
        </div>

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="card-title fw-bold mb-0">
                    <i class="bi bi-credit-card me-2"></i>Xác nhận thanh toán lệ phí phúc khảo
                </h5>
            </div>
            <div class="card-body p-4">
                <table class="table table-borderless mb-4">
                    <tr>
                        <th class="text-muted ps-0" style="width: 160px;">Mã yêu cầu:</th>
                        <td class="font-monospace fw-bold text-primary fs-6">{{ $phuckhao->ma_phuc_khao_hien_thi }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted ps-0">Mã giao dịch:</th>
                        <td class="font-monospace text-secondary">{{ $phuckhao->ma_giao_dich }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted ps-0">Bài thi:</th>
                        <td>{{ $phuckhao->baiThi?->dangKy?->lichThi?->ten_ky_thi ?? ($phuckhao->baiThi?->deThi?->ten_de ?? 'Bài thi') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted ps-0">Phương thức:</th>
                        <td><span class="badge bg-light text-dark border">{{ strtoupper($phuckhao->phuong_thuc_thanh_toan) }}</span></td>
                    </tr>
                    <tr>
                        <th class="text-muted ps-0">Số tiền:</th>
                        <td class="fw-bold text-success fs-5">{{ number_format($phuckhao->le_phi) }} VNĐ</td>
                    </tr>
                </table>

                <div class="d-flex gap-2">
                    <form method="POST" action="{{ route('sinhvien.phuc-khao.thanh-toan.mophong.xuly', $phuckhao) }}" class="flex-grow-1">
                        @csrf
                        <input type="hidden" name="ket_qua" value="thanh_cong">
                        <button type="submit" class="btn btn-success w-100 py-2 fw-semibold shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Mô phỏng thanh toán thành công
                        </button>
                    </form>
                    <form method="POST" action="{{ route('sinhvien.phuc-khao.thanh-toan.mophong.xuly', $phuckhao) }}" class="flex-grow-1">
                        @csrf
                        <input type="hidden" name="ket_qua" value="that_bai">
                        <button type="submit" class="btn btn-outline-danger w-100 py-2 fw-semibold">
                            <i class="bi bi-x-circle me-1"></i> Mô phỏng thất bại / Hủy
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

