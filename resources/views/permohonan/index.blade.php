@extends('layouts.app')

@section('title', 'Permohonan Saya')

@section('content')
<div class="py-8 px-4 sm:px-6">
    <div class="max-w-5xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Permohonan Saya</h1>
            <p class="text-sm text-gray-600">Daftar permohonan yang telah Anda ajukan.</p>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800">{{ $errors->first() }}</div>
        @endif

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
            <div class="divide-y divide-gray-100">
                @forelse($requests as $req)
                    <div class="p-4 flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <p class="text-sm text-gray-500">Barang</p>
                            <p class="text-base font-semibold text-gray-900">{{ $req->item->judul ?? 'Barang' }}</p>
                            <div class="mt-1 text-sm text-gray-600">Lokasi: {{ $req->item->lokasi ?? '-' }}</div>
                            <div class="mt-1 text-sm text-gray-600">Jumlah diajukan: {{ $req->requested_quantity }} unit</div>
                            <div class="mt-1 text-sm text-gray-600">Diajukan: {{ $req->created_at?->format('d M Y H:i') }}</div>
                        </div>
                        <div class="text-right">
                            @php
                                $statusLabel = [
                                    'pending' => 'Menunggu Review',
                                    'review' => 'Sedang Ditinjau',
                                    'approved' => 'Disetujui',
                                    'rejected' => 'Ditolak',
                                ][$req->status] ?? ucfirst($req->status);
                            @endphp
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold
                                {{ $req->status === 'pending' ? 'bg-yellow-50 text-yellow-700 border border-yellow-200' : '' }}
                                {{ $req->status === 'review' ? 'bg-blue-50 text-blue-700 border border-blue-200' : '' }}
                                {{ $req->status === 'approved' ? 'bg-green-50 text-green-700 border border-green-200' : '' }}
                                {{ $req->status === 'rejected' ? 'bg-rose-50 text-rose-700 border border-rose-200' : '' }}
                            ">
                                {{ $statusLabel }}
                            </span>
                            <div class="mt-2">
                                <a href="{{ route('items.show', $req->item->id) }}" class="text-sm text-teal-700 hover:text-teal-800">Lihat barang →</a>
                            </div>
                            @if($req->review_notes)
                                <p class="mt-2 text-xs text-gray-500">Catatan: {{ $req->review_notes }}</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-sm text-gray-600">Belum ada permohonan yang diajukan.</div>
                @endforelse
            </div>
        </div>

        <div class="mt-4">{{ $requests->links() }}</div>
    </div>
    </div>
@endsection
