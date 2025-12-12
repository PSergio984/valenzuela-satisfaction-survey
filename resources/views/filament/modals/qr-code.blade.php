<div style="display: flex; align-items: center; justify-content: center; min-height: 500px; padding: 0.5rem;"
    x-data="{ copied: false, showToast: false }">
    <div
        style="display: flex; flex-direction: column; align-items: center; gap: 1rem; text-align: center; width: 100%; max-width: 24rem;">
        {{-- Title --}}
        <div style="margin-bottom: 0.25rem;">
            <h3 style="font-size: 1.25rem; font-weight: bold; margin-bottom: 0.25rem;"
                class="text-gray-900 dark:text-gray-100">
                Share Your Survey
            </h3>
            <p style="font-size: 0.8125rem;" class="text-gray-600 dark:text-gray-400">
                Scan the QR code with your phone camera or share the link below
            </p>
        </div>

        {{-- QR Code --}}
        <div
            style="background: white; padding: 0.75rem; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            <div style="width: 280px; height: 280px; margin: 0 auto;">
                {!! $qrCode !!}
            </div>
        </div>

        {{-- URL with Copy Button --}}
        <div style="width: 100%; display: flex; align-items: center; gap: 0.5rem; padding: 0.625rem; border-radius: 0.375rem;"
            class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
            <code
                style="flex: 1; font-size: 0.7rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-family: monospace;"
                class="text-gray-700 dark:text-gray-300">
                {{ $surveyUrl }}
            </code>
            <button
                onclick="navigator.clipboard.writeText('{{ $surveyUrl }}').then(() => {
                    const btn = this;
                    const originalText = btn.textContent;
                    btn.textContent = 'Copied!';
                    btn.classList.add('bg-green-600');
                    setTimeout(() => {
                        btn.textContent = originalText;
                        btn.classList.remove('bg-green-600');
                    }, 2000);
                })"
                style="padding: 0.25rem 0.5rem; font-size: 0.75rem; border-radius: 0.375rem; font-weight: 500; cursor: pointer; border: none;"
                class="bg-blue-600 hover:bg-blue-700 text-white">
                Copy
            </button>
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
        <div style="display: flex; flex-direction: column; gap: 0.5rem; width: 100%;">
            <x-filament::button tag="a" href="data:image/svg+xml;base64,{{ base64_encode($qrCodeRaw) }}"
                download="{{ $survey->slug }}-qr-code.svg" icon="heroicon-o-arrow-down-tray" color="gray"
                size="sm" style="justify-content: center;">
                Download QR Code
            </x-filament::button>
            <x-filament::button tag="a" href="{{ $surveyUrl }}" target="_blank"
                icon="heroicon-o-arrow-top-right-on-square" size="sm" style="justify-content: center;">
                Open Survey
            </x-filament::button>
        </div>
    </div>
</div>

<style>
    @keyframes fade-in-up {
        0% {
            opacity: 0;
            transform: translateY(20px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in-up {
        animation: fade-in-up 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>
