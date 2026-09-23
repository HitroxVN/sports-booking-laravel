{{-- Bản đồ chọn vị trí khu sân: Leaflet + OpenStreetMap.
     Bấm lên bản đồ → ghim marker → tự điền lat/lng. Kéo marker để chỉnh lại.
     Dùng chung create + edit. CDN không tải được → 2 ô lat/lng vẫn nhập tay được. --}}
@php
    $lat = old('latitude', $venue->latitude ?? '');
    $lng = old('longitude', $venue->longitude ?? '');
@endphp
<div class="mb-6">
    <div class="flex items-center justify-between gap-3 mb-2">
        <label class="label-eyebrow">Vị trí trên bản đồ</label>
        <button type="button" id="venue-geocode" class="btn-secondary text-xs shrink-0">Tìm vị trí theo địa chỉ</button>
    </div>
    <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-2">Bấm lên bản đồ để ghim vị trí khu sân, kéo ghim để chỉnh lại. Toạ độ sẽ tự điền bên dưới.</p>
    <p id="venue-geocode-msg" class="text-xs text-primary-600 dark:text-primary-400 mb-2 hidden"></p>
    <div id="venue-map" class="relative z-0 h-72 w-full rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden"></div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-3">
        <div>
            <label for="venue-latitude" class="label-eyebrow block mb-2">Vĩ độ (Latitude)</label>
            <input id="venue-latitude" type="text" name="latitude" value="{{ $lat }}" class="input-base bg-zinc-50 dark:bg-zinc-900" placeholder="Bấm bản đồ để điền">
        </div>
        <div>
            <label for="venue-longitude" class="label-eyebrow block mb-2">Kinh độ (Longitude)</label>
            <input id="venue-longitude" type="text" name="longitude" value="{{ $lng }}" class="input-base bg-zinc-50 dark:bg-zinc-900" placeholder="Bấm bản đồ để điền">
        </div>
    </div>
</div>

