
class Mapa {

    constructor() {
        this.endpoint = 'Maps';
        this.map = undefined;
        this.marker = undefined;
        this.route = undefined;
        this.intervalo = undefined;
        this.marcador = undefined;
        this.actualresponse = undefined;
        this.tabla = undefined;
    }

    IniciarMapa(){

            this.map = L.map('map');
            this.map.setView({lat: -1.0655, lon: -78.3996}, 7);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png' , {
            maxZoom: 19,
            attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap contributors</a>'
        }).addTo(this.map);
            // show the scale bar on the lower left corner
            L.control.scale({imperial: true, metric: true}).addTo(this.map);
    }

    async OpenModalMap(boton){
        if(this.marker){
            this.map.removeLayer(this.marker);
        }

        this.route = L.featureGroup().addTo(this.map);
        var data = boton.getAttribute('data-info');
        data = data.replace(/\\'/g, '\\"');
        data = data.replace(/'/g, '"');
        data = JSON.parse(data);
        if(data.tipo === "estacionamiento"){
            showOrHideModal('estacionamientoModal')
            await this.delay(200);
            this.map.invalidateSize();
            this.marker = L.marker({lat: data.lat, lon: data.lon}).bindTooltip("Inicio de estacionamiento: " + data.inicio_est + "<br>Fin de estacionamiento: " + data.fin_est);
            this.route.addLayer(this.marker);
            this.map.fitBounds(this.route.getBounds());
        }
        else if(data.tipo === "horarios"){
            showOrHideModal('horariosModal')
            await this.delay(200);
            this.map.invalidateSize();
            this.marker = L.marker({lat: data.lat, lon: data.lon}).bindTooltip("Fecha y hora de salida: " + data.fecha);
            this.route.addLayer(this.marker);
            this.map.fitBounds(this.route.getBounds());
        }
        else if(data.tipo === "velocidad"){
            showOrHideModal('velocidadModal')
            await this.delay(200);
            this.map.invalidateSize();
            this.marker = L.marker({lat: data.lat, lon: data.lon}).bindTooltip("Fecha: " + data.fecha + "<br>Velocidad: " + data.velocidad);
            this.route.addLayer(this.marker);
            this.map.fitBounds(this.route.getBounds());
        }

    }

    delay(time) {
        return new Promise(resolve => setTimeout(resolve, time));
    }

    async HistorialRecorrido(inicio, fin){
        if (inicio === undefined) {
            showMessage('No se ha colocado una fecha de inicio para poder realizar la busqueda.', 'error');
            return;
        }
        if (fin === undefined) {
            showMessage('No se ha colocado una fecha de fin para poder realizar la busqueda.', 'error');
            return;
        }
        var fechaInicio2 = new Date(fechaInicio);
        var fechaFin2 = new Date(fechaFin);
        if (fechaFin2 < fechaInicio2) {
            showMessage('La fecha de inicio no puede ser mayor a la fecha de fin.', 'error');
            return;
        }
        this.route = L.featureGroup().addTo(this.map);
        var marker1;
        $(".leaflet-marker-icon").remove(); $(".leaflet-popup").remove();
        $(".leaflet-pane.leaflet-shadow-pane").remove()
        let jsonRQ = new jsonRequest('Maps', 'CoordenadasPorFecha');
        jsonRQ.add('fecha_inicio', inicio, 'string');
        jsonRQ.add('fecha_fin', fin, 'string');
        showLoading('Obteniendo datos...');
        jsonRQ.makeServerQuery(  (response)=> {
            this.actualresponse = response;
            swal.close();
            for (let i = 0; i < response.length; i++) {
                const item = response[i];
                //Muestra los tooltips
                marker1 = L.marker({lat: item.latitud, lon: item.longitud}).bindTooltip(item.fecha);
                this.route.addLayer(marker1)
            }
            this.latlngs = response.map(obj => [obj.latitud, obj.longitud, obj.fecha]);
            //Dibuja la linea
            var polyline = L.polyline(this.latlngs).addTo(this.map);
            this.map.fitBounds(this.route.getBounds());
        }, undefined, true);
    }

    async IniciarTracking(){
        let jsonRQ = new jsonRequest('Maps', 'ObtenerCoordenadas');
        this.intervalo = setInterval(async ()=> {
            if(this.marcador){
                this.map.removeLayer(this.marcador);
            }

            await jsonRQ.makeServerQuery(  (response)=> {
                this.marcador = L.marker([response.latitud, response.longitud]).bindPopup(response.fecha);
                var featureGroup = L.featureGroup([this.marcador]).addTo(this.map);
                this.map.fitBounds(featureGroup.getBounds());
            }, undefined, true);


        }, 5000)
    }

    Stop(){
        clearInterval(this.intervalo);
    }

    HistorialXlsx(){
        var wb = XLSX.utils.book_new();
        var ws = XLSX.utils.json_to_sheet(this.actualresponse, { header: ["latitud", "longitud", "fecha"] });
        XLSX.utils.book_append_sheet(wb, ws, 'Historial');
        XLSX.writeFile(wb, 'Reporte.xlsx');
    }

    ReportePorTabla(nombre = 'Reporte', ultimo_campo = true){
        if(this.tabla === undefined){
            showMessage('No existen datos en la tabla actual para poder generar un reporte.', 'error');
            return;
        }
        var datosTabla = undefined;
        var encabezados = undefined;

        if(ultimo_campo){
            datosTabla = this.tabla.rows().data().toArray().map(row => row.slice(0, -1));
            encabezados = this.tabla.columns().header().toArray().slice(0, -1).map(th => th.innerText);
        }
        else {
            datosTabla = this.tabla.rows().data().toArray();
            encabezados = this.tabla.columns().header().toArray().map(th => th.innerText);

        }

        var wb = XLSX.utils.book_new();
        var ws = XLSX.utils.aoa_to_sheet([encabezados, ...datosTabla]);

        var arrayAnchos = [];
        for (var i = 0; i < encabezados.length; i++) {
            arrayAnchos.push({ wpx: 150 });
        }

        ws['!cols'] = arrayAnchos;

        // Agregar la hoja de cálculo al libro
        XLSX.utils.book_append_sheet(wb, ws, nombre);

        XLSX.writeFile(wb, `${nombre}.xlsx`);

    }
}

let objMapa = new Mapa();