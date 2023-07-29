<?php

namespace utils;

use api\DataTypes;
use api\Response;
use database\SQLDatabase;

class Fichas
{

    /**
     * @param SQLDatabase $SQLDatabase
     * @param int $id_cita
     * @param int $id_doctor
     * @param $especialidad
     * @return bool
     */
    public static function comprobarCita(SQLDatabase $SQLDatabase, int $id_cita, int $id_doctor, $especialidad): bool
    {
        $cita = $SQLDatabase->dbRead('citas', ['*'], "WHERE id=$id_cita");
        if (count($cita) === 0) {
            return false;
        }
        $cita = $cita[0];
        if ($cita['estado'] !== 'COMPLETADA' || !$cita['id_doctor'] || !$cita['id_paciente']) {
            return false;
        }
        if (intval($cita['id_doctor']) !== $id_doctor) {
            return false;
        }
        if (!$SQLDatabase->existsField([
            'id' => $id_doctor,
            'AND',
            'tipo' => $especialidad
        ], 'doctores')) {
            return false;
        }
        return true;
    }

    /**
     * Regresa la precarga con la info del paciente para la ficha
     * @param SQLDatabase $sql
     * @param Response $response
     * @param int $id_cita
     * @param int $id_doctor
     * @param string $especialidad
     * @return array
     */
    public static function precarga(SQLDatabase $sql, Response $response, int $id_cita, int $id_doctor, string $especialidad): array
    {
        if (!self::comprobarCita($sql, $id_cita, $id_doctor, $especialidad)) {
            $response->printError('La cita no se encuentra en un estado valida o careces de permisos suficientes para completar esta acción', 400);
        }
        $paciente = $sql->dbRead('citas', [
            'usuarios.nombres',
            'usuarios.apellidos',
            'pacientes.numero_identificacion',
            'pacientes.fecha_nacimiento',
            'pacientes.telefono',
            'pacientes.direccion',
            'pacientes.lugar_trabajo'
        ], "INNER JOIN pacientes ON pacientes.id=citas.id_paciente INNER JOIN usuarios ON usuarios.id=pacientes.id_usuario WHERE citas.id=$id_cita");
        if($paciente[0]['fecha_nacimiento'] !== ''){
            $dias = ServerData::getDifferenceDates($paciente[0]['fecha_nacimiento'], ServerData::getDate());
            $paciente[0]['edad'] = ServerData::convertDaysToYears($dias);
        }
        if (count($paciente) === 0) {
            $response->printError('No se ha encontrado la información del paciente', 404);
        }
        return $paciente[0];
    }


}