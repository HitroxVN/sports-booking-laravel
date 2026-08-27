<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Báo cáo & Thống kê') }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-pink-50 via-white to-pink-50 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl shadow-sm border border-pink-100 p-8 text-center">
                <div class="flex flex-col items-center justify-center py-12">
                    <svg class="w-16 h-16 text-pink-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Tính năng báo cáo đang được cập nhật</h3>
                    <p class="text-sm text-gray-500">Hệ thống thống kê doanh thu và lượt đặt sân sẽ sớm ra mắt tại đây.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>