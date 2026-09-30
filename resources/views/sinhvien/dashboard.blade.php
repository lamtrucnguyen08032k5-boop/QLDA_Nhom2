@extends('layouts.sinhvien')
@section('title', 'Lịch thi đang mở đăng ký')
@section('content')

{{-- BỘ LỌC TÌM KIẾM --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('sinhvien.dashboard') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Tìm kiếm</label>
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Tên bài thi, phòng thi..." value="{{ request('q') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Loại chứng chỉ</label>
                <select name="loai_chung_chi" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Tất cả --</option>
                    <option value="cntt" {{ request('loai_chung_chi') === 'cntt' ? 'selected' : '' }}>Chứng chỉ CNTT</option>
                    <option value="ngoai_ngu" {{ request('loai_chung_chi') === 'ngoai_ngu' ? 'selected' : '' }}>Chứng chỉ Ngoại ngữ</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Từ ngày thi</label>
                <input type="date" name="tu_ngay" class="form-control form-control-sm" value="{{ request('tu_ngay') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Đến ngày thi</label>
                <input type="date" name="den_ngay" class="form-control form-control-sm" value="{{ request('den_ngay') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">Tìm kiếm</button>
            </div>
            @if(request()->anyFilled(['q', 'loai_chung_chi', 'tu_ngay', 'den_ngay']))
                <div class="col-md-1">
                    <a href="{{ route('sinhvien.dashboard') }}" class="btn btn-outline-secondary btn-sm w-100" title="Xóa bộ lọc">✕</a>
                </div>
            @endif
        </form>
    </div>
</div>

{{-- DANH SÁCH LỊCH THI --}}
@if($lichThis->isEmpty())
    <div class="text-center py-5 text-muted">
        <div class="fs-1 mb-2">📋</div>
        <p class="mb-0">Hiện chưa có lịch thi nào đang mở đăng ký.</p>
    </div>
@else
    <div class="row g-3">
        @foreach ($lichThis as $lt)
            @php
                $daDangKy = in_array($lt->id, $daDangKyIds);
                $conChoNgoi = $lt->dangKysDaDuyet()->count() < $lt->so_luong_toi_da;
            @endphp
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm hover-card">
                    {{-- Badge loại chứng chỉ --}}
                    <div class="card-header d-flex justify-content-between align-items-center py-2"
                         style="background: linear-gradient(135deg, #0d2b57 0%, #1a4a8a 100%); border-radius: 8px 8px 0 0;">
                        <span class="text-white fw-semibold small text-uppercase tracking-wide">
                            @if(strtolower($lt->loai_chung_chi) === 'cntt')
                                💻 Chứng chỉ CNTT
                            @elseif(str_contains(strtolower($lt->loai_chung_chi), 'ngoai') || str_contains(strtolower($lt->loai_chung_chi), 'ngữ'))
                                🌐 Chứng chỉ Ngoại ngữ
                            @else
                                🎓 {{ strtoupper($lt->loai_chung_chi) }}
                            @endif
                        </span>
                        @if($daDangKy)
                            <span class="badge bg-success">Đã đăng ký</span>
                        @elseif(!$conChoNgoi)
                            <span class="badge bg-secondary">Hết chỗ</span>
                        @endif
                    </div>

                    <div class="card-body p-3">
                        {{-- Tên bài thi --}}
                        <h6 class="fw-bold text-primary mb-3" style="min-height: 2.5rem;">{{ $lt->ten_ky_thi }}</h6>

                        {{-- Thông tin lịch thi --}}
                        <table class="table table-sm table-borderless mb-0 small">
                            <tr>
                                <td class="text-muted ps-0" style="width: 100px;">📅 Ngày thi:</td>
                                <td class="fw-semibold">{{ optional($lt->ngay_thi)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">⏰ Giờ thi:</td>
                                <td>{{ $lt->gio_bat_dau }}
                                    @if($lt->thoi_gian_thi_phut)
                                        <span class="text-muted">({{ $lt->thoi_gian_thi_phut }} phút)</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">🏛️ Địa điểm:</td>
                                <td>{{ optional($lt->khoa)->ten_khoa ?? 'Học viện Ngân hàng' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">🚪 Phòng thi:</td>
                                <td class="fw-semibold">{{ $lt->phong_thi }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">⌛ Hạn ĐK:</td>
                                <td class="text-danger fw-semibold">{{ optional($lt->han_dang_ky)->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">💰 Lệ phí:</td>
                                <td class="fw-bold text-success">{{ number_format($lt->le_phi) }}đ</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">👥 Còn chỗ:</td>
                                <td>
                                    @php $soDaDuyet = $lt->dangKysDaDuyet()->count(); @endphp
                                    <span class="{{ $soDaDuyet >= $lt->so_luong_toi_da ? 'text-danger fw-bold' : 'text-success fw-semibold' }}">
                                        {{ $lt->so_luong_toi_da - $soDaDuyet }} / {{ $lt->so_luong_toi_da }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="card-footer bg-transparent border-top-0 px-3 pb-3 pt-0">
                        @if($daDangKy)
                            <a href="{{ route('sinhvien.dangky.cua-toi') }}" class="btn btn-sm btn-outline-success w-100">
                                ✅ Đã đăng ký — Xem hồ sơ
                            </a>
                        @elseif(!$conChoNgoi)
                            <button class="btn btn-sm btn-secondary w-100" disabled>Hết chỗ</button>
                        @else
                            <a href="{{ route('sinhvien.dangky.buoc1', $lt) }}" class="btn btn-sm btn-primary w-100">
                                Đăng ký ngay →
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $lichThis->links() }}
    </div>
@endif

@endsection

@section('scripts')
<style>
.hover-card {
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    border-radius: 10px !important;
    overflow: hidden;
}
.hover-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12) !important;
}
</style>
@endsection
