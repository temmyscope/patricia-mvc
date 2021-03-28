<?php
namespace App\Providers;

use Seven\Vars\{Strings, Validation};

class Application{

	public function __construct(
        protected string $baseDirectory = ""
    )
    {
        if (!env('APP_DEBUG')){
            $this->setLogger();
        }
        $this->string = new Strings(env('APP_ALG'), env('APP_SALT'), env('APP_IV'));
    }

	private function setLogger()
    {
        ini_set("log_errors", true);
        ini_set("error_log", $this->baseDirectory . '/../../error.log');
    }

    public function request()
    {
        $request = new class(){
            public function __construct(){
                $this->data = $_POST ?? json_decode(file_get_contents('php://input'), true);
            }
            public function all()
            {
                return $this->data;
            }
            public function input(string $var, $value = null)
            {
                return $this->data[$var] ?? $value;
            }
            public function header(string $var)
            {
                return $_SERVER[$var] ?? NULL;
            }
            public function bearerToken()
            {
                $auth = $_SERVER["Authorization"] ?? $_SERVER["HTTP_AUTHORIZATION"] ?? "";
                $auth = explode(' ', $auth);
                return $auth[1] ?? $auth[0];
            }
            public function get(string $var)
            {
                return $_GET[$var] ?? NULL;
            }
            public function has(string $var)
            {
                return isset($this->data[$var]) ? true : false;
            }
            public function validate(array $rules)
            {
                return Validation::init($this->data)->rules($rules);
            }
            public function htmlSanitize(string $input)
            {
                return  htmlentities($input, ENT_QUOTES, 'UTF-8');
            }
            public function userAgent(string $userAgent = "")
            {
               return preg_replace($regx = '/\/[a-zA-Z0-9.]*/', '', $userAgent);
            }
        };
        return $request;
    }

    public function response()
    {
        return new class (){
            public function __construct(){}
            public function send($response, $statusCode = 200, $headers = [])
            {
                foreach ($headers as $key => $value) {
                    header("{$key}: {$value}");       
                }
                header('Content-Type: application/json; charset=utf-8');
                http_response_code($statusCode);
                echo json_encode($response, JSON_PRETTY_PRINT);
            }
        };
    }
	
    public function decrypt(string $str): string
    {
        return $this->string->decrypt($str);
    }

    public function encrypt(string $str): string
    {
        return $this->string->encrypt($str);
    }

    public function config()
    {
		$baseDirectory = $this->baseDirectory;
        return new class ($baseDirectory){
            public function __construct($baseDirectory)
            {
                $this->config = include $baseDirectory . '/config/app.php';
            }
            public function get(string $var)
            {
                return $this->config[$var] ?? $_ENV[$var] ?? null;
            }
            public function all()
            {
                return $this->config;
            }
        };
    }

    public function dateTime(string $str = 'now')
    {
        return $this->string->timeFromString($str, $this->config()->get('APP_TIMEZONE'));
    }

}