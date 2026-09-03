@extends('layouts.customer')

@section('title', 'Hồ sơ cá nhân')

@section('content')
    <div class="container py-8 mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto space-y-6">
            <h2 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Hồ sơ cá nhân</h2>

            <div class="card-base p-6 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="card-base p-6 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="card-base p-6 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
@endsection
