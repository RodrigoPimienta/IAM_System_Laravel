<?php
namespace Tests\Feature;

use App\Models\User;
use function Laravel\Prompts\table;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_login_logout(): void
    {
        //0 .- Check if the user is already registered, if yes delete it

        $user = User::where("email", "testLogin@gmail.com")->select(['id', 'name', 'email', 'status'])->first();

        if ($user) {
            // Delete the user and all its tokens
            $user->tokens()->delete();
            $user->delete();
        }

        // 1.- Create a new user
        $user = User::factory()->create([
            'name'     => 'Test User',
            'email'    => 'testLogin@gmail.com',
            'password' => bcrypt('123456'),
        ]);

        if (! $user->exists) {
            $this->fail('User not created');
        }

        $user = User::where("email", "testLogin@gmail.com")->select(['id', 'name', 'email', 'status'])->first();

        //2,- Make a POST request to the login endpoint
        $response = $this->postJson('/api/auth/login', [
            'email'                 => 'testLogin@gmail.com',
            'password'              => '123456',
            'password_confirmation' => '123456',
        ], [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200);

        $user->token = $response['data']['token'];
        $token       = $response['data']['token'];
        $response->assertJson([
            'status'  => 200,
            'error'   => false,
            'message' => 'Login',
            'data'    => [
                'id'     => $user->id,
                'name'   => $user->name,
                'email'  => $user->email,
                'status' => $user->status,
                'token'  => $token,
            ],
        ]);

        //3.- Make a POST request to the logout endpoint, using the token from the login response
        $response = $this->postJson('/api/auth/logout', [], [
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status'  => 200,
            'error'   => false,
            'message' => 'Logout',
        ]);

        //4.-- Check if the token was deleted
        $token        = explode('|', $token)[0];
        $tokenDeleted = DB::table('personal_access_tokens')->where('token', $token)->first();
        $this->assertNull($tokenDeleted);
    }
}
