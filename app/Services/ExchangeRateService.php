<?php

namespace App\Services;

use App\Models\ExchangeRate;
use App\Models\PriceInvoice;
use Carbon\Carbon;

class ExchangeRateService
{
  /**
   * Devuelve la tasa guardada para la fecha (YYYY-MM-DD) o null.
   */
  public function getForDate(string $date): ?ExchangeRate
  {
    return ExchangeRate::query()->whereDate('date', $date)->first();
  }

  /**
   * Si no hay tasa guardada, intenta inferirla desde la última factura del día.
   * Esto permite una transición suave con data previa.
   */
  public function getOrCreateFromInvoices(string $date): ?ExchangeRate
  {
    $existing = $this->getForDate($date);
    if ($existing) {
      return $existing;
    }

    $tasaFromInvoice = PriceInvoice::query()
      ->whereDate('fecha', $date)
      ->orderByDesc('id')
      ->value('tasa');

    if (!$tasaFromInvoice || (float) $tasaFromInvoice <= 0) {
      return null;
    }

    // Si hay concurrencia, unique(date) puede lanzar excepción; dejamos que el controlador lo maneje.
    return ExchangeRate::query()->create([
      'date' => Carbon::parse($date)->toDateString(),
      'rate' => (float) $tasaFromInvoice,
    ]);
  }

  /**
   * Resuelve la tasa para una fecha. Si ya existe, la usa.
   * Si no existe, requiere una tasa de entrada y la persiste para ese día.
   */
  public function resolveRateForDate(string $date, ?string $inputRate): float
  {
    $date = Carbon::parse($date)->toDateString();

    $existing = $this->getForDate($date);
    if ($existing) {
      return (float) $existing->rate;
    }

    $inferred = $this->getOrCreateFromInvoices($date);
    if ($inferred) {
      return (float) $inferred->rate;
    }

    $raw = is_string($inputRate) ? str_replace(',', '.', $inputRate) : $inputRate;
    $rate = (float) $raw;

    if ($rate <= 0) {
      throw new \InvalidArgumentException('La tasa debe ser mayor a 0.');
    }

    // Única por día.
    $record = ExchangeRate::query()->firstOrCreate(
      ['date' => $date],
      ['rate' => $rate]
    );

    return (float) $record->rate;
  }
}
