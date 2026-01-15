<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report Absensi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
        }

        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .header .subtitle {
            font-size: 12px;
            color: #666;
        }

        .filters {
            margin-bottom: 15px;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 5px;
        }

        .filters p {
            font-size: 11px;
            margin: 3px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 5px 4px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #5966f7;
            color: #fff;
            font-weight: bold;
            white-space: nowrap;
        }

        tr:nth-child(even) td {
            background: #f9f9f9;
        }

        .date-cell {
            min-width: 100px;
            font-size: 8px;
        }

        .stat-cell {
            text-align: center;
            font-weight: bold;
        }

        .stat-green {
            color: #065f46;
            background: #ecfdf5;
        }

        .stat-red {
            color: #991b1b;
            background: #fef2f2;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }

        @media print {
            body {
                padding: 10px;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORT ABSENSI PEGAWAI</h1>
    </div>

    @if($filters['tanggal_from'] || $filters['tanggal_to'])
    <div class="filters">
        <p><strong>Periode:</strong> 
            {{ $filters['tanggal_from'] ? \Carbon\Carbon::parse($filters['tanggal_from'])->format('d/m/Y') : 'Awal' }} 
            - 
            {{ $filters['tanggal_to'] ? \Carbon\Carbon::parse($filters['tanggal_to'])->format('d/m/Y') : 'Akhir' }}
        </p>
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>NIP</th>
                <th>Nama</th>
                <th>Atasan</th>
                <th>Unit Kerja</th>
                @foreach($dates as $date)
                    <th class="date-cell">
                        {{ $date }}
                    </th>
                @endforeach
                <th>ABSEN S.D 08:00</th>
                <th>ABSEN >08:00 / TIDAK ABSEN</th>
                <th>RATA² JAM KERJA</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData as $row)
            <tr>
                <td>{{ $row['nip'] }}</td>
                <td>{{ $row['nama'] }}</td>
                <td>{{ $row['nama_atasan'] }}</td>
                <td>{{ $row['biro_name'] }}</td>
                @foreach($dates as $date)
                    <td class="date-cell">
                        {{ strip_tags($row['absen_data'][$date]['display'] ?? '-') }}
                    </td>
                @endforeach
                <td class="stat-cell stat-green">{{ $row['absen_on_time'] }}</td>
                <td class="stat-cell stat-red">{{ $row['absen_late_or_absent'] }}</td>
                <td class="stat-cell">{{ $row['avg_work_time'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ 4 + count($dates) + 3 }}" style="text-align: center; padding: 20px;">
                    Tidak ada data
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Total: {{ count($reportData) }} pegawai | {{ count($dates) }} hari kerja
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #5966f7; color: #fff; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">
            🖨️ Print / Save as PDF
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6b7280; color: #fff; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; margin-left: 10px;">
            ✕ Close
        </button>
    </div>
</body>
</html>
