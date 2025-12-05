<div class="space-y-4">
    <div class="flex justify-center p-4 bg-white rounded-lg">
        {!! $qrCode !!}
    </div>

    <div class="text-center space-y-2">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Scan this QR code to access the survey
        </p>

        <div class="flex items-center justify-center gap-2">
            <input
                type="text"
                readonly
                value="{{ $surveyUrl }}"
                class="text-sm bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded-lg text-center w-full max-w-md"
                id="survey-url-{{ $survey->id }}"
            />
            <button
                type="button"
                onclick="navigator.clipboard.writeText('{{ $surveyUrl }}').then(() => alert('URL copied to clipboard!'))"
                class="px-3 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition text-sm"
            >
                Copy
            </button>
        </div>
    </div>

    <div class="flex justify-center gap-2 pt-2">
        <a
            href="data:image/svg+xml;base64,{{ base64_encode($qrCode) }}"
            download="{{ $survey->slug }}-qr-code.svg"
            class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-800 rounded-lg hover:bg-gray-700 dark:hover:bg-gray-300 transition text-sm font-medium"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Download QR Code
        </a>
        <a
            href="{{ $surveyUrl }}"
            target="_blank"
            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition text-sm font-medium"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
            Open Survey
        </a>
    </div>
</div>
