@extends('layouts.app')
@section('title', 'Kết quả thi - Quản lý công bố kết quả')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-1 text-primary fw-bold">
            <i class="bi bi-clipboard-data me-2"></i>Quản lý & Công bố kết quả thi
        </h4>
        <p class="text-muted small mb-0">Theo dõi kết quả thi và thực hiện công bố kết quả thi cho sinh viên theo từng kỳ thi & phòng thi</p>
    </div>
</div>

<!-- Thông báo phòng thi sẵn sàng công bố -->
@if (isset($soPhongSanSangCongBo) && $soPhongSanSangCongBo > 0)
    <div class="alert alert-success border-success d-flex align-items-center justify-content-between mb-4 shadow-sm rounded-3" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-bell-fill fs-3 text-success me-3"></i>
            <div>
                <h6 class="fw-bold mb-1 text-success-emphasis">
                    Thông báo: Có {{ $soPhongSanSangCongBo }} phòng thi đã chấm hoàn thành 100% bài thi!
                </h6>
                <div class="small text-dark">
                    Toàn bộ bài làm trong các phòng thi này đã được chấm xong và đạt đủ điều kiện để Admin thực hiện <strong>"Trả kết quả thi"</strong> cho sinh viên.
                </div>
            </div>
        </div>
        <a href="{{ route('admin.ketqua.index', ['trang_thai_cong_bo' => 'chua_cong_bo']) }}" class="btn btn-sm btn-success shadow-sm text-nowrap rounded-pill px-3">
            <i class="bi bi-list-check me-1"></i>Xem các phòng sẵn sàng
        </a>
    </div>
@endif

<!-- Thanh tìm kiếm & bộ lọc kỳ thi -->
<div class="card mb-4 border-0 shadow-sm rounded-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.ketqua.index') }}" class="row g-2 align-items-center">
            @if(isset($selectedKyThi) && $selectedKyThi)
                <input type="hidden" name="ky_thi" value="{{ $selectedKyThi }}">
            @endif
            <div class="{{ $mode === 'cards' ? 'col-md-9' : 'col-md-4' }}">
                <label class="form-label small fw-semibold text-muted mb-1">Tìm kiếm theo tên kỳ thi</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control" placeholder="Nhập tên kỳ thi..." value="{{ request('q') }}">
                </div>
            </div>
            @if($mode === 'list')
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Ngày thi</label>
                    <input type="date" name="ngay_thi" class="form-control form-control-sm" value="{{ request('ngay_thi') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Phòng thi</label>
                    <input type="text" name="phong_thi" class="form-control form-control-sm" placeholder="VD: P301..." value="{{ request('phong_thi') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Trạng thái công bố</label>
                    <select name="trang_thai_cong_bo" class="form-select form-select-sm">
                        <option value="">-- Tất cả --</option>
                        <option value="chua_cong_bo" {{ request('trang_thai_cong_bo') === 'chua_cong_bo' ? 'selected' : '' }}>Chưa công bố</option>
                        <option value="da_cong_bo" {{ request('trang_thai_cong_bo') === 'da_cong_bo' ? 'selected' : '' }}>Đã công bố</option>
                    </select>
                </div>
            @endif
            <div class="{{ $mode === 'cards' ? 'col-md-3' : 'col-md-2' }} d-flex align-items-end gap-1" style="height: 58px;">
                <button type="submit" class="btn btn-sm btn-primary flex-grow-1" title="Tìm kiếm">
                    <i class="bi bi-filter me-1"></i>Lọc
                </button>
                <a href="{{ route('admin.ketqua.index') }}" class="btn btn-sm btn-outline-secondary" title="Xóa tất cả bộ lọc">
                    <i class="bi bi-x-circle me-1"></i>Xóa lọc
                </a>
            </div>
        </form>
    </div>
</div>

