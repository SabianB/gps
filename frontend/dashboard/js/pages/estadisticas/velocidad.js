class Velocidad {

    constructor() {
        this.endpoint = 'Maps';
        this.map = undefined;
        this.marker = undefined;
        this.route = undefined;
    }





async search(fechaInicio, fechaFin, showSweeetAlert = false, showCustomLoader = false, actionAfterSearchComplete = () => {
}) {

    if (fechaInicio === undefined) {
        showMessage('No se ha colocado una fecha de inicio para poder realizar la busqueda.', 'error');
        return;
    }
    if (fechaFin === undefined) {
        showMessage('No se ha colocado una fecha de fin para poder realizar la busqueda.', 'error');
        return;
    }
    var fechaInicio2 = new Date(fechaInicio);
    var fechaFin2 = new Date(fechaFin);
    if (fechaFin2 < fechaInicio2) {
        showMessage('La fecha de inicio no puede ser mayor a la fecha de fin.', 'error');
        return;
    }
    if (showSweeetAlert) {
        showLoading('Cargando datos, por favor espere...', undefined, true);
    }
    if (showCustomLoader) {
        showCustomLoader = 'containerTableItems';
    }
    await loadPage('pages/estadisticas/velocidad_handler.php', 'containerTableItems', () => {
        actionAfterSearchComplete();
    }, showSweeetAlert, showCustomLoader, {
        'fecha_inicio': fechaInicio,
        'fecha_fin': fechaFin
    });
}



}
let objVel = new Velocidad();