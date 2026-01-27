<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditableObserver
{
  public function created(Model $model): void
  {
    $this->log('created', $model, null, $this->cleanAttributes($model->getAttributes()));
  }

  public function updated(Model $model): void
  {
    $changes = $this->cleanAttributes($model->getChanges());
    if ($changes === []) {
      return;
    }

    $original = $this->cleanAttributes($model->getOriginal());
    $old = array_intersect_key($original, $changes);

    $this->log('updated', $model, $old, $changes);
  }

  public function deleted(Model $model): void
  {
    $this->log('deleted', $model, $this->cleanAttributes($model->getOriginal()), null);
  }

  protected function log(string $event, Model $model, ?array $old, ?array $new): void
  {
    if ($model instanceof AuditLog) {
      return;
    }

    if (! Auth::check()) {
      return;
    }

    $userId = Auth::id();

    $url = null;
    $ip = null;
    $userAgent = null;

    if (app()->bound('request') && ! app()->runningInConsole()) {
      $request = request();
      $url = $request?->fullUrl();
      $ip = $request?->ip();
      $userAgent = $request?->userAgent();
    }

    AuditLog::create([
      'user_id' => $userId,
      'event' => $event,
      'auditable_type' => $model::class,
      'auditable_id' => $model->getKey(),
      'old_values' => $old,
      'new_values' => $new,
      'url' => $url,
      'ip' => $ip,
      'user_agent' => $userAgent,
    ]);
  }

  protected function cleanAttributes(array $attributes): array
  {
    unset($attributes['password'], $attributes['remember_token']);
    unset($attributes['created_at'], $attributes['updated_at'], $attributes['deleted_at']);

    return $attributes;
  }
}
