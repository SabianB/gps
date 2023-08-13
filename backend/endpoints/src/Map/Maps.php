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

        //REMPLAZAR 'latitud' y 'longitud' por:  'ROUND(latitud, 3) AS latitud', 'ROUND(longitud, 3) AS longitud',
        //Asi se reciben 3 decimales y la precision se redondea
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

    public function Velocidad(){
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
        if(count($data) < 2){
            $this->response->printError("No se han encontrado suficientes localizaciones para tomar la ciudades");
        }
        $coordenadas = $data;

        $velocidadMaxima = 0;
        $velocidadMinima = PHP_INT_MAX;
        $distanciaTotal = 0;
        $tiempoTotal = 0;
        $registros = [];
        for ($i = 0; $i < count($coordenadas) - 1; $i++) {
            $lat1 = deg2rad($coordenadas[$i]['latitud']);
            $lon1 = deg2rad($coordenadas[$i]['longitud']);
            $lat2 = deg2rad($coordenadas[$i + 1]['latitud']);
            $lon2 = deg2rad($coordenadas[$i + 1]['longitud']);

            $distancia = 6371 * acos(sin($lat1) * sin($lat2) + cos($lat1) * cos($lat2) * cos($lon2 - $lon1));
            $tiempo = strtotime($coordenadas[$i + 1]['fecha']) - strtotime($coordenadas[$i]['fecha']);
            $velocidad = $distancia / ($tiempo / 3600); // Convertir el tiempo a horas
            $velocidad = number_format($velocidad, 2);
            $reg = [
                'lat_inicio' => $coordenadas[$i]['latitud'],
                'lon_inicio' => $coordenadas[$i]['longitud'],
                'fecha_inicio' => $coordenadas[$i]['fecha'],
                'lat_fin' => $coordenadas[$i + 1]['latitud'],
                'lon_fin' => $coordenadas[$i + 1]['longitud'],
                'fecha_fin' => $coordenadas[$i + 1]['fecha'],
                'velocidad' => $velocidad
            ];
            array_push($registros,$reg);

            $distanciaTotal += $distancia;
            $tiempoTotal += $tiempo;
            $velocidadMaxima = max($velocidadMaxima, $velocidad);
            $velocidadMinima = min($velocidadMinima, $velocidad);
            // Calcular la ciudades promedio
            $velocidadPromedio = $distanciaTotal / ($tiempoTotal / 3600); // Convertir el tiempo a horas
        }

        $this->response->addValue('data', [
            'velocidad_max' => number_format($velocidadMaxima, 2),
            'velocidad_min' => number_format($velocidadMinima, 2),
            'velocidad_med' => number_format($velocidadPromedio, 2),
            'coordenadas' => $registros
        ])->printResponse();



    }

    public function Ciudades(){
        $this->request->checkInput([
            'fecha_inicio' => DataTypes::string,
            'fecha_fin' => DataTypes::string
        ], true);
        $payload = $this->request->getPayload();
        $usuario_id = $payload['id'];
        $fecha_inicio = $this->request->getValue('fecha_inicio');
        $fecha_fin = $this->request->getValue('fecha_fin');
        $data = $this->getSQLDatabase()->dbRead('registros', [
            'fecha',
            'ciudad'
        ], "WHERE id_usuario = $usuario_id AND fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'" );

        if(count($data) === 0){
            $this->response->printError("No se han encontrado registros en las fechas indicadas");
        }
        $primerosUltimosRegistros = array();
        $ciudadActual = null;
        $contador = -1;

        foreach ($data as $registro) {
            $ciudad = $registro["ciudad"];

            if ($ciudadActual !== $ciudad) {
                $ciudadActual = $ciudad;
                $contador++;
                $primerosUltimosRegistros[$contador]["ciudad"] = $ciudad;
                $primerosUltimosRegistros[$contador]["primer_registro"] = $registro['fecha'];
            }

            $primerosUltimosRegistros[$contador]["ultimo_registro"] = $registro['fecha'];
        }
        $this->response->addValue('data', $primerosUltimosRegistros)->printResponse();
    }

    public function Estacionamiento(){
        $this->request->checkInput([
            'fecha_inicio' => DataTypes::string,
            'fecha_fin' => DataTypes::string,
            'minutos' => DataTypes::integer
        ], true);
        $horarios_salida = $this->request->getValue('horario_salida');
        $minutos = $this->request->getValue('minutos');
        $minutos = $minutos * 60;
        if($horarios_salida){
            $minutos = 5 * 60;
        }
        $payload = $this->request->getPayload();
        $usuario_id = $payload['id'];
        $fecha_inicio = $this->request->getValue('fecha_inicio');
        $fecha_fin = $this->request->getValue('fecha_fin');
        $data = $this->getSQLDatabase()->dbRead('registros', [
            'ROUND(latitud, 3) AS latitud',
            'ROUND(longitud, 3) AS longitud',
            'fecha'
        ], "WHERE id_usuario = $usuario_id AND fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'" );
        if(count($data) < 2){
            $this->response->printError("No se han encontrado estacionamientos en las fechas indicadas");
        }

        $primerosUltimosRegistros = array();
        $latitudActual = null;
        $longitudActual = null;
        $results = [];
        $contador = -1;

        foreach ($data as $registro) {
            $latitud = $registro["latitud"];
            $longitud = $registro["longitud"];
            $fecha = $registro["fecha"];

            if ($latitudActual !== $latitud || $longitudActual !== $longitud) {
                $latitudActual = $latitud;
                $longitudActual = $longitud;
                $contador++;
                $primerosUltimosRegistros[$contador]["latitud"] = $latitud;
                $primerosUltimosRegistros[$contador]["longitud"] = $longitud;
                $primerosUltimosRegistros[$contador]["primer_registro"] = $fecha;
                $primerosUltimosRegistros[$contador]["ultimo_registro"] = $fecha;
                if($contador > 0){
                    if ($this->diferenciaSegundos($primerosUltimosRegistros[$contador-1]["primer_registro"], $fecha) >= $minutos) {
                        array_push($results, $primerosUltimosRegistros[$contador-1]);
                    }
                }

            }
            $primerosUltimosRegistros[$contador]["ultimo_registro"] = $fecha;
            if($data[count($data)-1] === $registro){
                if ($this->diferenciaSegundos($primerosUltimosRegistros[$contador]["primer_registro"], $fecha) >= $minutos ) {
                    array_push($results, $primerosUltimosRegistros[$contador]);
                }
            }
        }
        $this->response->addValue('data', $results)->printResponse();

    }

    function diferenciaSegundos($fechaInicio, $fechaFin) {
        $inicio = new \DateTime($fechaInicio);
        $fin = new \DateTime($fechaFin);
        $diferencia = $inicio->diff($fin);
        $totalSegundos = $diferencia->s + ($diferencia->i * 60) + ($diferencia->h * 3600) + ($diferencia->days * 86400);
        return $totalSegundos; // Devuelve la diferencia en segundos
    }

}