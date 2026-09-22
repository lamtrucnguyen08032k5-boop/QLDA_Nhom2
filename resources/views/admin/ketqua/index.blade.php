@extends('layouts.app')
@section('title', 'Kết quả thi - Quản lý công bố kết quả')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-1 text-primary fw-bold">
            <i class="bi bi-clipboard-data me-2"></i>Quản lý & Công bố kết quả thi
        </h4>
        <p class="text-muted small mb-0">Theo dõi tiến độ chấm bài và thực hiện công bố kết quả đồng loạt theo từng phòng thi</p>
    </div>
</div>

<!-- Thanh tìm kiếm & bộ lọc -->
@if (isset($soPhongSanSangCongBo) && $soPhongSanSangCongBo > 0)
    <div class="alert alert-success border-success d-flex align-items-center justify-content-between mb-4 shadow-sm" role="alert">
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
        <a href="{{ route('admin.ketqua.index', ['trang_thai_cong_bo' => 'chua_cong_bo']) }}" class="btn btn-sm btn-success shadow-sm text-nowrap">
            <i class="bi bi-list-check me-1"></i>Xem các phòng sẵn sàng
        </a>
    </div>
@endif

<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.ketqua.index') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Tìm kiếm bài thi / kỳ thi</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control" placeholder="Tên kỳ thi, ca thi, phòng thi..." value="{{ request('q') }}">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold text-muted mb-1">Ngày thi</label>
                <input type="date" name="ngay_thi" class="form-control form-control-sm" value="{{ request('ngay_thi') }}">
            </div>
            <div class="col-md-1">
                <label class="form-label small fw-semibold text-muted mb-1">Ca thi</label>
                <input type="text" name="ma_ca_thi" class="form-control form-control-sm" placeholder="VD: CA1..." value="{{ request('ma_ca_thi') }}">
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
            <div class="col-md-auto d-flex flex-column justify-content-end">
                <label class="form-label small text-transparent mb-1 d-none d-md-block">&nbsp;</label>
                <div class="d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary" title="Tìm kiếm">
                        <i class="bi bi-filter me-1"></i>Lọc
                    </button>
                    <a href="{{ route('admin.ketqua.index') }}" class="btn btn-sm btn-outline-secondary" title="Xóa tất cả bộ lọc">
                        <i class="bi bi-x-circle me-1"></i>Xóa lọc
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Bảng danh sách lịch thi / phòng thi -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 26%;">Bài thi / Kỳ thi</th>
                        <th style="width: 12%;">Ngày thi</th>
                        <th style="width: 10%;">Ca thi</th>
                        <th style="width: 12%;">Phòng thi</th>
                        <th class="text-center" style="width: 10%;">Số SV dự thi</th>
                        <th class="text-center" style="width: 10%;">Đã chấm xong</th>
                        <th style="width: 10%;">Tiến độ chấm</th>
                        <th class="text-center" style="width: 10%;">Trạng thái</th>
                        <th class="text-end pe-3" style="width: 10%;">Thao tác</th>
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
                            <div class="fw-bold text-dark">{{ $lt->ten_ky_thi }}</div>
                            @if($lt->deThi)
                                <div class="small text-muted"><i class="bi bi-file-earmark-text me-1"></i>Đề thi: {{ $lt->deThi->ten_de }}</div>
                            @endif
                            <div class="small text-muted">
                                <span class="badge bg-secondary-subtle text-secondary border">{{ strtoupper($lt->loai_chung_chi) }}</span>
                                <span class="ms-1">{{ optional($lt->khoa)->ten_khoa ?? 'Khảo thí' }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ optional($lt->ngay_thi)->format('d/m/Y') }}</div>
                            <small class="text-muted">{{ optional($lt->ngay_thi)->locale('vi')->diffForHumans() }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace">{{ $lt->ma_ca_thi }}</span>
                            @if($lt->gio_bat_dau)
                                <div class="small text-muted mt-1">{{ \Carbon\Carbon::parse($lt->gio_bat_dau)->format('H:i') }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                <i class="bi bi-door-closed me-1"></i>{{ $lt->phong_thi }}
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
                                         role="progressbar"
                                         style="width: {{ $phanTramCham }}%;"
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
                            <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                            <div>Không tìm thấy lịch thi hoặc phòng thi nào phù hợp với điều kiện tra cứu.</div>
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

@endsection
