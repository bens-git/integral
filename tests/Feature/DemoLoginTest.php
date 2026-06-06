<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class DemoLoginTest extends TestCase
{
    public function test_demo_login_creates_user_and_redirects()
    {
        $response = $this->post('/demo-login', ['nickname' => 'trial-user']);

        $response->assertRedirect('/');

        $this->assertDatabaseHas('users', ['email' => 'demo+trial-user@example.test']);
    }
}
