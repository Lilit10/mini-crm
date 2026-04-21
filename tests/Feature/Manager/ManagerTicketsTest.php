<?php

namespace Tests\Feature\Manager;

use App\Enums\TicketStatus;
use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ManagerTicketsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/manager/tickets');

        $response->assertRedirect('/login');
    }

    public function test_non_manager_gets_forbidden(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user);

        $response = $this->get('/manager/tickets');
        $response->assertForbidden();
    }

    public function test_manager_can_view_tickets_list_and_filter_by_status(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $manager = User::factory()->create([
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
        ]);
        $manager->syncRoles([RolesAndPermissionsSeeder::ROLE_MANAGER]);

        $customer = Customer::factory()->create();
        Ticket::factory()->for($customer, 'customer')->create(['status' => TicketStatus::New]);
        Ticket::factory()->for($customer, 'customer')->create(['status' => TicketStatus::Processed]);

        $this->actingAs($manager);

        $response = $this->get('/manager/tickets?status='.TicketStatus::Processed->value);

        $response->assertOk();
        $response->assertSee('Tickets', false);
        $response->assertSee(TicketStatus::Processed->value, false);
    }

    public function test_manager_can_view_ticket_and_update_status(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $manager = User::factory()->create([
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
        ]);
        $manager->syncRoles([RolesAndPermissionsSeeder::ROLE_MANAGER]);

        $ticket = Ticket::factory()->create(['status' => TicketStatus::New]);

        $this->actingAs($manager);

        $this->get('/manager/tickets/'.$ticket->id)->assertOk();

        $this->withoutMiddleware(ValidateCsrfToken::class);

        $response = $this->patch('/manager/tickets/'.$ticket->id.'/status', [
            'status' => TicketStatus::Processed->value,
        ]);

        $response->assertRedirect('/manager/tickets/'.$ticket->id);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => TicketStatus::Processed->value,
        ]);
    }
}
