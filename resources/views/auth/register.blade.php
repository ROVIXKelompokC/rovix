@extends('layouts.app')

@section('title', 'Registrasi Warga — ROVIX')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-slate-900 p-6">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-amber-500 text-slate-950 font-extrabold flex items-center justify-center">RV</div>
            <div>
                <h1 class="font-bold text-lg text-slate-800">Registrasi Warga Pelapor</h1>
                <p class="text-xs text-slate-500">Buat akun untuk melapor & memantau perbaikan jalan</p>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs space-y-1">
                @foreach($errors->all() as $err)
                    <div>• {{ $err }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('register.submit') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Telepon / WhatsApp *</label>
                <input type="text" name="phone_number" value="{{ old('phone_number') }}" required placeholder="0812xxxxxxxx"
                    class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi *</label>
                    <input type="password" name="password" required
                        class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Ulangi Sandi *</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-3.5 py-2 rounded-lg border border-slate-300 text-sm">
                </div>
            </div>

            <button type="submit" class="w-full py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-lg text-sm">
                Daftar sebagai Warga
            </button>
        </form>

        <p class="text-center text-xs text-slate-500 mt-5">
            Sudah punya akun? <a href="{{ route('login') }}" class="text-amber-600 font-semibold hover:underline">Masuk di sini</a>
        </p>
    </div>
</div>
@endsection
