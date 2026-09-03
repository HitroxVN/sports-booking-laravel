<x-owner-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">
            {{ __('Báo cáo & Thống kê') }}
        </h1>
        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Theo dõi doanh thu và lượt đặt sân của bạn</p>
    </x-slot>

    <div class="max-w-7xl mx-auto">

        <div class="card-base p-8">
            <x-empty-state icon="📊" title="Tính năng báo cáo đang được cập nhật" description="Hệ thống thống kê doanh thu và lượt đặt sân sẽ sớm ra mắt tại đây." />
        </div>
    </div>
</x-owner-layout>
