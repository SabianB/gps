<?php
chdir(__DIR__);
require_once '../../auth/request_sub_page.php';
chdir(__DIR__);
require_once '../../utils/networking.php';
chdir(__DIR__);

global $token;
global $config;

$token = $_COOKIE['token'];
$response = serverQuery($token, array(
    'endpoint' => 'Authentication',
    'action' => 'decodeToken',
    'token' => $token
));

?>

<body>
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <div class="row">
                <div class="col">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Ciudades</li>
                    </ol>
                </div><!--end col-->
            </div><!--end row-->
        </div><!--end page-title-box-->
    </div><!--end col-->
</div>
<div class="row" style="height: 95%">
    <div class="col-lg-2">
        <div class="container mt-5">
            <div class="row">
                <div class="col text-center">
                    <label for="fechaInicio" class="form-label">Fecha de inicio</label>
                    <input type="datetime-local" class="form-control" id="fechaInicio">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col text-center">
                    <label for="fechaFin" class="form-label">Fecha de fin</label>
                    <input type="datetime-local" class="form-control" id="fechaFin" value="<?php echo gmdate('Y-m-d\TH:i', time() - 5 * 3600); ?>">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col text-center">
                    <button class="btn btn-success" onclick="objCiudad.search(fechaini, fechafin, false, true)">Buscar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-10">
        <div >
            <div class="card mb3">
                <div class="card-body">
                    <div id="containerTableItems" class="table-responsive">
                        <table id="ciudadesTable" class="table table-bordered dt-responsive nowrap"
                               style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                            <tr>
                                <th>Ciudad</th>
                                <th>Fecha de entrada</th>
                                <th>Fecha de salida</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Sin registros</td>
                                <td>Sin registros</td>
                                <td>Sin registros</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>


</div>

<script>
    initDataTable('ciudadesTable');
    //objMapa.IniciarMapa();



    var fechaInput = document.getElementById("fechaInicio");
    var fechaInputFin = document.getElementById("fechaFin");
    var fechaini;
    var fechafin;
    var fecha2 = new Date(fechaInputFin.value);
    fecha2.setHours(fecha2.getHours() - 5);
    var formattedDate2 = fecha2.toISOString().substring(0, 19).replace("T", " ");
    fechafin = formattedDate2;
    fechaInput.addEventListener("input", function() {
        var fecha = new Date(this.value);
        fecha.setHours(fecha.getHours() - 5);
        var formattedDate = fecha.toISOString().substring(0, 19).replace("T", " ");
        this.value = formattedDate;
        fechaini = formattedDate;
    });
    fechaInputFin.addEventListener("input", function() {
        fecha2 = new Date(this.value);
        fecha2.setHours(fecha2.getHours() - 5);
        formattedDate2 = fecha2.toISOString().substring(0, 19).replace("T", " ");
        this.value = formattedDate2;
        fechafin = formattedDate2;
    });

</script>



</body>