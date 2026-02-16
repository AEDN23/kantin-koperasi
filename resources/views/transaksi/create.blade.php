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
                <h5>Item Belanja</h5>

                <div id="items-container">
                    <div class="row mb-2 item-row align-items-center">
                        <div class="col-md-5">
                            <select class="form-select select2-barang barang-select" name="items[0][barang_id]" required>
                                <option value="">-- Pilih Barang --</option>
                                @foreach($barangs as $barang)
                                    <option value="{{ $barang->id }}" data-harga="{{ $barang->harga_jual }}">
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
                        <div class="col-md-3">
                            <input type="text" class="form-control subtotal-display" readonly placeholder="Subtotal">
                        </div>
                        <div class="col-md-2">
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
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function () {
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
                    <option value="{{ $barang->id }}" data-harga="{{ $barang->harga_jual }}">{{ $barang->nama_barang }} - Rp {{ number_format($barang->harga_jual, 0, ',', '.') }} (Stok: {{ $barang->stok }})</option>
                @endforeach`;

            // Tambah item baru
            $('#btn-add-item').on('click', function () {
                const newRow = $(`
                    <div class="row mb-2 item-row align-items-center">
                        <div class="col-md-5">
                            <select class="form-select select2-barang barang-select" name="items[${itemIndex}][barang_id]" required>
                                ${barangOptionsHtml}
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control jumlah-input" name="items[${itemIndex}][jumlah]"
                                placeholder="Jumlah" min="1" required>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control subtotal-display" readonly placeholder="Subtotal">
                        </div>
                        <div class="col-md-2">
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