{{-- Địa chỉ hành chính sau sáp nhập 07/2025: dropdown Tỉnh/TP → Phường/Xã (2 cấp).
     Dữ liệu local: public/data/vn-addresses.json (34 tỉnh/TP, 3321 phường/xã — nguồn provinces.open-api.vn/api/v2).
     Dùng chung create + edit. Chỉ nhập tay: số nhà + tên đường (ô address ngoài partial). --}}
@php
    $oldCity = old('city', $venue->city ?? '');
    $oldWard = old('ward', $venue->ward ?? '');
@endphp
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label for="venue-city" class="label-eyebrow block mb-2">Tỉnh/Thành phố <span class="text-red-500">*</span></label>
        <select id="venue-city" name="city" required data-old="{{ $oldCity }}" data-ph="Tỉnh/Thành phố" class="input-base">
            <option value="">-- Tỉnh/Thành phố --</option>
        </select>
        @error('city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="venue-ward" class="label-eyebrow block mb-2">Phường/Xã <span class="text-red-500">*</span></label>
        <select id="venue-ward" name="ward" required data-old="{{ $oldWard }}" data-ph="Phường/Xã" class="input-base" disabled>
            <option value="">-- Phường/Xã --</option>
        </select>
        @error('ward') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<script>
(async function () {
    const citySel = document.getElementById('venue-city');
    const wardSel = document.getElementById('venue-ward');
    if (!citySel) return;

    // So khớp tên đã lưu với tên data: bỏ tiền tố "TP.", "Tỉnh", "Quận"...
    const norm = s => (s || '').toLowerCase()
        .replace(/^(thành phố|tỉnh|quận|huyện|phường|xã|thị xã|thị trấn|tp\.?|q\.?|p\.?)\s*/, '')
        .replace(/\./g, '').trim();

    // Điền danh sách option (value = tên, khớp với DB đang lưu tên)
    const fill = (sel, list) => {
        sel.innerHTML = '<option value="">-- ' + sel.dataset.ph + ' --</option>';
        list.forEach(i => sel.add(new Option(i.name, i.name)));
        sel.disabled = false;
    };

    const reset = sel => {
        sel.innerHTML = '<option value="">-- ' + sel.dataset.ph + ' --</option>';
        sel.disabled = true;
    };

    // Chọn sẵn option khớp giá trị cũ; không khớp thì thêm option giữ nguyên giá trị cũ
    const pick = (sel, list) => {
        const old = sel.dataset.old;
        const nOld = norm(old);
        if (!nOld) return null;
        let hit = list.find(i => norm(i.name) === nOld)
              || list.find(i => norm(i.name).includes(nOld) || nOld.includes(norm(i.name)));
        if (!hit) {
            hit = { name: old };
            list.push(hit);
        }
        sel.value = hit.name;
        return hit;
    };

    try {
        const res = await fetch('{{ asset("data/vn-addresses.json") }}');
        if (!res.ok) throw new Error(res.status);
        const provinces = await res.json();

        citySel.addEventListener('change', () => {
            reset(wardSel);
            const p = provinces.find(i => i.name === citySel.value);
            if (!p) return;
            fill(wardSel, p.wards || []);
        });

        // Khởi tạo: nạp tỉnh → chọn sẵn theo giá trị cũ → nạp tiếp phường/xã
        fill(citySel, provinces);
        const c = pick(citySel, provinces);
        if (c) {
            fill(wardSel, c.wards || []);
            pick(wardSel, c.wards || []);
        }
    } catch (e) {
        // Không tải được data → trả về ô nhập tay như cũ
        [citySel, wardSel].forEach(sel => {
            const inp = document.createElement('input');
            inp.type = 'text';
            inp.name = sel.name;
            inp.id = sel.id;
            inp.value = sel.dataset.old || '';
            inp.required = sel.hasAttribute('required');
            inp.className = sel.className;
            inp.placeholder = 'Nhập ' + sel.dataset.ph;
            sel.replaceWith(inp);
        });
    }
})();
</script>
