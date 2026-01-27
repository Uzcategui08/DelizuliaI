<?php

namespace App\Http\Controllers;

use App\Models\MilkEntry;
use App\Models\Payee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MilkEntryController extends Controller
{
  public function index(Request $request)
  {
    $mode = $request->query('mode', 'week');
    if (!in_array($mode, ['week', 'year'], true)) {
      $mode = 'week';
    }

    $availableWeeks = MilkEntry::query()
      ->whereNotNull('week_end')
      ->select('week_end')
      ->distinct()
      ->orderBy('week_end', 'desc')
      ->pluck('week_end')
      ->map(fn($d) => Carbon::parse($d)->toDateString());

    $availableYears = $availableWeeks
      ->map(fn($d) => Carbon::parse($d)->year)
      ->unique()
      ->sortDesc()
      ->values();

    $selectedWeekEnd = null;
    $selectedYear = null;

    $selectedWeekEntries = collect();
    $selectedWeekTotals = null;

    $yearSummaries = collect();
    $yearTotals = null;

    if ($mode === 'year') {
      $selectedYear = (int) $request->query('year', Carbon::now()->year);
      $yearStart = Carbon::create($selectedYear, 1, 1)->toDateString();
      $yearEnd = Carbon::create($selectedYear, 12, 31)->toDateString();

      $yearSummaries = MilkEntry::query()
        ->select([
          'week_end',
          DB::raw('COALESCE(SUM(liters), 0) as total_liters'),
          DB::raw('COALESCE(SUM(amount), 0) as total_amount'),
          DB::raw('COUNT(*) as entries_count'),
        ])
        ->whereNotNull('week_end')
        ->whereDate('week_end', '>=', $yearStart)
        ->whereDate('week_end', '<=', $yearEnd)
        ->groupBy('week_end')
        ->orderBy('week_end', 'desc')
        ->get();

      $yearTotals = MilkEntry::query()
        ->whereNotNull('week_end')
        ->whereDate('week_end', '>=', $yearStart)
        ->whereDate('week_end', '<=', $yearEnd)
        ->select([
          DB::raw('COALESCE(SUM(liters), 0) as total_liters'),
          DB::raw('COALESCE(SUM(amount), 0) as total_amount'),
          DB::raw('COUNT(*) as entries_count'),
        ])
        ->first();
    } else {
      $selectedWeekEnd = $request->query('week_end');
      if (empty($selectedWeekEnd)) {
        $now = Carbon::now();
        $selectedWeekEnd = $now->isTuesday() ? $now->toDateString() : $now->next(Carbon::TUESDAY)->toDateString();
      }

      $selectedWeekEntries = MilkEntry::query()
        ->with('payee')
        ->whereDate('week_end', $selectedWeekEnd)
        ->orderBy('date')
        ->orderBy('id')
        ->get();

      $selectedWeekTotals = MilkEntry::query()
        ->whereDate('week_end', $selectedWeekEnd)
        ->select([
          DB::raw('COALESCE(SUM(liters), 0) as total_liters'),
          DB::raw('COALESCE(SUM(amount), 0) as total_amount'),
          DB::raw('COUNT(*) as entries_count'),
        ])
        ->first();
    }

    return view('milk_entries.index', compact(
      'mode',
      'availableWeeks',
      'availableYears',
      'selectedWeekEnd',
      'selectedYear',
      'selectedWeekEntries',
      'selectedWeekTotals',
      'yearSummaries',
      'yearTotals'
    ));
  }

  public function create()
  {
    $payees = Payee::orderBy('name')->get();
    return view('milk_entries.create', compact('payees'));
  }

  public function store(Request $request)
  {
    // Normalize special select value used by the UI when the user chooses "Nuevo proveedor".
    $entries = $request->input('entries', []);
    if (is_array($entries)) {
      foreach ($entries as $i => $entry) {
        if (is_array($entry) && array_key_exists('payee_id', $entry) && $entry['payee_id'] === '__new') {
          $entries[$i]['payee_id'] = null;
        }
      }
      $request->merge(['entries' => $entries]);
    }

    $data = $request->validate([
      'entries' => 'required|array|min:1',
      'entries.*.date' => 'required|date',
      'entries.*.payee_id' => 'nullable|exists:payees,id',
      'entries.*.payee_name' => 'nullable|string|max:255',
      'entries.*.liters' => 'required|numeric',
      'entries.*.amount' => 'nullable|numeric',
    ]);

    foreach ($data['entries'] as $entry) {
      $payeeId = $entry['payee_id'] ?? null;

      if (empty($payeeId) && !empty($entry['payee_name'])) {
        $payee = Payee::firstOrCreate([
          'name' => $entry['payee_name']
        ], [
          'alias' => null,
          'contact_info' => null,
        ]);
        $payeeId = $payee->id;
      }

      $date = Carbon::parse($entry['date']);
      // Determine the week-end (Tuesday) for this date: the next Tuesday on or after the date
      $weekEnd = $date->isTuesday() ? $date->copy() : $date->copy()->next(Carbon::TUESDAY);

      MilkEntry::create([
        'date' => $date->toDateString(),
        'payee_id' => $payeeId,
        'payee_name' => $entry['payee_name'] ?? null,
        'liters' => $entry['liters'],
        'amount' => $entry['amount'] ?? null,
        'week_end' => $weekEnd->toDateString(),
      ]);
    }

    return redirect()->route('milk-entries.index')->with('success', 'Entradas de leche guardadas correctamente.');
  }
}
