@extends('layouts.app')
@section('title', 'Quản lý Khoa')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Danh sách Khoa</h5>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createKhoaModal">
            <i class="bi bi-plus-lg me-1"></i>Thêm mới Khoa
        </button>
        <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#importKhoaModal">
            <i class="bi bi-file-earmark-arrow-up me-1"></i>Import Khoa
        </button>
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
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin.khoa.edit', $khoa) }}" class="btn btn-outline-primary" title="Chỉnh sửa Khoa & Giảng viên">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.khoa.destroy', $khoa) }}" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa Khoa &quot;{{ $khoa->ten_khoa }}&quot;?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" style="border-top-left-radius: 0; border-bottom-left-radius: 0;" title="Xóa Khoa">
                                        <i class="bi bi-trash"></i>
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
