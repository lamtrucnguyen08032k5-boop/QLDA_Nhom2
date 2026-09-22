@extends('layouts.app')
@section('title', 'Chi tiết kết quả phòng thi ' . $lichthi->phong_thi)
@section('content')

<div class="mb-3">
    <a href="{{ route('admin.ketqua.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
        <i class="bi bi-arrow-left me-1"></i>Quay lại danh sách
    </a>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4 class="text-primary fw-bold mb-1">
                {{ $lichthi->ten_ky_thi }} — Phòng thi: <span class="badge bg-primary fs-6">{{ $lichthi->phong_thi }}</span>
            </h4>
            <div class="text-muted small">
                <span><i class="bi bi-calendar3 me-1"></i>Ngày thi: <strong>{{ optional($lichthi->ngay_thi)->format('d/m/Y') }}</strong></span>
                <span class="mx-2">•</span>
                <span><i class="bi bi-clock me-1"></i>Ca thi: <strong>{{ $lichthi->ma_ca_thi }}</strong> ({{ $lichthi->gio_bat_dau }})</span>
                @if($lichthi->deThi)
                    <span class="mx-2">•</span>
                    <span><i class="bi bi-file-earmark-text me-1"></i>Đề thi: <strong>{{ $lichthi->deThi->ten_de }}</strong></span>
                @endif
                @if($lichthi->trang_thai_cong_bo === 'da_cong_bo')
                    <span class="mx-2">•</span>
                    <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Công bố bởi: <strong>{{ optional($lichthi->nguoiCongBo)->name ?? 'Admin' }}</strong> ({{ optional($lichthi->ngay_cong_bo)->format('H:i d/m/Y') }})</span>
                @endif
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if ($daCongBo)
                {{-- Luồng phụ 3: Kết quả đã được công bố --}}
                <button type="button" class="btn btn-success btn-sm px-3 shadow-sm" disabled>
                    <i class="bi bi-check2-all me-1"></i>Đã công bố kết quả
                </button>
            @elseif ($tatCaDaCham)
                {{-- Luồng chính: Tất cả bài đã chấm xong -> Kích hoạt nút Trả kết quả thi --}}
                <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalXacNhanTraKetQua">
                    <i class="bi bi-send-check me-1"></i>Trả kết quả thi
                </button>
            @else
                {{-- Luồng phụ 1: Chưa hoàn thành chấm tất cả bài -> Nút bị vô hiệu hóa --}}
                <button type="button" class="btn btn-secondary btn-sm px-3" disabled title="Chưa thể trả kết quả do vẫn còn bài chưa chấm xong">
                    <i class="bi bi-lock me-1"></i>Trả kết quả thi
                </button>
            @endif
        </div>
    </div>
</div>

{{-- Thông báo hoàn thành chấm điểm tất cả bài thi --}}
@if ($tatCaDaCham && ! $daCongBo)
    <div class="alert alert-success border-success d-flex align-items-center justify-content-between mb-4 shadow-sm" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-bell-fill fs-3 text-success me-3"></i>
            <div>
                <h6 class="fw-bold mb-1 text-success-emphasis">
                    Thông báo hoàn thành chấm: Toàn bộ bài làm trong phòng thi này đã được chấm xong ({{ $soBaiDaCham }}/{{ $tongSoBai }} bài)!
                </h6>
                <div class="small text-dark">
                    Phòng thi đã đủ điều kiện công bố. Hệ thống đã kích hoạt nút <strong>"Trả kết quả thi"</strong> để bạn trả điểm đồng loạt cho toàn bộ sinh viên.
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-success btn-sm ms-3 shadow-sm text-nowrap" data-bs-toggle="modal" data-bs-target="#modalXacNhanTraKetQua">
            <i class="bi bi-send-check me-1"></i>Trả kết quả ngay
        </button>
    </div>
@endif

