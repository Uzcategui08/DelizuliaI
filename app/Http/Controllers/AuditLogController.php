<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
  public function index(Request $request): View
  {
    $query = AuditLog::query()->with('user')->latest();

    if ($request->filled('event')) {
      $query->where('event', $request->string('event'));
    }

    if ($request->filled('model')) {
      $query->where('auditable_type', $request->string('model'));
    }

    if ($request->filled('user_id')) {
      $query->where('user_id', (int) $request->input('user_id'));
    }

    $logs = $query->paginate(50)->withQueryString();

    $eventOptions = ['created', 'updated', 'deleted'];

    return view('admin.auditoria.index', [
      'logs' => $logs,
      'eventOptions' => $eventOptions,
    ]);
  }
}
