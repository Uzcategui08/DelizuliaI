<?php

namespace App\Http\Controllers;

use App\Models\Payee;
use App\Models\Payment;
use App\Models\Todo;
use Illuminate\View\View;

class OrganizadorController extends Controller
{
  /**
   * Display the organizer dashboard with tasks and payments overview.
   */
  public function index(): View
  {
    $todos = Todo::query()
      ->orderBy('is_completed')
      ->orderByRaw('COALESCE(due_at, reminder_at, created_at) ASC')
      ->get();

    $pendingPayments = Payment::query()
      ->with('payee')
      ->where('is_paid', false)
      ->orderByRaw('COALESCE(scheduled_for, created_at) ASC')
      ->get();

    $completedPayments = Payment::query()
      ->with('payee')
      ->where('is_paid', true)
      ->orderByDesc('paid_at')
      ->limit(10)
      ->get();

    $payees = Payee::query()->orderBy('name')->pluck('name', 'id');

    return view('organizador.index', [
      'todos' => $todos,
      'pendingPayments' => $pendingPayments,
      'completedPayments' => $completedPayments,
      'payees' => $payees,
    ]);
  }
}
