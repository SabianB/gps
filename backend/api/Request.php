<?php


namespace api;

use api\JWTConfig;
use Exception;
use Firebase\JWT\JWT;
use Roles;

class Request
{
    private array $request = [];
    private Response $response;

    public function __construct($request)
    {
        $this->request = $request;
        $this->response = new Response();
    }


    /**
     * @param $key
     * @param false $escape
     * @return mixed
     */
    public function getValue($key, bool $escape = true)
    {
        if (!isset($this->request[$key])) {
            /*$this->response->printError("El campo '$key' no existe en la solicitud");*/
            return null;
        }
        if ($escape) {
            return (gettype($this->request[$key]) === 'string') ? htmlspecialchars($this->request[$key]) : ($this->request[$key]);
        }
        return $this->request[$key];
    }

    public function removeValue($key)
    {
        unset($this->request[$key]);
    }

    public function allow_cors()
    {
        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            exit(0);
        }
    }

    /**
     * This method will check if the user cookie 'token' is valid, this will represent
     * the user as a logged one.
     * If this method determinate that the user is not authenticated or the recieve rol is not the same
     * that the specified in the token, the code will die with a json response for the client (FrontEnd)
     * @param array $roles ((Roles que pueden verificar al usuario)Even when this parameter is an string, just send values availables as fields in /api/Roles.php class) if you send an 'NO_ROL' field front the before mentioned file this method wont let the sequence die when having a valid session token not caring about the rol inside the token
     */
    public function hasRols(array $roles)
    {
        if (!isset($_COOKIE['token'])) {
            $this->response->printError('Usuario no autenticado', 401);
        }
        $token = $_COOKIE['token'];
        try {
            $payload = JWT::decode($token, JWTConfig::jwt_key, array('HS256'));
            $user_rol = $payload->user_data->rol;
            $has = false;
            if (in_array(\Roles::NO_ROL, $roles)) {
                $has = true;
            } else {
                foreach ($roles as $rol) {
                    if ($user_rol === $rol) {
                        $has = true;
                        break;
                    }
                }
            }

            if (!$has) {
                $this->response->printError('No tienes permisos para realizar esta acción', 401);
                return false;
            }
            return true;
        } catch (Exception $e) {
            $this->response->printError('Autenticación no válida: ' . $e->getMessage(), 401);
        }
        return false;
    }

    public function checkInput($array, $die_on_empty_field = false)
    {
        $message = '';
        $error = false;
        foreach ($array as $key => $value) {
            if (!isset($this->request[$key])) {
                $message = "The field '$key' has not been found in the request";
                $error = true;
                break;
            } else {
                $datatype = gettype($this->request[$key]);
                if ($value === DataTypes::dynamic) {
                    continue;
                }
                if ($value == DataTypes::number && ($datatype == DataTypes::integer || $datatype == DataTypes::double)) {
                    $datatype = DataTypes::number;
                }
                if ($datatype != $value) {
                    $message = "The data type of the field '$key' in the request must be '$value' but the received was '$datatype'";
                    $error = true;
                    break;
                }
                if ($datatype == DataTypes::array && count($this->request[$key]) == 0 && $die_on_empty_field) {
                    $message = "The field '$key' in the request is empty the request can not be processed";
                    $error = true;
                    break;
                }
                if ($datatype == DataTypes::string && $die_on_empty_field &&/* !in_array($key, $exluce_empty) &&*/ $this->request[$key] == '') {
                    $message = "The field '$key' in the request has and empty string";
                    $error = true;
                    break;
                }
            }
        }
        if ($error) {
            $this->response->printError($message, 400);
        }
    }

    public function checkInputArrayValuesTypes(string $key, array $datatypes)
    {
        if (isset($this->request[$key]) && gettype($this->request[$key]) === DataTypes::array && count($this->request[$key]) !== 0) {
            $str_datatypes = implode(",", $datatypes);
            foreach ($this->request[$key] as $value) {
                $type = gettype($value);
                if (!in_array($type, $datatypes)) {
                    $this->response->printError("All values of array '$key' must be one of the next: [$str_datatypes] but a value with '$type' was found", 400);
                }
            }
        } else {
            $this->response->printError("The key '$key' is not an array or the array is empty", 400);
        }
    }

    public function getPayload(): array
    {
        if (!isset($_COOKIE['token'])) {
            $this->response->printError('Usuario no autenticado', 401);
        }
        $token = $_COOKIE['token'];
        try {
            $payload = JWT::decode($token, JWTConfig::jwt_key, array('HS256'));
            return (array)$payload->user_data;
        } catch (\Exception $e) {
            $this->response->printError('Autenticación no válida: ' . $e->getMessage(), 401);
            return [];
        }
    }

    public function getRequestAsRaw(): array
    {
        return $this->request;
    }


}




