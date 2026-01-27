<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
  /**
   * List notifications (placeholder implementation).
   */
  public function index(): JsonResponse
  {
    return response()->json([
      'message' => 'Listado de notificaciones no implementado aún.',
    ]);
  }

  /**
   * Mark a notification as read (placeholder implementation).
   */
  public function markAsRead(string $id): JsonResponse
  {
    return response()->json([
      'message' => 'Acción no implementada.',
      'notification_id' => $id,
    ]);
  }

  /**
   * Mark all notifications as read (placeholder implementation).
   */
  public function markAllAsRead(): JsonResponse
  {
    return response()->json([
      'message' => 'Acción no implementada.',
    ]);
  }

  /**
   * Return unread notification count (placeholder implementation).
   */
  public function unreadCount(): JsonResponse
  {
    return response()->json([
      'unread' => 0,
    ]);
  }
}
