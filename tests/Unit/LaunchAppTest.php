<?php

namespace Tests\Unit;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class LaunchAppTest extends TestCase
{
    #[Test]
    public function root_route_redirects_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));

        $loginResponse = $this->followingRedirects()->get('/');
        $loginResponse->assertStatus(200);
    }
}
