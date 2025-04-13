<?php
namespace Drupal\event_booking_api\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\event_booking_api\Service\BookingService;

class BookingController extends ControllerBase {
  protected $bookingService;

  public function __construct(BookingService $bookingService) {
    $this->bookingService = $bookingService;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('event_booking_api.booking_service')
    );
  }

  public function bookEvent(Request $request): JsonResponse {
    $data = json_decode($request->getContent(), TRUE);

    try {
      $booking = $this->bookingService->createBooking($data);
      return new JsonResponse(['status' => 'success', 'booking' => $booking]);
    } catch (\Exception $e) {
      return new JsonResponse(['error' => $e->getMessage()], 400);
    }
  }
}
