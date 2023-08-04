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
$minutos = (int)$_POST["minutos"];

$response = serverQuery($token, array(
    'endpoint' => 'Maps',
    'action' => 'Estacionamiento',
    'minutos' => $minutos,
    'fecha_inicio' => $fecha_inicio,
    'fecha_fin' => $fecha_fin
));
$horarios = $response['status'] ? $response['data'] : [];
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

<div class="table-responsive" id="table_container">
    <table class="table table-bordered table-striped table-vcenter js-dataTable-buttons block-content block-content-full"
           id="table_items">
        <thead>
        <tr>
        <tr>
            <th>Coordenadas</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Ver en el mapa</th>
        </tr>
        </tr>
        </thead>
        <tbody id="table_body">
        <?php
        foreach ($horarios as $record) {
            ?>
            <tr>
                <td><?= $record['latitud'] .", " .$record['longitud'] ?></td>
                <td><?= date("Y-m-d", strtotime($record['ultimo_registro']));?></td>
                <td><?= date("H:i:s", strtotime($record['ultimo_registro']));?></td>
                <td>
                    <div class="text-center">
                        <div class="btn-group">
                            <button data-toggle="tooltip" title="Ver en el mapa" type="button" class="btn-dark btn"
                                    data-info="{'tipo': 'horarios','lat': '<?= $record['latitud']?>','lon':'<?= $record['longitud'] ?>',
                                     'fecha':'<?= $record['ultimo_registro']?>'}"
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
        // initDataTable('table_items');
        objMapa.tabla = $('#table_items').DataTable({
            "language": {
                "url": config['jsonPath'] + "Spanish.json"
            },
            "columnDefs": [{
                "targets": 'no-sort',
                "orderable": false,
            }]
        });
    });

</script>