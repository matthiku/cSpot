<?php

namespace App\Http\Controllers\Auth;

use Log;

use App\Models\User;
use App\Models\Role;
use App\Models\Social;

//use Validator;
use Auth;
use Socialite;
use Carbon\Carbon;

use App\Mailers\AppMailer;

use App\Events\UserLogin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';



    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }




    public function login(Request $request, AppMailer $mailer)
    {
        $this->validateLogin($request);

        Log::info($request->ip().' - trying to login user '.$request->input('email'));


        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->credentials($request);

        //if ($this->guard()->attempt($credentials, $request->has('remember'))) {
        //    return $this->sendLoginResponse($request);
        //}

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);


        if (Auth::guard()->attempt($credentials, $request->has('remember'))) {
            $user = Auth::user();
            // notify admin 
            $mailer->notifyAdmin( $user, $user->fullName .' logged in on IP '.$request->ip() );
            // write last login field in users table
            $user->last_login = Carbon::now();
            $user->save();
            
            //return $this->handleUserWasAuthenticated($request);  // old 5.2
            return $this->sendLoginResponse($request);      // new 5.3
        }

        return $this->sendFailedLoginResponse($request);
    }






    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, User $user)
    {
        //
        broadcast(new UserLogin($user));
    }


    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return Response
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return Response
     */
    public function handleProviderCallback($provider)
    {
        $user = Socialite::driver($provider)->user();

        // $user->token;
    }

}
