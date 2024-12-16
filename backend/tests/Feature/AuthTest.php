<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected array $exam_data;
    protected Collection $user_list;

    protected function setUp(): void
    {
        parent::setUp();
        $this->exam_data = [
            'name' => 'Quang',
            'user_name' => 'quang',
            'email' => 'quang@gmail.com',
            'password' => 'abcd12345678',
            'password_confirmation' => 'abcd12345678'
        ];
        $this->user_list = User::factory(15)->create();
    }

    public function test_register_with_empty_data()
    {
        $response = $this->postJson(route('register'), []);

        $response->assertUnprocessable();
        $response->assertInvalid([
            'name',
            'email',
            'user_name',
            'password'
        ]);
    }

    public function test_register_with_invalid_user_name()
    {
        $this->exam_data['user_name'] = '123@#!';
        $response = $this->postJson(route('register'), $this->exam_data);

        $response->assertUnprocessable();
        $response->assertInvalid([
            'user_name',
        ]);
    }

    public function test_register_with_invalid_email()
    {
        $this->exam_data['email'] = '123sndf';
        $response = $this->postJson(route('register'), $this->exam_data);

        $response->assertUnprocessable();
        $response->assertInvalid([
            'email',
        ]);
    }

    public function test_register_with_invalid_password()
    {
        $this->exam_data['password'] = '123sndf';
        $this->exam_data['password_confirmation'] = '123sndf';

        $response = $this->postJson(route('register'), $this->exam_data);

        $response->assertUnprocessable();
        $response->assertInvalid([
            'password' => 'The password field format is invalid'
        ]);
    }

    public function test_register_with_password_confirmation_doesnt_match()
    {
        $this->exam_data['password'] = '123sndf';
        $this->exam_data['password_confirmation'] = '12e3sndf';

        $response = $this->postJson(route('register'), $this->exam_data);

        $response->assertUnprocessable();
        $response->assertInvalid([
            'password' => 'The password field confirmation does not match'
        ]);
    }

    public function test_register_with_duplicate_user_name()
    {
        $this->exam_data['user_name'] = data_get($this->user_list, '0.user_name');

        $response = $this->postJson(route('register'), $this->exam_data);

        $response->assertUnprocessable();
        $response->assertInvalid([
            'user_name' => 'The user name has already been taken'
        ]);
    }

    public function test_register_with_duplicate_email()
    {
        $this->exam_data['email'] = data_get($this->user_list, '0.email');

        $response = $this->postJson(route('register'), $this->exam_data);

        $response->assertUnprocessable();
        $response->assertInvalid([
            'email' => 'The email has already been taken'
        ]);
    }

    public function test_register_with_valid_data()
    {
        $response = $this->postJson(route('register'), $this->exam_data);

        $response->assertOk();

        $new_user = User::query()->latest()->whereNull('email_verified_at')->first();
        $this->assertEquals(data_get($new_user, 'name'), data_get($this->exam_data, 'name'));
        $this->assertEquals(data_get($new_user, 'user_name'), data_get($this->exam_data, 'user_name'));
        $this->assertEquals(data_get($new_user, 'email'), data_get($this->exam_data, 'email'));
        $this->assertTrue(Hash::check(data_get($this->exam_data, 'password'), data_get($new_user, 'password')));
        $response->assertJson([
            'message' => 'success'
        ]);
    }

    public function test_login_with_empty_data()
    {
        $response = $this->postJson(route('apiLogin'), []);

        $response->assertUnprocessable();
        $response->assertInvalid([
            'user_name' => 'The user name field is required.',
            'password' => 'The password field is required.'
        ]);
    }

    public function test_login_with_wrong_data()
    {
        $response = $this->postJson(route('apiLogin'), Arr::only($this->exam_data, ['user_name', 'password']));

        $response->assertUnauthorized();
        $response->assertJson([
            'message' => 'Tài khoản hoặc mật khẩu không chính xác',
        ]);
    }

    public function test_login_with_unverified_user()
    {
        $unverified_user = User::factory()->unverified()->create();
        $response = $this->postJson(route('apiLogin'), [
            'user_name' => $unverified_user->user_name,
            'password' => 'password'
        ]);

        $response->assertForbidden();
        $response->assertJson([
            'message' => 'Vui lòng xác thực email!',
        ]);
    }

    public function test_login_with_correct_data()
    {
        $response = $this->postJson(route('apiLogin'), [
            'user_name' => $this->user->user_name,
            'password' => 'password'
        ]);

        $response->assertOk();
        $this->assertEquals($response->json('user.user_name'), $this->user->user_name);
        $response->assertJsonStructure([
            'user',
        ]);
    }

    public function test_check_authorization_with_anonymous_user()
    {
        $response = $this->postJson(route('checkAuthorization'));

        $response->assertUnauthorized();
    }

    public function test_check_authorization_with_authenicated_user()
    {
        $response = $this->actingAs($this->user)->postJson(route('checkAuthorization'));

        $response->assertOk();
        $this->assertEquals($response->json('user_name'), $this->user->user_name);
    }

    public function test_logout()
    {
        $response = $this->actingAs($this->user)->postJson(route('logout'));
        $response->assertOk();
        $response->assertJson([
            'message' => 'success'
        ]);
    }
}
