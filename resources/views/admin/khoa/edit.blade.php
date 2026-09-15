@extends('layouts.app')
@section('title', 'Chỉnh sửa Khoa: ' . $khoa->ten_khoa)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Chỉnh sửa Khoa: <span class="text-primary">{{ $khoa->ten_khoa }}</span></h5>
    <a href="{{ route('admin.khoa.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Quay lại danh sách Khoa
    </a>
</div>

{{-- KHỐI 1 (Ở TRÊN): THÔNG TIN KHOA --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold text-primary">
            <i class="bi bi-info-circle me-1"></i>1. Thông tin Khoa
        </h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.khoa.update', $khoa) }}">
            @csrf
            @method('PUT')
            
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Mã khoa <span class="text-danger">*</span></label>
                    <input name="ma_khoa" class="form-control" value="{{ old('ma_khoa', $khoa->ma_khoa) }}" placeholder="Ví dụ: CNTT" required>
                </div>

                <div class="col-md-5">
                    <label class="form-label small fw-semibold">Tên khoa <span class="text-danger">*</span></label>
                    <input name="ten_khoa" class="form-control" value="{{ old('ten_khoa', $khoa->ten_khoa) }}" placeholder="Ví dụ: Khoa Công nghệ thông tin" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Email Khoa <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $khoa->email) }}" placeholder="khoa.cntt@hvnh.edu.vn" required>
                </div>

                <div class="col-md-9">
                    <label class="form-label small fw-semibold">Mô tả</label>
                    <input name="mo_ta" class="form-control" value="{{ old('mo_ta', $khoa->mo_ta) }}" placeholder="Nhập mô tả về khoa...">
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-semibold d-block">Trạng thái hoạt động</label>
                    <div class="form-check form-switch pt-1">
                        <input type="checkbox" class="form-check-input" id="activeSwitch" name="active" value="1" {{ old('active', $khoa->active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="activeSwitch">Hoạt động</label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-3 pt-2 border-top">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Lưu thông tin Khoa
                </button>
                <a href="{{ route('admin.khoa.index') }}" class="btn btn-outline-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>

{{-- KHỐI 2 (Ở DƯỚI): QUẢN LÝ GIẢNG VIÊN THUỘC KHOA --}}
<div class="card shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-success">
            <i class="bi bi-people me-1"></i>2. Giảng viên thuộc Khoa
        </h6>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-dark border">
                Tổng số: <strong>{{ $giangViens->total() }}</strong> giảng viên
            </span>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createGvModal">
                <i class="bi bi-plus-lg me-1"></i>Thêm Giảng viên
            </button>
            <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#importGvModal">
                <i class="bi bi-file-earmark-arrow-up me-1"></i>Import Giảng viên
            </button>
        </div>
    </div>
    <div class="card-body">

        {{-- Bảng Danh sách Giảng viên --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle border mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 60px;">STT</th>
                        <th style="width: 140px;">Mã GV</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th style="width: 160px;">Trạng thái</th>
                        <th class="text-center" style="width: 120px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($giangViens as $gv)
                    <tr>
                        <td class="text-center text-muted fw-semibold">{{ $giangViens->firstItem() + $loop->index }}</td>
                        <td class="fw-semibold text-primary">{{ $gv->ma_so }}</td>
                        <td class="fw-semibold">{{ $gv->name }}</td>
                        <td>{{ $gv->email }}</td>
                        <td>
                            {!! $gv->active ? '<span class="badge text-bg-success">Hoạt động</span>' : '<span class="badge text-bg-secondary">Đã khóa</span>' !!}
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary py-0 px-2" data-bs-toggle="modal" data-bs-target="#editGvModal{{ $gv->id }}" title="Chỉnh sửa Giảng viên">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <form method="POST" action="{{ route('admin.khoa.giangvien.destroy', [$khoa, $gv]) }}" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa giảng viên &quot;{{ $gv->name }}&quot;?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger py-0 px-2" style="border-top-left-radius:0; border-bottom-left-radius:0;" title="Xóa Giảng viên">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>

                            {{-- Modal Chỉnh sửa Giảng viên --}}
                            <div class="modal fade text-start" id="editGvModal{{ $gv->id }}" tabindex="-1" aria-labelledby="editGvModalLabel{{ $gv->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('admin.khoa.giangvien.update', [$khoa, $gv]) }}">
                                             @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h6 class="modal-title fw-bold" id="editGvModalLabel{{ $gv->id }}">
                                                    Chỉnh sửa Giảng viên: {{ $gv->name }}
                                                </h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Mã giảng viên</label>
                                                    <input type="text" class="form-control" value="{{ $gv->ma_so }}" disabled>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                                                    <input type="text" name="ho_ten" class="form-control" value="{{ old('ho_ten', $gv->name) }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Email <span class="text-danger">*</span></label>
                                                    <input type="email" name="email" class="form-control" value="{{ old('email', $gv->email) }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Đổi mật khẩu mới</label>
                                                    <div class="input-group">
                                                        <input type="password" name="password" class="form-control" placeholder="Để trống nếu giữ nguyên mật khẩu cũ">
                                                        <button class="btn btn-outline-secondary toggle-password" type="button" tabindex="-1" title="Hiện/Ẩn mật khẩu">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold d-block">Trạng thái tài khoản</label>
                                                    <div class="form-check form-switch">
                                                        <input type="checkbox" class="form-check-input" id="gvActiveSwitch{{ $gv->id }}" name="active" value="1" {{ $gv->active ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="gvActiveSwitch{{ $gv->id }}">Hoạt động</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
                                                <button type="submit" class="btn btn-primary btn-sm">Lưu thay đổi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Chưa có Giảng viên nào trong Khoa.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            @include('partials.pagination', ['paginator' => $giangViens])
        </div>
    </div>
</div>

{{-- Modal Thêm mới Giảng viên --}}
<div class="modal fade" id="createGvModal" tabindex="-1" aria-labelledby="createGvModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.khoa.giangvien.store', $khoa) }}">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-bold" id="createGvModalLabel">
                        <i class="bi bi-person-plus text-primary me-1"></i>Thêm Giảng viên vào {{ $khoa->ten_khoa }}
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Mã giảng viên <span class="text-danger">*</span></label>
                        <input name="ma_giang_vien" class="form-control" placeholder="Ví dụ: GV001" value="{{ old('ma_giang_vien') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Họ tên <span class="text-danger">*</span></label>
                        <input name="ho_ten" class="form-control" placeholder="Ví dụ: Nguyễn Văn A" value="{{ old('ho_ten') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="Ví dụ: gv.a@hvnh.edu.vn" value="{{ old('email') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Mật khẩu khởi tạo <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password" class="form-control" placeholder="Tối thiểu 6 ký tự (VD: GiangVien@123)" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" tabindex="-1" title="Hiện/Ẩn mật khẩu">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i>Thêm Giảng viên
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Import Giảng viên --}}
<div class="modal fade" id="importGvModal" tabindex="-1" aria-labelledby="importGvModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.khoa.giangvien.import', $khoa) }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-bold" id="importGvModalLabel">
                        <i class="bi bi-file-earmark-arrow-up text-success me-1"></i>Import Giảng viên vào {{ $khoa->ten_khoa }}
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-2">
                        Chọn file CSV chứa danh sách giảng viên cần thêm vào <strong>{{ $khoa->ten_khoa }}</strong>.
                    </p>
                    <div class="alert alert-info py-2 small mb-3">
                        <strong>Cấu trúc tiêu đề cột trong file CSV:</strong><br>
                        <code>Mã giảng viên,Họ tên,Email</code><br>
                        <span class="text-muted fst-italic">(File mẫu để trống dữ liệu, bạn chỉ cần nhập danh sách bên dưới tiêu đề)</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Chọn file CSV (.csv, .txt) <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control" accept=".csv,.txt" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Mật khẩu khởi tạo tài khoản <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password" class="form-control" placeholder="Tối thiểu 6 ký tự (VD: GiangVien@123)" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" tabindex="-1" title="Hiện/Ẩn mật khẩu">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="form-text small text-muted mt-1">Mật khẩu này sẽ được áp dụng cho toàn bộ tài khoản Giảng viên trong file import.</div>
                    </div>
                    <div class="text-end">
                        <a href="{{ route('admin.khoa.giangvien.sample', $khoa) }}" class="btn btn-sm btn-link text-decoration-none">
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
