@extends('layouts.app')
@section('title', 'Danh sách đăng ký thi')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-1 text-primary fw-bold">
            <i class="bi bi-card-checklist me-2"></i>Danh sách đăng ký thi của sinh viên
        </h4>
        <p class="text-muted small mb-0">Quản lý hồ sơ đăng ký dự thi của sinh viên theo từng Kỳ thi và Lịch thi</p>
    </div>
</div>

<!-- Thanh tìm kiếm & bộ lọc -->
<div class="card mb-4 border-0 shadow-sm rounded-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.dangky.danhsach') }}" class="row g-2 align-items-center">
            @if(isset($selectedKyThi) && $selectedKyThi)
                <input type="hidden" name="ky_thi" value="{{ $selectedKyThi }}">
            @endif
            <div class="{{ $mode === 'cards' ? 'col-md-9' : 'col-md-6' }}">
                <label class="form-label small fw-semibold text-muted mb-1">Tìm kiếm theo tên kỳ thi</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control" placeholder="Nhập tên kỳ thi..." value="{{ request('q') }}">
                </div>
            </div>
            @if($mode === 'list')
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Trạng thái ca thi</label>
                    <select name="trang_thai" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="dang_mo_dang_ky" {{ request('trang_thai') === 'dang_mo_dang_ky' ? 'selected' : '' }}>Đang mở đăng ký</option>
                        <option value="da_dong_dang_ky" {{ request('trang_thai') === 'da_dong_dang_ky' ? 'selected' : '' }}>Đã đóng đăng ký</option>
                        <option value="dang_thi" {{ request('trang_thai') === 'dang_thi' ? 'selected' : '' }}>Đang thi</option>
                        <option value="da_ket_thuc" {{ request('trang_thai') === 'da_ket_thuc' ? 'selected' : '' }}>Đã kết thúc</option>
                    </select>
                </div>
            @endif
            <div class="{{ $mode === 'cards' ? 'col-md-3' : 'col-md-3' }} d-flex align-items-end gap-1" style="height: 58px;">
                <button type="submit" class="btn btn-sm btn-primary flex-grow-1">
                    <i class="bi bi-filter me-1"></i>Lọc
                </button>
                <a href="{{ route('admin.dangky.danhsach') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i>Xóa lọc
                </a>
            </div>
        </form>
    </div>
</div>

