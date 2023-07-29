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
use utils\ServerData;
use api\JWTConfig;
use function MongoDB\BSON\toJSON;

class Maps extends EndPoint{
    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response, [DataBaseTypes::SQLDatabase]);
        //$this->usuariosTable = 'usuarios';
    }


    public function GetLatLon(){


        //Codigo para recuperar la latitud y longitud del gps arduino
        $v = mt_rand( 0, 10 ) / 10;
        $lat = -1.0655 + $v;
        $v = mt_rand( 0, 10 ) / 10;
        $lon =  -78.3996 + $v;

        $this->getSQLDatabase()->dbCreate('coordenadas', [
            'latitud' => $lat,
            'longitud' => $lon,
            'id_recorrido' => 1
        ]);

        $data = ['latitud' => $lat,
                'longitud' => $lon
            ];

        $this->response->addValue('data', $data)->printResponse();
    }

    //FUNCION QUE CONSUMIRA EL ARDUINO PARA GUARDAR LOS REGISTROS
    public function RegistrarLugar(){
        $this->request->checkInput([
            'id_usuario' => DataTypes::integer,
            'latitud' => DataTypes::string,
            'longitud' => DataTypes::string
        ], true);
        $id_usuario = $this->request->getValue('id_usuario');
        $latitud = $this->request->getValue('latitud');
        $longitud = $this->request->getValue('longitud');
        $date = ServerData::getDate(true);
        $apiosm = "https://nominatim.openstreetmap.org/reverse?format=json&lat=$latitud&lon=$longitud"; //API DE OPENSTREETMAPS PARA DATOS DE DIRECCION
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiosm);
        $headers = array(
            'authority: nominatim.openstreetmap.org',
            'method: GET',
            'scheme: https',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'Accept-Encoding: gzip, deflate, br',
            'Accept-Language: es,es-ES;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6',
            'Cache-Control: max-age=0',
            'Sec-Ch-Ua: "Not/A)Brand";v="99", "Microsoft Edge";v="115", "Chromium";v="115"',
            'Sec-Ch-Ua-Mobile: ?0',
            'Sec-Ch-Ua-Platform: "Windows"',
            'Sec-Fetch-Dest: document',
            'Sec-Fetch-Mode: navigate',
            'Sec-Fetch-Site: none',
            'Sec-Fetch-User: ?1',
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36 Edg/115.0.1901.188'
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $jsonResponse = curl_exec($ch);
        curl_close($ch);
        $dataArray = json_decode($jsonResponse, true);
        $ciudad = $dataArray['address']['county'];
        $this->getSQLDatabase()->dbCreate('registros', [
            'latitud' => $latitud,
            'longitud' => $longitud,
            'id_usuario' => $id_usuario,
            'fecha' => $date,
            'ciudad' => $ciudad
        ]);
        $this->response->addValue('message', "Coordenadas registradas exitosamente")->printResponse();

    }

    public function ObtenerCoordenadas(){
        $payload = $this->request->getPayload();
        $usuario_id = $payload['id'];

        $data = $this->getSQLDatabase()->dbRead('registros', [
            'id',
            'latitud',
            'longitud',
            'fecha'
        ], "WHERE id_usuario = $usuario_id ORDER BY id DESC LIMIT 1");
        if(count($data) === 0){
            $this->response->printError("Ultimo id no coincide con el cliente actual o no existen registros");
        }else{
            $this->response->addValue('data', $data[0])->printResponse();
        }

        $this->response->addValue('data', $payload)->printResponse();
    }

    public function CoordenadasPorFecha(){
        $this->request->checkInput([
            'fecha_inicio' => DataTypes::string,
            'fecha_fin' => DataTypes::string
            ], true);
        $payload = $this->request->getPayload();
        $usuario_id = $payload['id'];
        $fecha_inicio = $this->request->getValue('fecha_inicio');
        $fecha_fin = $this->request->getValue('fecha_fin');
        $data = $this->getSQLDatabase()->dbRead('registros', [
            'latitud',
            'longitud',
            'fecha'
        ], "WHERE id_usuario = $usuario_id AND fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'" );
        if(count($data) === 0){
            $this->response->printError("No se han encontrado registros en las fechas indicadas");
        }else{
            $this->response->addValue('data', $data)->printResponse();
        }
    }

    public function Estacionamiento(){
        $this->request->checkInput([
            'fecha_inicio' => DataTypes::string,
            'fecha_fin' => DataTypes::string,
            'minutos' => DataTypes::integer
        ], true);
        $minutos = $this->request->getValue('minutos');
        $minutos = $minutos * 60;
        $payload = $this->request->getPayload();
        $usuario_id = $payload['id'];
        $fecha_inicio = $this->request->getValue('fecha_inicio');
        $fecha_fin = $this->request->getValue('fecha_fin');
        $data = $this->getSQLDatabase()->dbRead('registros', [
            'latitud',
            'longitud',
            'fecha'
        ], "WHERE id_usuario = $usuario_id AND fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'" );
        if(count($data) < 2){
            $this->response->printError("No se han encontrado estacionamientos en las fechas indicadas");
        }
        //$idcoordenada = $data[0]['id'];
        $estacionamientos = [];
        $itemestacionamiento = [];
        for ($i = 1; $i< count($data); $i++){
            if(($data[$i]['latitud'] == $data[$i-1]['latitud']) && ($data[$i]['longitud'] == $data[$i-1]['longitud'])){
                array_push($itemestacionamiento, $data[$i-1]);
                if(count($data) == $i+1){
                    array_push($itemestacionamiento, $data[$i]);
                    $fecha1 = new \DateTime($itemestacionamiento[0]['fecha']);
                    $fecha2 = new \DateTime($itemestacionamiento[count($itemestacionamiento)-1]['fecha']);
                    $diferencia = $fecha1->diff($fecha2);
                    $totalSegundos = $diferencia->s + ($diferencia->i * 60) + ($diferencia->h * 3600) + ($diferencia->days * 86400);
                    if($totalSegundos > $minutos){
                        $itemestacionamiento[0]["fecha_fin"] = $itemestacionamiento[count($itemestacionamiento)-1]['fecha'];
                        array_push($estacionamientos, $itemestacionamiento[0]);
                    }
                    $itemestacionamiento = [];
                }
            }
            else{
                if(count($itemestacionamiento) > 0){
                    array_push($itemestacionamiento, $data[$i-1]);
                    $fecha1 = new \DateTime($itemestacionamiento[0]['fecha']);
                    $fecha2 = new \DateTime($itemestacionamiento[count($itemestacionamiento)-1]['fecha']);
                    $diferencia = $fecha1->diff($fecha2);
                    $totalSegundos = $diferencia->s + ($diferencia->i * 60) + ($diferencia->h * 3600) + ($diferencia->days * 86400);
                    if($totalSegundos > $minutos){
                        $itemestacionamiento[0]["fecha_fin"] = $itemestacionamiento[count($itemestacionamiento)-1]['fecha'];
                        array_push($estacionamientos, $itemestacionamiento[0]);
                    }
                    $itemestacionamiento = [];
                }
            }
        }
        if(count($estacionamientos) === 0){
            $this->response->printError("No se han encontrado estacionamientos en las fechas indicadas");
        }else{
            $this->response->addValue('data', $estacionamientos)->printResponse();
        }
    }


}