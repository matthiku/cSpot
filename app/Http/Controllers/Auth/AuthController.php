<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Http\Controllers\Auth;

use Log;

use App\Models\User;
use App\Models\Role;
use App\Models\Social;

use Validator;
use Auth;
use Input;
use Socialite;

use App\Mailers\AppMailer;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
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


    protected $auth;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;

        $this->middleware('guest', ['except' => 'logout']);
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
     * Perform the registration.
     *
     * @param  Request   $request
     * @param  AppMailer $mailer
     * @return \Redirect
     */
    public function register(Request $request, AppMailer $mailer) 
    {
        Log::info('trying to validate user registration');

        $this->validate($request, [
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required|email|unique:users',
            'password'   => 'required',
        ]);

        $user = User::create( $request->all() );

        //Assign Role
        $role = Role::whereName('user')->first();
        $user->assignRole($role);
        
        Log::info('trying to send registration email to '.$user->name);

        $mailer->sendEmailConfirmationTo($user);

        flash('Please check your inbox for an email containing a link to confirm your email address.');

        return redirect('/');
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
     * User clicked on Social Auth button, redirect them to their provider for consent
     */
    public function getSocialRedirect( $provider )
    {
        Log::info('getSocialRedirect - User trying to register using '.$provider);

        $providerKey = \Config::get('services.' . $provider);

        if(empty($providerKey))
            return view('auth.login')
                ->with('error','No such provider');

        return Socialite::driver( $provider )->redirect();

    }

    /**
     * Provider used the "callback URL" and now we process the returned information
     */
    public function getSocialHandle( $provider )
    {
        Log::info('getSocialHandle - User gave consent to register using '.$provider);

        $user = Socialite::driver( $provider )->user();

        $code = Input::get('code');
        if(!$code)
            return redirect()->route('auth.login')
                ->with('status', 'danger')
                ->with('message', 'You did not share your profile data with our socail app.');
        if(!$user->email)
        {
            return redirect()->route('auth.login')
                ->with('status', 'danger')
                ->with('message', 'You did not share your email with our social app. You need to visit App Settings and remove our app, than you can come back here and login again. Or you can create new account.');
        }
        $socialUser = null;
        //Check is this email present
        $userCheck = User::where('email', '=', $user->email)->first();
        if(!empty($userCheck))
        {
            $socialUser = $userCheck;
        }
        else
        {
            $sameSocialId = Social::where('social_id', '=', $user->id)->where('provider', '=', $provider )->first();
            if(empty($sameSocialId))
            {
                //There is no combination of this social id and provider, so create new one
                $newSocialUser = new User;
                $newSocialUser->email              = $user->email;
                $name = explode(' ', $user->name);
                $newSocialUser->first_name         = $name[0];
                $newSocialUser->last_name          = $name[1];
                $newSocialUser->save();
                $socialData = new Social;
                $socialData->social_id = $user->id;
                $socialData->provider= $provider;
                $newSocialUser->social()->save($socialData);
                // Add role
                $role = Role::whereName('user')->first();
                $newSocialUser->assignRole($role);
                $socialUser = $newSocialUser;
            }
            else
            {
                //Load this existing social user
                $socialUser = $sameSocialId->user;
            }
        }

        Log::info('getSocialHandle - trying to do social-sign in');

        $this->auth->login($socialUser, true);

        return redirect()->intended($this->redirectPath());

    }



}
