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
        $this->getSQLDatabase()->dbCreate('registros', [
            'latitud' => $latitud,
            'longitud' => $longitud,
            'id_usuario' => $id_usuario,
            'fecha' => $date
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
            $this->response->printError("No se han encontrado estacionamientos en las fechas indicadas");
        }
        //$idcoordenada = $data[0]['id'];
        $estacionamientos = [];
        $itemestacionamiento = [];
        for ($i = 1; $i< count($data); $i++){
            if(($data[$i]['latitud'] == $data[$i-1]['latitud']) && ($data[$i]['longitud'] == $data[$i-1]['longitud'])){
                array_push($itemestacionamiento, $data[$i-1]);
            }
            else{
                if(count($itemestacionamiento) > 0){
                    array_push($itemestacionamiento, $data[$i-1]);
                }
                $fecha1 = new \DateTime($data[0]['fecha']);
                $fecha2 = new \DateTime($data[count($data)-1]['fecha']);
                $diferencia = $fecha1->diff($fecha2);
                $totalSegundos = $diferencia->s + ($diferencia->i * 60) + ($diferencia->h * 3600) + ($diferencia->days * 86400);
                if($totalSegundos > 300){
                    array_push($estacionamientos, [$fecha1, $fecha2['fecha']]);
                }
                
            }
        }
        $this->response->addValue('data', $estacionamientos)->printResponse();
    }


}