@if ($mode === 'cards')
    <!-- CHẾ ĐỘ XEM CÁC THẺ KỲ THI -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-dark mb-0">
            <i class="bi bi-grid-fill me-2 text-primary"></i>Danh sách các Kỳ thi ({{ count($kyThiCards) }})
        </h5>
        <span class="text-muted small">Nhấn vào thẻ Kỳ thi để xem danh sách lịch thi & ca thi chi tiết</span>
    </div>

    <div class="row g-4">
        @forelse ($kyThiCards as $card)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm card-hover rounded-3 overflow-hidden transition-all" style="border-top: 4px solid #0d6efd !important;">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 small">
                                {{ strtoupper($card->loai_chung_chi ?: 'KỲ THI') }}
                            </span>
                            @if ($card->trang_thai_overall === 'da_cong_bo')
                                <span class="badge bg-success text-white px-2 py-1 small">
                                    <i class="bi bi-check-circle-fill me-1"></i>Đã công bố
                                </span>
                            @elseif ($card->trang_thai_overall === 'san_sang_cong_bo')
                                <span class="badge bg-success-subtle text-success border border-success px-2 py-1 small fw-bold">
                                    <i class="bi bi-bell-fill me-1 text-warning"></i>Sẵn sàng trả KQ
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 small">
                                    <i class="bi bi-hourglass-split me-1"></i>Chưa công bố
                                </span>
                            @endif
                        </div>

                        <h5 class="card-title fw-bold text-dark mb-2 line-clamp-2" style="min-height: 2.8rem;">
                            {{ $card->ten_ky_thi }}
                        </h5>
                        <p class="text-muted small mb-3">
                            <i class="bi bi-building me-1"></i>Khoa / Đơn vị: <strong>{{ $card->ten_khoa }}</strong>
                        </p>

                        @if ($card->ngay_thi_str)
                            <div class="small text-muted mb-3 bg-light p-2 rounded">
                                <i class="bi bi-calendar3 me-1 text-primary"></i><strong>Ngày thi:</strong> {{ $card->ngay_thi_str }}
                            </div>
                        @endif

                        <div class="row g-2 mb-4 mt-auto">
                            <div class="col-6">
                                <div class="p-2 border rounded text-center bg-light">
                                    <span class="d-block text-muted small" style="font-size:0.75rem;">Số ca thi / Phòng</span>
                                    <strong class="fs-6 text-dark"><i class="bi bi-door-closed me-1 text-primary"></i>{{ $card->so_ca_thi }} ca</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 border rounded text-center bg-light">
                                    <span class="d-block text-muted small" style="font-size:0.75rem;">Số SV dự thi</span>
                                    <strong class="fs-6 text-dark"><i class="bi bi-people me-1 text-success"></i>{{ $card->tong_sinh_vien }} SV</strong>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('admin.ketqua.index', ['ky_thi' => $card->ten_ky_thi]) }}"
                           class="btn btn-outline-primary w-100 rounded-pill fw-semibold shadow-sm text-nowrap">
                            <i class="bi bi-list-ul me-1"></i>Xem các lịch thi & ca thi
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted card border-0 shadow-sm">
                <i class="bi bi-inbox fs-1 text-secondary opacity-50 d-block mb-2"></i>
                <div class="fw-semibold">Không tìm thấy kỳ thi nào.</div>
            </div>
        @endforelse
    </div>

