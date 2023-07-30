class Horarios {

    constructor() {
        this.endpoint = 'Maps';
        this.map = undefined;
        this.marker = undefined;
        this.route = undefined;
    }





async search(fechaInicio, fechaFin, minutos, showSweeetAlert = false, showCustomLoader = false, actionAfterSearchComplete = () => {
}) {

    if (showSweeetAlert) {
        showLoading('Cargando datos, por favor espere...', undefined, true);
    }
    if (showCustomLoader) {
        showCustomLoader = 'containerTableItems';
    }
    await loadPage('pages/estadisticas/horarios_handler.php', 'containerTableItems', () => {
        actionAfterSearchComplete();
    }, showSweeetAlert, showCustomLoader, {
        'fecha_inicio': fechaInicio,
        'fecha_fin': fechaFin,
        'minutos': minutos,
    });
}



}
let objHorarios = new Horarios();