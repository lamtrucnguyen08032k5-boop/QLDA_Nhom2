@extends('layouts.app')
@section('title', 'Quản lý Khoa')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h5 class="mb-0 fw-bold text-primary">Danh sách Khoa</h5>
        <small class="text-muted">Quản lý các Khoa và tài khoản đại diện đơn vị</small>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#importKhoaModal">
            <i class="bi bi-file-earmark-arrow-up me-1"></i>Import Khoa
        </button>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createKhoaModal">
            <i class="bi bi-plus-lg me-1"></i>Thêm mới Khoa
        </button>
    </div>
</div>

{{-- Thanh Tìm kiếm & Lọc Khoa --}}
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body p-3 bg-light rounded-3">
        <form method="GET" action="{{ route('admin.khoa.index') }}" class="row g-2 align-items-end">
            <div class="col-md-7">
                <label class="form-label small fw-semibold text-secondary mb-1">Tìm kiếm Khoa:</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input name="search" class="form-control border-start-0" placeholder="Nhập mã khoa, tên khoa, email..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold text-secondary mb-1">Trạng thái:</label>
                <select name="trang_thai" class="form-select form-select-sm">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="1" {{ request('trang_thai') === '1' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="0" {{ request('trang_thai') === '0' ? 'selected' : '' }}>Ngừng hoạt động</option>
                </select>
            </div>

            <div class="col-md-2">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                        <i class="bi bi-funnel me-1"></i>Tìm kiếm
                    </button>
                    @if(request()->filled('search') || request()->filled('trang_thai'))
                        <a href="{{ route('admin.khoa.index') }}" class="btn btn-outline-secondary btn-sm" title="Bỏ lọc">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 60px;">STT</th>
                        <th style="width: 120px;">Mã khoa</th>
                        <th>Tên khoa</th>
                        <th>Email</th>
                        <th style="width: 160px;">Số Giảng viên</th>
                        <th style="width: 140px;">Trạng thái</th>
                        <th class="text-center" style="width: 120px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($khoas as $khoa)
                    <tr>
                        <td class="text-center text-muted fw-semibold">{{ $khoas->firstItem() + $loop->index }}</td>
                        <td class="fw-bold text-primary">{{ $khoa->ma_khoa }}</td>
                        <td class="fw-semibold">{{ $khoa->ten_khoa }}</td>
                        <td>{{ $khoa->email }}</td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-person me-1"></i>{{ $khoa->giang_viens_count }} giảng viên
                            </span>
                        </td>
                        <td>
                            {!! $khoa->active ? '<span class="badge text-bg-success">Hoạt động</span>' : '<span class="badge text-bg-secondary">Ngừng hoạt động</span>' !!}
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-3">
                                <a href="{{ route('admin.khoa.edit', $khoa) }}" class="text-secondary text-decoration-none" title="Chỉnh sửa Khoa & Giảng viên" style="font-size: 1.15rem; transition: color 0.15s;" onmouseover="this.style.color='#0d6efd'" onmouseout="this.style.color=''">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.khoa.destroy', $khoa) }}" class="d-inline m-0" data-confirm="Bạn có chắc chắn muốn xóa Khoa &quot;{{ $khoa->ten_khoa }}&quot;?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link p-0 text-secondary border-0 text-decoration-none" title="Xóa Khoa" style="font-size: 1.15rem; line-height: 1; transition: color 0.15s;" onmouseover="this.style.color='#dc3545'" onmouseout="this.style.color=''">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Chưa có khoa nào trong hệ thống.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
<div class="mt-3">
    @include('partials.pagination', ['paginator' => $khoas])
</div>

{{-- Modal Thêm mới Khoa --}}
<div class="modal fade" id="createKhoaModal" tabindex="-1" aria-labelledby="createKhoaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.khoa.store') }}">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-bold" id="createKhoaModalLabel">
                        <i class="bi bi-building-add text-primary me-1"></i>Thêm mới Khoa
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Mã khoa <span class="text-danger">*</span></label>
                        <input name="ma_khoa" class="form-control" placeholder="Ví dụ: CNTT" value="{{ old('ma_khoa') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Tên khoa <span class="text-danger">*</span></label>
                        <input name="ten_khoa" class="form-control" placeholder="Ví dụ: Khoa Công nghệ thông tin" value="{{ old('ten_khoa') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email Khoa <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="Ví dụ: khoa.cntt@hvnh.edu.vn" value="{{ old('email') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Mật khẩu khởi tạo <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password" class="form-control" placeholder="Tối thiểu 6 ký tự (VD: Khoa@123)" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" tabindex="-1" title="Hiện/Ẩn mật khẩu">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Mô tả</label>
                        <textarea name="mo_ta" class="form-control" rows="3" placeholder="Nhập mô tả về khoa...">{{ old('mo_ta') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i>Thêm Khoa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Import Khoa --}}
<div class="modal fade" id="importKhoaModal" tabindex="-1" aria-labelledby="importKhoaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.khoa.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-bold" id="importKhoaModalLabel">
                        <i class="bi bi-file-earmark-arrow-up text-success me-1"></i>Import danh sách Khoa từ file CSV
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-2">
                        Chọn file CSV chứa danh sách các Khoa cần thêm. Hệ thống sẽ tự động tạo Khoa và tạo tài khoản đăng nhập tương ứng.
                    </p>
                    <div class="alert alert-info py-2 small mb-3">
                        <strong>Cấu trúc tiêu đề cột trong file CSV:</strong><br>
                        <code>Mã khoa,Tên khoa,Email,Mô tả</code><br>
                        <span class="text-muted fst-italic">(File mẫu để trống dữ liệu, bạn chỉ cần nhập danh sách bên dưới tiêu đề)</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Chọn file CSV (.csv, .txt) <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control" accept=".csv,.txt" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Mật khẩu khởi tạo tài khoản <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password" class="form-control" placeholder="Tối thiểu 6 ký tự (VD: Khoa@123)" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" tabindex="-1" title="Hiện/Ẩn mật khẩu">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="form-text small text-muted mt-1">Mật khẩu này sẽ được áp dụng cho toàn bộ tài khoản Khoa trong file import.</div>
                    </div>
                    <div class="text-end">
                        <a href="{{ route('admin.khoa.sample') }}" class="btn btn-sm btn-link text-decoration-none">
                            <i class="bi bi-download me-1"></i>Tải file mẫu (.csv)
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-cloud-arrow-up me-1"></i>Bắt đầu Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
