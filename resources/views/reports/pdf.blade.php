<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kinerja - {{ date('d M Y') }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; }
        
        .info-table { width: 100%; margin-bottom: 20px; border: none; }
        .info-table td { padding: 3px; border: none; }

        .summary-box { border: 2px dashed #d9534f; padding: 15px; background-color: #fdf2f2; margin-bottom: 20px; border-radius: 5px; }
        .summary-box h4 { margin-top: 0; color: #d9534f; }
        .summary-item { margin-bottom: 5px; font-size: 13px; }
        
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 6px 8px; text-align: left; vertical-align: middle; }
        table.data-table th { background-color: #e9ecef; text-align: center; font-weight: bold; }
        
        .text-center { text-align: center; }
        .text-danger { color: #d9534f; font-weight: bold; }
        .text-success { color: #28a745; font-weight: bold; }
        .text-warning { color: #f0ad4e; font-weight: bold; }
        /* Warna khusus untuk Insidentil yang lewat deadline */
        .text-orange { color: #d97706; font-weight: bold; } 
    </style>
</head>
<body>

    <table style="width: 100%; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 20px; border-collapse: collapse;">
        <tr>
            <td style="width: 15%; text-align: center; border: none; vertical-align: middle;">
                <img src="{{ public_path('img/Logo UTM berwarna.JPEG') }}" style="width: 80px; height: auto;">
            </td>
            <td style="width: 70%; text-align: center; border: none; vertical-align: middle;">
                <h2 style="margin: 0; padding: 0; font-size: 20px; text-transform: uppercase;">LAPORAN KINERJA LABORATORIUM</h2>
                <h4 style="margin: 5px 0 0 0; padding: 0; font-size: 14px; font-weight: normal;">Program Studi Teknik Informatika - Universitas Trunojoyo Madura</h4>
            </td>
            <td style="width: 15%; border: none;"></td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td width="20%"><strong>Periode Laporan</strong></td>
            <td width="5%">:</td>
            <td width="75%">{{ \Carbon\Carbon::parse($request->tgl_awal)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($request->tgl_akhir)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td><strong>Pelaksana Tugas</strong></td>
            <td>:</td>
            <td>{{ $targetUser }}</td>
        </tr>
    </table>

    <div class="summary-box">
        <table style="width: 100%; border: none; border-collapse: collapse;">
            <tr>
                <td style="width: 65%; border: none; padding: 0; vertical-align: top;">
                    <h4 style="margin-top: 0; margin-bottom: 8px; color: #d9534f;">Ringkasan Ketepatan Waktu:</h4>
                    <div class="summary-item">Total Tugas Diberikan: <strong>{{ $summary['total'] }} tugas</strong></div>
                    <div class="summary-item text-success">Selesai Tepat Waktu: {{ $summary['tepat_waktu'] }} tugas</div>
                    <div class="summary-item text-danger">Selesai Terlambat: {{ $summary['terlambat'] }} tugas</div>
                    
                    @if(isset($summary['revisi']) && $summary['revisi'] > 0)
                        <div class="summary-item text-warning">Sedang Direvisi: {{ $summary['revisi'] }} tugas</div>
                    @endif
                    
                    <div class="summary-item text-danger">Tidak Terlaksana (Tugas Harian): {{ $summary['tidak_terlaksana'] }} tugas</div>
                    <div class="summary-item text-orange">Pending Lewat Deadline (Tugas Insidentil): {{ $summary['pending_telat'] }} tugas</div>
                    <div class="summary-item" style="color: #666;">Belum Dikerjakan (Aman / Pending): {{ $summary['pending_aman'] }} tugas</div>
                </td>
                
                <td style="width: 35%; border: none; padding: 0; text-align: center; vertical-align: middle; border-left: 2px dashed #f0b2b1;">
                    <div style="font-size: 15px; font-weight: bold; color: #555; margin-bottom: 5px; text-transform: uppercase;">Nilai Kinerja</div>
                    
                    @if(isset($tugasDinilai) && $tugasDinilai > 0)
                        <div style="font-size: 52px; font-weight: bold; color: {{ $nilaiKinerja >= 80 ? '#28a745' : ($nilaiKinerja >= 60 ? '#f0ad4e' : '#d9534f') }}; line-height: 1;">
                            {{ $nilaiKinerja }}<span style="font-size: 30px;">%</span>
                        </div>
                        <div style="font-size: 11px; color: #777; margin-top: 5px;">(Dari {{ $tugasDinilai }} tugas yang dievaluasi)</div>
                    @else
                        <div style="font-size: 36px; font-weight: bold; color: #aaa; line-height: 1; margin-top: 10px;">N/A</div>
                        <div style="font-size: 11px; color: #777; margin-top: 5px;">(Belum ada tugas untuk dievaluasi)</div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <h4 style="margin-bottom: 5px;">Rincian Pekerjaan</h4>
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                @if(in_array('deadline', $kolom)) <th>Deadline</th> @endif
                @if(in_array('pelaksana', $kolom)) <th>Pelaksana</th> @endif
                @if(in_array('pekerjaan', $kolom)) <th>Nama Pekerjaan</th> @endif
                @if(in_array('kategori', $kolom)) <th>Kategori</th> @endif
                
                @if(in_array('uraian', $kolom)) <th>Rincian Tugas</th> @endif
                
                @if(in_array('status', $kolom)) <th>Status Pengerjaan</th> @endif
                @if(in_array('persetujuan', $kolom)) <th>Persetujuan</th> @endif
            </tr>
        </thead>
        <tbody>
            @forelse($tasks as $index => $task)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                
                @if(in_array('deadline', $kolom))
                    <td class="text-center">{{ \Carbon\Carbon::parse($task->tenggat_waktu)->format('d/m/Y H:i') }}</td>
                @endif
                
                @if(in_array('pelaksana', $kolom))
                    <td>{{ $task->penerimaTugas->name ?? '-' }}</td>
                @endif
                
                @if(in_array('pekerjaan', $kolom))
                    <td><strong>{{ $task->jenis_pekerjaan }}</strong></td>
                @endif

                @if(in_array('kategori', $kolom))
                    <td class="text-center">{{ ucfirst($task->kategori_tugas) }}</td>
                @endif
                
                @if(in_array('uraian', $kolom))
                    <td>
                        @if(strtolower($task->kategori_tugas) == 'insidentil')
                            <span style="font-size: 10.5px;">{{ $task->uraian_pekerjaan }}</span>
                        @else
                            <div style="text-align: center; color: #999;">-</div>
                        @endif
                    </td>
                @endif

                @if(in_array('status', $kolom))
                    <td class="text-center">
                        @if($task->status == 'Selesai Terlambat' || $task->status == 'Tidak Terlaksana' || $task->status == 'Hangus')
                            <span class="text-danger">{{ $task->status }}</span>
                        @elseif($task->status == 'Selesai Tepat Waktu')
                            <span class="text-success">{{ $task->status }}</span>
                        @elseif($task->status == 'Pending (Lewat Deadline)')
                            <span class="text-orange">{{ $task->status }}</span>
                        @elseif($task->status == 'Pending')
                            <span class="text-warning">{{ $task->status }}</span>
                        @elseif(in_array($task->status, ['Revisi', 'Direvisi']))
                            <span class="text-warning">{{ $task->status }}</span>
                        @else
                            {{ $task->status }}
                        @endif
                    </td>
                @endif
                
                {{-- PERBAIKAN LOGIKA RATA TENGAH UNTUK STRIP "-" --}}
                @if(in_array('persetujuan', $kolom))
                    <td>
                        @if($task->status_approval == '-')
                            <div style="text-align: center; color: #999;">-</div>
                        @else
                            {{ $task->status_approval }}
                        @endif
                    </td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="{{ count($kolom) + 1 }}" class="text-center" style="padding: 20px;">Tidak ada rekam pekerjaan pada rentang tanggal tersebut.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 40px; width: 100%;">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 50%; text-align: center; border: none; vertical-align: top;">
                    <br>
                    Pelaksana Tugas,<br>
                    <br><br><br><br><br>
                    <strong>( {{ $targetUser }} )</strong>
                </td>
                <td style="width: 50%; text-align: center; border: none; vertical-align: top;">
                    Bangkalan, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    Mengetahui,<br>
                    <strong>Kepala Laboratorium</strong>
                    <br><br><br><br><br>
                    <strong>(......................................................)</strong>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>