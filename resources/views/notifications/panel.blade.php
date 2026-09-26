{{-- Trang thông báo cho chủ sân (owner-layout) và admin (admin-layout) --}}
<x-dynamic-component :component="$layout">
    @include('notifications._list')
</x-dynamic-component>
