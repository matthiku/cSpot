<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function a_user_may_register_for_an_account_but_must_confirm_their_email_address()
    {
        // When we register...
        $this->visit('register')
             ->type('John', 'first_name')
             ->type('Doe', 'last_name')
             ->type('john@example.com', 'email')
             ->type('password', 'password')
             ->type('password', 'password_confirmation')
             ->press('Register');

        // We should have an account - but one that is not yet confirmed/verified.
        $this->see('>Click the link contained in that email in order to conclude your registration.')
             ->seeInDatabase('users', ['first_name' => 'John', 'verified' => 0]);

        $user = User::whereName('John')->first();

        // You can't login until you confirm your email address.
        $this->login($user)->see('These credentials do not match our records or your email address has not been verified.');

        // Like this...
        $this->visit("register/confirm/{$user->token}")
             ->see('You are now confirmed. Please sign in.')
             ->seeInDatabase('users', ['email' => 'john@example.com', 'verified' => 1]);
    }

    /** @test */
    public function a_user_may_login()
    {
        $this->login()->see('Welcome to c-SPOT!')->onPage('/');
    }

    protected function login($user = null)
    {
        $user = $user ?: $this->factory->create('App\Models\User', ['password' => 'password']);

        return $this->visit('login')
                    ->type($user->email, 'email')
                    ->type('password', 'password') // You might want to change this.
                    ->press('Login');
    }
}
