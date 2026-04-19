<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan - {{ $sampel->kode_sample }}</title>
    <style>
        /* Base Style */
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #000;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }

        @page {
            margin: 30px 40px 40px 40px;
        }

        footer {
            position: fixed;
            bottom: -20px;
            left: 0px;
            right: 0px;
            height: 20px;
            text-align: center;
            font-size: 8px;
            color: #777;
        }
        .pagenum:before { content: counter(page); }

        /* Header Table */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #000;
            margin-bottom: 15px;
        }
        .header-table td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: middle;
        }
        .logo-box { width: 15%; text-align: center; }
        .title-box { width: 60%; text-align: center; font-weight: bold; font-size: 13px; text-transform: uppercase; }
        .doc-info { width: 25%; font-size: 8px; line-height: 1.3; }

        /* Meta Data */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            border: 1px solid #000;
        }
        .meta-table td {
            padding: 4px 8px;
            vertical-align: top;
            width: 25%;
            border: 1px solid #eee;
        }
        .meta-label { font-weight: bold; background: #fafafa; width: 20%; }

        /* Departemen Block */
        .dept-block { margin-bottom: 15px; width: 100%; }
        .dept-header {
            width: 100%;
            margin-bottom: 3px;
            border-bottom: 1.5px solid #000;
        }
        .dept-num {
            display: inline-block;
            background: #000;
            color: #fff;
            width: 18px;
            height: 18px;
            text-align: center;
            line-height: 18px;
            font-weight: bold;
            font-size: 10px;
            margin-right: 5px;
        }
        .dept-name {
            display: inline-block;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }

        .logistik-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; font-size: 9px; }
        .logistik-table td { width: 33.3%; vertical-align: top; padding: 1px 0; }

        /* Style Tabel (Digunakan untuk Data Tambahan & Pemeriksaan) */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.2px solid #000;
            margin-bottom: 8px;
        }
        .data-table th {
            background-color: #f0f0f0;
            border: 1px solid #000;
            padding: 4px;
            font-size: 8.5px;
            text-align: center;
            text-transform: uppercase;
        }
        .data-table td {
            border: 1px solid #000;
            padding: 4px;
            font-size: 9px;
        }

        /* Status & Signature - Page Break Fix */
        .signature-wrapper {
            page-break-inside: avoid;
            width: 100%;
            margin-top: 10px;
        }
        .status-section { text-align: right; margin-bottom: 5px; }
        .status-box {
            display: inline-block;
            border: 2px solid #000;
            padding: 3px 10px;
            font-weight: bold;
            font-size: 10px;
        }

        .signature-section {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-section td {
            border: 1px solid #000;
            width: 50%;
            padding: 0;
            text-align: center;
        }
        .sig-header { background: #f0f0f0; border-bottom: 1px solid #000; padding: 4px; font-weight: bold; font-size: 8.5px; }
        .sig-space { height: 60px; vertical-align: middle; }
        .sig-footer { padding: 5px; line-height: 1.1; }
        .sig-name { font-weight: bold; text-decoration: underline; text-transform: uppercase; font-size: 10px; }

        .badge-sign {
            display: inline-block;
            color: #28a745;
            border: 2px solid #28a745;
            padding: 2px 8px;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
        }
        .badge-sign-blue {
            color: #007bff;
            border: 2px solid #007bff;
        }
        .small-sign-text { font-size: 6px; color: #777; display: block; margin-top: 2px; }
    </style>
</head>
<body>

    <footer>
        Halaman <span class="pagenum"></span> | {{ $sampel->kode_sample }} | {{ date('d/m/Y H:i') }}
    </footer>

    {{-- HEADER --}}
    <table class="header-table">
        <tr>
            <td class="logo-box">
                <img src="{{ public_path('icon_mark.png') }}" style="max-height: 30px;">
            </td>
            <td class="title-box">Formulir Pembuatan Sample Customer</td>
            <td class="doc-info">
                No. Dok : <strong>MDDFM-QC37</strong><br>
                Revisi : 02<br>
                Tgl Efektif : {{ $formulir->created_at->format('d/m/Y') }}
            </td>
        </tr>
    </table>

    {{-- META DATA --}}
    <table class="meta-table">
        <tr>
            <td class="meta-label">Kode Sample</td><td>: {{ $sampel->kode_sample }}</td>
            <td class="meta-label">Customer</td><td>: {{ $sampel->customer }}</td>
        </tr>
        <tr>
            <td class="meta-label">Model / Size</td><td>: {{ $sampel->model }} / {{ $formulir->size }}</td>
            <td class="meta-label">Tgl Permintaan</td><td>: {{ date('d/m/Y', strtotime($formulir->tanggal_permintaan)) }}</td>
        </tr>
        <tr>
            <td class="meta-label">Spesifikasi</td><td>: {{ $sampel->spesifikasi }}</td>
            <td class="meta-label">Qty Kirim</td><td>: <strong>{{ $formulir->qty_sampel_kirim }}</strong></td>
        </tr>
        <tr>
            <td class="meta-label">Running Ke</td><td>: {{ $formulir->running_ke }}</td>
            <td class="meta-label">Status Dokumen</td><td>: {{ strtoupper($formulir->status) }}</td>
        </tr>
    </table>

    {{-- DEPARTEMEN LIST --}}
    @foreach($formulir->departemen_terlibat as $dept)

        @if($loop->last)
            <div class="signature-wrapper">
        @endif

        <div class="dept-block" style="page-break-inside: avoid;">
            <div class="dept-header">
                <span class="dept-num">{{ $loop->iteration }}</span>
                <span class="dept-name">{{ $dept->sub_departemen->nama ?? 'N/A' }}</span>
            </div>

            <table class="logistik-table">
                <tr>
                    <td>
                        Tgl Terima: {{ $dept->tanggal_diterima ? date('d/m/Y H:i', strtotime($dept->tanggal_diterima)) : '-' }}<br>
                        Penerima: {{ $dept->penerima->name ?? '-' }}
                    </td>
                    <td style="text-align: center;">
                        Tgl Selesai: {{ $dept->tanggal_selesai ? date('d/m/Y H:i', strtotime($dept->tanggal_selesai)) : '-' }}<br>
                        Qty: <strong>{{ $dept->qty ?? 0 }}</strong>
                    </td>
                    <td style="text-align: right;">
                        Paraf QC: {{ $dept->qcUser->name ?? '-' }}<br>
                        Paraf SPV: {{ $dept->spvUser->name ?? '-' }}
                    </td>
                </tr>
            </table>

            {{-- DATA TAMBAHAN DALAM BENTUK TABEL --}}
            @if($dept->data_tambahan && is_array($dept->data_tambahan))
                <table class="data-table">
                    <thead>
                        <tr>
                            <th colspan="2" style="text-align: left; background-color: #f9f9f9;">Informasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dept->data_tambahan as $key => $value)
                        <tr>
                            <td width="50%" style="background-color: #fafafa; font-weight: bold; text-transform: uppercase;">{{ $key }}</td>
                            <td>{{ $value ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            {{-- TABEL PEMERIKSAAN --}}
            @if($dept->item_pemeriksaan && count($dept->item_pemeriksaan) > 0)
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="50%">ITEM PEMERIKSAAN</th>
                            <th width="25%">SPEC QC</th>
                            <th width="25%">ACTUAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dept->item_pemeriksaan as $row)
                        <tr>
                            <td>{{ $row['item'] ?? '-' }}</td>
                            <td style="text-align: center; font-family: monospace;">{{ $row['spec'] ?? '-' }}</td>
                            <td style="text-align: center; font-weight: bold;">{{ $row['actual'] ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>


                    {{-- Tempelkan Signature tepat setelah departemen terakhir di dalam wrapper yang sama --}}
        @if($loop->last)
            <div class="status-section">
                <div class="status-box">STATUS: {{ strtoupper($formulir->status) }}</div>
            </div>

            <table class="signature-section">
                <tr>
                    <td>
                        <div class="sig-header">DI PERIKSA OLEH :</div>
                        <div class="sig-space">
                            @if($formulir->diperiksa_oleh)
                                <div class="badge-sign">✔ PASSED</div>
                                <span class="small-sign-text">Digitally Signed by QC Manager</span>
                            @endif
                        </div>
                        <div class="sig-footer">
                            <span class="sig-name">{{ $formulir->pemeriksa->name ?? '..........................' }}</span><br>
                            QC MANAGER
                        </div>
                    </td>
                    <td>
                        <div class="sig-header">DI SETUJUI OLEH :</div>
                        <div class="sig-space">
                            @if($formulir->disetujui_oleh)
                                <div class="badge-sign badge-sign-blue">✔ APPROVED</div>
                                <span class="small-sign-text">Digitally Signed by Factory Manager</span>
                            @endif
                        </div>
                        <div class="sig-footer">
                            <span class="sig-name">{{ $formulir->penyetuju->name ?? '..........................' }}</span><br>
                            FACTORY MANAGER / GM
                        </div>
                    </td>
                </tr>
            </table>
            </div> {{-- Tutup signature-wrapper --}}
        @endif



    @endforeach

</body>
</html>
