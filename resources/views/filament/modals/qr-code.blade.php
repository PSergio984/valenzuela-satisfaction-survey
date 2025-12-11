<div class="flex flex-col items-center justify-center gap-4 text-center mx-auto">
    {{-- QR Code --}}
    <div class="bg-white p-2 rounded-lg mx-auto">
        <div style="width: 280px; height: 280px; margin: 0 auto;">
            {!! $qrCode !!}
        </div>
    </div>

    {{-- Instructions --}}
    <p class="text-sm text-gray-500 dark:text-gray-400 mx-auto">
        Scan this QR code to access the survey
    </p>

    {{-- URL with Copy Button --}}
    <div class="w-full max-w-md flex items-center gap-2 bg-gray-50 dark:bg-gray-800 rounded-lg p-2 mx-auto">
        <code class="flex-1 text-xs text-gray-600 dark:text-gray-300 truncate">
            {{ $surveyUrl }}
        </code>
        <x-filament::button
            color="gray"
            size="xs"
            x-data="{ copied: false }"
            x-on:click="navigator.clipboard.writeText('{{ $surveyUrl }}'); copied = true; setTimeout(() => copied = false, 2000)"
            x-text="copied ? 'Copied!' : 'Copy'"
        />
    </div>

    {{-- Action Buttons --}}
    <div class="flex gap-2 justify-center mx-auto">
        <x-filament::button
            tag="a"
            href="data:image/svg+xml;base64,{{ base64_encode($qrCodeRaw) }}"
            download="{{ $survey->slug }}-qr-code.svg"
            icon="heroicon-o-arrow-down-tray"
            color="gray"
            size="sm"
        >
            Download
        </x-filament::button>

        <x-filament::button
            tag="a"
            href="{{ $surveyUrl }}"
            target="_blank"
            icon="heroicon-o-arrow-top-right-on-square"
            size="sm"
        >
            Open Survey
        </x-filament::button>
    </div>
</div>
