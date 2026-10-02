@extends('layouts.app')
@section('title', 'Giảng viên trong Khoa')
@section('content')
<div class="row g-3">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Thêm Giảng viên</div>
            <div class="card-body">
                <form method="POST" action="{{ route('khoa.giangvien.store') }}">
                    @csrf
                    <div class="mb-2"><input name="ma_giang_vien" class="form-control form-control-sm" placeholder="Mã GV" required></div>
                    <div class="mb-2"><input name="ho_ten" class="form-control form-control-sm" placeholder="Họ tên" required></div>
                    <div class="mb-2"><input type="email" name="email" class="form-control form-control-sm" placeholder="Email" required></div>
                    <button class="btn btn-primary btn-sm w-100">Thêm</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <form method="GET" action="{{ route('khoa.giangvien.index') }}" class="row g-2 align-items-center">
                    <div class="col-md-8">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                            <input name="search" class="form-control" placeholder="Tìm mã GV, họ tên, email..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Tìm kiếm</button>
                        @if(request()->filled('search'))
                            <a href="{{ route('khoa.giangvien.index') }}" class="btn btn-outline-secondary btn-sm" title="Bỏ lọc">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                </form>
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
                            </tr>
                        </thead>
                        <tbody>
                        @forelse ($giangViens as $gv)
                            <tr>
                                <td class="fw-semibold text-primary">{{ $gv->ma_so }}</td>
                                <td class="fw-bold">{{ $gv->name }}</td>
                                <td>{{ $gv->email }}</td>
                                <td>{!! $gv->active ? '<span class="badge text-bg-success">Hoạt động</span>' : '<span class="badge text-bg-secondary">Khoá</span>' !!}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Không tìm thấy giảng viên nào.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($giangViens->hasPages())
                <div class="card-footer bg-white border-top py-2">
                    @include('partials.pagination', ['paginator' => $giangViens])
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
