<div class="p-4 space-y-6">
    {{-- QR Code Display --}}
    <div class="flex justify-center">
        <div class="p-6 bg-white rounded-xl shadow-md border border-gray-100" style="width: 260px; height: 260px;">
            {!! $qrCode !!}
        </div>
    </div>

    {{-- Instructions and URL --}}
    <div class="text-center space-y-4">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Scan this QR code to access the survey
        </p>

        <div class="bg-gray-50 dark:bg-white/5 rounded-lg p-3">
            <div class="flex items-center gap-2">
                <input
                    type="text"
                    readonly
                    value="{{ $surveyUrl }}"
                    class="flex-1 text-sm bg-white dark:bg-gray-800 px-3 py-2 rounded-lg text-center border border-gray-200 dark:border-gray-600 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    id="survey-url-{{ $survey->id }}"
                />
                <x-filament::button
                    color="gray"
                    size="sm"
                    icon="heroicon-o-clipboard-document"
                    x-data="{
                        copied: false,
                        copy() {
                            navigator.clipboard.writeText('{{ $surveyUrl }}');
                            this.copied = true;
                            setTimeout(() => this.copied = false, 2000);
                        }
                    }"
                    x-on:click="copy()"
                    x-text="copied ? 'Copied!' : 'Copy'"
                >
                    Copy
                </x-filament::button>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex flex-col sm:flex-row justify-center gap-3 pt-2">
        <x-filament::button
            tag="a"
            href="data:image/svg+xml;base64,{{ base64_encode($qrCodeRaw) }}"
            download="{{ $survey->slug }}-qr-code.svg"
            icon="heroicon-o-arrow-down-tray"
            color="gray"
            class="w-full sm:w-auto justify-center"
        >
            Download QR Code
        </x-filament::button>

        <x-filament::button
            tag="a"
            href="{{ $surveyUrl }}"
            target="_blank"
            icon="heroicon-o-arrow-top-right-on-square"
            class="w-full sm:w-auto justify-center"
        >
            Open Survey
        </x-filament::button>
    </div>
</div>
