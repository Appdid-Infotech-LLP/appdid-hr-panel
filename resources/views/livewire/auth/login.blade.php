<div class="flex min-h-screen">
    {{-- Brand panel — hidden on small screens. --}}
    <div class="relative hidden w-1/2 flex-col justify-between bg-brand-ink p-12 lg:flex">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/appdid-logo.png') }}" alt="Appdid Technologies" class="h-10 w-10 object-contain">
            <div>
                <p class="text-lg font-semibold text-white">Appdid Technologies</p>
                <p class="text-sm text-teal-100/70">HR Panel</p>
            </div>
        </div>

        <div>
            <h1 class="text-3xl font-semibold text-white">Recruitment, organized.</h1>
            <p class="mt-3 max-w-sm text-teal-100/70">Track candidates, schedule interviews, and manage your hiring pipeline in one place.</p>
        </div>

        <p class="text-xs text-teal-100/40">&copy; {{ date('Y') }} Appdid Technologies Pvt Ltd.</p>
    </div>

    {{-- Form panel --}}
    <div class="flex w-full flex-col items-center justify-center px-6 py-12 lg:w-1/2">
        <div class="w-full max-w-sm">
            <div class="mb-8 flex flex-col items-center text-center lg:hidden">
                <img src="{{ asset('images/appdid-logo.png') }}" alt="Appdid Technologies" class="h-12 w-12 object-contain">
                <p class="mt-2 text-lg font-semibold text-slate-900">Appdid Technologies</p>
            </div>

            <h2 class="text-2xl font-semibold text-slate-900">Welcome back</h2>
            <p class="mt-1 text-sm text-slate-500">Log in to your HR account to continue.</p>

            <form wire:submit="login" class="mt-8 space-y-5">
                <x-hr.input name="email" type="email" label="Email" placeholder="you@appdid.com" required autofocus />

                <x-hr.input name="password" type="password" label="Password" placeholder="••••••••" required />

                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" wire:model="remember" class="rounded border-slate-300 text-brand-teal focus:ring-brand-teal/30">
                    Remember me
                </label>

                <button
                    type="submit"
                    class="w-full rounded-lg bg-brand-teal px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-teal-dark disabled:opacity-60"
                    wire:loading.attr="disabled" wire:target="login"
                >
                    <span wire:loading.remove wire:target="login">Log In</span>
                    <span wire:loading wire:target="login">Signing in...</span>
                </button>
            </form>

           
        </div>
    </div>
</div>
