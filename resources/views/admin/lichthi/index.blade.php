@extends('layouts.app')
@section('title', 'Quản lý kỳ thi & Lịch thi')
@section('content')

{{-- Header trang --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small text-muted">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Quản lý kỳ thi</li>
            </ol>
        </nav>
        <h4 class="fw-bold text-dark mb-1">Quản lý kỳ thi &amp; Lịch thi</h4>
        <p class="text-muted small mb-0">Thiết lập kỳ thi, phân chia lịch thi môn học, bố trí ca thi và gán phòng thi cho sinh viên</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.lichthi.create') }}" class="btn btn-primary px-3 shadow-sm fw-semibold">
            + Thêm kỳ thi mới
        </a>
    </div>
</div>

{{-- KPI Stats Overview (Giao diện số liệu tối giản, sạch sẽ) --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
            <div class="card-body p-3">
                <div class="text-muted small fw-medium mb-1">Tổng số kỳ thi</div>
                <div class="fs-3 fw-bold text-dark">{{ $stats['total_ky_thi'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
            <div class="card-body p-3">
                <div class="text-muted small fw-medium mb-1">Đang mở đăng ký</div>
                <div class="fs-3 fw-bold text-success">{{ $stats['dang_mo'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
            <div class="card-body p-3">
                <div class="text-muted small fw-medium mb-1">Tổng ca thi / Phòng</div>
                <div class="fs-3 fw-bold text-dark">{{ $stats['total_ca_thi'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
            <div class="card-body p-3">
                <div class="text-muted small fw-medium mb-1">Lượt thí sinh đăng ký</div>
                <div class="fs-3 fw-bold text-primary">{{ $stats['total_thi_sinh'] ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Khối Bộ lọc & Tìm kiếm nhanh --}}
<div class="card shadow-sm mb-4 border-0 rounded-3">
    <div class="card-body p-3">
        {{-- Status Filter Tabs --}}
        <div class="d-flex flex-wrap gap-2 mb-3 pb-2 border-bottom">
            @php
                $currentStatus = request('trang_thai');
            @endphp
            <a href="{{ route('admin.lichthi.index', request()->except('trang_thai', 'page')) }}"
               class="btn btn-sm rounded-pill px-3 {{ empty($currentStatus) ? 'btn-primary' : 'btn-light text-secondary' }}">
                Tất cả <span class="badge bg-white text-dark ms-1">{{ $stats['total_ky_thi'] ?? 0 }}</span>
            </a>
            <a href="{{ route('admin.lichthi.index', array_merge(request()->except('page'), ['trang_thai' => 'dang_mo_dang_ky'])) }}"
               class="btn btn-sm rounded-pill px-3 {{ $currentStatus === 'dang_mo_dang_ky' ? 'btn-success' : 'btn-light text-secondary' }}">
                Đang mở ĐK <span class="badge bg-white text-success ms-1">{{ $stats['dang_mo'] ?? 0 }}</span>
            </a>
            <a href="{{ route('admin.lichthi.index', array_merge(request()->except('page'), ['trang_thai' => 'da_dong_dang_ky'])) }}"
               class="btn btn-sm rounded-pill px-3 {{ $currentStatus === 'da_dong_dang_ky' ? 'btn-warning text-dark' : 'btn-light text-secondary' }}">
                Đã đóng ĐK <span class="badge bg-white text-dark ms-1">{{ $stats['da_dong'] ?? 0 }}</span>
            </a>
            <a href="{{ route('admin.lichthi.index', array_merge(request()->except('page'), ['trang_thai' => 'dang_dien_ra'])) }}"
               class="btn btn-sm rounded-pill px-3 {{ $currentStatus === 'dang_dien_ra' ? 'btn-info text-white' : 'btn-light text-secondary' }}">
                Đang diễn ra <span class="badge bg-white text-info ms-1">{{ $stats['dang_dien_ra'] ?? 0 }}</span>
            </a>
            <a href="{{ route('admin.lichthi.index', array_merge(request()->except('page'), ['trang_thai' => 'da_ket_thuc'])) }}"
               class="btn btn-sm rounded-pill px-3 {{ $currentStatus === 'da_ket_thuc' ? 'btn-secondary' : 'btn-light text-secondary' }}">
                Đã kết thúc <span class="badge bg-white text-secondary ms-1">{{ $stats['da_ket_thuc'] ?? 0 }}</span>
            </a>
        </div>

        <form method="GET" action="{{ route('admin.lichthi.index') }}" class="row g-2 align-items-center">
            @if ($currentStatus)
                <input type="hidden" name="trang_thai" value="{{ $currentStatus }}">
            @endif
            <div class="col-md-6">
                <input type="text" name="tu_khoa" class="form-control form-control-sm" placeholder="Tìm theo tên kỳ thi, năm học, môn thi..." value="{{ request('tu_khoa') }}">
            </div>
            <div class="col-md-3">
                <select name="nam_hoc" class="form-select form-select-sm">
                    <option value="">-- Tất cả năm học --</option>
                    @foreach ($namHocs as $nh)
                        <option value="{{ $nh }}" {{ request('nam_hoc') === $nh ? 'selected' : '' }}>Năm học {{ $nh }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">Tìm kiếm</button>
                @if (request()->hasAny(['tu_khoa', 'nam_hoc', 'trang_thai']))
                    <a href="{{ route('admin.lichthi.index') }}" class="btn btn-outline-secondary btn-sm" title="Đặt lại bộ lọc">Bỏ lọc</a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Danh sách các Kỳ thi --}}
@forelse ($kyThis as $kt)
    <div class="card shadow-sm mb-4 border-0 rounded-3 overflow-hidden">
        {{-- Header Kỳ thi --}}
        <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="flex-grow-1">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <h5 class="fw-bold text-dark mb-0">{{ $kt->ten_ky_thi }}</h5>
                    {!! $kt->trang_thai_badge !!}
                </div>
                <div class="d-flex flex-wrap align-items-center gap-3 text-muted small mt-2">
                    @if ($kt->nam_hoc)
                        <span>Năm học: <strong>{{ $kt->nam_hoc }}</strong></span>
                    @endif
                    @if ($kt->hoc_ky)
                        <span>{{ $kt->hoc_ky }}</span>
                    @endif
                    <span>Quy mô: <strong>{{ $kt->lichThis->count() }}</strong> ca thi</span>
                    <span>Đã đăng ký: <strong>{{ $kt->tong_so_thi_sinh }} / {{ $kt->tong_so_luong_toi_da }}</strong> thí sinh</span>
                </div>
            </div>

            {{-- Thao tác Kỳ thi --}}
            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                <a href="{{ route('admin.lichthi.edit', $kt->id) }}" class="btn btn-outline-primary btn-sm px-3 shadow-sm">
                    Chỉnh sửa
                </a>
                <button type="button" class="btn btn-outline-danger btn-sm px-3" data-bs-toggle="modal" data-bs-target="#deleteKyThiModal{{ $kt->id }}">
                    Xóa
                </button>
            </div>
        </div>

        @if ($kt->mo_ta)
            <div class="px-4 py-2 bg-light bg-opacity-50 text-muted small border-bottom fst-italic">
                {{ $kt->mo_ta }}
            </div>
        @endif

        {{-- Bảng chi tiết Lịch thi & Ca thi thuộc Kỳ thi --}}
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-secondary">
                        <tr>
                            <th class="text-center" style="width: 50px;">STT</th>
                            <th style="width: 140px;">Môn thi</th>
                            <th>Khoa chấm</th>
                            <th style="width: 120px;">Ngày thi</th>
                            <th style="width: 170px;">Ca thi / Thời gian</th>
                            <th>Phòng thi</th>
                            <th class="text-center" style="width: 130px;">Tiến độ ĐK</th>
                            <th style="width: 120px;">Lệ phí</th>
                            <th style="width: 150px;">Hạn đăng ký</th>
                            <th class="text-center" style="width: 140px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                    @forelse ($kt->lichThis as $lt)
                        @php
                            $registeredCount = $lt->dangKys->whereNotIn('trang_thai', ['da_huy', 'tu_choi'])->count();
                            $percent = $lt->so_luong_toi_da > 0 ? round(($registeredCount / $lt->so_luong_toi_da) * 100) : 0;
                        @endphp
                        <tr>
                            <td class="text-center text-muted fw-semibold">{{ $loop->iteration }}</td>
                            <td>
                                @if ($lt->loai_chung_chi === 'cntt')
                                    <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">
                                        CNTT
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-purple bg-opacity-10 text-purple border border-purple border-opacity-25 px-2 py-1" style="color: #6f42c1; background-color: #f3ebff; border-color: #d1baf7 !important;">
                                        Tiếng Anh
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="text-dark fw-medium">{{ $lt->khoa->ten_khoa ?? 'Chưa phân công' }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $lt->ngay_thi ? $lt->ngay_thi->format('d/m/Y') : '-' }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark font-monospace">{{ $lt->ma_ca_thi }}</div>
                                <div class="text-muted small mt-1">
                                    {{ \Carbon\Carbon::parse($lt->gio_bat_dau)->format('H:i') }} - {{ $lt->gio_ket_thuc }} ({{ $lt->thoi_gian_thi_phut }}p)
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    {{ $lt->phong_thi }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1 mb-1">
                                    <span class="fw-bold text-primary">{{ $registeredCount }}</span>
                                    <span class="text-muted">/ {{ $lt->so_luong_toi_da }}</span>
                                </div>
                                <div class="progress" style="height: 4px;" title="{{ $percent }}% số chỗ">
                                    <div class="progress-bar {{ $percent >= 100 ? 'bg-danger' : ($percent >= 75 ? 'bg-warning' : 'bg-primary') }}" style="width: {{ min(100, $percent) }}%"></div>
                                </div>
                            </td>
                            <td class="fw-bold text-success">{{ number_format($lt->le_phi, 0, ',', '.') }} đ</td>
                            <td>
                                <div class="small {{ $lt->daHetHanDangKy() ? 'text-danger' : 'text-dark' }}">
                                    {{ $lt->han_dang_ky ? $lt->han_dang_ky->format('d/m/Y H:i') : '-' }}
                                </div>
                                @if ($lt->daHetHanDangKy())
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25" style="font-size: 0.7rem;">Hết hạn ĐK</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('admin.dangky.index', $lt->id) }}" class="btn btn-outline-primary btn-sm py-1 px-2" title="Xem danh sách thí sinh">
                                        Thí sinh
                                    </a>
                                    <div class="dropdown">
                                        <button class="btn btn-light btn-sm py-1 px-2 border" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            ⋮
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm small">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.ketqua.index', $lt->id) }}">
                                                    Quản lý kết quả thi
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#deleteCaThiModal{{ $lt->id }}">
                                                    Xóa ca thi này
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                {{-- Modal Xóa riêng 1 Ca thi --}}
                                <div class="modal fade text-start" id="deleteCaThiModal{{ $lt->id }}" tabindex="-1" aria-labelledby="deleteCaThiModalLabel{{ $lt->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <form method="POST" action="{{ route('admin.lichthi.cathi.destroy', $lt->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <div class="modal-header bg-light">
                                                    <h6 class="modal-title fw-bold text-danger" id="deleteCaThiModalLabel{{ $lt->id }}">
                                                        Xác nhận xóa ca thi
                                                    </h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <p class="mb-2">Bạn có chắc chắn muốn xóa ca thi <strong>{{ $lt->ma_ca_thi }}</strong> (Phòng {{ $lt->phong_thi }}, Ngày {{ $lt->ngay_thi ? $lt->ngay_thi->format('d/m/Y') : '' }})?</p>
                                                    <div class="alert alert-warning small py-2 mb-0">
                                                        Lưu ý: Hệ thống chỉ cho phép xóa ca thi khi <strong>chưa có thí sinh nào đăng ký dự thi</strong>.
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Hủy bỏ</button>
                                                    <button type="submit" class="btn btn-danger btn-sm px-3">
                                                        Xác nhận xóa
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">Chưa có lịch thi hoặc ca thi nào trong kỳ thi này.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modal Xóa toàn bộ Kỳ thi --}}
        <div class="modal fade text-start" id="deleteKyThiModal{{ $kt->id }}" tabindex="-1" aria-labelledby="deleteKyThiModalLabel{{ $kt->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <form method="POST" action="{{ route('admin.lichthi.destroy', $kt->id) }}">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header bg-light">
                            <h6 class="modal-title fw-bold text-danger" id="deleteKyThiModalLabel{{ $kt->id }}">
                                Xác nhận xóa kỳ thi
                            </h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <p class="mb-2">Bạn có chắc chắn muốn xóa kỳ thi <strong>&quot;{{ $kt->ten_ky_thi }}&quot;</strong> cùng toàn bộ các lịch thi và ca thi trực thuộc?</p>
                            <div class="alert alert-danger small py-2 mb-0">
                                Thao tác này không thể hoàn tác. Nếu kỳ thi đã có sinh viên đăng ký, hệ thống sẽ bảo vệ dữ liệu và từ chối xóa.
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Hủy bỏ</button>
                            <button type="submit" class="btn btn-danger btn-sm px-3">
                                Đồng ý xóa kỳ thi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="card shadow-sm border-0 rounded-3 py-5 text-center bg-white">
        <div class="card-body">
            <h5 class="fw-bold text-dark mb-2">Không tìm thấy kỳ thi phù hợp</h5>
            <p class="text-muted small mb-4">Chưa có kỳ thi nào hoặc không có kết quả phù hợp với bộ lọc hiện tại.</p>
            <div class="d-flex justify-content-center gap-2">
                @if (request()->hasAny(['tu_khoa', 'nam_hoc', 'trang_thai']))
                    <a href="{{ route('admin.lichthi.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                        Xóa bộ lọc
                    </a>
                @endif
                <a href="{{ route('admin.lichthi.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm">
                    + Thêm kỳ thi mới
                </a>
            </div>
        </div>
    </div>
@endforelse

<div class="mt-4">
    @include('partials.pagination', ['paginator' => $kyThis])
</div>
@endsection


