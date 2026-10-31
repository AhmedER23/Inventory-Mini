<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    public function test_root_redirects_to_default_locale(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/tr');
    }

    public function test_welcome_page_is_available_in_every_locale(): void
    {
        foreach (['en', 'tr', 'ar'] as $locale) {
            $this->get("/{$locale}")->assertStatus(200);
        }
    }

    public function test_unknown_locale_is_not_found(): void
    {
        $this->get('/xx')->assertStatus(404);
    }
}
