<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    // READ (SCRUM-34): Menampilkan daftar & pencarian wilayah kecamatan
    public function index(Request $request)
    {
        $query = District::withCount(['users', 'reports']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('city_code', 'like', "%{$search}%");
            });
        }

        $districts = $query->latest()->paginate(10);
        $totalDistricts = District::count();

        return view('districts.index', compact('districts', 'totalDistricts'));
    }

    // CREATE (SCRUM-30): Form tambah data wilayah baru
    public function create()
    {
        return view('districts.create');
    }

    // CREATE (SCRUM-30): Simpan data wilayah ke database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city_code' => 'required|string|max:50|unique:districts,city_code',
        ]);

        District::create([
            'name' => $request->name,
            'city_code' => strtoupper($request->city_code),
        ]);

        return redirect()->route('admin.districts.index')
            ->with('success', 'Data wilayah kecamatan berhasil ditambahkan!');
    }

    // READ (SCRUM-34): Detail wilayah kecamatan
    public function show($id)
    {
        $district = District::withCount(['users', 'reports'])->findOrFail($id);
        return view('districts.show', compact('district'));
    }
}