# Architecture notes

This project follows a simple, layered structure to keep controllers thin and make the domain logic testable.

## Goals

- **Thin controllers**: HTTP layer only orchestrates request/response.
- **Actions**: application use-cases (create ticket, list tickets, update status).
- **Repositories**: database access isolated from controllers/actions.
- **FormRequest / Rules**: validation close to the input boundary.
- **API Resources**: consistent JSON responses.

## Layers

### Controllers

- Only:
  - accept validated input (FormRequest)
  - call Actions
  - return Resources / Views

### Actions

- Contain application flow / use-cases:
  - `CreateTicketAction` (ticket creation + attachments)
  - `GetTicketStatisticsAction`
  - manager actions for listing and updating tickets
  - auth actions (login/logout)

Actions are easy to unit-test and keep controllers free of domain logic.

### Repositories

- All Eloquent queries live here:
  - `TicketRepository` (statistics, uniqueness checks, manager filters/pagination)
  - `CustomerRepository` (find-or-create behavior)

### Validation

- `StoreTicketRequest` validates ticket payload + attachments.
- `UniqueTicketPerDayForPhone/Email` Rules implement the “one ticket per day” restriction using `TicketRepository`.

## Key library choices

### spatie/laravel-permission

- Simple role checks via middleware (e.g. `role:manager`).
- Roles seeded in `RolesAndPermissionsSeeder`.

### spatie/laravel-medialibrary

- Attachments are stored via Media Library and exposed in API response (`attachments[].url`).
- Requires `php artisan storage:link` and correct `APP_URL` for public URLs.

## Testing strategy

- Feature tests cover:
  - API create ticket (JSON + multipart)
  - validation and rate-limits
  - statistics endpoint
  - widget page availability
  - manager UI access control + status update

## Trade-offs

- **Simplicity over over-engineering**: no extra DTO/query-builder packages; Actions + Repositories keep the codebase small and readable for a test-sized project.
- **Swagger UI via CDN**: avoids pulling heavy PHP tooling into `composer.json`; acceptable for local/demo deployments.

