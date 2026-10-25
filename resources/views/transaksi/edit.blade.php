@extends('layouts.app')

@section('title', 'Edit Transaksi - ' . $transaksi->kode_transaksi)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Edit Transaksi</h2>
            <span class="badge bg-primary fs-6">
                <i class="bi bi-receipt me-1"></i>{{ $transaksi->kode_transaksi }}
            </span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('transaksi.show', $transaksi) }}" class="btn btn-outline-info">
                <i class="bi bi-eye"></i> Lihat Detail
            </a>
            <a href="{{ route('transaksi.riwayat') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Riwayat
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4">
        <i class="bi bi-info-circle-fill fs-4 text-info me-3"></i>
        <div>
            <strong>Mode Edit Transaksi:</strong> Anda dapat mengubah data karyawan, tanggal, keterangan, menambah/mengurangi item barang, serta metode pembayaran.
            <div class="small text-muted">Stok barang akan otomatis dikalkulasi dan disesuaikan kembali dengan perubahan yang Anda simpan.</div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('transaksi.update', $transaksi) }}" method="POST" id="formEditTransaksi">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-5">
                        <label for="karyawan_id" class="form-label fw-semibold">Karyawan <span class="text-danger">*</span></label>
                        <select class="form-select select2 @error('karyawan_id') is-invalid @enderror" id="karyawan_id"
                            name="karyawan_id" required>
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach($karyawans as $karyawan)
                                <option value="{{ $karyawan->id }}"
                                    data-dept="{{ $karyawan->departemen->nama_departemen ?? '-' }}"
                                    data-nama="{{ $karyawan->nama_karyawan }}"
                                    data-nip="{{ $karyawan->nip ?? '-' }}"
                                    {{ old('karyawan_id', $transaksi->karyawan_id) == $karyawan->id ? 'selected' : '' }}>
                                    {{ $karyawan->departemen->nama_departemen ?? '-' }} | {{ $karyawan->nama_karyawan }} | {{ $karyawan->nip ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                        @error('karyawan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="created_at" class="form-label fw-semibold">Tanggal & Waktu Transaksi</label>
                        <input type="datetime-local" class="form-control @error('created_at') is-invalid @enderror"
                            id="created_at" name="created_at"
                            value="{{ old('created_at', $transaksi->created_at?->format('Y-m-d\TH:i')) }}">
                        @error('created_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="keterangan" class="form-label fw-semibold">Keterangan</label>
                        <input type="text" class="form-control @error('keterangan') is-invalid @enderror"
                            id="keterangan" name="keterangan"
                            placeholder="Catatan / keterangan (opsional)"
                            value="{{ old('keterangan', $transaksi->keterangan) }}">
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                {{-- ===== SCAN BARCODE SECTION ===== --}}
                <div class="card border-2 border-dashed mb-4" id="scan-area" style="border-color: #6c757d !important; transition: all 0.3s ease;">
                    <div class="card-body py-3">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="scan-icon-wrapper rounded-circle d-flex align-items-center justify-content-center"
                                     style="width: 50px; height: 50px; background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%); transition: all 0.3s ease;">
                                    <i class="bi bi-upc-scan text-white fs-4"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bi bi-lightning-charge text-warning me-1"></i>Scan Barcode
                                </h6>
                                <small class="text-muted" id="scan-status">Siap scan... Arahkan scanner Honeywell atau ketik barcode untuk menambah item</small>
                            </div>
                            <div class="col-auto">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="text" id="manual-barcode-input" class="form-control form-control-sm"
                                        placeholder="Ketik kode barcode..." style="width: 200px;">
                                    <button type="button" class="btn btn-sm btn-primary" id="btn-manual-scan">
                                        <i class="bi bi-search"></i> Cari
                                    </button>
                                </div>
                            </div>
                        </div>
                        {{-- Scan result toast --}}
                        <div id="scan-result" class="mt-2 d-none">
                            <div class="alert mb-0 py-2 px-3 d-flex align-items-center" id="scan-result-alert">
                                <i class="bi me-2 fs-5" id="scan-result-icon"></i>
                                <span id="scan-result-text"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-cart-check me-2"></i>Item Belanja</h5>
                    <small class="text-muted">Minimal harus ada 1 item</small>
                </div>

                @php
                    // Ambil item dari old() jika validasi gagal, atau dari transaksiDetails
                    $itemsData = old('items');
                    if (!$itemsData) {
                        $itemsData = $transaksi->transaksiDetails->map(function($detail) {
                            return [
                                'barang_id' => $detail->barang_id,
                                'jumlah' => $detail->jumlah,
                                'metode_pembayaran' => $detail->metode_pembayaran,
                            ];
                        })->toArray();
                    }
                    if (empty($itemsData)) {
                        $itemsData = [['barang_id' => '', 'jumlah' => 1, 'metode_pembayaran' => 'piutang']];
                    }
                @endphp

                <div id="items-container">
                    @foreach($itemsData as $index => $item)
                        <div class="row mb-2 item-row align-items-center">
                            <div class="col-md-4">
                                <label class="form-label d-md-none small fw-semibold">Barang</label>
                                <select class="form-select select2-barang barang-select" name="items[{{ $index }}][barang_id]" required>
                                    <option value="">-- Pilih Barang --</option>
                                    @foreach($barangs as $barang)
                                        <option value="{{ $barang->id }}"
                                            data-harga="{{ $barang->harga_jual }}"
                                            data-qr="{{ $barang->qr_code }}"
                                            {{ (string)($item['barang_id'] ?? '') === (string)$barang->id ? 'selected' : '' }}>
                                            {{ $barang->nama_barang }} - Rp {{ number_format($barang->harga_jual, 0, ',', '.') }} (Stok: {{ $barang->stok_tersedia ?? $barang->stok }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label d-md-none small fw-semibold">Jumlah</label>
                                <input type="number" class="form-control jumlah-input" name="items[{{ $index }}][jumlah]"
                                    placeholder="Jumlah" min="1" value="{{ $item['jumlah'] ?? 1 }}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label d-md-none small fw-semibold">Metode</label>
                                <select class="form-select" name="items[{{ $index }}][metode_pembayaran]" required>
                                    <option value="piutang" {{ ($item['metode_pembayaran'] ?? 'piutang') == 'piutang' ? 'selected' : '' }}>piutang</option>
                                    <option value="tunai" {{ ($item['metode_pembayaran'] ?? '') == 'tunai' ? 'selected' : '' }}>tunai</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label d-md-none small fw-semibold">Subtotal</label>
                                <input type="text" class="form-control subtotal-display bg-light" readonly placeholder="Subtotal">
                            </div>
                            <div class="col-md-1 text-end text-md-center">
                                <button type="button" class="btn btn-outline-danger btn-remove-item" title="Hapus Item" {{ count($itemsData) <= 1 ? 'disabled' : '' }}>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mb-4">
                    <button type="button" class="btn btn-outline-primary" id="btn-add-item">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Item
                    </button>
                </div>

                <hr class="my-4">

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <span class="text-muted d-block small">TOTAL PEMBAYARAN:</span>
                        <h3 class="mb-0 fw-bold text-primary" id="grand-total">Rp 0</h3>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('transaksi.riwayat') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-x-circle me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-warning btn-lg text-dark fw-bold">
                            <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .karyawan-option {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .karyawan-option .col-dept {
            display: inline-block;
            width: 120px;
            min-width: 120px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .karyawan-option .col-nama {
            display: inline-block;
            width: 180px;
            min-width: 180px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .karyawan-option .col-nip {
            display: inline-block;
            color: #6c757d;
            white-space: nowrap;
        }
        .karyawan-option .separator {
            color: #adb5bd;
        }

        .border-dashed {
            border-style: dashed !important;
        }

        #scan-area.scanning {
            border-color: #0d6efd !important;
            background: rgba(13, 110, 253, 0.03);
        }
        #scan-area.scanning .scan-icon-wrapper {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
            animation: pulse-scan 1.5s ease-in-out infinite;
        }

        #scan-area.scan-success {
            border-color: #198754 !important;
            background: rgba(25, 135, 84, 0.05);
        }
        #scan-area.scan-success .scan-icon-wrapper {
            background: linear-gradient(135deg, #198754 0%, #146c43 100%) !important;
        }

        #scan-area.scan-error {
            border-color: #dc3545 !important;
            background: rgba(220, 53, 69, 0.05);
        }
        #scan-area.scan-error .scan-icon-wrapper {
            background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%) !important;
        }

        @keyframes pulse-scan {
            0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.4); }
            50% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(13, 110, 253, 0); }
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function () {
            const csrfToken = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();

            // ===== Audio Context untuk beep =====
            let audioCtx = null;
            function playBeep(frequency, duration, type) {
                try {
                    if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    const oscillator = audioCtx.createOscillator();
                    const gainNode = audioCtx.createGain();
                    oscillator.connect(gainNode);
                    gainNode.connect(audioCtx.destination);
                    oscillator.frequency.value = frequency;
                    oscillator.type = type || 'sine';
                    gainNode.gain.value = 0.3;
                    oscillator.start();
                    setTimeout(() => { oscillator.stop(); }, duration);
                } catch(e) { }
            }
            function beepSuccess() { playBeep(1200, 150, 'sine'); }
            function beepError() { playBeep(300, 300, 'square'); }

            // ===== Barcode Scanner Detection =====
            let barcodeBuffer = '';
            let barcodeTimeout = null;
            const SCAN_THRESHOLD_MS = 80;

            $(document).on('keydown', function (e) {
                const activeEl = document.activeElement;
                const tagName = activeEl ? activeEl.tagName.toLowerCase() : '';
                const isFormField = (tagName === 'input' || tagName === 'textarea' || tagName === 'select');

                if (isFormField) return;

                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (barcodeBuffer.length >= 3) {
                        processScan(barcodeBuffer.trim());
                    }
                    barcodeBuffer = '';
                    clearTimeout(barcodeTimeout);
                    return;
                }

                if (e.key.length === 1) {
                    barcodeBuffer += e.key;
                    e.preventDefault();
                    clearTimeout(barcodeTimeout);
                    barcodeTimeout = setTimeout(function () {
                        barcodeBuffer = '';
                    }, SCAN_THRESHOLD_MS);
                }
            });

            $('#manual-barcode-input').on('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const code = $(this).val().trim();
                    if (code.length >= 1) {
                        processScan(code);
                        $(this).val('');
                    }
                }
            });

            $('#btn-manual-scan').on('click', function () {
                const code = $('#manual-barcode-input').val().trim();
                if (code.length >= 1) {
                    processScan(code);
                    $('#manual-barcode-input').val('');
                }
            });

            function processScan(qrCode) {
                showScanStatus('scanning', 'Mencari barang...');

                $.ajax({
                    url: '{{ route("transaksi.scan") }}',
                    method: 'POST',
                    data: {
                        _token: csrfToken,
                        qr_code: qrCode
                    },
                    success: function (response) {
                        if (response.success) {
                            const barang = response.barang;
                            addOrIncrementItem(barang);
                            showScanResult('success', '✅ ' + barang.nama_barang + ' — Rp ' + parseInt(barang.harga_jual).toLocaleString('id-ID'));
                            beepSuccess();
                        }
                    },
                    error: function (xhr) {
                        const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Barang tidak ditemukan';
                        showScanResult('error', '❌ ' + msg);
                        beepError();
                    }
                });
            }

            function addOrIncrementItem(barang) {
                let existingRow = null;
                $('.item-row').each(function () {
                    const select = $(this).find('.barang-select');
                    if (select.val() == barang.id) {
                        existingRow = $(this);
                        return false;
                    }
                });

                if (existingRow) {
                    const jumlahInput = existingRow.find('.jumlah-input');
                    const currentQty = parseInt(jumlahInput.val()) || 0;
                    jumlahInput.val(currentQty + 1);
                    calculateSubtotal(existingRow);

                    existingRow.css('background-color', '#fff3cd');
                    setTimeout(() => existingRow.css('background-color', ''), 800);
                } else {
                    const firstRow = $('.item-row').first();
                    const firstSelect = firstRow.find('.barang-select');

                    if (!firstSelect.val()) {
                        firstSelect.val(barang.id).trigger('change');
                        firstRow.find('.jumlah-input').val(1);
                        calculateSubtotal(firstRow);

                        firstRow.css('background-color', '#fff3cd');
                        setTimeout(() => firstRow.css('background-color', ''), 800);
                    } else {
                        addNewItemRow(barang);
                    }
                }
                calculateGrandTotal();
            }

            function addNewItemRow(barang) {
                const newRow = $(`
                    <div class="row mb-2 item-row align-items-center" style="background-color: #fff3cd; transition: background-color 0.5s;">
                        <div class="col-md-4">
                            <label class="form-label d-md-none small fw-semibold">Barang</label>
                            <select class="form-select select2-barang barang-select" name="items[${itemIndex}][barang_id]" required>
                                ${barangOptionsHtml}
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label d-md-none small fw-semibold">Jumlah</label>
                            <input type="number" class="form-control jumlah-input" name="items[${itemIndex}][jumlah]"
                                placeholder="Jumlah" min="1" value="1" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label d-md-none small fw-semibold">Metode</label>
                            <select class="form-select" name="items[${itemIndex}][metode_pembayaran]" required>
                                <option value="piutang" selected>piutang</option>
                                <option value="tunai">tunai</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label d-md-none small fw-semibold">Subtotal</label>
                            <input type="text" class="form-control subtotal-display bg-light" readonly placeholder="Subtotal">
                        </div>
                        <div class="col-md-1 text-end text-md-center">
                            <button type="button" class="btn btn-outline-danger btn-remove-item" title="Hapus Item">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                `);

                $('#items-container').append(newRow);
                initSelect2Barang(newRow.find('.select2-barang'));

                if (barang && barang.id) {
                    newRow.find('.barang-select').val(barang.id).trigger('change');
                }
                attachEventListeners(newRow);
                calculateSubtotal(newRow);

                itemIndex++;
                updateRemoveButtons();

                setTimeout(() => newRow.css('background-color', ''), 800);
            }

            function showScanStatus(status, text) {
                const scanArea = $('#scan-area');
                scanArea.removeClass('scanning scan-success scan-error');
                if (status) scanArea.addClass(status);
                $('#scan-status').text(text);
            }

            function showScanResult(type, message) {
                const scanArea = $('#scan-area');
                scanArea.removeClass('scanning scan-success scan-error');
                scanArea.addClass(type === 'success' ? 'scan-success' : 'scan-error');

                const resultDiv = $('#scan-result');
                const alertDiv = $('#scan-result-alert');
                const iconEl = $('#scan-result-icon');
                const textEl = $('#scan-result-text');

                alertDiv.removeClass('alert-success alert-danger');
                iconEl.removeClass('bi-check-circle-fill bi-x-circle-fill');

                if (type === 'success') {
                    alertDiv.addClass('alert-success');
                    iconEl.addClass('bi-check-circle-fill');
                    $('#scan-status').text('Barang berhasil ditambahkan!');
                } else {
                    alertDiv.addClass('alert-danger');
                    iconEl.addClass('bi-x-circle-fill');
                    $('#scan-status').text('Scan gagal — coba lagi');
                }

                textEl.text(message);
                resultDiv.removeClass('d-none');

                setTimeout(function () {
                    resultDiv.addClass('d-none');
                    showScanStatus(null, 'Siap scan... Arahkan scanner Honeywell atau ketik barcode untuk menambah item');
                }, 3000);
            }

            function formatKaryawan(item) {
                if (!item.id) return item.text;
                var $el = $(item.element);
                var dept = $el.data('dept') || '-';
                var nama = $el.data('nama') || '-';
                var nip  = $el.data('nip')  || '-';
                return $(
                    '<div class="karyawan-option">' +
                        '<span class="col-dept">' + dept + '</span>' +
                        '<span class="separator">|</span>' +
                        '<span class="col-nama">' + nama + '</span>' +
                        '<span class="separator">|</span>' +
                        '<span class="col-nip">' + nip + '</span>' +
                    '</div>'
                );
            }

            $('#karyawan_id').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Pilih Karyawan --',
                allowClear: true,
                width: '100%',
                templateResult: formatKaryawan,
                templateSelection: formatKaryawan
            });

            let itemIndex = {{ count($itemsData) }};

            const barangOptionsHtml = `<option value="">-- Pilih Barang --</option>
                @foreach($barangs as $barang)
                    <option value="{{ $barang->id }}" data-harga="{{ $barang->harga_jual }}" data-qr="{{ $barang->qr_code }}">
                        {{ $barang->nama_barang }} - Rp {{ number_format($barang->harga_jual, 0, ',', '.') }} (Stok: {{ $barang->stok_tersedia ?? $barang->stok }})
                    </option>
                @endforeach`;

            function initSelect2Barang(element) {
                element.select2({
                    theme: 'bootstrap-5',
                    placeholder: '-- Pilih Barang --',
                    allowClear: true,
                    width: '100%'
                });
            }

            // Inisialisasi semua select2 barang yang sudah ada
            initSelect2Barang($('.select2-barang'));

            $('#btn-add-item').on('click', function () {
                addNewItemRow(null);
            });

            $(document).on('click', '.btn-remove-item', function () {
                const btn = $(this);
                if (!btn.prop('disabled')) {
                    btn.closest('.item-row').remove();
                    updateRemoveButtons();
                    calculateGrandTotal();
                }
            });

            function updateRemoveButtons() {
                const rows = $('.item-row');
                rows.find('.btn-remove-item').prop('disabled', rows.length <= 1);
            }

            function attachEventListeners(row) {
                row.find('.barang-select').on('change', function () { calculateSubtotal(row); });
                row.find('.jumlah-input').on('input', function () { calculateSubtotal(row); });
            }

            function calculateSubtotal(row) {
                const select = row.find('.barang-select')[0];
                const jumlah = row.find('.jumlah-input').val();
                let harga = 0;
                if (select && select.selectedIndex >= 0) {
                    const option = select.options[select.selectedIndex];
                    harga = option ? option.dataset.harga : 0;
                }
                const subtotal = (harga || 0) * (jumlah || 0);
                row.find('.subtotal-display').val(subtotal > 0 ? 'Rp ' + parseInt(subtotal).toLocaleString('id-ID') : 'Rp 0');
                calculateGrandTotal();
            }

            function calculateGrandTotal() {
                let total = 0;
                $('.item-row').each(function () {
                    const select = $(this).find('.barang-select')[0];
                    const jumlah = $(this).find('.jumlah-input').val();
                    let harga = 0;
                    if (select && select.selectedIndex >= 0) {
                        const option = select.options[select.selectedIndex];
                        harga = option ? option.dataset.harga : 0;
                    }
                    total += (harga || 0) * (jumlah || 0);
                });
                $('#grand-total').text('Rp ' + parseInt(total).toLocaleString('id-ID'));
            }

            $('.item-row').each(function () {
                attachEventListeners($(this));
                calculateSubtotal($(this));
            });

            calculateGrandTotal();
            updateRemoveButtons();

            $('#formEditTransaksi').on('submit', function() {
                const btn = $(this).find('button[type="submit"]');
                btn.prop('disabled', true);
                btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan Perubahan...');
                return true;
            });
        });
    </script>
@endpush
