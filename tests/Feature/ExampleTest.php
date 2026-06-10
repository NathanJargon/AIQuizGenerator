<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase; // 1. Add this import
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase; // 2. Add this line here

    public function test_the_application_redirects_guests_to_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_the_application_redirects_authenticated_users_to_dashboard(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/')
            ->assertRedirect(route('dashboard'));
    }
}