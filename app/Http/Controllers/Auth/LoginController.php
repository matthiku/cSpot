<?php

namespace App\Http\Controllers\Auth;

use Log;

use App\Models\User;
use App\Models\Role;
use App\Models\Social;
use App\Models\Login;

//use Validator;
use Auth;
use Socialite;
use Carbon\Carbon;

use App\Mailers\AppMailer;

use App\Events\UserLogin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Lang;


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



    /**
     * Override the default function to add 'verified' (i.e. make sure the email address was verfied)
     */
    public function credentials(Request $request)
    {
        return [
            'email' => $request->email,
            'password' => $request->password,
            'verified' => 1,
        ];
    }

    /**
     * Get the failed login response instance.
     * (Override the default function from \Illuminate\Foundation\Auth\AuthenticatesUsers)
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        Log::info('Failed login attempt from '.$request->ip().' with email '.$request->input('email').' and PW '
            . ($request->has('password') ? $request->input('password') : '(missing'));

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                $this->username() => Lang::get('auth.failed'),
            ]);
    }


    public function login(Request $request)
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
        // notify admin
        // $mailer->notifyAdmin( $user, $user->fullName .' logged in on IP '.$request->ip() );

        //
        broadcast(new UserLogin($request, $user));
    }


    /**
     * Redirect the user to the selected Authentication Provider's authentication page
     *
     * @return Response
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from selected Authentication Provider
     *
     * @return Response
     */
    public function handleProviderCallback($provider)
    {
        $user = Socialite::driver($provider)->user();
    }

}
