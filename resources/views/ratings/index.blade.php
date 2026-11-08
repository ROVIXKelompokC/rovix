@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6">Daftar Evaluasi & Rating Kepuasan Warga</h2>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="p-3">Waktu</th>
                    <th class="p-3">Pelapor</th>
                    <th class="p-3">Skor Rating</th>
                    <th class="p-3">Ulasan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ratings as $rating)
                    <tr class="border-b">
                        <td class="p-3">{{ $rating->created_at->format('d M Y H:i') }}</td>
                        <td class="p-3">{{ $rating->user->name ?? 'Warga' }}</td>
                        <td class="p-3 text-yellow-500 font-bold">⭐ {{ $rating->score }} / 5</td>
                        <td class="p-3">{{ $rating->review_text ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-4 text-center text-gray-500">Belum ada ulasan yang dikirimkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection