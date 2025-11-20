@extends('layouts.app')

@section('title', 'Mengelola Data Pengguna & Barang')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <p class="text-sm uppercase tracking-wide text-teal-600 mb-2 font-semibold">Dashboard Admin</p>
        <h1 class="text-3xl font-semibold text-gray-900">Mengelola Data Pengguna & Barang</h1>
        <p class="text-gray-500 mt-2">Pantau aktivitas komunitas NextUse, verifikasi data, dan jaga kualitas platform.</p>
    </div>

    <section class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-2">Total Pengguna</p>
            <p class="text-3xl font-semibold text-gray-900">{{ number_format($stats['total_users']) }}</p>
            <p class="text-xs text-gray-400 mt-1">Organisasi terverifikasi aktif di NextUse</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-2">Total Barang Aktif</p>
            <p class="text-3xl font-semibold text-gray-900">{{ number_format($stats['total_items']) }}</p>
            <p class="text-xs text-gray-400 mt-1">Barang yang siap dibagikan atau ditukar</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-2">Perlu Ditinjau</p>
            <p class="text-3xl font-semibold text-gray-900">{{ number_format($stats['need_review']) }}</p>
            <p class="text-xs text-gray-400 mt-1">Barang berstatus reserved yang menunggu verifikasi</p>
        </div>
    </section>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    <section class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Data Pengguna</h2>
                <p class="text-sm text-gray-500">Kelola organisasi/komunitas yang terdaftar di NextUse.</p>
            </div>
            <form method="GET" class="w-full md:w-80">
                <input type="text" name="search_user" value="{{ request('search_user', '') }}" placeholder="Cari nama, email, atau tipe organisasi..." class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 focus:outline-none text-sm">
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr class="text-left text-gray-500 uppercase tracking-wide text-xs">
                        <th class="py-3">Organisasi</th>
                        <th class="py-3">Email</th>
                        <th class="py-3">Tipe</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="py-4">
                                <p class="font-semibold text-gray-900">{{ $user->organization_name }}</p>
                                <p class="text-xs text-gray-400">ID {{ $user->id }}</p>
                            </td>
                            <td class="py-4 text-gray-600">{{ $user->email }}</td>
                            <td class="py-4 text-gray-600 capitalize">{{ str_replace('-', ' ', $user->organization_type) }}</td>
                            <td class="py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $user->is_active ? 'bg-green-50 text-green-700' : 'bg-yellow-50 text-yellow-700' }}">
                                    {{ $user->is_active ? 'Aktif' : 'Menunggu Verifikasi' }}
                                </span>
                            </td>
                            <td class="py-4">
                                <div class="flex justify-end gap-2">
                                    <form action="{{ route('admin.mengelola-data.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus pengguna ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-2 rounded-lg text-xs font-medium text-red-600 hover:bg-red-50">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-gray-500">Tidak ada data pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div>
            {{ $users->links() }}
        </div>
    </section>

    <section class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Data Barang</h2>
                <p class="text-sm text-gray-500">Pantau barang yang dipublikasikan oleh komunitas.</p>
            </div>
            <form method="GET" class="w-full md:w-80">
                <input type="text" name="search_item" value="{{ request('search_item', '') }}" placeholder="Cari judul, kategori, atau lokasi..." class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-teal-500 focus:outline-none text-sm">
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr class="text-left text-gray-500 uppercase tracking-wide text-xs">
                        <th class="py-3">Barang</th>
                        <th class="py-3">Kategori</th>
                        <th class="py-3">Lokasi</th>
                        <th class="py-3">Pemilik</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="py-4">
                                <p class="font-semibold text-gray-900">{{ $item->judul }}</p>
                                <p class="text-xs text-gray-400">ID {{ $item->id }}</p>
                            </td>
                            <td class="py-4 text-gray-600">{{ $item->kategori }}</td>
                            <td class="py-4 text-gray-600">{{ $item->lokasi }}</td>
                            <td class="py-4 text-gray-600">{{ optional($item->organization)->organization_name ?? 'Tidak diketahui' }}</td>
                            <td class="py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                    @class([
                                        'bg-blue-50 text-blue-700' => $item->status === 'tersedia',
                                        'bg-yellow-50 text-yellow-700' => $item->status === 'reserved',
                                        'bg-gray-100 text-gray-500' => $item->status === 'habis',
                                    ])
                                ">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="py-4">
                                <div class="flex justify-end gap-2">
                                    <form action="{{ route('admin.mengelola-data.items.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus barang ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-2 rounded-lg text-xs font-medium text-red-600 hover:bg-red-50">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-500">Tidak ada data barang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div>
            {{ $items->links() }}
        </div>
    </section>
</div>
@endsection

