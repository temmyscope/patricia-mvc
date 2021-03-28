<?php
namespace App;

use \Firebase\JWT\JWT;

class Auth{

    protected static $table = 'users';
    
	public static function generateUserToken(object $user, bool $long = false): string
	{
		$iat = time(); //time of token issued at
		$nbf = $iat + 5; //not before in seconds
		$exp = ( $long === true ) ? $iat + 2592000 : $iat + 9000; //expiry time of token in seconds
		$token = [
			"iat" => $iat, "nbf" => $nbf, "exp" => $exp,
			"user" => $user->id, "email" => $user->email
		];
		return self::encryptToken($token);
	}

	public static function encryptToken(Array $data): string
	{
		return JWT::encode( $data, env("JWT_SECRET"), env('JWT_ALG') );
	}

	public static function getValuesFromToken($token)
	{
		$app = app();
		try {
			$data = JWT::decode($token, env("JWT_SECRET"), [ env('JWT_ALG') ]);
		} catch (\Exception $e) {
			$data = [];
		}
		return $data;
	}

	public static function isValid($token): bool
	{
		$decoded = self::getValuesFromToken( $token );
		if ( !empty ( $decoded ) ){
			return true;
		}
		return false;
	}

	public static function findfirst(array $clause): mixed
	{
		$data = static::loadDB();
		if (!empty($data) && !empty($clause)){
			foreach($clause as $entry => $entryValue){
				foreach($data as $key => $value){
					if (
						isset($data[$key][$entry]) && 
						$data[$key][$entry] === $entryValue
					) {
						return (object)$data[$key];
					}
				}
			}
		}
		return [];
	}

	public static function exists(array $clause): bool
	{
		$data = static::loadUserDB();
		if (!empty($data) && !empty($clause)){
			foreach($clause as $entry => $entryValue){
				foreach($data as $key => $value){
					if (isset($data[$key][$entry]) && $data[$key][$entry] === $entryValue) {
						return true;
					}
				}
			}
		}
		return false;
	}

	public static function insert(array $user): mixed
	{
		$data = static::loadUserDB();
		$lastInsertId = ($data) ? count($data) : 0;
		
		$user['id'] = $lastInsertId+1;
		$data[] = $user;
		file_put_contents(__DIR__.'/../storage/users.json', json_encode($data));
		
		return $data[$lastInsertId];
	}

	public static function LogUser($user, $userToken)
	{
		$sessions = static::loadSessionDB();
		$sessions[$user] = $userToken;

		file_put_contents(__DIR__.'/../storage/session.json', json_encode($data));
		return true;
	}

	public static function LogOut($user)
	{
		$sessions = static::loadSessionDB();
		unset($sessions[$user]);

		file_put_contents(__DIR__.'/../storage/session.json', json_encode($sessions));
		return true;
	}

	protected static function loadUserDB($array = true): array
	{
		return json_decode(file_get_contents(__DIR__.'/../storage/users.json'), $array) ?? [];
	}
	
	protected static function loadSessionDB($array = true): array
	{
		return json_decode(file_get_contents(__DIR__.'/../storage/session.json'), $array) ?? [];
	}
}