class Mapa {

    constructor() {
        this.endpoint = 'Maps';
        this.map = undefined;
        this.marker = undefined;
        this.route = undefined;
        this.intervalo = undefined;
        this.marcador = undefined;
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

    async HistorialRecorrido(inicio, fin){
        this.route = L.featureGroup().addTo(this.map);
        var marker1;
        $(".leaflet-marker-icon").remove(); $(".leaflet-popup").remove();
        $(".leaflet-pane.leaflet-shadow-pane").remove()
        let jsonRQ = new jsonRequest('Maps', 'CoordenadasPorFecha');
        jsonRQ.add('fecha_inicio', inicio, 'string');
        jsonRQ.add('fecha_fin', fin, 'string');
        showLoading('Obteniendo datos...');
        jsonRQ.makeServerQuery(  (response)=> {
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

    async Estacionamientos(inicio,fin, tiempo){

    }

    async Ciudades(){

    }

    async upload() {
        let nombre = document.getElementById('nombre');
        if (nombre.value.trim() === "") {
            showMessage('Debe escribir un nombre para el hotel.', 'error');
            return;
        }
        let descripcion = document.getElementById('descripcion');
        if (descripcion.value.trim() === "") {
            showMessage('Debe escribir una descripción para el hotel.', 'error');
            return;
        }
        let img = document.getElementById('img');
        if (img.value.trim() === "") {
            showMessage('Debe cargar un url de una imagen del hotel.', 'error');
            return;
        }
        let img1 = document.getElementById('img1');
        if (img1.value.trim() === "") {
            showMessage('Debe cargar un url de una imagen del hotel.', 'error');
            return;
        }
        let img2 = document.getElementById('img2');
        if (img2.value.trim() === "") {
            showMessage('Debe cargar un url de una imagen del hotel.', 'error');
            return;
        }
        let img3 = document.getElementById('img3');
        if (img3.value.trim() === "") {
            showMessage('Debe cargar un url de una imagen del hotel.', 'error');
            return;
        }
        let puntaje = document.getElementById('puntaje');
        if (puntaje.value.trim() === "" || puntaje.value > 5) {
            showMessage('Debe elegir un puntaje entre 1 y 5 para el hotel', 'error');
            return;
        }
        let x = document.getElementById('x');
        if (x.value.trim() === "") {
            showMessage('Debe escribir las coordenadas (x) del hotel', 'error');
            return;
        }
        let y = document.getElementById('y');
        if (y.value.trim() === "") {
            showMessage('Debe escribir las coordenadas (y) del hotel', 'error');
            return;
        }
        showLoading('Registrando...');
        const data = new FormData();
        data.append('request', JSON.stringify({
            endpoint: 'Hoteles',
            action: 'registrarHotel',
            nombre: nombre.value.trim(),
            img: img.value.trim(),
            img1: img1.value.trim(),
            img2: img2.value.trim(),
            img3: img3.value.trim(),
            descripcion: descripcion.value.trim(),
            puntaje: parseInt(puntaje.value.trim()),
            x: x.value.trim(),
            y: y.value.trim()
        }));
        try {
            const fetcher = await fetch(config['serverApi'], {
                method: 'POST',
                headers: {
                    'Accept': 'application/json;utf-8'
                },
                body: data
            });
            const json = await fetcher.json();
            if (json.status) {
                Swal.close();
                showMessageWithAction(json.message, 'success', undefined, undefined, () => {
                    showOrHideModal('modalAdd');
                    location.reload();
                });
            } else {
                swal.close();
                showMessage(json.message, 'error');
            }
        } catch (err) {
            swal.close();
            showMessage(err.message, 'error');
        }
    }

    async updateFile() {
        let id = getAttribute('btn_update', 'data-id');
        let nombre = document.getElementById('nombreU');
        if (nombre.value.trim() === "") {
            showMessage('Debe escribir un nombre para el hotel.', 'error');
            return;
        }
        let descripcion = document.getElementById('descripcionU');
        if (descripcion.value.trim() === "") {
            showMessage('Debe escribir una descripción para el hotel.', 'error');
            return;
        }
        let img = document.getElementById('imgU');
        if (img.value.trim() === "") {
            showMessage('Debe cargar un url de una imagen del hotel.', 'error');
            return;
        }
        let img1 = document.getElementById('img1U');
        if (img1.value.trim() === "") {
            showMessage('Debe cargar un url de una imagen del hotel.', 'error');
            return;
        }
        let img2 = document.getElementById('img2U');
        if (img2.value.trim() === "") {
            showMessage('Debe cargar un url de una imagen del hotel.', 'error');
            return;
        }
        let img3 = document.getElementById('img3U');
        if (img3.value.trim() === "") {
            showMessage('Debe cargar un url de una imagen del hotel.', 'error');
            return;
        }
        let puntaje = document.getElementById('puntajeU');
        if (puntaje.value.trim() === "" || puntaje.value > 5) {
            showMessage('Debe elegir un puntaje entre 1 y 5 para el hotel', 'error');
            return;
        }
        let x = document.getElementById('xU');
        if (x.value.trim() === "") {
            showMessage('Debe escribir las coordenadas (x) del hotel', 'error');
            return;
        }
        let y = document.getElementById('yU');
        if (y.value.trim() === "") {
            showMessage('Debe escribir las coordenadas (y) del hotel', 'error');
            return;
        }
        showLoading('Actualizando...');
        const data = new FormData();
        data.append('request', JSON.stringify({
            endpoint: 'Hoteles',
            action: 'actualizarHotel',
            id: parseInt(id),
            nombre: nombre.value.trim(),
            img: img.value.trim(),
            img1: img1.value.trim(),
            img2: img2.value.trim(),
            img3: img3.value.trim(),
            descripcion: descripcion.value.trim(),
            puntaje: parseInt(puntaje.value.trim()),
            x: x.value.trim(),
            y: y.value.trim()
        }));
            try {
            const fetcher = await fetch(config['serverApi'], {
                method: 'POST',
                headers: {
                    'Accept': 'application/json;utf-8'
                },
                body: data
            });
            const json = await fetcher.json();
            if (json.status) {
                Swal.close();
                showMessageWithAction(json.message, 'success', undefined, undefined, () => {
                    showOrHideModal('modalUpdate');
                    location.reload();
                });
            } else {
                swal.close();
                showMessage(json.message, 'error');
            }
        } catch (err) {
            swal.close();
            showMessage(err.message, 'error');
        }
    }


    async deleteFile(fileId) {
        let thisClass = this;
        if (await customConfirmModal('¿Está seguro que desea eliminar este hotel?', undefined, 'question')) {
            let jsonRQ = new jsonRequest(thisClass.endpoint, 'eliminarHotel');
            jsonRQ.add('id', fileId);
            await jsonRQ.makeServerQuery(function (response) {
                const message = getResponse(response, 'message');
                showMessageWithAction(message, 'success', undefined, undefined, () => {
                    location.reload();
                });
            });
        }
    }


    loadData(id) {
        let thisClass = this;
        let jsonRQ = new jsonRequest(thisClass.endpoint, 'obtenerHotelPorID');
        jsonRQ.add('id', id, 'int');
        showLoading('Obteniendo datos...');
        jsonRQ.makeServerQuery(function (response) {
            swal.close();
            set('nombreU', response.nombre);
            set('descripcionU', response.descripcion);
            set('puntajeU',response.puntaje);
            set('xU',response.x);
            set('yU',response.y);
            set('imgU',response.img);
            set('img1U',response.img1);
            set('img2U',response.img2);
            set('img3U',response.img3);
            setAttribute('btn_update', 'data-id', response.id, 'modalUpdate');
            showOrHideModal('modalUpdate');
        }, undefined, true);
    }
}

let objMapa = new Mapa();