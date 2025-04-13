<?php
namespace Drupal\event_booking_api\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

class BookingService {
  protected $entityTypeManager;

  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  public function createBooking(array $data) {
    if (empty($data['event_id']) || empty($data['attendee_id'])) {
      throw new \Exception('Missing event_id or attendee_id');
    }

    $event = Node::load($data['event_id']);
    $attendee = User::load($data['attendee_id']);

    if (!$event || $event->bundle() !== 'event') {
      throw new \Exception('Invalid event');
    }

    if (!$attendee) {
      throw new \Exception('Invalid attendee');
    }

    $bookings = $this->entityTypeManager->getStorage('node')->loadByProperties([
      'type' => 'booking',
      'field_event' => $event->id(),
      'field_attendee' => $attendee->id(),
    ]);

    if ($bookings) {
      throw new \Exception('Duplicate booking');
    }

    $current_count = count($this->entityTypeManager->getStorage('node')->loadByProperties([
      'type' => 'booking',
      'field_event' => $event->id(),
    ]));

    if ($current_count >= $event->get('field_capacity')->value) {
      throw new \Exception('Event is fully booked');
    }

    $booking = Node::create([
      'type' => 'booking',
      'title' => 'Booking for ' . $attendee->getDisplayName(),
      'field_event' => $event->id(),
      'field_attendee' => $attendee->id(),
    ]);
    $booking->save();

    return ['id' => $booking->id(), 'event_id' => $event->id(), 'attendee_id' => $attendee->id()];
  }
}
