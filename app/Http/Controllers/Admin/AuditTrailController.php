<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class AuditTrailController extends Controller
{
    public function index(Request $request)
    {
        if (! Schema::hasTable('audit_logs')) {
            $logs = new LengthAwarePaginator([], 0, 15);

            return view('admin.audit.index', compact('logs'));
        }

        $query = AuditLog::query()->latest('id');

        // Filter Pencarian
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%")
                    ->orWhere('resource_id', 'like', "%{$search}%")
                    ->orWhere('resource_type', 'like', "%{$search}%");
            });
        }

        // Filter Aksi
        if ($action = $request->query('action')) {
            $query->where('action', $action);
        }

        // Filter Role
        if ($role = $request->query('role')) {
            $query->where('user_role', $role);
        }

        $logs = $query->paginate(15)->withQueryString();

        return view('admin.audit.index', compact('logs'));
    }
}
