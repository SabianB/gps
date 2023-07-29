<?php
namespace endpoints\src;

use api\DataTypes;
use api\Request;
use api\Response;
use database\Connection;
use database\SQLDatabase;
use DataBaseTypes;
use endpoints\Crud;
use endpoints\EndPoint;
use Exception;
use \Firebase\JWT\JWT;
use Roles;
use utils\Functions;
use api\JWTConfig;

class Authentication extends EndPoint{
    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response, [DataBaseTypes::SQLDatabase]);
        $this->usuariosTable = 'usuarios';
    }
    private function getUserToken(array $user_data): string
    {
        $iat = time();
        $exp = $iat * 60 * 60;
        $payload = array(
            "iss" => "GPS",
            "sub" => "GPS",
            "aud" => "Login",
            "iat" => $iat,
            "exp" => $exp,
            "user_data" => $user_data
        );
        return JWT::encode($payload, JWTConfig::jwt_key);
    }

    public function decodeToken()
    {
        $request = $this->request;
        $response = $this->response;
        $request->checkInput(["token" => DataTypes::string]);
        $token = $request->getValue("token");
        try {
            $decoded = JWT::decode($token, JWTConfig::jwt_key, array('HS256'));
            $response->addValue("data", $decoded);
        } catch (Exception $exception) {
            $response->printError("Token invalido", 400);
            return;
        }
        $response->addValue('data', $decoded);
        $response->printResponse();
    }

    public function login()
    {

        $this->request->checkInput([
            'correo' => DataTypes::string,
            'clave' => DataTypes::string
        ], true);
        $email = $this->request->getValue('correo');
        $password = hash('SHA512', $this->request->getValue('clave'));
        $userData = $this->getSQLDatabase()->dbRead($this->usuariosTable, [
            'id',
            'nombres',
            'apellidos',
            'correo'
        ], "WHERE correo='$email' AND clave='$password'");
        if (count($userData) == 0) {
            $this->response->printError('El email o la contraseña son erróneos', 404);
        }
        $userData = $userData[0];
        $userData['id'] = intval($userData['id']);

        $token = $this->getUserToken($userData);
        header("Set-Cookie: token=$token; HttpOnly");
        $this->response->addValue('user', $userData)->addValue('token', $token)
            ->printResponse();
    }

    public function register(){
        $this->request->checkInput([
            'correo' => DataTypes::string,
            'clave' => DataTypes::string,
            'clave2' => DataTypes::string,
            'nombres' => DataTypes::string,
            'apellidos' => DataTypes::string,
        ], true);

        $email = $this->request->getValue('correo');
        $correos = $this->getSQLDatabase()->dbRead($this->usuariosTable, [
            'correo'
        ], "WHERE correo='$email'");
        if (count($correos) > 0) {
            $this->response->printError('Este email ya se encuentra en uso.', 404);
        }
        $c1 = $this->request->getValue('clave');
        $c2 = $this->request->getValue('clave2');
        if($c1 !== $c2){
            $this->response->printError('Las contraseñas no coinciden.', 404);
        }
        $clave = hash('SHA512', $this->request->getValue('clave'));

        $this->getSQLDatabase()->dbCreate('usuarios', [
            'nombres' => $this->request->getValue('nombres'),
            'apellidos' => $this->request->getValue('apellidos'),
            'correo' => $email,
            'clave' => $clave,
        ]);
        $this->response->addValue('message', 'Usuario registrado correctamente')
            ->printResponse();
    }


}