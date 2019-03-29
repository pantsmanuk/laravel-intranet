<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Adldap\Auth\BindException;
use Adldap\Laravel\Facades\Adldap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');
    }

	/**
	 * We're using username as the database "important value"
	 *
	 * @return \Illuminate\Config\Repository|mixed
	 */
	public function username() {
		return config('ldap_auth.identifiers.database.username_column');
	}

	/**
	 * Validate AD request values
	 *
	 * @param Request $request
	 * @throws \Illuminate\Validation\ValidationException
	 */
	protected function validateLogin(Request $request) {
		$this->validate($request, [
			$this->username() => 'required|string|regex:/^[\w.\-]+$/',
			'password' => 'required|string',
		]);
	}

	protected function attemptLogin(Request $request) {
		$credentials = $request->only($this->username(), 'password');
		$username = $credentials[$this->username()];
		$password = $credentials['password'];

		// Attempt to bind with the request credentials
		try {
			Adldap::auth()->attempt($username, $password, $bindAsUser = true);
		} catch (BindException $e) {
			// Error binding as the user

			$e->getCode();
			$e->getMessage();

			return false;
		}

		// User not already in the database
		if (!$user = \App\User::where($this->username(), $username)->first()) {
			$user = new \App\User();

			$user->username = $username;

			if ($sync_attributes = self::retrieveSyncAttributes($username)) {
				foreach ($sync_attributes as $field => $value) {
					$user->$field = $value !== null ? $value : '';
				}
			} else {
				return false;
			}
		}

		// Don't want accounts with no email address (former staff) logging in
		if (empty($user->email)) {
			return false;
		}

		// Force database password hash to match password request value always
		// This will "trip" for new users as well
		if (empty($user->password) || !Hash::check($password, $user->password)) {
			$user->password = Hash::make($password);
		}

		//dd($sync_attributes); // Why when "tester" logs in does it (a) allow the login, then (b) not get the "name" attribute?

		$this->guard()->login($user, true);
		return true;
	}

	/**
	 * Retrieve AD attributes.
	 *
	 * @param $username
	 * @return array|bool
	 * @throws \ReflectionException
	 */
	protected function retrieveSyncAttributes($username)
	{
		if (!$ldapuser = Adldap::search()->where(env('LDAP_USER_ATTRIBUTE'), '=', $username)->first()) {
			return false;
		}

		$ldapuser_attrs = null;
		$attrs = [];

		foreach (config('ldap_auth.sync_attributes') as $local_attr => $ldap_attr) {
			if ($local_attr == 'username') {
				continue;
			}

			$method = 'get' . $ldap_attr;
			if (method_exists($ldapuser, $method)) {
				$attrs[$local_attr] = $ldapuser->$method();
				continue;
			}

			if ($ldapuser_attrs === null) {
				$ldapuser_attrs = self::accessProtected($ldapuser, 'attributes');
			}

			if (!isset($ldapuser_attrs[$ldap_attr])) {
				$attrs[$local_attr] = null;
				continue;
			}

			if (!is_array($ldapuser_attrs[$ldap_attr])) {
				$attrs[$local_attr] = $ldapuser_attrs[$ldap_attr];
			}

			if (count($ldapuser_attrs[$ldap_attr]) == 0) {
				$attrs[$local_attr] = null;
				continue;
			}

			$attrs[$local_attr] = $ldapuser_attrs[$ldap_attr][0];
		}

		return $attrs;
	}

	/**
	 * Accesses protected objects/properties.
	 *
	 * @param $obj
	 * @param $prop
	 * @return mixed
	 * @throws \ReflectionException
	 */
	protected static function accessProtected ($obj, $prop)
	{
		$reflection = new \ReflectionClass($obj);
		$property = $reflection->getProperty($prop);
		$property->setAccessible(true);
		return $property->getValue($obj);
	}
}
