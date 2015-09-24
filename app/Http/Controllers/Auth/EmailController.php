<?php namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;

use Auth;
use Mail;
use Validator;
use Illuminate\Http\Request;

class EmailController extends Controller {

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
	 * Create a new email controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard  $auth
	 * @return void
	 */
	public function __construct(Guard $auth, User $user)
	{
		$this->user = $user;
		$this->auth = $auth;

		$this->middleware('auth', ['only' => 'resend' ]);
	}

	/**
	 * Confirm user email and activate account
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function verify(Request $request)
	{
		if(Auth::user()) {
			if(Auth::user()->status != 'unactivated') {
				return redirect($this->redirectPath());
			}
		}
		$validator = Validator::make($request->all(), [
			'uid' => 'required',
			'token' => 'required',
		]);

		if($validator->fails()) {
			abort(404);
		}

		$user = User::find($request->uid);
		if(is_null($user)) {
			abort(404);
		}
		if($user->email_confirm_token != $request->token) {
			abort(404);
		}
		$user->status = 'activated';
		$user->email_confirm_token = null;
		$user->save();
		// Log user in if not
		if ($this->auth->guest()) {
			$this->auth->login($user);
		}

		if($request->ajax()) {
			return response()->json([
				'error' => false,
				'message' => $this->getEmailVerifiedMessage()
			]);
		} else {
			session()->flash('message', $this->getEmailVerifiedMessage());
			return redirect($this->redirectPath());
		}
	}

	/**
	 * Resend email confirmation
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function resend(Request $request) {
		$this->user = $this->auth->user();
		if($this->user->status != 'unactivated') {
			return redirect($this->redirectPath());
		}
		$this->user->email_confirm_token = str_random(32);
		$this->user->save();

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
				'message' => $this->getVerifyEmailSentMessage()
			]);
		} else {
			session()->flash('message', $this->getVerifyEmailSentMessage());
			return redirect($this->redirectPath());
		}
	}

	/**
	 * Get the email verified message.
	 *
	 * @return string
	 */
	protected function getEmailVerifiedMessage()
	{
		return 'Thank you for signing up at ToGoList, your account is activated.';
	}

	/**
	 * Get the verify email sent message.
	 *
	 * @return string
	 */
	protected function getVerifyEmailSentMessage()
	{
		return 'We have sent you a confirmation email, '
		.'click the link provided in the email to activate your account.';
	}

	/**
	 * Get the redirect path.
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
