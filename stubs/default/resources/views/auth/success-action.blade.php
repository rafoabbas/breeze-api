<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        @if (session('status'))
            <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600']) }}>
                {{ session('status') }}
            </div>
        @endif
    </x-auth-card>
</x-guest-layout>
