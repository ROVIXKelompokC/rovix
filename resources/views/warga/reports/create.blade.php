@extends('layouts.app')

@section('title', 'Buat Laporan Kerusakan Jalan — ROVIX')
@section('header', 'Formulir Pengaduan Kerusakan Jalan')
@section('subheader', 'Unggah bukti foto kerusakan jalan, tentukan titik koordinat GPS akurat, dan validasi citra berbasis AI Vision')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Breadcrumb & Navigasi Cepat -->
    <div class="mb-3 flex items-center justify-between">
        <a href="{{ route('warga.reports.index') }}" class="inline-flex items-center gap-1.5 text-xs text-[#263B36] hover:text-[#C76B45] font-semibold transition">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            <span>Kembali ke Riwayat Laporan</span>
        </a>
        <span class="text-[11px] font-mono-tech text-[#353A37]/70">Modul Pelaporan Warga (SCRUM-6)</span>
    </div>

    <!-- Alert Notifikasi Error Validasi Global (jika ada) -->
    @if($errors->any())
        <div class="mb-4 p-4 rounded-lg bg-red-50 border-l-4 border-red-500 text-red-900 text-xs shadow-sm">
            <div class="flex items-center gap-2 font-bold mb-1">
                <svg class="w-4 h-4 text-red-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span>Mohon periksa kembali isian formulir:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-[11px] text-red-800">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('warga.reports.store') }}" method="POST" enctype="multipart/form-data" id="reportForm" class="space-y-4">
        @csrf

        <!-- 1. KOTAK UNGGAH FOTO BUKTI & VALIDASI AI -->
        <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-4 sm:p-5 shadow-sm">
            <div class="flex items-center justify-between border-b border-[#263B36]/10 pb-3 mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-[#263B36] text-white flex items-center justify-center font-mono-tech text-xs font-bold">1</span>
                    <h3 class="font-bold text-[#263B36] text-sm tracking-tight">Foto Bukti Kerusakan Jalan</h3>
                    <span class="text-red-500 font-bold text-xs">*</span>
                </div>
                <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 text-[10px] font-mono-tech font-bold">
                    <svg class="w-3 h-3 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4"/><path d="m4.93 4.93 2.83 2.83"/><path d="M2 12h4"/><path d="m4.93 19.07 2.83-2.83"/><path d="M12 22v-4"/><path d="m19.07 19.07-2.83-2.83"/><path d="M22 12h-4"/><path d="m19.07 4.93-2.83 2.83"/></svg>
                    <span>AI Vision Ready</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
                <!-- Dropzone Input File -->
                <div>
                    <label for="photoInput"
                        id="dropzoneLabel"
                        class="flex flex-col items-center justify-center border-2 border-dashed border-[#263B36]/30 hover:border-[#C76B45] rounded-lg p-6 bg-white/60 hover:bg-white cursor-pointer transition text-center group min-h-[220px]">
                        <div class="w-12 h-12 rounded-full bg-[#263B36]/10 group-hover:bg-[#C76B45]/15 text-[#263B36] group-hover:text-[#C76B45] flex items-center justify-center mb-2 transition">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        </div>
                        <p class="font-bold text-xs text-[#263B36] group-hover:text-[#C76B45]">
                            Klik untuk memilih foto atau seret ke sini
                        </p>
                        <p class="text-[11px] text-[#353A37]/70 mt-1">
                            Format: JPG, JPEG, PNG, atau WEBP (Maksimal 10 MB)
                        </p>
                        <span class="mt-3 px-3 py-1 bg-[#263B36] group-hover:bg-[#C76B45] text-white text-[11px] font-semibold rounded shadow-sm transition">
                            Pilih Berkas Foto
                        </span>
                        <input type="file" name="photo" id="photoInput" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden" required>
                    </label>

                    @error('photo')
                        <p class="text-red-600 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                    @error('foto')
                        <p class="text-red-600 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pratinjau Foto & Simulasi AI Banner -->
                <div>
                    <div id="previewContainer" class="hidden">
                        <p class="text-xs font-semibold text-[#263B36] mb-1.5 flex items-center justify-between">
                            <span>Pratinjau Citra Terpilih:</span>
                            <span id="fileSizeBadge" class="text-[10px] font-mono-tech text-[#353A37]/70"></span>
                        </p>
                        <div class="relative w-full h-[180px] rounded-lg overflow-hidden border border-[#263B36]/20 bg-black/5">
                            <img id="imagePreview" src="#" alt="Pratinjau Foto" class="w-full h-full object-cover">
                            <button type="button" id="removePhotoBtn"
                                class="absolute top-2 right-2 p-1.5 bg-black/60 hover:bg-red-600 text-white rounded-full transition"
                                title="Hapus foto">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>
                    </div>

                    <div id="placeholderPreview" class="h-[180px] rounded-lg border border-dashed border-[#263B36]/20 bg-white/30 flex flex-col items-center justify-center text-center p-4">
                        <svg class="w-8 h-8 text-[#353A37]/40 mb-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                        <p class="text-xs text-[#353A37]/60">Belum ada foto yang dipilih</p>
                        <p class="text-[10px] text-[#353A37]/50 mt-0.5">Pastikan foto jalan terlihat jelas, terang, dan fokus pada kerusakan</p>
                    </div>

                    <!-- Informasi Modul AI Vision -->
                    <div class="mt-3 p-2.5 rounded bg-[#E9E5DA]/80 border border-[#263B36]/15 text-[11px] text-[#353A37]/80 flex items-start gap-2">
                        <svg class="w-4 h-4 text-[#C76B45] shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        <div>
                            <span class="font-bold text-[#263B36]">Verifikasi Otomatis AI ROVIX:</span>
                            Sistem akan secara instan memindai keabsahan objek kerusakan jalan (lubang, aspal amblas, retak) serta menyaring konten spam sebelum diteruskan ke Admin Dinas PU.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. KLASIFIKASI KATEGORI & WILAYAH KECAMATAN -->
        <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-4 sm:p-5 shadow-sm">
            <div class="flex items-center gap-2 border-b border-[#263B36]/10 pb-3 mb-4">
                <span class="w-6 h-6 rounded-full bg-[#263B36] text-white flex items-center justify-center font-mono-tech text-xs font-bold">2</span>
                <h3 class="font-bold text-[#263B36] text-sm tracking-tight">Kategori & Wilayah Administrasi</h3>
                <span class="text-red-500 font-bold text-xs">*</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Dropdown Kategori Kerusakan -->
                <div>
                    <label for="category_id" class="block text-xs font-bold text-[#263B36] mb-1.5">
                        Jenis Kerusakan Jalan <span class="text-red-500">*</span>
                    </label>
                    <select name="category_id" id="category_id" required
                        class="w-full px-3 py-2 text-xs bg-white border border-[#263B36]/25 rounded focus:outline-none focus:ring-1 focus:ring-[#C76B45] text-[#353A37]">
                        <option value="">-- Pilih Jenis Kerusakan --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->category_name }} (Tingkat Bahaya: Level {{ $cat->severity_level }}/5)
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-[#353A37]/65 mt-1">
                        Tingkat keparahan mempengaruhi prioritas disposisi SPK perbaikan.
                    </p>
                    @error('category_id')
                        <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Dropdown Wilayah Kecamatan -->
                <div>
                    <label for="district_id" class="block text-xs font-bold text-[#263B36] mb-1.5">
                        Wilayah Kecamatan <span class="text-red-500">*</span>
                    </label>
                    <select name="district_id" id="district_id" required
                        class="w-full px-3 py-2 text-xs bg-white border border-[#263B36]/25 rounded focus:outline-none focus:ring-1 focus:ring-[#C76B45] text-[#353A37]">
                        <option value="">-- Pilih Wilayah Kecamatan --</option>
                        @foreach($districts as $dis)
                            <option value="{{ $dis->id }}" {{ (old('district_id', $user->district_id) == $dis->id) ? 'selected' : '' }}>
                                {{ $dis->name }} [Kode: {{ $dis->city_code }}]
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-[#353A37]/65 mt-1">
                        Pilih kecamatan tempat objek kerusakan jalan berada.
                    </p>
                    @error('district_id')
                        <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- 3. TITIK KOORDINAT SPASIAL & PETA GIS LEAFLET -->
        <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-4 sm:p-5 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-[#263B36]/10 pb-3 mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-[#263B36] text-white flex items-center justify-center font-mono-tech text-xs font-bold">3</span>
                    <h3 class="font-bold text-[#263B36] text-sm tracking-tight">Titik Koordinat Lokasi (Peta GIS & GPS)</h3>
                    <span class="text-red-500 font-bold text-xs">*</span>
                </div>
                <!-- Tombol GPS Geolocation -->
                <button type="button" id="btnDetectGPS"
                    class="px-3 py-1.5 bg-[#263B36] hover:bg-[#1e302b] text-white text-xs font-bold rounded transition flex items-center gap-1.5 shadow-sm self-start sm:self-auto">
                    <svg class="w-3.5 h-3.5 text-[#A7B5A0]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
                    <span id="btnGpsText">Gunakan GPS Saya Saat Ini</span>
                </button>
            </div>

            <!-- Petunjuk Peta -->
            <p class="text-[11px] text-[#353A37]/75 mb-2.5">
                Klik langsung pada peta di bawah ini atau geser pin merah untuk menandai lokasi kerusakan jalan dengan presisi.
            </p>

            <!-- Container Peta Leaflet.js -->
            <div id="mapPicker" class="w-full h-72 sm:h-80 rounded-lg border border-[#263B36]/25 shadow-inner z-10"></div>

            <!-- Input Nilai Koordinat Latitude & Longitude -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                <div>
                    <label for="latitude" class="block text-xs font-bold text-[#263B36] mb-1">
                        Lintang (Latitude) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#353A37]/50 font-mono-tech text-xs">LAT</span>
                        <input type="number" step="any" name="latitude" id="latitude"
                            value="{{ old('latitude', -6.30720000) }}" required
                            class="w-full pl-12 pr-3 py-2 text-xs font-mono-tech bg-white border border-[#263B36]/25 rounded focus:outline-none focus:ring-1 focus:ring-[#C76B45] text-[#353A37]">
                    </div>
                    @error('latitude')
                        <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="longitude" class="block text-xs font-bold text-[#263B36] mb-1">
                        Bujur (Longitude) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#353A37]/50 font-mono-tech text-xs">LNG</span>
                        <input type="number" step="any" name="longitude" id="longitude"
                            value="{{ old('longitude', 106.83980000) }}" required
                            class="w-full pl-12 pr-3 py-2 text-xs font-mono-tech bg-white border border-[#263B36]/25 rounded focus:outline-none focus:ring-1 focus:ring-[#C76B45] text-[#353A37]">
                    </div>
                    @error('longitude')
                        <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- 4. DESKRIPSI & CIRI KHUSUS LOKASI -->
        <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-4 sm:p-5 shadow-sm">
            <div class="flex items-center justify-between border-b border-[#263B36]/10 pb-3 mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-[#263B36] text-white flex items-center justify-center font-mono-tech text-xs font-bold">4</span>
                    <h3 class="font-bold text-[#263B36] text-sm tracking-tight">Deskripsi Detail & Patokan Lokasi</h3>
                    <span class="text-red-500 font-bold text-xs">*</span>
                </div>
                <span id="charCounter" class="text-[10px] font-mono-tech text-[#353A37]/60">0 / 1000 karakter (min. 10)</span>
            </div>

            <div>
                <label for="description" class="block text-xs font-bold text-[#263B36] mb-1.5">
                    Uraian Kerusakan Jalan & Patokan Lokasi <span class="text-red-500">*</span>
                </label>
                <textarea name="description" id="description" rows="4" required minlength="10" maxlength="1000"
                    placeholder="Contoh: Lubang jalan berdiameter ±60 cm dengan kedalaman 10 cm di depan minimarket dekat pertigaan jalan. Sangat membahayakan pengendara motor khususnya saat malam hari atau hujan tergenang air..."
                    class="w-full px-3.5 py-2.5 text-xs bg-white border border-[#263B36]/25 rounded-lg focus:outline-none focus:ring-1 focus:ring-[#C76B45] text-[#353A37] placeholder:text-[#353A37]/45">{{ old('description') }}</textarea>
                <p class="text-[10px] text-[#353A37]/65 mt-1">
                    Sebutkan estimasi dimensi kerusakan serta patokan landmark terdekat agar regu teknis lapangan dapat menemukan lokasi dengan cepat.
                </p>
                @error('description')
                    <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
                @error('deskripsi')
                    <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- 5. TOMBOL AKSI SUBMIT FORM -->
        <div class="bg-[#F4F1EA] rounded-lg border border-[#263B36]/15 p-4 flex flex-col sm:flex-row items-center justify-between gap-3 shadow-sm">
            <p class="text-[11px] text-[#353A37]/70 text-center sm:text-left">
                Dengan menekan tombol kirim, Anda menyatakan bahwa data yang dilaporkan adalah benar dan dapat dipertanggungjawabkan.
            </p>
            <div class="flex items-center gap-2.5 w-full sm:w-auto">
                <a href="{{ route('warga.reports.index') }}"
                    class="flex-1 sm:flex-none px-4 py-2.5 rounded text-xs font-semibold bg-white hover:bg-[#263B36]/10 text-[#263B36] border border-[#263B36]/25 text-center transition">
                    Batal
                </a>
                <button type="submit" id="btnSubmitReport"
                    class="flex-1 sm:flex-none px-5 py-2.5 rounded text-xs font-bold bg-[#C76B45] hover:bg-[#a95734] text-white transition flex items-center justify-center gap-2 shadow-sm">
                    <svg id="submitIcon" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    <span id="submitText">Kirim Aduan & Validasi AI</span>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Script Interaktif Leaflet GIS, Pratinjau Foto, & Deteksi GPS -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- 1. PREVIEW FOTO UPLOAD ---
        const photoInput = document.getElementById('photoInput');
        const previewContainer = document.getElementById('previewContainer');
        const placeholderPreview = document.getElementById('placeholderPreview');
        const imagePreview = document.getElementById('imagePreview');
        const fileSizeBadge = document.getElementById('fileSizeBadge');
        const removePhotoBtn = document.getElementById('removePhotoBtn');

        if (photoInput) {
            photoInput.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    // Validasi batas ukuran client 10MB
                    if (file.size > 10 * 1024 * 1024) {
                        alert('Ukuran foto melebihi batas maksimal 10 MB. Silakan pilih foto dengan ukuran lebih kecil.');
                        photoInput.value = '';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function (event) {
                        imagePreview.src = event.target.result;
                        previewContainer.classList.remove('hidden');
                        placeholderPreview.classList.add('hidden');
                        const sizeKB = (file.size / 1024).toFixed(1);
                        fileSizeBadge.textContent = `${file.name} (${sizeKB} KB)`;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        if (removePhotoBtn) {
            removePhotoBtn.addEventListener('click', function () {
                photoInput.value = '';
                imagePreview.src = '#';
                previewContainer.classList.add('hidden');
                placeholderPreview.classList.remove('hidden');
                fileSizeBadge.textContent = '';
            });
        }

        // --- 2. HITUNG KARAKTER DESKRIPSI ---
        const descInput = document.getElementById('description');
        const charCounter = document.getElementById('charCounter');
        if (descInput && charCounter) {
            const updateCount = () => {
                const len = descInput.value.length;
                charCounter.textContent = `${len} / 1000 karakter (min. 10)`;
                charCounter.className = len >= 10 ? 'text-[10px] font-mono-tech text-emerald-700' : 'text-[10px] font-mono-tech text-[#C76B45]';
            };
            descInput.addEventListener('input', updateCount);
            updateCount();
        }

        // --- 3. PETA INTERAKTIF GIS LEAFLET & GPS PICKER ---
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');

        const initLat = parseFloat(latInput.value) || -6.30720000;
        const initLng = parseFloat(lngInput.value) || 106.83980000;

        const map = L.map('mapPicker', {
            center: [initLat, initLng],
            zoom: 14,
            zoomControl: true,
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors | ROVIX GIS'
        }).addTo(map);

        // Custom Icon Pin Merah Terracotta
        const customIcon = L.divIcon({
            className: 'custom-pin',
            html: `
                <div style="background-color: #C76B45; width: 28px; height: 28px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); display: flex; align-items: center; justify-content: center; border: 2px solid #ffffff; box-shadow: 0 2px 6px rgba(0,0,0,0.35);">
                    <div style="background-color: #ffffff; width: 8px; height: 8px; border-radius: 50%;"></div>
                </div>
            `,
            iconSize: [28, 28],
            iconAnchor: [14, 28],
            popupAnchor: [0, -28]
        });

        let marker = L.marker([initLat, initLng], {
            draggable: true,
            icon: customIcon
        }).addTo(map);

        marker.bindPopup('<strong style="font-family:sans-serif;font-size:12px;">Lokasi Kerusakan Jalan</strong><br><span style="font-size:11px;font-family:monospace;">Geser pin atau klik peta untuk ubah titik</span>').openPopup();

        function updateInputs(lat, lng) {
            latInput.value = parseFloat(lat).toFixed(8);
            lngInput.value = parseFloat(lng).toFixed(8);
        }

        marker.on('dragend', function (e) {
            const coord = e.target.getLatLng();
            updateInputs(coord.lat, coord.lng);
        });

        map.on('click', function (e) {
            marker.setLatLng(e.latlng);
            updateInputs(e.latlng.lat, e.latlng.lng);
            map.panTo(e.latlng);
        });

        // Sinkronisasi input teks manual ke peta
        [latInput, lngInput].forEach(inp => {
            inp.addEventListener('change', function () {
                const lat = parseFloat(latInput.value);
                const lng = parseFloat(lngInput.value);
                if (!isNaN(lat) && !isNaN(lng)) {
                    marker.setLatLng([lat, lng]);
                    map.panTo([lat, lng]);
                }
            });
        });

        // --- 4. GEOLOCATION API DENGAN TOMBOL GPS ---
        const btnGPS = document.getElementById('btnDetectGPS');
        const btnGpsText = document.getElementById('btnGpsText');

        if (btnGPS) {
            btnGPS.addEventListener('click', function () {
                if (!navigator.geolocation) {
                    alert('Peramban Anda tidak mendukung deteksi lokasi GPS otomatis.');
                    return;
                }

                btnGpsText.textContent = 'Mendeteksi GPS...';
                btnGPS.disabled = true;

                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        const userLat = position.coords.latitude;
                        const userLng = position.coords.longitude;

                        marker.setLatLng([userLat, userLng]);
                        map.setView([userLat, userLng], 16);
                        updateInputs(userLat, userLng);
                        marker.openPopup();

                        btnGpsText.textContent = 'GPS Terkunci ✓';
                        btnGPS.classList.remove('bg-[#263B36]');
                        btnGPS.classList.add('bg-emerald-700');

                        setTimeout(() => {
                            btnGpsText.textContent = 'Gunakan GPS Saya Saat Ini';
                            btnGPS.classList.remove('bg-emerald-700');
                            btnGPS.classList.add('bg-263B36]');
                            btnGPS.disabled = false;
                        }, 3000);
                    },
                    function (error) {
                        alert('Gagal mendeteksi koordinat GPS: ' + error.message + '. Silakan klik langsung titik lokasi pada peta.');
                        btnGpsText.textContent = 'Gunakan GPS Saya Saat Ini';
                        btnGPS.disabled = false;
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            });
        }

        // --- 5. INDIKATOR LOADING SAAT SUBMIT FORM ---
        const form = document.getElementById('reportForm');
        const btnSubmit = document.getElementById('btnSubmitReport');
        const submitText = document.getElementById('submitText');
        const submitIcon = document.getElementById('submitIcon');

        if (form && btnSubmit) {
            form.addEventListener('submit', function () {
                btnSubmit.disabled = true;
                btnSubmit.classList.add('opacity-75', 'cursor-not-allowed');
                submitText.textContent = 'Memvalidasi AI & Mengirim Laporan...';
                if (submitIcon) {
                    submitIcon.innerHTML = '<circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="31.4 31.4" class="animate-spin" />';
                }
            });
        }

        // Invalidate map size saat window resize
        window.addEventListener('resize', () => map.invalidateSize());
    });
</script>
@endsection

