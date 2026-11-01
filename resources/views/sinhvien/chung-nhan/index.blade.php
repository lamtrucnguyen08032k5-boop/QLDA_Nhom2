@extends('layouts.sinhvien')
@section('title', 'Chứng nhận & Chứng chỉ của tôi')
@section('content')

{{-- Tiêu đề trang --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold text-primary mb-1">
            <i class="bi bi-award me-2"></i>Chứng nhận &amp; Chứng chỉ của tôi
        </h4>
        <p class="text-muted small mb-0">
            Danh sách các bài thi đạt chuẩn đầu ra đủ điều kiện cấp chứng chỉ. Bạn có thể xem chứng nhận điện tử trực tuyến hoặc đăng ký nhận bản cứng.
        </p>
    </div>
</div>

{{-- Thông báo status nếu có --}}
@if (session('status'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
        <div>{{ session('status') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('info'))
    <div class="alert alert-info alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-info-circle-fill me-2 fs-5"></i>
        <div>{{ session('info') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Bảng danh sách bài thi đạt --}}
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-secondary">
                    <tr>
                        <th class="ps-3" style="width: 28%;">Bài thi / Kỳ thi</th>
                        <th class="text-center" style="width: 12%;">Kết quả thi</th>
                        <th style="width: 20%;">Mã yêu cầu &amp; Ngày gửi</th>
                        <th style="width: 22%;">Hình thức &amp; Trạng thái xử lý</th>
                        <th class="text-end pe-3" style="width: 18%;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($baiThisDat as $bt)
                    @php
                        $cn = $bt->chungNhan;
                        $lt = $bt->dangKy?->lichThi;
                    @endphp
                    <tr>
                        {{-- Cột 1: Thông tin bài thi --}}
                        <td class="ps-3">
                            <div class="fw-bold text-dark">{{ $lt->ten_ky_thi ?? ($bt->deThi?->ten_de ?? 'Bài thi') }}</div>
                            <div class="text-muted small mt-1">
                                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-0">
                                    {{ strtoupper($lt->loai_chung_chi ?? $bt->deThi?->loai_chung_chi ?? 'CHỨNG CHỈ') }}
                                </span>
                                @if($lt && $lt->ngay_thi)
                                    <span class="ms-1">📅 Ngày thi: {{ $lt->ngay_thi->format('d/m/Y') }}</span>
                                @endif
                            </div>
                        </td>

                        {{-- Cột 2: Điểm thi --}}
                        <td class="text-center">
                            <span class="badge bg-success-subtle text-success border border-success-subtle fs-6 px-3 py-1">
                                {{ (float)$bt->diem_tong }} / 100
                            </span>
                            <div class="small text-success fw-semibold mt-1">ĐẠT CHUẨN</div>
                        </td>

                        {{-- Cột 3: Mã yêu cầu & Ngày gửi --}}
                        <td>
                            @if ($cn)
                                <div class="font-monospace text-primary fw-bold fs-6">
                                    <i class="bi bi-ticket-perforated me-1"></i>{{ $cn->ma_yeu_cau_hien_thi }}
                                </div>
                                <div class="text-muted small">
                                    Gửi ngày: {{ $cn->created_at ? $cn->created_at->format('d/m/Y H:i') : '—' }}
                                </div>
                                @if ($cn->so_chung_nhan)
                                    <div class="small text-success mt-1">
                                        <i class="bi bi-patch-check-fill me-1"></i>Số CN: <strong>{{ $cn->so_chung_nhan }}</strong>
                                    </div>
                                @endif
                            @else
                                <span class="text-muted small fst-italic">
                                    <i class="bi bi-dash-circle me-1"></i>Chưa gửi yêu cầu bản cứng
                                </span>
                            @endif
                        </td>

                        {{-- Cột 4: Hình thức & Trạng thái xử lý --}}
                        <td>
                            @if ($cn)
                                <div class="mb-1">
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi {{ $cn->hinh_thuc_nhan === 'buu_dien' ? 'bi-truck' : 'bi-building' }} me-1 text-primary"></i>
                                        {{ $cn->hinh_thuc_nhan_text }}
                                    </span>
                                </div>
                                <div>
                                    @switch($cn->trang_thai)
                                        @case('cho_xu_ly')
                                        @case('cho_duyet')
                                            <span class="badge bg-secondary-subtle text-secondary border">
                                                <i class="bi bi-hourglass-split me-1"></i>Chờ tiếp nhận
                                            </span>
                                            @break
                                        @case('dang_xu_ly')
                                            <span class="badge bg-warning-subtle text-warning-emphasis border">
                                                <span class="spinner-grow spinner-grow-sm me-1" role="status" style="width: 0.4rem; height: 0.4rem;"></span>Đang xử lý / Đang in
                                            </span>
                                            @break
                                        @case('da_cap')
                                            <span class="badge bg-success-subtle text-success border">
                                                <i class="bi bi-check-circle-fill me-1"></i>Đã cấp bản cứng
                                            </span>
                                            @break
                                        @case('tu_choi')
                                            <span class="badge bg-danger-subtle text-danger border">
                                                <i class="bi bi-x-circle-fill me-1"></i>Từ chối cấp
                                            </span>
                                            @break
                                        @default
                                            <span class="badge bg-light text-dark">{{ $cn->trang_thai }}</span>
                                    @endswitch
                                </div>
                                <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none small mt-1" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $bt->id }}">
                                    <i class="bi bi-info-circle me-1"></i>Thông tin xử lý &amp; nhận
                                </button>
                            @else
                                <span class="badge bg-light text-secondary border">Chưa đăng ký</span>
                            @endif
                        </td>

                        {{-- Cột 5: Thao tác --}}
                        <td class="text-end pe-3">
                            <div class="d-flex flex-column flex-sm-row justify-content-end gap-1">
                                @if (!$cn)
                                    <a href="{{ route('sinhvien.chung-nhan.create', $bt) }}" class="btn btn-sm btn-primary shadow-sm text-nowrap" title="Đăng ký nhận chứng nhận bản cứng">
                                        <i class="bi bi-card-checklist me-1"></i>Đăng ký nhận
                                    </a>
                                @endif
                                <a href="{{ route('sinhvien.chung-nhan.show', $bt) }}" class="btn btn-sm btn-outline-success text-nowrap" title="Xem chứng nhận điện tử và tải xuống">
                                    <i class="bi bi-award me-1"></i>Xem trực tuyến
                                </a>
                            </div>
                        </td>
                    </tr>

                    {{-- Modal thông tin xử lý & nhận (nếu đã có đăng ký) --}}
                    @if ($cn)
                        <div class="modal fade" id="modalDetail{{ $bt->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header bg-primary text-white py-3">
                                        <h6 class="modal-title fw-bold">
                                            <i class="bi bi-file-earmark-text me-2"></i>Thông tin xử lý &amp; nhận chứng nhận
                                        </h6>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="alert alert-light border d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <small class="text-muted d-block">Mã yêu cầu</small>
                                                <span class="font-monospace fw-bold text-primary fs-6">{{ $cn->ma_yeu_cau_hien_thi }}</span>
                                            </div>
                                            <div class="text-end">
                                                <small class="text-muted d-block">Ngày gửi yêu cầu</small>
                                                <span class="fw-semibold text-dark">{{ $cn->created_at ? $cn->created_at->format('d/m/Y H:i') : '—' }}</span>
                                            </div>
                                        </div>

                                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                            <i class="bi bi-person-check me-2 text-primary"></i>Thông tin xác nhận thí sinh
                                        </h6>
                                        <div class="row g-2 small mb-3">
                                            <div class="col-6">
                                                <span class="text-muted d-block">Họ và tên:</span>
                                                <strong class="text-dark">{{ auth()->user()->name }}</strong>
                                            </div>
                                            <div class="col-6">
                                                <span class="text-muted d-block">Mã sinh viên:</span>
                                                <strong class="font-monospace">{{ auth()->user()->ma_so }}</strong>
                                            </div>
                                            <div class="col-6">
                                                <span class="text-muted d-block">Khóa / Lớp:</span>
                                                <span class="text-dark">{{ auth()->user()->khoa_hoc ?? 'K22' }} / {{ auth()->user()->lop ?? 'K22CLC1' }}</span>
                                            </div>
                                            <div class="col-6">
                                                <span class="text-muted d-block">Điểm bài thi:</span>
                                                <span class="badge bg-success">{{ (float)$bt->diem_tong }} / 100 điểm</span>
                                            </div>
                                        </div>

                                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                            <i class="bi bi-truck me-2 text-primary"></i>Hình thức &amp; Địa chỉ nhận
                                        </h6>
                                        <div class="mb-3 small">
                                            <div class="mb-2">
                                                <span class="text-muted d-block">Hình thức nhận:</span>
                                                <span class="badge bg-light text-dark border fs-6">
                                                    {{ $cn->hinh_thuc_nhan_text }}
                                                </span>
                                            </div>
                                            <div class="mb-2">
                                                <span class="text-muted d-block">Địa chỉ nhận / Nơi nhận:</span>
                                                <strong class="text-dark">{{ $cn->dia_chi_nhan }}</strong>
                                            </div>
                                            <div>
                                                <span class="text-muted d-block">Số điện thoại liên hệ:</span>
                                                <strong class="font-monospace text-primary">{{ $cn->so_dien_thoai }}</strong>
                                            </div>
                                        </div>

                                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                            <i class="bi bi-clock-history me-2 text-primary"></i>Tiến độ xử lý
                                        </h6>
                                        <div class="small">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Trạng thái:</span>
                                                <span>
                                                    @if($cn->trang_thai === 'da_cap')
                                                        <span class="badge bg-success">Đã cấp chứng nhận</span>
                                                    @elseif($cn->trang_thai === 'dang_xu_ly')
                                                        <span class="badge bg-warning text-dark">Đang xử lý / Đang in</span>
                                                    @elseif($cn->trang_thai === 'tu_choi')
                                                        <span class="badge bg-danger">Từ chối cấp</span>
                                                    @else
                                                        <span class="badge bg-secondary">Chờ tiếp nhận</span>
                                                    @endif
                                                </span>
                                            </div>
                                            @if($cn->so_chung_nhan)
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Số chứng nhận:</span>
                                                    <strong class="text-success font-monospace">{{ $cn->so_chung_nhan }}</strong>
                                                </div>
                                            @endif
                                            @if($cn->ngay_cap)
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Ngày cấp:</span>
                                                    <span class="text-dark">{{ $cn->ngay_cap->format('d/m/Y') }}</span>
                                                </div>
                                            @endif
                                            @if($cn->ghi_chu)
                                                <div class="p-2 bg-light rounded mt-2">
                                                    <span class="text-muted d-block">Ghi chú từ Phòng Khảo thí:</span>
                                                    <span class="text-dark">{{ $cn->ghi_chu }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light py-2">
                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                        <a href="{{ route('sinhvien.chung-nhan.show', $bt) }}" class="btn btn-sm btn-success">
                                            <i class="bi bi-award me-1"></i>Xem trực tuyến
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-award fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            <div class="fw-semibold">Hiện chưa có bài thi nào đạt ngưỡng cấp chứng chỉ.</div>
                            <small class="text-muted">Kết quả các bài thi đạt từ 50/100 điểm trở lên sẽ tự động hiển thị tại đây sau khi công bố.</small>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