@if ($mode === 'cards')
    <!-- CHẾ ĐỘ XEM CÁC THẺ KỲ THI DÀNH CHO ĐĂNG KÝ THI -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-dark mb-0">
            <i class="bi bi-grid-fill me-2 text-primary"></i>Danh sách các Kỳ thi ({{ count($kyThiCards) }})
        </h5>
        <span class="text-muted small">Nhấn vào thẻ Kỳ thi để xem danh sách lịch thi & hồ sơ đăng ký chi tiết</span>
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
                            @if ($card->so_ho_so_cho_duyet > 0)
                                <span class="badge bg-warning text-dark px-2 py-1 small fw-bold">
                                    <i class="bi bi-exclamation-circle me-1"></i>{{ $card->so_ho_so_cho_duyet }} hồ sơ chờ duyệt
                                </span>
                            @else
                                <span class="badge bg-light text-muted border px-2 py-1 small">
                                    0 hồ sơ chờ duyệt
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
                                    <span class="d-block text-muted small" style="font-size:0.75rem;">Số ca / Lịch thi</span>
                                    <strong class="fs-6 text-dark"><i class="bi bi-door-closed me-1 text-primary"></i>{{ $card->so_ca_thi }} ca</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 border rounded text-center bg-light">
                                    <span class="d-block text-muted small" style="font-size:0.75rem;">Tổng SV đăng ký</span>
                                    <strong class="fs-6 text-dark"><i class="bi bi-people me-1 text-success"></i>{{ $card->tong_dang_ky }} / {{ $card->tong_chi_tieu }}</strong>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('admin.dangky.danhsach', ['ky_thi' => $card->ten_ky_thi]) }}"
                           class="btn btn-outline-primary w-100 rounded-pill fw-semibold shadow-sm text-nowrap">
                            <i class="bi bi-list-ul me-1"></i>Xem danh sách lịch thi đăng ký
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
    <!-- CHẾ ĐỘ XEM DANH SÁCH LỊCH THI ĐÃ CHỌN KỲ THI -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('admin.dangky.danhsach') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm">
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
                <i class="bi bi-calendar-range me-2"></i>Danh sách các lịch thi có sinh viên đăng ký
            </h6>
            <span class="badge bg-light text-dark border">{{ $lichThis->total() }} lịch thi</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 30%;">Bài thi / Lịch thi</th>
                            <th style="width: 12%;">Ngày thi</th>
                            <th style="width: 12%;">Ca thi</th>
                            <th style="width: 15%;">Địa điểm / Phòng thi</th>
                            <th class="text-center" style="width: 12%;">Số lượng SV đăng ký</th>
                            <th class="text-center" style="width: 12%;">Số hồ sơ chờ duyệt</th>
                            <th class="text-center" style="width: 12%;">Trạng thái lịch thi</th>
                            <th class="text-end pe-3" style="width: 12%;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($lichThis as $lt)
                        <tr>
                            <td class="ps-3 fw-semibold">
                                <div class="text-dark fs-6 d-flex align-items-center gap-2">
                                    <span class="badge {{ ($lt->loai_chung_chi ?? '') === 'tienganh' ? '' : 'bg-primary' }} text-white px-2" style="{{ ($lt->loai_chung_chi ?? '') === 'tienganh' ? 'background-color: #6f42c1 !important;' : '' }}">
                                        {{ ($lt->loai_chung_chi ?? '') === 'tienganh' ? 'Tiếng Anh' : 'CNTT' }}
                                    </span>
                                    <span>{{ $lt->phong_thi }}</span>
                                </div>
                                <div class="small text-muted mt-1">
                                    <i class="bi bi-building me-1"></i>{{ optional($lt->khoa)->ten_khoa ?? 'Toàn trường' }}
                                </div>
                            </td>
                            <td>{{ optional($lt->ngay_thi)->format('d/m/Y') }}</td>
                            <td>
                                @if($lt->gio_bat_dau)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-6">
                                        <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($lt->gio_bat_dau)->format('H:i') }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td><span class="badge text-bg-light border text-dark"><i class="bi bi-door-closed me-1"></i>{{ $lt->phong_thi }}</span></td>
                            <td class="text-center">
                                <span class="fw-bold text-primary">{{ $lt->so_luong_dang_ky }}</span>
                                <span class="text-muted">/ {{ $lt->so_luong_toi_da }}</span>
                            </td>
                            <td class="text-center">
                                @if ($lt->so_ho_so_cho_duyet > 0)
                                    <span class="badge bg-warning text-dark px-2 py-1 fs-6">{{ $lt->so_ho_so_cho_duyet }} hồ sơ</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1">0</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($lt->trang_thai === 'dang_mo_dang_ky')
                                    <span class="badge bg-success">Đang mở đăng ký</span>
                                @elseif ($lt->trang_thai === 'da_dong_dang_ky')
                                    <span class="badge bg-secondary">Đã đóng đăng ký</span>
                                @elseif ($lt->trang_thai === 'dang_thi')
                                    <span class="badge bg-info text-dark">Đang thi</span>
                                @else
                                    <span class="badge bg-dark">Đã kết thúc</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <a href="{{ route('admin.dangky.index', $lt) }}" class="btn btn-sm btn-outline-primary shadow-sm">
                                    Xem danh sách đăng ký ({{ $lt->so_luong_dang_ky }})
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary opacity-50"></i>
                                Không tìm thấy lịch thi nào có đăng ký.
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
