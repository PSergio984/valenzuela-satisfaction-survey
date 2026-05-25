<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Satisfaction Survey</title>
    @vite(['resources/css/app.css'])
</head>
<body class="h-full">
    <div class="flex h-screen w-full items-center justify-center bg-slate-100">
        <div class="flex h-[80vh] w-[1000px] overflow-hidden rounded-3xl bg-white shadow-2xl">
            <!-- Left Pane: Branding & Gradient -->
            <div class="hidden w-1/2 flex-col justify-center bg-gradient-to-br from-slate-900 to-slate-700 p-12 text-white lg:flex">
                <h1 class="text-4xl font-extrabold tracking-tight">Satisfaction Survey System</h1>
                <p class="mt-6 text-lg text-slate-300">Feedback Management Portal</p>
                <div class="mt-auto opacity-50">
                    <p class="text-sm">Secure Access Portal</p>
                </div>
            </div>

            <!-- Right Pane: Login Form -->
            <div class="flex w-full flex-col justify-center p-12 lg:w-1/2">
                <div class="mb-8 lg:hidden">
                    <h1 class="text-2xl font-bold text-slate-900">Satisfaction Survey</h1>
                </div>
                
                {{ $this->form }}

                <div class="mt-6">
                    {{ $this->authenticateAction }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
