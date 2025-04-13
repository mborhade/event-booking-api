<?php
namespace Drupal\event_booking_api\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\user\Entity\User;

class AttendeeController {
  public function registerAttendee(Request $request): JsonResponse {
    $data = json_decode($request->getContent(), TRUE);
    if (empty($data['name']) || empty($data['email'])) {
      return new JsonResponse(['error' => 'Missing name or email'], 400);
    }

    $user = User::create([
      'name' => $data['name'],
      'mail' => $data['email'],
      'status' => 1,
    ]);
    $user->enforceIsNew();
    $user->save();

    return new JsonResponse(['status' => 'success', 'user_id' => $user->id()]);
  }
}
