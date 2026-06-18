@extends('layouts.app')

@section('title', 'Transaksi Baru')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Transaksi Baru</h2>
        <a href="{{ route('transaksi.riwayat') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Riwayat Transaksi
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('transaksi.store') }}" method="POST" id="formTransaksi">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="karyawan_id" class="form-label">Karyawan <span class="text-danger">*</span></label>
                        <select class="form-select select2 @error('karyawan_id') is-invalid @enderror" id="karyawan_id"
                            name="karyawan_id" required>
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach($karyawans as $karyawan)
                                <option value="{{ $karyawan->id }}"
                                    data-dept="{{ $karyawan->departemen->nama_departemen ?? '-' }}"
                                    data-nama="{{ $karyawan->nama_karyawan }}"
                                    data-nip="{{ $karyawan->nip ?? '-' }}"
                                    {{ old('karyawan_id') == $karyawan->id ? 'selected' : '' }}>
                                    {{ $karyawan->departemen->nama_departemen ?? '-' }} | {{ $karyawan->nama_karyawan }} | {{ $karyawan->nip ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                        @error('karyawan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <input type="text" class="form-control" id="keterangan" name="keterangan"
                            value="{{ old('keterangan') }}">
                    </div>
                </div>

                <hr>

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
                                <small class="text-muted" id="scan-status">Siap scan... Arahkan scanner Honeywell ke barcode produk</small>
                            </div>
                            <div class="col-auto">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="text" id="manual-barcode-input" class="form-control form-control-sm"
                                        placeholder="Ketik kode barcode..." style="width: 200px;">
                                    <button type="button" class="btn btn-sm btn-primary" id="btn-manual-scan">
                                        <i class="bi bi-search"></i>
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

                <h5>Item Belanja</h5>

                <div id="items-container">
                    <div class="row mb-2 item-row align-items-center">
                        <div class="col-md-4">
                            <select class="form-select select2-barang barang-select" name="items[0][barang_id]" required>
                                <option value="">-- Pilih Barang --</option>
                                @foreach($barangs as $barang)
                                    <option value="{{ $barang->id }}" data-harga="{{ $barang->harga_jual }}" data-qr="{{ $barang->qr_code }}">
                                        {{ $barang->nama_barang }} - Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}
                                        (Stok: {{ $barang->stok }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control jumlah-input" name="items[0][jumlah]"
                                placeholder="Jumlah" min="1" required>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="items[0][metode_pembayaran]" required>
                                <option value="piutang" selected>piutang</option>
                                <option value="tunai">tunai</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control subtotal-display" readonly placeholder="Subtotal">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn-remove-item" disabled>
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-outline-primary mb-3" id="btn-add-item">
                    <i class="bi bi-plus"></i> Tambah Item
                </button>

                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <h4>Total: <span id="grand-total">Rp 0</span></h4>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-save"></i> Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Kolom dropdown karyawan sejajar */
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

        /* Scan area styles */
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
            // ===== CSRF Token untuk AJAX =====
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
                } catch(e) { /* ignore audio errors */ }
            }
            function beepSuccess() { playBeep(1200, 150, 'sine'); }
            function beepError() { playBeep(300, 300, 'square'); }

            // ===== Barcode Scanner Detection =====
            // Honeywell scanners work in keyboard wedge mode:
            // They type characters very fast and end with Enter key
            let barcodeBuffer = '';
            let barcodeTimeout = null;
            const SCAN_THRESHOLD_MS = 80; // Max ms between keystrokes to count as scanner input

            $(document).on('keydown', function (e) {
                // Ignore if focus is on manual barcode input, select2, or other form fields
                const activeEl = document.activeElement;
                const tagName = activeEl ? activeEl.tagName.toLowerCase() : '';
                const isFormField = (tagName === 'input' || tagName === 'textarea' || tagName === 'select');

                // If typing in any form field, let the field handle it normally
                if (isFormField) return;

                // Enter key = end of barcode scan
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (barcodeBuffer.length >= 3) {
                        processScan(barcodeBuffer.trim());
                    }
                    barcodeBuffer = '';
                    clearTimeout(barcodeTimeout);
                    return;
                }

                // Only accept printable characters
                if (e.key.length === 1) {
                    barcodeBuffer += e.key;
                    e.preventDefault(); // Prevent character from appearing in any field

                    // Reset the timeout - if too slow, it's manual typing not a scanner
                    clearTimeout(barcodeTimeout);
                    barcodeTimeout = setTimeout(function () {
                        barcodeBuffer = ''; // Clear if too slow
                    }, SCAN_THRESHOLD_MS);
                }
            });

            // ===== Manual barcode input =====
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

            // ===== Process Scanned Barcode =====
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

            // ===== Add item to cart or increment quantity =====
            function addOrIncrementItem(barang) {
                // Check if item already exists in cart
                let existingRow = null;
                $('.item-row').each(function () {
                    const select = $(this).find('.barang-select');
                    if (select.val() == barang.id) {
                        existingRow = $(this);
                        return false; // break
                    }
                });

                if (existingRow) {
                    // Increment quantity
                    const jumlahInput = existingRow.find('.jumlah-input');
                    const currentQty = parseInt(jumlahInput.val()) || 0;
                    jumlahInput.val(currentQty + 1);
                    calculateSubtotal(existingRow);

                    // Flash highlight
                    existingRow.css('background-color', '#d4edda');
                    setTimeout(() => existingRow.css('background-color', ''), 800);
                } else {
                    // Check if first row is empty (no barang selected)
                    const firstRow = $('.item-row').first();
                    const firstSelect = firstRow.find('.barang-select');

                    if (!firstSelect.val()) {
                        // Use the first empty row
                        firstSelect.val(barang.id).trigger('change');
                        firstRow.find('.jumlah-input').val(1);
                        calculateSubtotal(firstRow);

                        firstRow.css('background-color', '#d4edda');
                        setTimeout(() => firstRow.css('background-color', ''), 800);
                    } else {
                        // Add new row
                        addNewItemRow(barang);
                    }
                }
                calculateGrandTotal();
            }

            // ===== Add new item row with pre-selected barang =====
            function addNewItemRow(barang) {
                const newRow = $(`
                    <div class="row mb-2 item-row align-items-center" style="background-color: #d4edda; transition: background-color 0.5s;">
                        <div class="col-md-4">
                            <select class="form-select select2-barang barang-select" name="items[${itemIndex}][barang_id]" required>
                                ${barangOptionsHtml}
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control jumlah-input" name="items[${itemIndex}][jumlah]"
                                placeholder="Jumlah" min="1" value="1" required>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="items[${itemIndex}][metode_pembayaran]" required>
                                <option value="piutang" selected>piutang</option>
                                <option value="tunai">tunai</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control subtotal-display" readonly placeholder="Subtotal">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn-remove-item">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                `);

                $('#items-container').append(newRow);
                initSelect2Barang(newRow.find('.select2-barang'));

                // Set the barang value
                newRow.find('.barang-select').val(barang.id).trigger('change');
                attachEventListeners(newRow);
                calculateSubtotal(newRow);

                itemIndex++;
                updateRemoveButtons();

                // Remove highlight
                setTimeout(() => newRow.css('background-color', ''), 800);
            }

            // ===== Scan UI helpers =====
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

                // Auto-hide after 3 seconds
                setTimeout(function () {
                    resultDiv.addClass('d-none');
                    showScanStatus(null, 'Siap scan... Arahkan scanner Honeywell ke barcode produk');
                }, 3000);
            }

            // ===== ORIGINAL TRANSACTION LOGIC =====

            // Template karyawan: kolom sejajar
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

            // Inisialisasi Select2 untuk karyawan
            $('#karyawan_id').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Pilih Karyawan --',
                allowClear: true,
                width: '100%',
                templateResult: formatKaryawan,
                templateSelection: formatKaryawan
            });

            // Inisialisasi Select2 untuk barang pertama
            initSelect2Barang($('.select2-barang'));

            let itemIndex = 1;

            // Template options barang untuk row baru
            const barangOptionsHtml = `<option value="">-- Pilih Barang --</option>
                @foreach($barangs as $barang)
                    <option value="{{ $barang->id }}" data-harga="{{ $barang->harga_jual }}" data-qr="{{ $barang->qr_code }}">{{ $barang->nama_barang }} - Rp {{ number_format($barang->harga_jual, 0, ',', '.') }} (Stok: {{ $barang->stok }})</option>
                @endforeach`;

            // Tambah item baru
            $('#btn-add-item').on('click', function () {
                const newRow = $(`
                    <div class="row mb-2 item-row align-items-center">
                        <div class="col-md-4">
                            <select class="form-select select2-barang barang-select" name="items[${itemIndex}][barang_id]" required>
                                ${barangOptionsHtml}
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control jumlah-input" name="items[${itemIndex}][jumlah]"
                                placeholder="Jumlah" min="1" required>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="items[${itemIndex}][metode_pembayaran]" required>
                                <option value="piutang" selected>piutang</option>
                                <option value="tunai">tunai</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control subtotal-display" readonly placeholder="Subtotal">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn-remove-item">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                `);

                $('#items-container').append(newRow);

                // Inisialisasi Select2 pada select baru
                initSelect2Barang(newRow.find('.select2-barang'));
                attachEventListeners(newRow);

                itemIndex++;
                updateRemoveButtons();
            });

            // Hapus item
            $(document).on('click', '.btn-remove-item', function () {
                const btn = $(this);
                if (!btn.prop('disabled')) {
                    btn.closest('.item-row').remove();
                    updateRemoveButtons();
                    calculateGrandTotal();
                }
            });

            function initSelect2Barang(element) {
                element.select2({
                    theme: 'bootstrap-5',
                    placeholder: '-- Pilih Barang --',
                    allowClear: true,
                    width: '100%'
                });
            }

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
                const option = select.options[select.selectedIndex];
                const harga = option ? option.dataset.harga : 0;
                const subtotal = (harga || 0) * (jumlah || 0);
                row.find('.subtotal-display').val(subtotal > 0 ? 'Rp ' + subtotal.toLocaleString('id-ID') : '');
                calculateGrandTotal();
            }

            function calculateGrandTotal() {
                let total = 0;
                $('.item-row').each(function () {
                    const select = $(this).find('.barang-select')[0];
                    const jumlah = $(this).find('.jumlah-input').val();
                    const option = select.options[select.selectedIndex];
                    const harga = option ? option.dataset.harga : 0;
                    total += (harga || 0) * (jumlah || 0);
                });
                $('#grand-total').text('Rp ' + total.toLocaleString('id-ID'));
            }

            // Attach events ke row pertama
            $('.item-row').each(function () {
                attachEventListeners($(this));
            });

            // Handle form submission to prevent double input
            $('#formTransaksi').on('submit', function() {
                const btn = $(this).find('button[type="submit"]');
                btn.prop('disabled', true);
                btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...');
                return true;
            });
        });
    </script>
@endpush