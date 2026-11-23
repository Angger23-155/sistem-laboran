@extends('layouts.admin')

@section('title', 'Edit Pekerjaan')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-warning">
                <div class="card-header bg-warning text-dark">
                    <h3 class="card-title"><i class="fas fa-edit mr-2"></i> Edit Pekerjaan Laboratorium</h3>
                </div>
                
                <form action="{{ route('tasks.update', $task->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body">
                        {{-- Notifikasi Error Validasi --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-group">
                            <label>Jenis Pekerjaan <span class="text-danger">*</span></label>
                            <input type="text" name="jenis_pekerjaan" class="form-control @error('jenis_pekerjaan') is-invalid @enderror" 
                                value="{{ old('jenis_pekerjaan', $task->jenis_pekerjaan) }}" required>
                        </div>

                        <div class="form-group">
                            <label>Batas Waktu (Tenggat) <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="tenggat_waktu" class="form-control @error('tenggat_waktu') is-invalid @enderror" 
                                value="{{ old('tenggat_waktu', \Carbon\Carbon::parse($task->tenggat_waktu)->format('Y-m-d\TH:i')) }}" required>
                        </div>

                        <div class="form-group">
                            <label>Uraian Pekerjaan <span class="text-danger">*</span></label>
                            @if($task->kategori_tugas === 'harian')
                                <div class="alert alert-info py-2 px-3 mb-2" style="font-size: 0.85rem;">
                                    <i class="fas fa-info-circle mr-1"></i> <b>Info Edit Tugas Harian:</b> Setiap baris yang diawali dengan tanda strip (<b>-</b>) akan otomatis diubah menjadi kotak centang (<i>checklist</i>) pada saat Laboran melapor. Silakan hapus atau tambahkan baris sesuai kebutuhan.
                                </div>
                            @endif
                            <textarea name="uraian_pekerjaan" class="form-control @error('uraian_pekerjaan') is-invalid @enderror" 
                                rows="10" required>{{ old('uraian_pekerjaan', $task->uraian_pekerjaan) }}</textarea>
                        </div>
                    </div>

                    <div class="card-footer bg-light d-flex justify-content-end" style="gap: 10px;">
                        <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection