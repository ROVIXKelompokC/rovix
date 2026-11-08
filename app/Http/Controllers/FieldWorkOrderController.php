<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use Illuminate\Support\Facades\Auth;

class FieldWorkOrderController extends Controller
{
    /**
     * SCRUM-42 (Field View): halaman detail SPK untuk petugas.
     * Menampilkan foto kondisi awal (Before), SLA, instruksi admin, dan tautan navigasi peta.
     */
    public function show(WorkOrder $workOrder)
    {
        $user = Auth::user();

        // Petugas hanya boleh melihat SPK miliknya sendiri (admin boleh semua).
        abort_if(
            $user->role === 'petugas' && (int) $workOrder->officer_id !== (int) $user->id,
            403,
            'Anda tidak memiliki akses ke SPK ini.'
        );

        $workOrder->load(['report.district', 'report.category']);

        return view('petugas.work-orders.show', compact('workOrder'));
    }
}
