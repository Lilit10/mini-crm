<?php

namespace Tests\Feature;

use Tests\TestCase;

class WidgetTest extends TestCase
{
    public function test_widget_page_returns_ok(): void
    {
        foreach (['/widget', '/feedback-widget'] as $path) {
            $response = $this->get($path);

            $response->assertOk();
            $response->assertSee('Contact us', false);
            $response->assertSee('/api/tickets', false);
        }
    }
}
