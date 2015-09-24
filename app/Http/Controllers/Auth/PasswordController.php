<?php namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth;
use Mail;
use Validator;
use Illuminate\Http\Request;

class PasswordController extends Controller {

	/**
	 * user model instance
	 *
	 * @var User
	 */
	protected $user;

	/**
	 * Create a new password controller instance.
	 *
	 * @return void
	 */
	public function __construct(User $user)
	{
		$this->user = $user;

		$this->middleware('guest');
	}

	/**
	 * Get forgot password page.
	 *
	 * @return \Illuminate\View\View
	 */
	public function getForgotPassword() {
		return view('auth.forgot_password');
	}

	/**
	 * Send user password reset link upon request.
	 *
	 * @param Request $request
	 * @return \Illuminate\View\View
	 */
	public function postForgotPassword(Request $request) {
		$this->validate($request, [
			'email' => 'required|email|max:255'
		]);

		$this->user = User::where('email', $request->email)->first();
		// Return error if user email not found
		if(is_null($this->user)) {
			if($request->ajax()) {
				return response()->json([
					'error' => true,
					'message' => $this->getEmailNotFoundError($request->email),
				]);
			} else {
				return redirect()->back()
					->withInput($request->input())
					->withErrors([$this->getEmailNotFoundError($request->email), 'error']);
			}
		}

		// Create password reset token
		$this->user->password_reset_token = str_random(32);
		$this->user->save();

		// Set email with password reset link
		Mail::send('emails.reset_password', [
			'username' => $this->user->username,
			'uid' => $this->user->id,
			'password_reset_token' => $this->user->password_reset_token,
		], function($message)
		{
			$message->to($this->user->email, $this->user->username)->subject('Password reset request');
		});

		if($request->ajax()) {
			return response()->json([
				'error' => false,
				'flash_message' => [
					'error' => false,
					'message' => $this->getResetLinkSentMessage()
				],
				'close_window' => true
			]);
		} else {
			session()->flash('message', $this->getResetLinkSentMessage());
			return redirect($this->redirectPath());
		}
		return view('auth.forgot_password');
	}

	public function getResetPassword(Request $request) {
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

		if($user->password_reset_token != $request->token) {
			abort(404);
		}

		return view('auth.reset_password')->with(array(
			'password_reset_token' => $user->password_reset_token,
			'email' => $user->email
		));
	}

	public function postResetPassword(Request $request) {
		$validator = Validator::make($request->all(), [
			'email' => 'required',
			'token' => 'required',
			'password' => 'required|confirmed|min:6'
		]);
		if($validator->fails()) {
			if( !array_key_exists('password', $validator->failed()) ) {
				// the validation failure is caused by something else missing
				return redirect($this->redirectPath());
			} else {
				// the validation failure is caused by user input: password
				return redirect()->back()
					->withErrors([$validator->messages()->get('password')[0], 'password']);
			}
		}

		$user = User::where('email', $request->email)->first();
		if(is_null($user)) {
			abort(404);
		}
		if($user->password_reset_token != $request->token) {
			abort(404);
		}
		$user->password_reset_token = null;
		$user->password = bcrypt($request->password);
		$user->save();
		Auth::login($user);

		session()->flash('message', $this->getResetSuccessMessage());
		return redirect($this->redirectPath());
	}

	protected function getEmailNotFoundError($email) {
		return 'No account exists for '.$email.'.';
	}

	protected function getResetLinkSentMessage() {
		return 'A password reset email has been sent to you, '
		.'please use the link provided in the email to reset your password.';
	}

	protected function getResetSuccessMessage() {
		return 'Your password is successfully reset.';
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

		return property_exists($this, 'redirectTo') ? $this->redirectTo : '/';
	}
}
