@php $notifs = ($navNotifs ?? collect())->values()->all(); @endphp

<div class="relative" x-data="{
        open: false,
        items: @js($notifs),
        dismiss(n) {
            this.items = this.items.filter(i => i.id !== n.id);
            if (n.read_url) {
                fetch(n.read_url, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content } });
            }
        }
    }" @click.outside="open = false">

    <button type="button" @click="open = !open" class="relative rounded-full p-2 text-slate-700 hover:bg-slate-100">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>
        <span x-show="items.length" x-cloak class="absolute right-1.5 top-1.5 h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white"></span>
    </button>

    <div x-show="open" x-cloak x-transition class="absolute right-0 z-50 mt-3 w-80 space-y-3">
        <template x-if="items.length === 0">
            <div class="rounded-2xl bg-white p-5 text-center text-xs text-slate-400 shadow-xl">Belum ada notifikasi.</div>
        </template>

        <template x-for="n in items" :key="n.id">
            <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-xl">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-center gap-2 text-sm font-bold text-[#0c203b]">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
                        <span x-text="n.title"></span>
                    </div>
                    <button type="button" @click="dismiss(n)" class="text-slate-400 hover:text-slate-700">✕</button>
                </div>
                <p class="mt-3 text-xs leading-relaxed text-slate-600" x-text="n.body"></p>
                <a :href="n.url" class="mt-3 flex items-center justify-end gap-1 text-xs font-semibold text-[#0c203b] hover:underline">Menuju Aktivitas →</a>
            </div>
        </template>
    </div>
</div>