@else
    <!-- CHẾ ĐỘ XEM DANH SÁCH LỊCH THI CỦA KỲ THI ĐÃ CHỌN -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('admin.ketqua.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm">
            <i class="bi bi-arrow-left me-1"></i>Quay lại danh sách các Thẻ Kỳ thi
        </a>
        @if ($selectedKyThi)
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 fs-6">
                <i class="bi bi-journal-bookmark me-1"></i>Kỳ thi: {{ $selectedKyThi }}
            </span>
        @endif
    </div>

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-primary">
                <i class="bi bi-calendar-range me-2"></i>Danh sách các lịch thi / ca thi
            </h6>
            <span class="badge bg-light text-dark border">{{ $lichThis->total() }} ca thi</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 28%;">Bài thi / Lịch thi</th>
                            <th style="width: 12%;">Ngày thi</th>
                            <th style="width: 12%;">Ca thi (Giờ thi)</th>
                            <th style="width: 12%;">Phòng thi</th>
                            <th class="text-center" style="width: 10%;">Số SV dự thi</th>
                            <th class="text-center" style="width: 10%;">Đã chấm xong</th>
                            <th style="width: 10%;">Tiến độ chấm</th>
                            <th class="text-center" style="width: 10%;">Trạng thái</th>
                            <th class="text-end pe-3" style="width: 9%;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($lichThis as $lt)
                        @php
                            $tongBai = $lt->tong_bai_thi;
                            $daCham = $lt->tong_bai_da_cham;
                            $phanTramCham = $tongBai > 0 ? round(($daCham / $tongBai) * 100) : 0;
                            $isDaCongBo = ($lt->trang_thai_cong_bo === 'da_cong_bo');
                        @endphp
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                    <span class="badge {{ $lt->loai_chung_chi === 'tienganh' ? 'bg-purple' : 'bg-primary' }} text-white px-2" style="{{ $lt->loai_chung_chi === 'tienganh' ? 'background-color: #6f42c1 !important;' : '' }}">
                                        {{ $lt->loai_chung_chi === 'tienganh' ? 'Tiếng Anh' : 'CNTT' }}
                                    </span>
                                    <span>{{ $lt->phong_thi }}</span>
                                </div>
                                <div class="small text-muted mt-1">
                                    <i class="bi bi-building me-1"></i>{{ optional($lt->khoa)->ten_khoa ?? 'Khảo thí' }}
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ optional($lt->ngay_thi)->format('d/m/Y') }}</div>
                                <small class="text-muted">{{ optional($lt->ngay_thi)->locale('vi')->diffForHumans() }}</small>
                            </td>
                            <td>
                                @if($lt->gio_bat_dau)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-6">
                                        <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($lt->gio_bat_dau)->format('H:i') }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    <i class="bi bi-door-closed me-1 text-secondary"></i>{{ $lt->phong_thi }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold text-dark fs-6">{{ $tongBai }}</span>
                                @if($lt->tong_sinh_vien > $tongBai)
                                    <small class="text-muted d-block" title="Tổng số sinh viên được duyệt đăng ký">({{ $lt->tong_sinh_vien }} ĐK)</small>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="fw-semibold text-success fs-6">{{ $daCham }}</span>
                                <span class="text-muted">/ {{ $tongBai }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 7px;">
                                        <div class="progress-bar {{ $phanTramCham === 100 ? 'bg-success' : ($phanTramCham > 0 ? 'bg-info' : 'bg-secondary') }}"
                                             role="progressbar" style="width: {{ $phanTramCham }}%;"
                                             aria-valuenow="{{ $phanTramCham }}" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                    <span class="small fw-semibold text-muted">{{ $phanTramCham }}%</span>
                                </div>
                                <div class="small mt-1 text-muted" style="font-size: 0.75rem;">
                                    @if($tongBai === 0)
                                        <span class="text-secondary">Chưa có bài</span>
                                    @elseif($phanTramCham === 100)
                                        <span class="text-success"><i class="bi bi-check2 me-1"></i>Hoàn thành</span>
                                    @elseif($daCham > 0)
                                        <span class="text-info">Đang chấm</span>
                                    @else
                                        <span class="text-warning">Chưa chấm</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                @if ($isDaCongBo)
                                    <span class="badge bg-success text-white px-2 py-1">
                                        <i class="bi bi-check-circle-fill me-1"></i>Đã công bố
                                    </span>
                                    @if($lt->ngay_cong_bo)
                                        <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                                            {{ $lt->ngay_cong_bo->format('d/m/Y') }}
                                        </div>
                                    @endif
                                @elseif ($tongBai > 0 && $phanTramCham === 100)
                                    <span class="badge bg-success-subtle text-success border border-success px-2 py-1 shadow-sm fw-bold">
                                        <i class="bi bi-bell-fill me-1 text-warning"></i>Sẵn sàng trả KQ
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                        <i class="bi bi-hourglass-split me-1"></i>Chưa công bố
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <a href="{{ route('admin.ketqua.show', $lt) }}" class="btn btn-sm btn-outline-primary shadow-sm" title="Xem chi tiết phòng thi và quản lý kết quả">
                                    <i class="bi bi-eye me-1"></i>Xem chi tiết
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary opacity-50"></i>
                                <div>Không tìm thấy lịch thi nào phù hợp với điều kiện tra cứu.</div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($lichThis->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                <div class="d-flex justify-content-end">
                    {{ $lichThis->links() }}
                </div>
            </div>
        @endif
    </div>
@endif

@endsection
