<?php

namespace Tests\Feature\Api;

use App\Enums\TicketStatus;
use App\Models\Customer;
use App\Models\Ticket;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class TicketsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_ticket(): void
    {
        $payload = [
            'name' => 'John Doe',
            'phone_e164' => '+15551234567',
            'email' => 'john@example.com',
            'subject' => 'Need help',
            'message' => 'Hello!',
        ];

        $response = $this->postJson('/api/tickets', $payload);

        $response->assertCreated();
        $response->assertJsonPath('data.customer.phone_e164', '+15551234567');
        $response->assertJsonPath('data.status', TicketStatus::New->value);

        $this->assertDatabaseHas('customers', [
            'phone_e164' => '+15551234567',
            'email' => 'john@example.com',
        ]);
        $this->assertDatabaseHas('tickets', [
            'subject' => 'Need help',
        ]);
    }

    public function test_can_create_ticket_with_attachments(): void
    {
        $file = UploadedFile::fake()->create('note.pdf', 50, 'application/pdf');

        $response = $this->post('/api/tickets', [
            'name' => 'Jane',
            'phone_e164' => '+15559876543',
            'email' => 'jane@example.com',
            'subject' => 'With file',
            'message' => 'See attachment',
            'attachments' => [$file],
        ], ['Accept' => 'application/json']);

        $response->assertCreated();
        $response->assertJsonPath('data.attachments.0.file_name', 'note.pdf');
    }

    public function test_validation_fails_when_attachments_exceed_max_count(): void
    {
        $files = [];
        for ($i = 0; $i < 6; $i++) {
            $files[] = UploadedFile::fake()->create("doc{$i}.pdf", 10, 'application/pdf');
        }

        $response = $this->post('/api/tickets', [
            'name' => 'Test',
            'phone_e164' => '+15558888001',
            'email' => 'maxfiles@example.com',
            'subject' => 'Too many files',
            'message' => 'Hello',
            'attachments' => $files,
        ], ['Accept' => 'application/json']);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['attachments']);
    }

    public function test_validation_fails_when_attachment_exceeds_max_size(): void
    {
        $file = UploadedFile::fake()->create('heavy.pdf', 10241, 'application/pdf');

        $response = $this->post('/api/tickets', [
            'name' => 'Test',
            'phone_e164' => '+15558888002',
            'email' => 'bigfile@example.com',
            'subject' => 'Too large',
            'message' => 'Hello',
            'attachments' => [$file],
        ], ['Accept' => 'application/json']);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['attachments.0']);
    }

    public function test_validation_fails_for_invalid_phone(): void
    {
        $response = $this->postJson('/api/tickets', [
            'name' => 'John Doe',
            'phone_e164' => '555123',
            'email' => 'john@example.com',
            'subject' => 'Need help',
            'message' => 'Hello!',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['phone_e164']);
    }

    public function test_rate_limit_one_ticket_per_day_by_phone(): void
    {
        $customer = Customer::factory()->create([
            'phone_e164' => '+15551234567',
            'email' => null,
        ]);

        Ticket::factory()->for($customer, 'customer')->create([
            'created_at' => now(),
            'status' => TicketStatus::New,
        ]);

        $response = $this->postJson('/api/tickets', [
            'name' => 'John Doe',
            'phone_e164' => '+15551234567',
            'subject' => 'Second try',
            'message' => 'Hello again!',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['phone_e164']);
    }

    public function test_rate_limit_one_ticket_per_day_by_email(): void
    {
        $customer = Customer::factory()->create([
            'phone_e164' => '+15550000001',
            'email' => 'john@example.com',
        ]);

        Ticket::factory()->for($customer, 'customer')->create([
            'created_at' => now(),
            'status' => TicketStatus::New,
        ]);

        $response = $this->postJson('/api/tickets', [
            'name' => 'John Doe',
            'phone_e164' => '+15550000002',
            'email' => 'john@example.com',
            'subject' => 'Second try',
            'message' => 'Hello again!',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_statistics_returns_day_week_month(): void
    {
        $now = CarbonImmutable::now();

        Ticket::factory()->create([
            'status' => TicketStatus::New,
            'created_at' => $now->subHours(2),
        ]);

        Ticket::factory()->create([
            'status' => TicketStatus::Processed,
            'created_at' => $now->subDays(3),
        ]);

        Ticket::factory()->create([
            'status' => TicketStatus::InProgress,
            'created_at' => $now->subDays(20),
        ]);

        Ticket::factory()->create([
            'status' => TicketStatus::New,
            'created_at' => $now->subDays(40),
        ]);

        $response = $this->getJson('/api/tickets/statistics');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'day' => ['total', 'by_status'],
                'week' => ['total', 'by_status'],
                'month' => ['total', 'by_status'],
            ],
        ]);

        $response->assertJsonPath('data.day.total', 1);
        $response->assertJsonPath('data.week.total', 2);
        $response->assertJsonPath('data.month.total', 3);
    }
}
