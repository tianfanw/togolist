<?php namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;

use Validator;
use Mail;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\SignupRequest;
use App\Http\Requests\Auth\LoginRequest;

class AuthController extends Controller {

	/**
	 * user model instance
	 *
	 * @var User
	 */
	protected $user;

	/**
	 * The Guard implementation.
	 *
	 * @var \Illuminate\Contracts\Auth\Guard
	 */
	protected $auth;

	/**
	 * Create a new authentication controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard  $auth
	 * @param  \App\User  $user
	 */
	public function __construct(Guard $auth, User $user)
	{
		$this->user = $user;
		$this->auth = $auth;

		$this->middleware('guest', ['except' => ['getVerify', 'getLogout'] ]);
	}

	/**
	 * Show the application registration form.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function getSignup()
	{
		return view('auth.signup');
	}

	/**
	 * Handle a registration request for the application.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function postSignup(SignupRequest $request)
	{
		$this->user->email = $request->email;
		$this->user->password = bcrypt($request->password);
		$this->user->status = 'unactivated';
		$this->user->type = 'general';
		$this->user->first_name = $request->first_name;
		$this->user->last_name = $request->last_name;
		$this->user->bio = $request->bio;
		$this->user->email_confirm_token = str_random(32);
		$this->user->save();
		$this->auth->login($this->user);

		// Send out verification email
		Mail::send('emails.verify_email', [
			'username' => $this->user->username,
			'uid' => $this->user->id,
			'email_confirm_token' => $this->user->email_confirm_token,
		], function($message)
		{
			$message->to($this->user->email, $this->user->username)->subject('Activate your ToGoList account');
		});

		if($request->ajax()) {
			return response()->json([
				'error' => false,
				'html' => [
					'navbar-menu' => view('partials.navbar-menu')->render(),
				],
				'hidden_html' => [
					'navbar-popup' => view('partials.navbar-popup')->render()
				],
				'flash_message' => [
					'error' => false,
					'message' => $this->getSignupSuccessMessage(),
					'is_important' => true,
				],
				'close_window' => true,
			]);
		} else {
			session()->flash('message', $this->getSignupSuccessMessage());
			session()->flash('is_important', true);
			return redirect($this->redirectPath());
		}
	}

	/**
	 * Show the application login form.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function getLogin()
	{
		return view('auth.login');
	}

	/**
	 * Handle a login request to the application.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function postLogin(LoginRequest $request)
	{
		$credentials = $request->only('email', 'password');

		if ($this->auth->attempt($credentials, $request->has('remember')))
		{
			$user = $this->auth->user();
			if($request->ajax()) {
				return response()->json([
					'error' => false,
					'html' => [
						'navbar-menu' => view('partials.navbar-menu')->render()
					],
					'hidden_html' => [
						'navbar-popup' => view('partials.navbar-popup')->render()
					],
					'close_window' => true
				]);
			} else {
				return redirect()->intended($this->redirectPath());
			}
		}

		$error_message = 'Invalid email or password. Please try again.';
		if($request->ajax()) {
			return response()->json([
				'error' => true,
				'message'=> $error_message
			]);
		} else {
			return redirect('/login')
				->withInput($request->only('email', 'remember'))
				->withErrors([
					'email' => $error_message
				]);
		}
	}

	/**
	 * Log the user out of the application.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function getLogout()
	{
		$this->auth->logout();

		return redirect('/');
	}

	/**
	 * Get the signup success message.
	 *
	 * @return string
	 */
	protected function getSignupSuccessMessage()
	{
		return 'Welcome to ToGoList! We have sent you a confirmation email, '
			.'click the link provided in the email to activate your account.';
	}

	/**
	 * Get the post register / login redirect path.
	 *
	 * @return string
	 */
	public function redirectPath()
	{
		if (property_exists($this, 'redirectPath'))
		{
			return $this->redirectPath;
		}

		return property_exists($this, 'redirectTo') ? $this->redirectTo : '/mylist';
	}
}
