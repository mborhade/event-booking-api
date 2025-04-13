# Event Booking API

This Drupal 10 module provides a RESTful API for managing events, attendees, and bookings.

## Features

- Create and list events
- Register attendees
- Book attendees to events
- Prevent overbooking and duplicate bookings

## Installation

1. Enable the module:
drush en event_booking_api


2. Ensure content types:
- `event` with fields: `field_capacity`, `field_country`
- `booking` with fields: `field_event` (entity reference), `field_attendee` (entity reference)

3. Enable required modules:
drush en node field rest user

## API Endpoints

| Method | Endpoint        | Description       |
|--------|-----------------|-------------------|
| GET    | /api/events     | List events       |
| POST   | /api/events     | Create event      |
| POST   | /api/attendees  | Register attendee |
| POST   | /api/bookings   | Book event        |

## Notes

- Use roles and permissions to manage access.
- Anonymous users can book without authentication.
