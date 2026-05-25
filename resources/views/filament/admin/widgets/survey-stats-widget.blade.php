<x-filament-widgets::widget>
    <div class="relative">
        <div wire:loading.delay.shorter class="absolute inset-0 z-10 flex items-center justify-center bg-white/50 dark:bg-gray-900/50 backdrop-blur-[1px] rounded-2xl">
            <x-filament::loading-indicator class="h-8 w-8 text-primary-500" />
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Stat 1: Total Surveys --}}
        <div class="fi-wi-stats-overview-stat relative rounded-2xl bg-white p-6 shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-white/5 transition duration-200 hover:shadow-md">
            <div class="flex items-center gap-x-4">
                <div class="p-3 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                    <x-heroicon-o-document-duplicate class="h-6 w-6"/>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Total Surveys</p>
                    <p class="text-3xl font-bold tracking-tight text-gray-950 dark:text-white">{{ $totalSurveys }}</p>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1">
                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-500/10 dark:text-green-400">
                    {{ $activeSurveys }} Active
                </span>
            </div>
        </div>

        {{-- Stat 2: Total Responses --}}
        <div class="fi-wi-stats-overview-stat relative rounded-2xl bg-white p-6 shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-white/5 transition duration-200 hover:shadow-md">
             <div class="flex items-center gap-x-4">
                <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400">
                    <x-heroicon-o-chat-bubble-bottom-center-text class="h-6 w-6"/>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Total Responses</p>
                    <p class="text-3xl font-bold tracking-tight text-gray-950 dark:text-white">{{ $totalResponses }}</p>
                </div>
            </div>
            <div class="mt-4 text-xs font-medium text-gray-500 dark:text-gray-400">
                <span class="text-gray-900 dark:text-gray-300">{{ $responsesThisMonth }}</span> this month
            </div>
        </div>

        {{-- Stat 3: Weekly Trend --}}
        <div class="fi-wi-stats-overview-stat relative rounded-2xl bg-white p-6 shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-white/5 transition duration-200 hover:shadow-md">
            <div class="flex items-center gap-x-4">
                <div @class([
                    'p-3 rounded-xl',
                    'bg-green-50 dark:bg-green-500/10 text-green-600 dark:text-green-400' => $weeklyTrend >= 0,
                    'bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400' => $weeklyTrend < 0,
                ])>
                    @if($weeklyTrend >= 0)
                        <x-heroicon-o-arrow-trending-up class="h-6 w-6"/>
                    @else
                        <x-heroicon-o-arrow-trending-down class="h-6 w-6"/>
                    @endif
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Weekly Trend</p>
                    <p @class([
                        'text-3xl font-bold tracking-tight',
                        'text-green-600 dark:text-green-400' => $weeklyTrend >= 0,
                        'text-red-600 dark:text-red-400' => $weeklyTrend < 0,
                    ])>
                        {{ $weeklyTrend >= 0 ? '+' : '' }}{{ $weeklyTrend }}%
                    </p>
                </div>
            </div>
            <div class="mt-4 text-xs font-medium text-gray-500 dark:text-gray-400">
                <span class="text-gray-900 dark:text-gray-300">{{ $last7Days }}</span> in last 7 days
            </div>
        </div>

        {{-- Stat 4: Average Rating --}}
        <div class="fi-wi-stats-overview-stat relative rounded-2xl bg-white p-6 shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-white/5 transition duration-200 hover:shadow-md">
            <div class="flex items-center gap-x-4">
                <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400">
                    <x-heroicon-o-star class="h-6 w-6"/>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Avg Rating</p>
                    <p class="text-3xl font-bold tracking-tight text-gray-950 dark:text-white">{{ $avgRating }}<span class="text-lg font-medium text-gray-400">/5</span></p>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1">
                @php $rating = (float) $avgRating; @endphp
                <div class="flex text-amber-400">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= floor($rating))
                            <x-heroicon-s-star class="h-3 w-3"/>
                        @elseif ($i - 0.5 <= $rating)
                            <x-heroicon-s-star class="h-3 w-3 opacity-50"/>
                        @else
                            <x-heroicon-o-star class="h-3 w-3 opacity-30"/>
                        @endif
                    @endfor
                </div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter ml-1">Overall Satisfaction</span>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
