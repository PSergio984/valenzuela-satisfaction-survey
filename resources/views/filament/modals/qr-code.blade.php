<div class="flex flex-col items-center justify-center gap-6 text-center mx-auto py-8 px-4 max-w-2xl">
    {{-- QR Code Section --}}
    <div class="relative w-full max-w-sm">
        {{-- Decorative gradient background --}}
        <div
            class="absolute inset-0 bg-gradient-to-br from-primary-100 to-primary-50 dark:from-primary-900/20 dark:to-primary-800/10 rounded-2xl blur-xl scale-105 -z-10">
        </div>

        {{-- QR Code Container --}}
        <div
            class="relative bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-lg ring-1 ring-gray-200 dark:ring-gray-700">
            <div style="width: 280px; height: 280px; margin: 0 auto;">
                {!! $qrCode !!}
            </div>
        </div>
    </div>

    {{-- Title & Instructions --}}
    <div class="space-y-2">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
            Share Your Survey
        </h3>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Scan the QR code with your phone camera or share the link below
        </p>
    </div>

    {{-- URL with Copy Button --}}
    <div class="w-full">
        <div
            class="flex items-center gap-2 bg-gray-100 dark:bg-gray-800 rounded-xl p-3 ring-1 ring-gray-200 dark:ring-gray-700 hover:ring-primary-500 dark:hover:ring-primary-600 transition-all">
            <code class="flex-1 text-sm text-gray-700 dark:text-gray-300 truncate font-mono min-w-0">
                {{ $surveyUrl }}
            </code>
            <x-filament::button color="primary" size="xs" x-data="{ copied: false }"
                x-on:click="navigator.clipboard.writeText('{{ $surveyUrl }}'); copied = true; setTimeout(() => copied = false, 2000)"
                x-bind:icon="copied ? 'heroicon-o-check' : 'heroicon-o-clipboard-document'" class="flex-shrink-0">
                <span x-text="copied ? 'Copied!' : 'Copy'"></span>
            </x-filament::button>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex flex-wrap gap-3 justify-center">
        <x-filament::button tag="a" href="data:image/svg+xml;base64,{{ base64_encode($qrCodeRaw) }}"
            download="{{ $survey->slug }}-qr-code.svg" icon="heroicon-o-arrow-down-tray" color="gray" size="md"
            outlined>
            Download QR Code
        </x-filament::button>
        <x-filament::button tag="a" href="{{ $surveyUrl }}" target="_blank"
            icon="heroicon-o-arrow-top-right-on-square" size="md">
            Open Survey
        </x-filament::button>
    </div>
</div>
