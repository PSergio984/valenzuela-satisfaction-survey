<div class="flex flex-col items-center justify-center gap-4 text-center mx-auto w-full h-full min-h-[60vh] p-2 sm:p-4"
    x-data="{ copied: false, showToast: false }">
    {{-- QR Code --}}
    <div class="bg-white p-2 rounded-lg mx-auto w-full max-w-xs sm:max-w-sm">
        <div class="mx-auto" style="width: 100%; max-width: 280px; aspect-ratio: 1/1;">
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
            x-on:click.prevent="
                // Try clipboard API, fallback to execCommand
                if (window.navigator.clipboard) {
                    window.navigator.clipboard.writeText('{{ $surveyUrl }}')
                        .then(() => { copied = true; showToast = true; setTimeout(() => { copied = false; showToast = false }, 2000); })
                        .catch(() => {
                            // fallback
                            const el = document.createElement('textarea');
                            el.value = '{{ $surveyUrl }}';
                            document.body.appendChild(el);
                            el.select();
                            document.execCommand('copy');
                            document.body.removeChild(el);
                            copied = true; showToast = true; setTimeout(() => { copied = false; showToast = false }, 2000);
                        });
                } else {
                    const el = document.createElement('textarea');
                    el.value = '{{ $surveyUrl }}';
                    document.body.appendChild(el);
                    el.select();
                    document.execCommand('copy');
                    document.body.removeChild(el);
                    copied = true; showToast = true; setTimeout(() => { copied = false; showToast = false }, 2000);
                }
            "
            x-text="copied ? 'Copied!' : 'Copy'"
        />
    </div>

    {{-- Toast/Modal for Copied --}}
    <template x-if="showToast">
        <div class="fixed inset-0 flex items-end justify-center z-50 pointer-events-none">
            <div class="mb-12 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg animate-fade-in-up">
                <span class="font-semibold">Copied!</span> Survey link copied to clipboard.
            </div>
        </div>
    </template>

    {{-- Action Buttons --}}
    <div class="flex flex-wrap gap-2 justify-center mx-auto">
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

<style>
@media (max-width: 640px) {
    .filament-modal-content {
        width: 100vw !important;
        min-width: 0 !important;
        max-width: 100vw !important;
        border-radius: 0 !important;
        padding: 0 !important;
    }
}
@keyframes fade-in-up {
    0% { opacity: 0; transform: translateY(20px); }
    100% { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up {
    animation: fade-in-up 0.3s cubic-bezier(0.4,0,0.2,1);
}
</style>
