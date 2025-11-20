<header class="bg-white shadow-sm sticky top-0 z-50 border-b border-gray-100 h-[64.67px]">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-6 h-full flex justify-between items-center">
            <a href="{{ route('beranda') }}" class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-lg main-gradient flex items-center justify-center">
                    <span class="font-bold text-white text-base">N</span>
                </div>
                <span class="text-lg font-semibold text-gray-900">NextUse</span>
            </a>
            
            <nav class="hidden md:flex space-x-6 text-sm font-normal">
                @php
                    $isAdmin = session('is_admin') === true;
                @endphp
                @if($isAdmin)
                    @php
                        $isKelola = request()->routeIs('admin.mengelola-data.index');
                        $isTinjau = request()->routeIs('admin.tinjau') || request()->routeIs('admin.review.*');
                    @endphp
                    <a href="{{ route('admin.mengelola-data.index') }}" class="{{ $isKelola ? 'text-gray-900 border-b-2 border-teal-500 font-medium' : 'text-gray-500 hover:text-gray-900' }} transition duration-150">Kelola</a>
                    <a href="{{ route('admin.tinjau') }}" class="{{ $isTinjau ? 'text-gray-900 border-b-2 border-teal-500 font-medium' : 'text-gray-500 hover:text-gray-900' }} transition duration-150">Tinjau</a>
                @else
                    @php
                        $isInventoryPage = request()->routeIs('inventory.index');
                        $isPostItemPage = request()->routeIs('post-item.create');
                        $isChatPage = request()->routeIs('chat.*');
                    @endphp
                    <a href="{{ route('beranda') }}" class="{{ !$isInventoryPage && !$isPostItemPage && ! $isChatPage ? 'text-gray-900 border-b-2 border-teal-500 font-medium' : 'text-gray-500 hover:text-gray-900' }} transition duration-150">Browse</a>
                    <a href="{{ route('inventory.index') }}" class="{{ $isInventoryPage ? 'text-gray-900 border-b-2 border-teal-500 font-medium' : 'text-gray-500 hover:text-gray-900' }} transition duration-150">Inventory</a>
                    <a href="{{ route('post-item.create') }}" class="{{ $isPostItemPage ? 'text-gray-900 border-b-2 border-teal-500 font-medium' : 'text-gray-500 hover:text-gray-900' }} transition duration-150">Post Item</a>
                    <a href="{{ route('chat.index') }}" class="{{ $isChatPage ? 'text-gray-900 border-b-2 border-teal-500 font-medium' : 'text-gray-500 hover:text-gray-900' }} transition duration-150">Messages</a>
                @endif
            </nav>

            <div class="flex items-center space-x-3 relative">
                <button id="notif-btn" class="relative p-2 rounded-lg hover:bg-gray-100 transition duration-150">
                    <svg class="w-5 h-5 text-gray-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    <span class="absolute top-1 right-1 block w-2 h-2 bg-red-600 rounded-full"></span>
                </button>
                <a href="{{ route('profile.index') }}" class="w-8 h-8 rounded-full main-gradient flex items-center justify-center">
                <div id="notif-dropdown" class="hidden absolute right-0 top-12 w-80 bg-white border border-gray-200 rounded-xl shadow-lg z-50">
                    <div class="p-3 border-b border-gray-100 text-sm font-semibold text-gray-700">Notifikasi</div>
                    <div id="notif-list" class="max-h-80 overflow-y-auto divide-y divide-gray-100">
                        <div class="p-3 text-sm text-gray-500">Tidak ada notifikasi</div>
                    </div>
                </div>
                    <svg class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                </a>
            </div>
        </div>
    </header>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('notif-btn');
            const dropdown = document.getElementById('notif-dropdown');
            const list = document.getElementById('notif-list');
            btn?.addEventListener('click', async () => {
                dropdown?.classList.toggle('hidden');
                try {
                    const isAdmin = {{ session('is_admin') ? 'true' : 'false' }};
                    const notifUrl = isAdmin ? '{{ route('admin.notifications') }}' : '{{ route('chat.notifications') }}';
                    const resp = await fetch(notifUrl);
                    const data = await resp.json();
                    list.innerHTML = '';
                    if (!data || data.length === 0) {
                        list.innerHTML = '<div class="p-3 text-sm text-gray-500">Tidak ada notifikasi</div>';
                        return;
                    }
                    data.forEach(item => {
                        const el = document.createElement('a');
                        const href = item.url ? item.url : ('{{ url('/chat') }}/' + (item.conversation_id||''));
                        el.href = href;
                        el.className = 'flex items-start gap-3 p-3 hover:bg-gray-50';
                        const title = item.title || item.name || 'Notifikasi';
                        const preview = item.preview || '';
                        const avatarText = (item.avatar || title || 'N').slice(0,1).toUpperCase();
                        el.innerHTML = `
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-semibold">${avatarText}</div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">${title}</p>
                                <p class="text-xs text-gray-500 truncate">${preview}</p>
                            </div>
                            <div class="text-xs text-gray-400">${item.time||''}</div>
                        `;
                        list.appendChild(el);
                    });
                } catch (e) {
                    list.innerHTML = '<div class="p-3 text-sm text-red-600">Gagal memuat notifikasi</div>';
                }
            });
            document.addEventListener('click', (e) => {
                if (!dropdown || dropdown.classList.contains('hidden')) return;
                if (!dropdown.contains(e.target) && !btn.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        });
    </script>
    @endpush