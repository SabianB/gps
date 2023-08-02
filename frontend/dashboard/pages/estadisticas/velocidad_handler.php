<?php
chdir(__DIR__);
require_once '../../auth/request_sub_page.php';
chdir(__DIR__);
require_once '../../utils/networking.php';
chdir(__DIR__);

global $config;
global $token;

$fecha_inicio = $_POST["fecha_inicio"];
$fecha_fin = $_POST["fecha_fin"];

$response = serverQuery($token, array(
    'endpoint' => 'Maps',
    'action' => 'Velocidad',
    'fecha_inicio' => $fecha_inicio,
    'fecha_fin' => $fecha_fin

));
$result = $response['status'] ? $response['data'] : [];
$velocidad = $result['coordenadas'];
?>
<style>
    th {
        background-color: #f2f2f2;
        text-align: center; /* Centrar el contenido del encabezado */
        padding: 10px;
        border: 1px solid #ccc;
    }
    td {
        background-color: #f2f2f2;
        text-align: center; /* Centrar el contenido del encabezado */
        padding: 10px;
        border: 1px solid #ccc;
    }
</style>
<div class="card-mb3">
    <div class="card-body">
        Velocidad promedio: <?= $result['velocidad_med'] ?> Km/h<br>
        Velocidad máxima: <?= $result['velocidad_max'] ?> Km/h<br>
        Velocidad mínima: <?= $result['velocidad_min'] ?> Km/h<br>
    </div>
</div>
<div class="table-responsive" id="table_container">
    <table class="table table-bordered table-striped table-vcenter js-dataTable-buttons block-content block-content-full"
           id="table_items">
        <thead>
        <tr>
        <tr>
            <th>Coordenadas</th>
            <th>Fechas</th>
            <th>Velocidad</th>
            <th>Ver en el mapa</th>
        </tr>
        </tr>
        </thead>
        <tbody id="table_body">
        <?php
        foreach ($velocidad as $record) {
            ?>
            <tr>
                <td><?= $record['lat_inicio'] . ", " . $record['lon_inicio'] . " a " .  $record['lat_fin'] . ", " . $record['lon_fin']?></td>
                <td><?= $record['fecha_inicio'] . " - " . $record['fecha_fin'] ?></td>
                <td><?= $record['velocidad'] ?> Km/h</td>
                <td>
                    <div class="text-center">
                        <div class="btn-group">
                            <button data-toggle="tooltip" title="Ver en el mapa" type="button" class="btn-dark btn"
                                    data-info="{'tipo': 'velocidad','lat': '<?= $record['lat_fin']?>','lon':'<?= $record['lon_fin'] ?>',
                                     'fecha':'<?= $record['fecha_fin']?>', 'velocidad': '<?= $record['velocidad']?>Km/h'}"
                                    onclick="objMapa.OpenModalMap(this)">
                                <i class="fas fa-map"></i>
                            </button>
                        </div>
                    </div>
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
</div>



<script>
    initBootstrapTooltip();
    $(document).ready(function () {
        initDataTable('table_items');
    });

</script>