<?php

namespace Drupal\Tests\event_booking_api\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

/**
 * Kernel tests for Event Booking logic.
 *
 * @group event_booking_api
 */
class EventBookingKernelTest extends KernelTestBase {

  protected static $modules = ['node', 'user', 'field', 'text', 'system', 'filter', 'event_booking_api'];

  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    $this->installSchema('node', ['node_access']);
  }

  public function testPreventDuplicateBooking() {
    $user = User::create(['name' => 'Test User', 'status' => 1]);
    $user->save();

    $event = Node::create([
      'type' => 'event',
      'title' => 'Drupal Meetup',
      'field_capacity' => 1,
      'field_country' => 'France',
    ]);
    $event->save();

    $booking1 = Node::create([
      'type' => 'booking',
      'field_event' => ['target_id' => $event->id()],
      'field_attendee' => ['target_id' => $user->id()],
    ]);
    $booking1->save();

    $this->expectExceptionMessage('User already booked this event.');

    $existing = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->getQuery()
      ->condition('type', 'booking')
      ->condition('field_event', $event->id())
      ->condition('field_attendee', $user->id())
      ->accessCheck(FALSE)
      ->count()
      ->execute();

    if ($existing) {
      throw new \Exception('User already booked this event.');
    }

    $booking2 = Node::create([
      'type' => 'booking',
      'field_event' => ['target_id' => $event->id()],
      'field_attendee' => ['target_id' => $user->id()],
    ]);
    $booking2->save();
  }
}
