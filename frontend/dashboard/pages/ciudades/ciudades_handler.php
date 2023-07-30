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
    'action' => 'Ciudades',
    'fecha_inicio' => $fecha_inicio,
    'fecha_fin' => $fecha_fin

));
$result = $response['status'] ? $response['data'] : [];
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
            <th>Ciudad</th>
            <th>Fecha de entrada</th>
            <th>Fecha de salida</th>
        </tr>
        </tr>
        </thead>
        <tbody id="table_body">
        <?php
        foreach ($result as $record) {
            ?>
            <tr>
                <td><?= $record['ciudad']?></td>
                <td><?= $record['primer_registro'] ?></td>
                <td><?= $record['ultimo_registro'] ?></td>
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