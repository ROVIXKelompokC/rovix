<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * SCRUM-110 (PBI 2 - READ): Daftar & filter akun pengguna serta regu petugas (RBAC)
     */
    public function index(Request $request)
    {
        $query = User::with('district')->withCount(['reports', 'workOrders']);

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->string('role')->toString());
        }

        if ($request->filled('district_id')) {
            $query->where('district_id', $request->integer('district_id'));
        }

        $users = $query
            ->orderByRaw("CASE role WHEN 'admin' THEN 1 WHEN 'petugas' THEN 2 WHEN 'warga' THEN 3 ELSE 4 END ASC")
            ->orderBy('id', 'asc')
            ->paginate(8)
            ->withQueryString();
        $districts = District::orderBy('name')->get();

        $roleCounts = [
            'all' => User::count(),
            'admin' => User::where('role', 'admin')->count(),
            'petugas' => User::where('role', 'petugas')->count(),
            'warga' => User::where('role', 'warga')->count(),
        ];

        return view('users.index', compact('users', 'districts', 'roleCounts'));
    }

    /**
     * SCRUM-106 (PBI 1 - CREATE): Halaman form tambah akun pengguna baru & RBAC
     */
    public function create()
    {
        $districts = District::orderBy('name')->get();
        return view('users.create', compact('districts'));
    }

    /**
     * SCRUM-106 (PBI 1 - CREATE): Endpoint POST simpan akun pengguna baru & validasi email unik
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'phone_number' => ['required', 'string', 'max:20'],
            'role' => ['required', Rule::in(['warga', 'petugas', 'admin'])],
            'district_id' => ['nullable', 'exists:districts,id'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap pengguna wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Email tersebut sudah terdaftar pada akun lain. Gunakan email berbeda.',
            'phone_number.required' => 'Nomor telepon wajib diisi.',
            'role.required' => 'Peran hak akses (RBAC) wajib dipilih.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sesuai.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'role' => $validated['role'],
            'district_id' => $validated['district_id'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "Akun pengguna {$user->name} dengan peran " . strtoupper($user->role) . " berhasil ditambahkan!");
    }

    /**
     * SCRUM-110 (PBI 2 - READ): Halaman detail profil pengguna & riwayat aktivitasnya
     */
    public function show(User $user)
    {
        $user->load(['district', 'reports.category', 'workOrders.report']);
        return view('users.show', compact('user'));
    }
}
