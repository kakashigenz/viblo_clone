<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_change_password_fails_with_incorrect_current_password()
    {
        $request = [
            'password' => 'wrong_password',
            'new_password' => 'new123456',
            'new_password_confirmation' => 'new123456'
        ];

        $response = $this->actingAs($this->user)->putJson(route('user.changePassword'), $request);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Mật khẩu cũ không chính xác'
            ]);
    }
}