{{-- Leaflet tải local (public/vendor/leaflet) — CDN hay bị chặn. Tile lỗi → tự đổi nguồn dự phòng. --}}
<link rel="stylesheet" href="{{ asset('vendor/leaflet/leaflet.css') }}">
<script src="{{ asset('vendor/leaflet/leaflet.js') }}"></script>
<script>
(function () {
    const mapEl  = document.getElementById('venue-map');
    const latInp = document.getElementById('venue-latitude');
    const lngInp = document.getElementById('venue-longitude');
    if (!mapEl || typeof L === 'undefined') return;

    const hasFix = latInp.value && lngInp.value;
    const map = L.map(mapEl, { attributionControl: false }).setView(hasFix ? [+latInp.value, +lngInp.value] : [14.0583, 108.2772], hasFix ? 16 : 5);

    const SOURCES = [
        { url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', attr: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>' },
        { url: 'https://tile.openstreetmap.de/{z}/{x}/{y}.png',      attr: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> (de)' },
    ];
    let srcIdx = 0, errCount = 0, layer = null;
    const useSource = i => {
        srcIdx = i; errCount = 0;
        if (layer) map.removeLayer(layer);
        layer = L.tileLayer(SOURCES[i].url, { maxZoom: 19, attribution: SOURCES[i].attr }).addTo(map);
        layer.on('tileerror', () => {
            if (++errCount >= 2 && srcIdx < SOURCES.length - 1) useSource(srcIdx + 1);
        });
    };
    useSource(0);

    let marker = null;
    const setMarker = (lat, lng) => {
        latInp.value = lat.toFixed(7);
        lngInp.value = lng.toFixed(7);
        if (marker) return marker.setLatLng([lat, lng]);
        marker = L.marker([lat, lng], { draggable: true }).addTo(map);
        marker.on('dragend', () => {
            const p = marker.getLatLng();
            latInp.value = p.lat.toFixed(7);
            lngInp.value = p.lng.toFixed(7);
        });
    };

    if (hasFix) setMarker(+latInp.value, +lngInp.value);
    map.on('click', e => setMarker(e.latlng.lat, e.latlng.lng));

    // Nút "Lấy vị trí hiện tại" nằm trong map (góc phải trên)
    const LocateControl = L.Control.extend({
        options: { position: 'topright' },
        onAdd() {
            const div = L.DomUtil.create('div', 'leaflet-bar');
            const a = L.DomUtil.create('a', '', div);
            a.href = '#'; a.title = 'Lấy vị trí hiện tại';
            a.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:block;margin:auto"><circle cx="12" cy="12" r="3" fill="currentColor" stroke="none"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/><circle cx="12" cy="12" r="7"/></svg>';
            L.DomEvent.on(a, 'click', e => {
                L.DomEvent.stop(e);
                if (!navigator.geolocation) { alert('Trình duyệt không hỗ trợ định vị.'); return; }
                a.style.opacity = '0.5';
                navigator.geolocation.getCurrentPosition(pos => {
                    a.style.opacity = '1';
                    const { latitude: lat, longitude: lng, accuracy } = pos.coords;
                    setMarker(lat, lng);
                    map.setView([lat, lng], 17);
                    // Vòng tròn thể hiện độ chính xác của GPS
                    if (locateCircle) map.removeLayer(locateCircle);
                    locateCircle = L.circle([lat, lng], { radius: accuracy, color: '#2563eb', weight: 1, fillOpacity: 0.1 }).addTo(map);
                }, () => {
                    a.style.opacity = '1';
                    alert('Không lấy được vị trí. Hãy kiểm tra quyền truy cập vị trí của trình duyệt.');
                }, { enableHighAccuracy: true, timeout: 10000 });
            });
            return div;
        },
    });
    let locateCircle = null;
    map.addControl(new LocateControl());

    // Định vị tâm Phường/Xã đã chọn (dữ liệu OSM — luôn có, kể cả vùng nông thôn).
    const msg = document.getElementById('venue-geocode-msg');
    const btn = document.getElementById('venue-geocode');
    btn.addEventListener('click', async () => {
        const val = id => document.getElementById(id)?.value.trim() || '';
        const ward = val('venue-ward'), city = val('venue-city');
        const addr = val('venue-address');
        msg.classList.remove('hidden', 'text-red-500');
        if (!ward || !city) {
            msg.textContent = 'Chọn Tỉnh/TP và Phường/Xã trước đã.';
            msg.classList.add('text-red-500');
            return;
        }
        btn.disabled = true;
        msg.textContent = 'Đang định vị...';
        // Photon match mờ trả kết quả sai (VD "Phường Phú Diễn" → "Cột cờ Hà Nội")
        const strip = s => s.replace(/^(thành phố|tỉnh|tp\.?|quận|huyện|phường|xã|thị xã|thị trấn)\s+/i, '').trim();
        const norm = s => strip(s).toLowerCase();
        try {
            const q = encodeURIComponent(strip(ward) + ', ' + strip(city) + ', Việt Nam');
            const r = await fetch('https://photon.komoot.io/api/?limit=5&q=' + q, { headers: { 'Accept': 'application/json' } });
            if (!r.ok) throw new Error('http' + r.status);
            const d = await r.json();
            const feats = d.features || [];
            // Ưu tiên kết quả đúng tên phường; không có thì lấy kết quả đầu
            const nWard = norm(ward);
            const f = feats.find(x => norm(x.properties?.name || '') === nWard)
                   || feats.find(x => norm(x.properties?.name || '').includes(nWard) || nWard.includes(norm(x.properties?.name || '')))
                   || feats[0];
            if (!f) throw new Error('notfound');
            const lat = f.geometry.coordinates[1], lng = f.geometry.coordinates[0];
            setMarker(lat, lng);
            // extent = [minLon, maxLat, maxLon, minLat] → fit cả phường nếu có
            const ex = f.properties.extent;
            Array.isArray(ex) ? map.fitBounds([[ex[3], ex[0]], [ex[1], ex[2]]], { maxZoom: 15 })
                              : map.setView([lat, lng], 15);
            msg.textContent = 'Đã định vị ' + ward + ' — hãy kéo ghim tới đúng vị trí khu sân.';
        } catch (e) {
            msg.textContent = e.message === 'notfound'
                ? 'Không tìm thấy ' + ward + ' trên bản đồ. Thử bấm ghim tay trên bản đồ.'
                : 'Không kết nối được dịch vụ định vị. Hãy bấm ghim tay trên bản đồ.';
            msg.classList.add('text-red-500');
        } finally {
            btn.disabled = false;
        }
    });
})();
</script>
