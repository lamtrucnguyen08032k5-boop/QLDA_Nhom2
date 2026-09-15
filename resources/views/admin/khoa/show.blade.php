@extends('layouts.app')
@section('title', 'Quản lý Khoa: ' . $khoa->ten_khoa)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Chi tiết Khoa: <span class="text-primary">{{ $khoa->ten_khoa }}</span></h5>
    <a href="{{ route('admin.khoa.index') }}" class="btn btn-outline-secondary btn-sm">Quay lại danh sách Khoa</a>
</div>

<div class="row g-3">
    {{-- Bước 11: Thông tin Khoa --}}
    <div class="col-lg-4 col-md-5">
        <div class="card h-100 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Thông tin Khoa</span>
                <a href="{{ route('admin.khoa.edit', $khoa) }}" class="btn btn-sm btn-outline-primary">Chỉnh sửa</a>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <th class="text-muted" style="width: 110px;">Mã khoa:</th>
                        <td class="fw-semibold">{{ $khoa->ma_khoa }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Tên khoa:</th>
                        <td>{{ $khoa->ten_khoa }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Email Khoa:</th>
                        <td>{{ $khoa->email }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Trạng thái:</th>
                        <td>
                            {!! $khoa->active ? '<span class="badge text-bg-success">Hoạt động</span>' : '<span class="badge text-bg-secondary">Ngừng hoạt động</span>' !!}
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Mô tả:</th>
                        <td>{{ $khoa->mo_ta ?: '—' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- Bước 11-18: Danh sách Giảng viên và Form Thêm Giảng viên --}}
    <div class="col-lg-8 col-md-7">
        {{-- Form Thêm Giảng viên --}}
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white">
                <span class="fw-semibold">Thêm Giảng viên vào {{ $khoa->ten_khoa }}</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.khoa.giangvien.store', $khoa) }}">
                    @csrf
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small mb-1">Mã giảng viên <span class="text-danger">*</span></label>
                            <input name="ma_giang_vien" class="form-control form-control-sm" placeholder="GV001" value="{{ old('ma_giang_vien') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small mb-1">Họ tên <span class="text-danger">*</span></label>
                            <input name="ho_ten" class="form-control form-control-sm" placeholder="Nguyễn Văn A" value="{{ old('ho_ten') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small mb-1">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control form-control-sm" placeholder="gv@hvnh.edu.vn" value="{{ old('email') }}" required>
                        </div>
                        <div class="col-md-2 d-flex gap-1">
                            <button type="submit" class="btn btn-sm btn-primary w-100">Lưu</button>
                            <button type="reset" class="btn btn-sm btn-outline-secondary">Hủy</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Danh sách Giảng viên --}}
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Danh sách Giảng viên thuộc Khoa ({{ $giangViens->count() }})</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mã GV</th>
                                <th>Họ tên</th>
                                <th>Email</th>
                                <th>Trạng thái</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse ($giangViens as $gv)
                            <tr>
                                <td class="fw-semibold">{{ $gv->ma_so }}</td>
                                <td>{{ $gv->name }}</td>
                                <td>{{ $gv->email }}</td>
                                <td>
                                    {!! $gv->active ? '<span class="badge text-bg-success">Hoạt động</span>' : '<span class="badge text-bg-secondary">Đã khóa</span>' !!}
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-primary py-0 px-2" data-bs-toggle="modal" data-bs-target="#editGvModalShow{{ $gv->id }}" title="Chỉnh sửa Giảng viên">
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
                                    <div class="modal fade text-start" id="editGvModalShow{{ $gv->id }}" tabindex="-1" aria-labelledby="editGvModalShowLabel{{ $gv->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <form method="POST" action="{{ route('admin.khoa.giangvien.update', [$khoa, $gv]) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h6 class="modal-title fw-bold" id="editGvModalShowLabel{{ $gv->id }}">
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
                                                            <label class="form-label small fw-semibold d-block">Trạng thái tài khoản</label>
                                                            <div class="form-check form-switch">
                                                                <input type="checkbox" class="form-check-input" id="gvActiveSwitchShow{{ $gv->id }}" name="active" value="1" {{ $gv->active ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="gvActiveSwitchShow{{ $gv->id }}">Hoạt động</label>
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
                            <tr><td colspan="5" class="text-center text-muted py-3">Chưa có Giảng viên nào trong Khoa.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
