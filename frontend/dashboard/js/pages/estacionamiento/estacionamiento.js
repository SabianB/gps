class Estacionamiento {

    constructor() {
        this.endpoint = 'Maps';
        this.map = undefined;
        this.marker = undefined;
        this.route = undefined;
        this.intervalo = undefined;
        this.marcador = undefined;
    }





async search(fechaInicio, fechaFin, minutos, showSweeetAlert = false, showCustomLoader = false, actionAfterSearchComplete = () => {
}) {

    if (showSweeetAlert) {
        showLoading('Cargando datos, por favor espere...', undefined, true);
    }
    if (showCustomLoader) {
        showCustomLoader = 'estacionamiento_container';
    }
    await loadPage('pages/estadisticas/estacionamiento_handler.php', 'estacionamiento_container', () => {
        actionAfterSearchComplete();
    }, showSweeetAlert, showCustomLoader, {
        'fecha_inicio': fechaInicio,
        'fecha_fin': fechaFin,
        'minutos': minutos,
    });
}



}
let objEst = new Estacionamiento();