{{-- Cảnh báo nếu chưa hoàn thành chấm --}}
@if (! $daCongBo && ! $tatCaDaCham)
    <div class="alert alert-warning border-warning d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-warning"></i>
        <div class="flex-grow-1">
            <strong>Chưa thể trả kết quả. Vẫn còn bài thi chưa hoàn thành chấm.</strong>
            <div class="small mt-1">
                Hiện còn <strong>{{ $soBaiChuaCham + $soBaiDangCham }}</strong> bài thi chưa hoàn thành chấm trong phòng thi này.
                Nút "Trả kết quả thi" sẽ tự động được kích hoạt khi toàn bộ bài làm đều đạt trạng thái "Đã chấm".
            </div>
        </div>
        @if($baiChuaHoanThanh->isNotEmpty())
            <a href="#danhSachBaiChuaCham" class="btn btn-warning btn-sm ms-2 text-nowrap" data-bs-toggle="collapse">
                Xem bài chưa chấm ({{ $baiChuaHoanThanh->count() }})
            </a>
        @endif
    </div>

    @if($baiChuaHoanThanh->isNotEmpty())
        <div class="collapse mb-4" id="danhSachBaiChuaCham">
            <div class="card border-warning shadow-sm">
                <div class="card-header bg-warning-subtle text-warning-emphasis fw-semibold py-2">
                    <i class="bi bi-list-task me-1"></i>Danh sách bài thi chưa hoàn thành chấm
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Mã bài thi</th>
                                    <th>Mã SV</th>
                                    <th>Họ và tên</th>
                                    <th>Trạng thái chấm</th>
                                    <th>Điểm TN hiện tại</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($baiChuaHoanThanh as $bch)
                                    <tr>
                                        <td class="ps-3"><code>{{ $bch->ma_bai_thi_hien_thi }}</code></td>
                                        <td>{{ $bch->dangKy?->sinhVien?->ma_so ?? '—' }}</td>
                                        <td class="fw-semibold">{{ $bch->dangKy?->sinhVien?->name ?? '—' }}</td>
                                        <td>
                                            @if($bch->trang_thai === 'dang_cham')
                                                <span class="badge bg-info text-dark">Đang chấm</span>
                                            @else
                                                <span class="badge bg-secondary">Chưa chấm</span>
                                            @endif
                                        </td>
                                        <td>{{ $bch->diem_tu_dong }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif

@if ($daCongBo)
    <div class="alert alert-success border-success d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
        <div>
            <strong>Phòng thi đã được công bố kết quả.</strong>
            <div class="small mt-1">
                Toàn bộ sinh viên trong phòng thi này đã có thể tra cứu điểm thi và gửi phúc khảo/nhận chứng nhận trên tài khoản cá nhân.
            </div>
        </div>
    </div>
@endif

<!-- Khối thống kê 4 số lượng -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm border-start border-primary border-4">
            <div class="card-body py-3">
                <div class="text-muted small text-uppercase fw-semibold">Tổng số bài làm</div>
                <div class="fs-4 fw-bold text-dark mt-1">{{ $tongSoBai }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm border-start border-secondary border-4">
            <div class="card-body py-3">
                <div class="text-muted small text-uppercase fw-semibold">Số bài chưa chấm</div>
                <div class="fs-4 fw-bold text-secondary mt-1">{{ $soBaiChuaCham }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm border-start border-info border-4">
            <div class="card-body py-3">
                <div class="text-muted small text-uppercase fw-semibold">Số bài đang chấm</div>
                <div class="fs-4 fw-bold text-info mt-1">{{ $soBaiDangCham }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm border-start border-success border-4">
            <div class="card-body py-3">
                <div class="text-muted small text-uppercase fw-semibold">Số bài đã chấm</div>
                <div class="fs-4 fw-bold text-success mt-1">{{ $soBaiDaCham }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Thanh tìm kiếm & bộ lọc bài làm của sinh viên -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.ketqua.show', $lichthi) }}" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control"
                           placeholder="Tra cứu theo Họ và tên, Mã sinh viên, Số CCCD, Mã bài thi..."
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="trang_thai_bai" class="form-select form-select-sm">
                    <option value="">-- Tất cả trạng thái bài làm --</option>
                    <option value="chua_cham" {{ request('trang_thai_bai') === 'chua_cham' ? 'selected' : '' }}>Chưa chấm</option>
                    <option value="dang_cham" {{ request('trang_thai_bai') === 'dang_cham' ? 'selected' : '' }}>Đang chấm</option>
                    <option value="da_cham" {{ request('trang_thai_bai') === 'da_cham' ? 'selected' : '' }}>Đã chấm</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary flex-grow-1">
                    <i class="bi bi-search me-1"></i>Tra cứu
                </button>
                <a href="{{ route('admin.ketqua.show', $lichthi) }}" class="btn btn-sm btn-outline-secondary" title="Xóa bộ lọc tìm kiếm">
                    <i class="bi bi-x-circle me-1"></i>Xóa lọc
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Danh sách bài làm của sinh viên -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
        <h6 class="mb-0 fw-bold text-dark">
            <i class="bi bi-people me-1"></i>Danh sách bài làm sinh viên
            <span class="badge bg-light text-secondary border ms-1">{{ $baiThis->count() }} bài</span>
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 13%;">Mã bài thi</th>
                        <th style="width: 16%;">Họ và tên</th>
                        <th style="width: 10%;">Mã SV</th>
                        <th style="width: 11%;">Số CCCD</th>
                        <th class="text-center" style="width: 10%;">Trạng thái chấm</th>
                        <th class="text-center" style="width: 8%;">Điểm TN</th>
                        <th class="text-center" style="width: 8%;">Điểm TL</th>
                        <th class="text-center" style="width: 8%;">Tổng điểm</th>
                        <th class="text-center" style="width: 8%;">Công bố</th>
                        <th class="text-center pe-3" style="width: 8%;">Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($baiThis as $bt)
                    @php
                        $sv = $bt->dangKy?->sinhVien;
                        $isBtDaCongBo = ($bt->trang_thai === 'da_cong_bo');
                    @endphp
                    <tr>
                        <td class="ps-3">
                            <span class="font-monospace fw-bold text-primary">{{ $bt->ma_bai_thi_hien_thi }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $sv->name ?? '—' }}</div>
                            <small class="text-muted">{{ $sv->email ?? '' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $sv->ma_so ?? '—' }}</span>
                        </td>
                        <td>
                            <span class="small text-muted">{{ $bt->dangKy->so_cccd ?? '—' }}</span>
                        </td>
                        <td class="text-center">
                            @if ($bt->cham_xong || in_array($bt->trang_thai, ['da_cham', 'da_cong_bo']))
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="bi bi-check2 me-1"></i>Đã chấm
                                </span>
                            @elseif ($bt->trang_thai === 'dang_cham')
                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-1">
                                    <i class="bi bi-pencil-square me-1"></i>Đang chấm
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                    <i class="bi bi-hourglass me-1"></i>Chưa chấm
                                </span>
                            @endif
                        </td>
                        <td class="text-center fw-semibold text-secondary">
                            {{ $bt->diem_tu_dong !== null ? (float)$bt->diem_tu_dong : '—' }}
                        </td>
                        <td class="text-center fw-semibold text-secondary">
                            {{ $bt->diem_cham_tay !== null ? (float)$bt->diem_cham_tay : '—' }}
                        </td>
                        <td class="text-center">
                            @if ($bt->diem_tong !== null)
                                <span class="fs-6 fw-bold {{ $bt->is_dat ? 'text-success' : 'text-danger' }}">
                                    {{ (float)$bt->diem_tong }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($isBtDaCongBo)
                                <span class="badge bg-success text-white px-2 py-1">
                                    <i class="bi bi-check-circle me-1"></i>Đã công bố
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                    Chưa công bố
                                </span>
                            @endif
                        </td>
                        <td class="text-center pe-3">
                            <a href="{{ route('admin.ketqua.baithi', $bt) }}"
                               class="btn btn-xs btn-outline-info px-2 py-1"
                               title="Xem chi tiết bài làm: ai chấm, điểm từng câu"
                               style="font-size:0.78rem;">
                                <i class="bi bi-eye me-1"></i>Xem bài
                            </a>
                        </td>
                    </tr>
                @empty
                    {{-- Luồng phụ 2: Tra cứu không có kết quả --}}
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="bi bi-search fs-2 d-block mb-2 text-secondary"></i>
                            <div class="fw-semibold">Không tìm thấy bài thi của sinh viên phù hợp.</div>
                            <small class="text-muted">Vui lòng kiểm tra lại từ khóa tìm kiếm hoặc bỏ chọn các điều kiện lọc.</small>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Popup xác nhận Trả kết quả thi -->
<div class="modal fade" id="modalXacNhanTraKetQua" tabindex="-1" aria-labelledby="modalXacNhanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-primary" id="modalXacNhanLabel">
                    <i class="bi bi-question-circle-fill me-2 text-primary"></i>Xác nhận trả kết quả thi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="fs-6 mb-3">
                    Bạn có chắc chắn muốn trả kết quả thi cho toàn bộ sinh viên trong phòng thi này không?
                </p>
                <div class="bg-light p-3 rounded-2 small text-muted">
                    <div><i class="bi bi-info-circle me-1"></i><strong>Thông tin phòng thi:</strong> {{ $lichthi->phong_thi }} (Ca: {{ $lichthi->ma_ca_thi }})</div>
                    <div><i class="bi bi-calendar-event me-1"></i><strong>Kỳ thi:</strong> {{ $lichthi->ten_ky_thi }}</div>
                    <div><i class="bi bi-people me-1"></i><strong>Số lượng bài thi công bố:</strong> {{ $tongSoBai }} bài thi</div>
                    <hr class="my-2">
                    <span class="text-danger">* Sau khi công bố, sinh viên sẽ có thể xem điểm chi tiết trên tài khoản cá nhân và gửi yêu cầu phúc khảo nếu có nguyện vọng.</span>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                {{-- Luồng phụ 4: Admin hủy xác nhận -> đóng popup --}}
                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">
                    Hủy
                </button>
                <form method="POST" action="{{ route('admin.ketqua.congbo', $lichthi) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">
                        <i class="bi bi-check-circle me-1"></i>Xác nhận
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
