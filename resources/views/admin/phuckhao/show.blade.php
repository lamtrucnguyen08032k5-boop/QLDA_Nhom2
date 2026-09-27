@extends('layouts.app')
@section('title', 'Chi tiết Phúc khảo (Admin)')
@section('content')
<div class="mb-3">
    <a href="{{ route('admin.phuckhao.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
    </a>
</div>

<div class="row g-4">
    <div class="col-md-7">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title fw-bold mb-0">Thông tin bài thi & Lý do phúc khảo</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th style="width:150px;" class="text-muted">Sinh viên:</th>
                        <td class="fw-bold">{{ $phuckhao->sinhVien->name ?? 'N/A' }} ({{ $phuckhao->sinhVien->ma_so ?? '' }})</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Đề thi:</th>
                        <td>{{ $phuckhao->baiThi->deThi->ten_de ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Khoa:</th>
                        <td>{{ $phuckhao->baiThi->deThi->khoa->ten_khoa ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Điểm hiện tại:</th>
                        <td><span class="badge bg-secondary fs-6">{{ $phuckhao->baiThi->diem_tong }}</span></td>
                    </tr>
                    <tr>
                        <th class="text-muted">Lý do phúc khảo:</th>
                        <td class="bg-light p-3 rounded">{!! nl2br(e($phuckhao->ly_do)) !!}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($phuckhao->trang_thai === 'cho_admin_duyet' || $phuckhao->trang_thai === 'hoan_tat')
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title fw-bold mb-0">Kết quả chấm phúc khảo của Giảng viên</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Giảng viên chấm:</strong> {{ $phuckhao->giangVien->name ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Điểm trước phúc khảo:</strong> {{ $phuckhao->diem_truoc }}</p>
                    <p class="mb-1"><strong>Điểm sau phúc khảo:</strong> <span class="fw-bold text-success fs-5">{{ $phuckhao->diem_sau ?? 'Không đổi' }}</span></p>
                    <div class="p-3 bg-light rounded mt-2">
                        <strong>Phản hồi từ Giảng viên:</strong>
                        <p class="mb-0 text-dark">{!! nl2br(e($phuckhao->phan_hoi)) !!}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="col-md-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title fw-bold mb-0">Xử lý yêu cầu (Admin)</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted small fw-semibold">Trạng thái hiện tại:</label>
                    <div>
                        @switch($phuckhao->trang_thai)
                            @case('cho_tiep_nhan')
                                <span class="badge bg-secondary fs-6">Chờ tiếp nhận</span>
                                @break
                            @case('cho_phan_cong')
                                <span class="badge bg-warning text-dark fs-6">Chờ Khoa phân công</span>
                                @break
                            @case('dang_xu_ly')
                                <span class="badge bg-info text-dark fs-6">Đang xử lý</span>
                                @break
                            @case('cho_admin_duyet')
                                <span class="badge bg-primary fs-6">Chờ Admin duyệt</span>
                                @break
                            @case('hoan_tat')
                                <span class="badge bg-success fs-6">Hoàn tất (Đã duyệt)</span>
                                @break
                            @case('tu_choi')
                                <span class="badge bg-danger fs-6">Từ chối</span>
                                @break
                        @endswitch
                    </div>
                </div>

                @if(in_array($phuckhao->trang_thai, ['cho_tiep_nhan', 'cho_xu_ly']))
                    <form method="POST" action="{{ route('admin.phuckhao.tiep-nhan', $phuckhao->id) }}" class="mb-3">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-circle me-1"></i> Tiếp nhận & Chuyển cho Khoa
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.phuckhao.tu-choi', $phuckhao->id) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Lý do từ chối:</label>
                            <textarea name="ly_do_duyet" class="form-control" rows="2" placeholder="Nhập lý do từ chối..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-x-circle me-1"></i> Từ chối yêu cầu
                        </button>
                    </form>
                @elseif($phuckhao->trang_thai === 'cho_admin_duyet')
                    <form method="POST" action="{{ route('admin.phuckhao.duyet', $phuckhao->id) }}" class="mb-3">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ghi chú duyệt Admin:</label>
                            <textarea name="ly_do_duyet" class="form-control" rows="2" placeholder="Ghi chú khi duyệt..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('Xác nhận phê duyệt kết quả phúc khảo?')">
                            <i class="bi bi-check2-all me-1"></i> Duyệt kết quả phúc khảo (HĐ19)
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.phuckhao.tra-lai', $phuckhao->id) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Lý do trả lại:</label>
                            <textarea name="ly_do_duyet" class="form-control" rows="2" placeholder="Lý do yêu cầu chấm lại..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-outline-warning w-100">
                            <i class="bi bi-arrow-return-left me-1"></i> Trả lại cho Khoa / GV chấm lại
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
