<x-app-layout>
    <x-slot name="header"> Profile </x-slot>

    <div class="h-[calc(100vh-64px)] bg-slate-100 flex flex-col px-4 pb-6">

        <div class="flex-1 max-w-[900px] mx-auto w-full flex flex-col space-y-6">

            {{-- HEADER --}}
            <div class="mt-4">
                <h2 class="text-2xl font-bold text-slate-800 uppercase tracking-tight">
                    Profile
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Kelola informasi akun dan keamanan
                </p>
            </div>

            {{-- PROFILE INFO --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- PASSWORD --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- DELETE ACCOUNT --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>

    </div>
</x-app-layout>