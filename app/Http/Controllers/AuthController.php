<?php 
namespace App\Http\Controllers;

use App\Auth;
use Seven\Vars\Strings;

class AuthController extends Controller{

	public function login($request, $response){
		if (empty($request->all())) {
			return $response->send("Required: [email, password]", 400);
		}
		$request->validate([
			'email' => ['required' => true, 'email' => true ],
			'password' => [ 'required' => true, 'min' => 8 ]
		])->then(function() use ($request, $response){
			$user = Auth::findfirst([ 'email' => $request->input('email') ]);
			if (
				!empty($user) && 
				Strings::verifyHash($request->input('password').env('APP_SALT'), $user->password)
			) {
				$remember = ($request->input('remember_me') == 'true') ? true : false;
				$token = Auth::generateUserToken($user, $remember);
				Auth::LogUser($user->id, $token);
				return $response->send([ 
					'data' => $token, 'success' => true, 'message' => 'Authorised.'
				], 200);
			}
			return $response->send("Invalid login credentials", 400);
		})->catch(function($errors) use($response){
			return $response->send($errors, 400);
		});
	}

	public function register($request, $response)
	{
		if (empty($request->all())) {
			return $response->send("Required: [email, password, name]", 400);
		}
		$request->validate([
			'email' => ['required' => true, 'email' => true ],
			'password' => [ 'required' => true, 'min' => 8 ],
			'name' => [ 'required' => true, 'min' => 2 ],
		])->then(function() use ($request, $response){
			if ( Auth::exists(['email' => $request->input('email') ])) {
				return $response->send("An Account with this email already exists.", 200);
			}
			Auth::insert([
				'email' => $request->input('email'),
				'name' => $request->input('name'),
				'password' => Strings::hash($request->input('password').env('APP_SALT') ),
				'created_at' => $this->dateTime('now')
			]);
			//probably send a registration mail
			return $response->send("Your account has been created.", 201);
		})->catch(function($errors) use($response){
			return $response->send($errors[0], 200);
		});
	}

}