<?php

namespace App\Http\Controllers\Auth;

use Log;
use App\Models\User;
use App\Models\Role;
use Validator;
use Socialite;
use App\Mailers\AppMailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;


    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';


    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }



    /*
    |--------------------------------------------------------------------------
    | Socialite Implementation 
    | part 1 of 3
    | see http://www.codeanchor.net/blog/complete-laravel-socialite-tutorial/
    |--------------------------------------------------------------------------
    |
    */
    public function loginViaProvider(AuthenticateUser $authenticateUser, Request $request, $provider = null) 
    {
        return $authenticateUser->execute( $request->all(), $this, $provider );
    }
    // redirect to dashboard after a succesful login
    public function userHasLoggedIn($user) {
        \Session::flash( 'status', 'Welcome, '.$user->name.'! You have been logged in via '.$user->provider );
        return redirect('/tasks');
    }
    /**
     * Redirect the user to the Provider's authentication page.
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('github')->redirect();
    }
    /**
     * Obtain the user information from GitHub.
     *
     * @return Response
     */
    public function handleProviderCallback()
    {
        $user = Socialite::driver('github')->user();
        // $user->token;
    }




    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            $this->loginUsername() => 'required', 'password' => 'required',
        ]);

        Log::info('trying to login user '.$request->input('email'));

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        $throttles = $this->isUsingThrottlesLoginsTrait();

        if ($throttles && $this->hasTooManyLoginAttempts($request)) {
            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->getCredentials($request);

        if (Auth::guard($this->getGuard())->attempt($credentials, $request->has('remember'))) {
            return $this->handleUserWasAuthenticated($request, $throttles);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        if ($throttles) {
            $this->incrementLoginAttempts($request);
        }

        return $this->sendFailedLoginResponse($request);
    }





    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }



    /**
     * Perform the registration.
     *
     * @param  Request   $request
     * @param  AppMailer $mailer
     * @return \Redirect
     */
    public function register(Request $request, AppMailer $mailer) 
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ]);

        $user = User::create( $request->all() );

        //Assign Role
        $role = Role::whereName('user')->first();
        $user->assignRole($role);
        
        Log::info('trying to send registration email to '.$user->name);

        $mailer->sendEmailConfirmationTo($user);

        flash('Please check you inbox for an email containing a link to confirm your email address.');

        return redirect()->back();
    }




    /**
     * Confirm a user's email address.
     *
     * @param  string $token
     * @return mixed
     */
    public function confirmEmail($token)
    {

        Log::info('trying to confirm user from email');

        // get user with that token
        $user = User::whereToken($token)->first();
        if (!$user) {
            flash('You\'re already confirmed. Please sign in');
        } 
        else {
            // remove token from user record
            $user->confirmEmail();
            flash('You are now confirmed. Please sign in.');
        }

        Log::info('email confirmed for user '.$user->name);

        return redirect('/login');
    }



    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        Log::info('creating new user record for '.$data['name']);

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }



    /**
     * 
     */
    public function getSocialRedirect( $provider )
    {
        $providerKey = \Config::get('services.' . $provider);
        if(empty($providerKey))
            return view('pages.status')
                ->with('error','No such provider');

        return Socialite::driver( $provider )->redirect();

    }


}
