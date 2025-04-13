<?php
namespace Drupal\event_booking_api\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\node\Entity\Node;

class EventController {
  public function listEvents(): JsonResponse {
    $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'event']);
    $data = [];

    foreach ($nodes as $node) {
      $data[] = [
        'id' => $node->id(),
        'title' => $node->label(),
        'capacity' => $node->get('field_capacity')->value,
        'country' => $node->get('field_country')->value,
      ];
    }

    return new JsonResponse(['status' => 'success', 'data' => $data]);
  }

  public function createEvent(Request $request): JsonResponse {
    $data = json_decode($request->getContent(), TRUE);

    if (!isset($data['title']) || !isset($data['capacity'])) {
      return new JsonResponse(['error' => 'Invalid data'], 400);
    }

    $node = Node::create([
      'type' => 'event',
      'title' => $data['title'],
      'field_capacity' => $data['capacity'],
      'field_country' => $data['country'] ?? 'US',
    ]);
    $node->save();

    return new JsonResponse(['status' => 'success', 'id' => $node->id()]);
  }
}
