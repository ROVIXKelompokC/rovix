<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportCategory;
use Illuminate\Http\Request;

class ReportCategoryController extends Controller
{
    public function index()
    {
        $categories = ReportCategory::orderBy('category_name')->get();

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_name' => ['required', 'string', 'max:100', 'unique:report_categories,category_name'],
            'severity_level' => ['required', 'integer', 'between:1,5'],
        ], [
            'category_name.required' => 'Nama kategori wajib diisi.',
            'category_name.unique' => 'Nama kategori sudah ada.',
            'severity_level.required' => 'Tingkat keparahan wajib dipilih.',
            'severity_level.between' => 'Tingkat keparahan harus antara 1 sampai 5.',
        ]);

        ReportCategory::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori kerusakan berhasil ditambahkan.');
    }
}