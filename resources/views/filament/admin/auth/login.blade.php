<div class="flex h-screen w-full items-center justify-center bg-slate-100 dark:bg-slate-950">
    <div class="flex h-[80vh] w-[1000px] overflow-hidden rounded-3xl bg-white dark:bg-slate-900 shadow-2xl">
        <!-- Left Pane: Branding & Gradient -->
        <div class="hidden w-1/2 flex-col justify-center bg-gradient-to-br from-slate-900 to-slate-700 dark:from-black dark:to-slate-950 p-12 text-white lg:flex">
            <h1 class="text-4xl font-extrabold tracking-tight">Satisfaction Survey System</h1>
            <p class="mt-6 text-lg text-slate-300 dark:text-slate-400">Feedback Management Portal</p>
            <div class="mt-auto opacity-50">
                <p class="text-sm">Secure Access Portal</p>
            </div>
        </div>

        <!-- Right Pane: Login Form -->
        <div class="flex w-full flex-col justify-center p-12 lg:w-1/2">
            <div class="mb-8 lg:hidden">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Satisfaction Survey</h1>
            </div>
            
            {{ $this->content }}
        </div>
    </div>
</div>
