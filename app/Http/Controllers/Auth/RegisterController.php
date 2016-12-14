<?php

namespace App\Http\Controllers\Auth;

use Log;

use App\Models\User;
use App\Models\Role;
use App\Models\Social;

//use Validator;
use Auth;
use Input;
use Socialite;
use Validator;
use Carbon\Carbon;

use App\Mailers\AppMailer;

use App\Events\UserRegistered;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

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
        $this->middleware('guest');
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
            'first_name' => 'required|max:255',
            'last_name'  => 'required|max:255',
            'email'      => 'required|max:255|email|unique:users',
            'password'   => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(Request $request, array $data)
    {
        Log::info($request->ip().' - creating new user record for '.$data['first_name'].' '.$data['last_name'].' ('.$data['email'].')' );

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email'    => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        // fire an event
        //event(new UserRegistered($user));
        
        return $user;
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
        Log::info($request->ip().' - trying to validate user registration');


        $this->validator( $request->all() )->validate();


        // $this->validate($request, [        ]);


        $user = $this->create($request, $request->all()) ;

        //      $this->guard()->login($user);

        // $user = User::create( $request->all() );

        //Assign Role
        $role = Role::whereName('user')->first();
        $user->assignRole($role);
        
        Log::info($request->ip().' - trying to send registration email to '.$user->name);
        $mailer->notifyAdmin( $user, 'new user registration from IP '.$request->ip() );

        $mailer->sendEmailConfirmationTo($user);

        flash('Please check your inbox for an email containing a link to confirm your email address.');

        // return redirect($this->redirectPath());
        // return redirect('/login');
        return view('auth.registered');
    }




    /**
     * Confirm a user's email address.
     *
     * @param  string $token
     * @return mixed
     */
    public function confirmEmail(Request $request, $token)
    {

        Log::info($request->ip().' - trying to confirm user from email');

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

        Log::info($request->ip().' - email confirmed for user ');

        return redirect('/login');
    }




    /**
     //* User clicked on Social Auth button, redirect them to their provider for consent
     */
    public function getSocialRedirect( Request $request, $provider )
    {
        Log::info($request->ip().' - getSocialRedirect - User trying to register using '.$provider);

        $providerKey = \Config::get('services.' . $provider);

        if(empty($providerKey))
            return view('auth.login')
                ->with('error','No such provider');

        return Socialite::driver( $provider )->redirect();

    }

    /**
     * Provider used the "callback URL" and now we process the returned information
     */
    public function getSocialHandle( Request $request, $provider, AppMailer $mailer )
    {
        Log::info($request->ip().' - getSocialHandle - User gave consent to register using '.$provider);

        $social = Socialite::driver( $provider );
        $user   = $social->user();


        $code = Input::get('code');
        if(!$code)
            return redirect('login')
                ->with('status', 'danger')
                ->with('message', 'You did not share your profile data with c-SPOT.');
        if(!$user->email)
        {
            return redirect('login')
                ->with('status', 'danger')
                ->with('message', 'You did not share your email with c-SPOT. You need to visit your '. $provider.' App Settings and remove c-SPOT, than you can come back here and login again. Or you can create a new account.');
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
            if( empty($sameSocialId) )
            {
                // As there is no combination of this social id and provider, 
                // we create a new one
                $newSocialUser = new User;

                // the email address as provided by the service provider
                $newSocialUser->email              = $user->email;
                // perhaps the email contains a name?
                $emailName = explode('@', $user->email)[0];
                if ( strlen($user->name)<3 ) {
                    $user->name = str_replace( '.', ' ', $emailName );
                }

                // the name is hopefully a full name with first- and lastname
                $name = explode(' ', $user->name);
                $newSocialUser->first_name         = $name[0];
                $newSocialUser->last_name          = count($name)>1 ? $name[1] : $name[0];

                // save the new user
                $newSocialUser->save();

                // Add role
                $role = Role::whereName('user')->first();
                $newSocialUser->assignRole($role);

                // create record in the social table
                $socialData = new Social;
                $socialData->social_id = $user->id;
                $socialData->provider= $provider;
                $newSocialUser->social()->save($socialData);

                $socialUser = $newSocialUser;
            }
            else
            {
                //Load this existing social user
                $socialUser = $sameSocialId->user;
            }
        }

        Log::info($request->ip().' - getSocialHandle - trying to do social-sign in');
        $mailer->notifyAdmin( $socialUser, 'User confirmed via '.$provider );

        // $this->auth->login($socialUser, true);
        Auth::login($socialUser, true);

        // write last login field in users table
        Auth::user()->update(['last_login' => Carbon::now()]);

        return redirect()->intended($this->redirectPath());

    }



}
