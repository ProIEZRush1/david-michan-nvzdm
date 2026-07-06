<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The panel has no public landing page: "/" always redirects into the login/dashboard flow.
     */
    public function test_the_root_url_redirects_into_the_panel(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('dashboard'));
    }
}
