<?php

namespace Tests\Feature;

use Tests\TestCase;

class WidgetTest extends TestCase
{
    public function test_widget_page_returns_ok(): void
    {
        $response = $this->get('/widget');

        $response->assertOk();
        $response->assertSee('Contact us', false);
        $response->assertSee('/api/tickets', false);
    }
}
