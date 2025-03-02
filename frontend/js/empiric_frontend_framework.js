let event_rejected;
let event_rejected_message;
/**
 * @type {string}
 * {string} dark or light
 */
const sweetAlertColorMode = 'light'
const bgColorMode = (sweetAlertColorMode === 'dark') ? '#212121' : '#FFFFFF';
const fontColorMode = (sweetAlertColorMode === 'dark') ? '#FFFFFF' : '#595959';

function checkRejected() {
    return new Promise(resolve => {
        resolve(!!event_rejected);
    });
}

function cleanRejected() {
    event_rejected = false;
    event_rejected_message = '';
}

function getReadTime(content) {
    const wordsPerMinute = 100; // Average case.
    let textLength = content.split(" ").length; // Split by words
    if (textLength > 0) {
        let result = Math.round(((textLength / wordsPerMinute) * 60) * 1000);
        if (result < 2000) {
            return 2000;
        } else {
            return result;
        }
    }
}

function serverQuery($json, onSuccess, onError, onFatal) {
    fetch(config['serverApi'], {
        method: 'POST',
        headers: {
            'Accept': 'application/json;utf-8'
        },
        body: JSON.stringify($json)
    }).then(response => {
        return response.json();
    }).then(json => {
        const status = getResponse(json, 'status');
        if (status) {
            onSuccess(json);
        } else {
            onError(json);
        }
    }).catch(err => {
        showMessage(err, 'error');
        //onError(err);
    });
}

async function serverQueryAsync($json, onSuccess, onError) {
    let response = await new Promise(resolve => {
        fetch(config['serverApi'], {
            method: 'POST',
            headers: {
                'Accept': 'application/json;utf-8'
            },
            body: JSON.stringify($json)
        }).then(response => {
            resolve(response.json());
        }).then(json => {
            resolve(json);
        }).catch(err => {
            resolve(err);
        });
    });
    if (response.status) {
        onSuccess(response);
    } else {
        onError(response);
    }
    return response;
}

const parseJwt = (token) => {
    try {
        return JSON.parse(atob(token.split('.')[1]));
    } catch (e) {
        return null;
    }
};

function getCookie(name) {
    let nameEQ = name + "=";
    let ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function setCookie(name, value, days) {
    let expires = "";
    if (days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
}

function delete_cookie(name) {
    document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

/**
 * jsonRequest is a class for make json structuration more simple in frontend.
 */
class jsonRequest {
    constructor(EndPoint, Action) {
        this.endpoint = EndPoint.trim();
        this.action = Action.trim();
        this.jsonPrint = {};
        this.jsonPrint['endpoint'] = this.endpoint;
        this.jsonPrint['action'] = this.action;
        this.jsonArray = [];
        this.jsonResponse = "";
    }

    /**
     * add a new {key: value} to our json request
     * @param {string} key
     * @param {*}value
     * @param {*} type
     */
    add(key, value, type = 'string') {
        switch (type) {
            case "string": {
                this.jsonPrint[key] = value
                break;
            }
            case "int": {
                this.jsonPrint[key] = parseInt(value);
                break;
            }
            case 'array': {
                this.jsonPrint[key] = value
                break;
            }
            default: {
                this.jsonPrint[key] = value
                break;
            }
        }
    }

    /**
     * Add a new value to an array of values (use when you need to add values to an array from an iterator and then add them as value to a key).
     * @param value
     */
    addToArray(value) {
        this.jsonArray.push(value)
    }

    /**
     * Add a new {key, value} based on an HTML element id
     * @param {string} key
     * @param {string} elementID
     * @param {string} type
     * @param {boolean} SelectOptionText
     */
    addByElementID(key, elementID, type = 'string', SelectOptionText = false) {
        if (document.getElementById(elementID).value.trim() === '' || document.getElementById(elementID).value.trim() === '0') {
            event_rejected = true;
            event_rejected_message = 'Debe llenar todos los campos solicitados.';
        }
        switch (type) {
            case "string": {
                this.jsonPrint[key] = document.getElementById(elementID).value;
                break;
            }
            case "int": {
                this.jsonPrint[key] = parseInt(document.getElementById(elementID).value);
                break;
            }
            case "float": {
                this.jsonPrint[key] = parseFloat(document.getElementById(elementID).value);
                break;
            }
            default: {
                this.jsonPrint[key] = document.getElementById(elementID).value;
                break;
            }
        }
        if (SelectOptionText) {
            let el = document.getElementById(elementID);
            this.jsonPrint[key] = el.options[el.selectedIndex].text
        }
    }

    #addToJson(currentElement, useName = false, sendEmpty=false) {
        if (sendEmpty) {
            if (isNaN(currentElement.value)) {
                (useName) ? this.jsonPrint[currentElement.getAttribute('name')] = '' : this.jsonPrint[currentElement.id] = '';
            } else {
                if (currentElement.classList.contains('avoid-numeric')) {
                    (useName) ? this.jsonPrint[currentElement.getAttribute('name')] = '' : this.jsonPrint[currentElement.id] = '';
                } else {
                    (useName) ? this.jsonPrint[currentElement.getAttribute('name')] = 0 : this.jsonPrint[currentElement.id] = 0;
                }
            }
        } else {
            if (isNaN(currentElement.value)) {
                (useName) ? this.jsonPrint[currentElement.getAttribute('name')] = currentElement.value : this.jsonPrint[currentElement.id] = currentElement.value;
            } else {
                if (currentElement.classList.contains('avoid-numeric')) {
                    (useName) ? this.jsonPrint[currentElement.getAttribute('name')] = currentElement.value : this.jsonPrint[currentElement.id] = currentElement.value;
                } else {
                    (useName) ? this.jsonPrint[currentElement.getAttribute('name')] = parseFloat(currentElement.value) : this.jsonPrint[currentElement.id] = parseFloat(currentElement.value);
                }
            }
        }
    }

    /**
     * Print action from JSON REQUEST
     * use element class -> avoid-numeric (for evade numeric parsing)
     * use element class -> avoid-zero (for option selects where you want to avoid 0 option)
     * use element class -> required if you want to ask for required fields
     * use element class -> optional if you want to get data from (OPTIONAL) fields if user fills
     * @return {*}
     */
    addAuto(useName = false, onSuccess = () => {
    }, container = "", sendEmpty = false) {
        let requiredElements = document.getElementsByClassName('required');
        let optionalElements = document.getElementsByClassName('optional');
        if (container !== '') {
            let containerElements = document.getElementById(container);
            requiredElements = containerElements.getElementsByClassName('required');
            optionalElements = containerElements.getElementsByClassName('optional');
        }
        for (let currentElement of optionalElements) {
            if (currentElement.tagName.toLowerCase() === 'select') {
                if (currentElement.value === "0") {
                    this.#addToJson(currentElement,useName,true);
                    continue;
                }
            } else {
                if (currentElement.value.trim() === "") {
                    this.#addToJson(currentElement,useName,true);
                    continue;
                }
            }
            if (useName) {
                if (currentElement.getAttribute('name').trim() === '') {
                    showMessage('Uno de los elementos especificados como opcionales no tiene un nombre asignado en el atributo NAME.', 'error');
                    currentElement.focus();
                    return;
                }
            }
            this.#addToJson(currentElement, useName);
        }
        for (let currentElement of requiredElements) {
            if (currentElement.tagName.toLowerCase() === 'select') {
                if (!currentElement.classList.contains('avoid-zero')) {
                    if (currentElement.value === '0') {
                        showMessage('Debe completar todos los campos requeridos.', 'error');
                        currentElement.focus();
                        return;
                    }
                }
            } else {
                if (currentElement.value.trim() === "") {
                    showMessage('Debe completar todos los campos requeridos.', 'error');
                    currentElement.focus();
                    return;
                }
            }
            if (useName) {
                if (currentElement.getAttribute('name').trim() === '') {
                    showMessage('Uno de los elementos especificados como requeridos no tiene un nombre asignado en el atributo NAME.', 'error');
                    currentElement.focus();
                    return;
                }

            }
            this.#addToJson(currentElement, useName);
        }
        onSuccess();
    }

    printJsonAction() {
        return this.jsonPrint['action'];
    }

    /**
     * Print key from JSON REQUEST
     * @return {*}
     */
    printJsonKey(key) {
        return this.jsonPrint[key];
    }

    /**
     * Print our JSON REQUEST as a JSON object
     * @return {*}
     */
    printAsJsonObject() {
        return this.jsonPrint;
    }

    /**
     * Print our JSON array
     * @return {*}
     */
    printAsJsonArray() {
        return this.jsonArray;
    }

    /**
     * Print our JSON REQUEST as JSON object with a key that contains an array (previously iterated)
     * @return {*}
     */
    printAsJsonWithArray(key) {
        this.jsonPrint[key] = this.jsonArray;
        return this.jsonPrint;
    }

    /**
     * Print our JSON REQUEST as string
     * @return {*}
     */
    printAsJsonStringify() {
        return JSON.stringify(this.jsonPrint);
    }

    /**
     * Print our JSON REQUEST on browser console
     * @return {*}
     */
    printConsole() {
        console.log(this.jsonPrint);
    }

    get(key) {
        return this.jsonPrint[key];
    }

    set(key, value) {
        this.jsonPrint[key] = value;
    }

    delete (key){
        delete this.jsonPrint[key];
    }

    async makeServerQuery(onSuccess = function (response) {
        const message = getResponse(response, 'message');
        //showMessageAutoClose(message, 'success', getReadTime(message))
        showMessage(message, 'success');
    }, onError = function (response) {
        const message = getResponse(response, 'message');
        //showMessageAutoClose(message, 'error', getReadTime(message));
        showMessage(message, 'error');
    }, getData = false, returnResponse = false) {
        let check_reject = await checkRejected();
        if (check_reject) {
            showMessage(event_rejected_message, 'error');
            cleanRejected();
        } else {
            if (returnResponse) {
                let dataResponse = await serverQueryAsync(this.printAsJsonObject(), function () {
                }, function () {
                });
                if (getData) {
                    return getResponse(dataResponse, 'data');
                } else {
                    return dataResponse;
                }
            } else {
                serverQuery(this.printAsJsonObject(), (json) => {
                    if (onSuccess !== null) {
                        if (getData) {
                            const data = getResponse(json, 'data');
                            onSuccess(data);
                        } else {
                            onSuccess(json);
                        }
                    }
                }, (json) => {
                    if (onError !== null) {
                        onError(json);
                    }
                });
            }
        }
    }
}

/**
 * Show a SweetAlert2 popup loader for wait while your are waiting for data from request.
 * @param {string} message
 * @param {string} backgroundColor
 */
function showLoading(message, backgroundColor = bgColorMode, isText = false) {
    let text = "";
    if (isText) {
        text = message;
        message = "";
    }
    Swal.fire({
        icon: '',
        title: message,
        text: text,
        iconColor: '#ff5821',
        background: backgroundColor,
        allowOutsideClick: false,
        showClass: {
            backdrop: 'swal2-with-backdrop'
        },
        didOpen: () => {
            Swal.showLoading()
        }
    });
}

/**
 * Load a page by Web URL using JQUERY
 * @param {string} page
 */
async function loadPage(page, container = "web_content", action = () => {}, closeSwal = true, sameLoaderIdAsContainer = false, jsonToPost = {}) {
    container = (sameLoaderIdAsContainer) ? sameLoaderIdAsContainer : container;
    // toggleLoaderContainer(container);
    //showLoading('');
    //setTimeout(() => {
    $('#' + container).load(page, jsonToPost, () => {
        action();
        // toggleLoaderContainer(container);
        //One.loader('hide');
        if (closeSwal) {
            swal.close();
        }
    });
    //}, 200);
}

/**
 * Load a page by their hashName.
 * @param {string} Hash
 */
async function loadHashPage(Hash) {
    location.hash = '#' + Hash;
}

/**
 * Show or Hide bootstrap modal.
 * @param {string} id
 */
function showOrHideModal(id) {
    $('#' + id).modal('toggle');
}

/**
 * Show default sweetAlert popup notification with custom message and type.
 * @param {string} message
 * @param  {string} type
 * @param  {string} backgroundColor
 * @param  {string} fontColor
 */
function showMessage(message, type, backgroundColor = bgColorMode, fontColor = fontColorMode) {
    Swal.fire({
        title: '',
        html: `<span style="color: ${fontColor}">${message}</span>`,
        background: bgColorMode,
        icon: type,
        showConfirmButton: true,
        confirmButtonText: 'Aceptar',
        allowOutsideClick: false,
        showClass: {
            backdrop: 'swal2-with-backdrop'
        }
    });
}

/**
 * Show default sweetAlert popup notification with custom message and type.
 * @param {string} message
 * @param  {string} type
 * @param  {string} backgroundColor
 * @param  {string} fontColor
 */
function showMessageWithAction(message, type, backgroundColor = bgColorMode, fontColor = fontColorMode, action = () => {
}) {
    Swal.fire({
        title: '',
        background: bgColorMode,
        html: `<span style="color: ${fontColor}">${message}</span>`,
        icon: type,
        showConfirmButton: true,
        confirmButtonText: 'Aceptar',
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false,
        showClass: {
            backdrop: 'swal2-with-backdrop'
        }
    }).then(success => {
        if (success) {
            action();
        }
    });
}

/**
 * Show default sweetAlert popup notification with custom message and type.
 * @param {string} title
 * @param {string} icon
 * @param  {number}  time
 * @param {boolean} allowOutsideClick
 * @param {function} action
 */
function showMessageAutoCloseWithAction(title, icon, time = 2000, allowOutsideClick = true, action) {
    let timerInterval
    time = getReadTime(title);
    Swal.fire({
        title: title,
        html: 'Éste mensaje se cerrará en <b></b> segundos.',
        timer: time,
        icon: icon,
        allowOutsideClick: allowOutsideClick,
        backdrop: true,
        showClass: {
            backdrop: 'swal2-with-backdrop'
        },
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
            const b = Swal.getHtmlContainer().querySelector('b')
            timerInterval = setInterval(() => {
                b.textContent = (parseInt(Swal.getTimerLeft()) / 1000).toFixed(0).toString();
            }, 100)
        },
        willClose: () => {
            clearInterval(timerInterval)
        }
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.timer) {
            action();
        }
    })
}

/**
 * Show default sweetAlert popup notification with custom message and type.
 * @param {string} title
 * @param {string} icon
 * @param  {number}  time
 * @param  {*} redirHash
 */
function showMessageAutoClose(title, icon, time = 2000, redirHash = false, allowOutsideClick = true, reload = false) {
    let timerInterval
    time = getReadTime(title);
    Swal.fire({
        title: title,
        html: 'Éste mensaje se cerrará en <b></b> segundos.',
        timer: time,
        icon: icon,
        allowOutsideClick: allowOutsideClick,
        backdrop: true,
        showClass: {
            backdrop: 'swal2-with-backdrop'
        },
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
            const b = Swal.getHtmlContainer().querySelector('b')
            timerInterval = setInterval(() => {
                b.textContent = (parseInt(Swal.getTimerLeft()) / 1000).toFixed(0).toString();
            }, 100)
        },
        willClose: () => {
            clearInterval(timerInterval)
        }
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.timer) {
            if (reload) {
                window.location.reload();
            }
            if (redirHash) {
                window.location.hash = redirHash;
            }
        }
    })
}


/**
 * This function show a message with HTML tags on Message área.
 * @param {string} Message
 * @param {string} Type
 */
function showMessageHTML(Message, Type) {
    Swal.fire({
        icon: Type,
        showConfirmButton: true,
        confirmButtonText: 'Aceptar',
        allowOutsideClick: false,
        html: Message
    });
}

/**
 * This function show a message and redirect to #hashPage
 * @param {string} Message
 * @param {string} Type
 * @param {string} HashName
 */
function showMessageRedir(Message, Type, HashName) {
    Swal.fire({
        title: Message,
        icon: Type,
        showConfirmButton: true,
        confirmButtonText: 'Aceptar',
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.hash = HashName;
        }
    });
}

/**
 * Shows a default confirmation modal.
 * @return {Promise<unknown>}
 */
function confirmModal() {
    return new Promise(resolve => {
        Swal.fire({
            title: '¿Está seguro que desea eliminar este registro?',
            text: "No podrá revertir esta acción.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí',
            cancelButtonText: 'Cancelar',
            showClass: {
                backdrop: 'swal2-with-backdrop'
            }
        }).then((result) => {
            swal.close();
            resolve(!!result.value);
        })
    });
}

/**
 * Shows a customizable confirmation modal.
 */
function customConfirmModal(question = '¿Está seguro que desea realizar esta acción?', description = 'Recuerde que no podrá revetir esta accción.', icon = 'question', backgroundColor = bgColorMode, fontcolor = fontColorMode) {
    return new Promise(resolve => {
        Swal.fire({
            title: `<span style="color: ${fontcolor}">${question}</span>`,
            html: `<span style="color: ${fontcolor}">${description}</span>`,
            icon: icon,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            background: backgroundColor,
            color: 'white',
            confirmButtonText: 'Sí',
            cancelButtonText: 'No',
            showClass: {
                backdrop: 'swal2-with-backdrop'
            }
        }).then((result) => {
            swal.close();
            resolve(!!result.value);
        })
    });
}

/**
 * Will logout from dashboard.
 */
function logout() {
    window.location.href = './components/logout.php';
}

function type(object) {
    let stringConstructor = "test".constructor;
    let arrayConstructor = [].constructor;
    let objectConstructor = {}.constructor;
    if (object === null) {
        return "null";
    } else if (object === undefined) {
        return "undefined";
    } else if (object.constructor === stringConstructor) {
        return "String";
    } else if (object.constructor === arrayConstructor) {
        return "Array";
    } else if (object.constructor === objectConstructor) {
        return "Object";
    } else {
        return "null";
    }
}

/**
 * Will get final response  from getResponseFromJSON function, second position [1]
 * @param {Object} jsonObj
 * @param {string} keyName
 * @return {Object}
 */
function getResponse(jsonObj, keyName) {
    let response = getResponseFromJSON(jsonObj, keyName);
    return (response === null) ? null : response[1];
}

/**
 * Will get a key from a JsonObject as array
 * @param {Object} jsonObj
 * @param {string} keyName
 * @return {Array}
 */
function getResponseFromJSON(jsonObj, keyName) {
    for (let key in jsonObj) {
        let value = jsonObj[key];
        if (keyName === key) return [keyName, value];
        if (typeof (value) === "object" && !Array.isArray(value)) {
            let y = getResponseFromJSON(value, keyName);
            if (y && y[0] === keyName) return y;
        }
        if (Array.isArray(value)) {
            for (let i in value) {
                let x = getResponseFromJSON(value[i], keyName);
                if (x && x[0] === keyName) return x;
            }
        }
    }
    return null;
}

/**
 * Will set a value to an input element.
 * @param {string} id
 * @param {*} value
 */
function set(id, value, useName = false) {
    if (useName) {
        document.querySelector(`[name='${id}']`).value = value;
        return;
    }
    document.getElementById(id).value = value;
}

/**
 * Will get a input and return their value.
 * @param {string} id
 * @param {string} type
 */
function get(id, type = 'string') {
    switch (type) {
        case 'string':
            return document.getElementById(id).value;
        case 'int':
            return parseInt(document.getElementById(id).value);
        default:
            break;
    }
}

function getAttribute(elementID, attribute) {
    return document.getElementById(elementID).getAttribute(attribute);
}

function setAttribute(elementID, attribute, value, containerId) {
    if (containerId) {
        let container = document.getElementById(containerId)
        let elements = container.getElementsByTagName('button');
        let findState = true;
        for (let currentElement of elements) {
            if (currentElement.id === elementID) {
                currentElement.setAttribute(attribute, value);
                findState = true;
                return;
            }
            findState = false;
        }
        if (!findState) {
            showMessage('Lo sentimos, no hemos podido establecer el atributo debido a que no fue encontrado el elemento al que se desea ubicar el atributo.', 'error')
        }
        return;
    }
    document.getElementById(elementID).setAttribute(attribute, value);
}

function getDOM(elementID) {
    return document.getElementById(elementID);
}

function cleanContainer(elementID) {
    document.getElementById(elementID).innerHTML = '';
}

/**
 * This function will show a toast on the screen.
 * @param {string} message
 * @param {string} type
 * @param {number} time
 * @param {string} position
 * @param {string} backgroundColor
 * @param {string} titleColor
 * @param {string} barColor
 */
function showToast(message, type = 'success', time = 3000, position = 'bottom-end', backgroundColor = '#212121', titleColor = 'white', barColor = '#028DE5') {
    const Toast = Swal.mixin({
        toast: true,
        position: position,
        showConfirmButton: false,
        timer: time,
        timerProgressBar: true,
        onOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
            document.querySelector('.swal2-timer-progress-bar').style.backgroundColor = barColor
        }
    })

    Toast.fire({
        icon: type,
        background: backgroundColor,
        title: `<span style="color: ${titleColor};">${message}<span>`
    })
}

function uuid() {
    let uuid = "", i, random;
    for (i = 0; i < 32; i++) {
        random = Math.random() * 16 | 0;
        if (i === 8 || i === 12 || i === 16 || i === 20) {
            uuid += "-";
        }
        uuid += (i === 12 ? 4 : (i === 16 ? (random & 3 | 8) : random)).toString(16);
    }
    return uuid;
}

function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function appendContent(idElementToAppend, content, scroll = false, scrollElementID) {
    $('#' + idElementToAppend).append(content);
    if (scroll) {
        $("#" + scrollElementID).animate({scrollTop: $('#' + scrollElementID).prop("scrollHeight")}, 1000);
    }
}


function cleanAndFocus(elementID) {
    document.getElementById(elementID).innerHTML = '';
    document.getElementById(elementID).focus();
}

function parseName(input) {
    let name_splitted = input.split(" ");
    firstName = name_splitted[0].toLowerCase();
    lastName = name_splitted[2].toLowerCase();
    return firstName.charAt(0).toUpperCase() + firstName.slice(1) + " " + lastName.charAt(0).toUpperCase() + lastName.slice(1);
}

function printDataTable() {

    document.querySelector('.buttons-print').click();
}

function printDataTableSecondary() {
    document.querySelector('.btn-print-secondary').click();
}

function toggleLoaderContainer(containerId) {
    if (document.getElementById(containerId).classList.contains('block-mode-loading-custom')) {
        document.getElementById(containerId).classList.remove('block', 'block-rounded');
        document.getElementById(containerId).classList.remove('block-mode-loading-custom');
    } else {
        document.getElementById(containerId).classList.add('block', 'block-rounded');
        document.getElementById(containerId).classList.add('block-mode-loading-custom');
    }
}

function openPdfNewTab(pdfUrl) {
    window.open(`${config.pdfViewerPath}${pdfUrl}`, '_blank').focus();
}

function openNewPageBlank(url) {
    window.open(url, '_blank').focus();
}

function getElementFromString(HTMLString, IdString) {
    let result, temp = document.createElement('div');
    temp.innerHTML = HTMLString;
    result = temp.querySelector('#' + IdString).outerHTML;
    return result;
}

function downloadFileFormat(formatFile, element) {
    let a = document.createElement("a");
    a.href = config['format_files'] + formatFile;
    a.setAttribute("download", formatFile);
    a.click();
    element.classList.add('disabled');
    setTimeout(function () {
        element.classList.remove('disabled');
    }, 3000);
}

function isJson(item) {
    item = typeof item !== "string" ? JSON.stringify(item) : item;
    try {
        item = JSON.parse(item);
    } catch (e) {
        return false;
    }

    return typeof item === "object" && item !== null;
}

function initDataTable(tableId) {
    $(`#${tableId}`).DataTable({
        "language": {
            "url": config['jsonPath'] + "Spanish.json"
        },
        "columnDefs": [{
            "targets": 'no-sort',
            "orderable": false,
        }]
    });
}

function destroyDataTable(tableId) {
    $(`#${tableId}`).DataTable().destroy();
}

function updateDataTable(tableId, customOptions = false) {
    if (customOptions) {
        if (isJson(customOptions)) {
            $(`#${tableId}`).DataTable(customOptions).draw();
        } else {
            showMessage('custom options are not valid JSON', 'error');
        }
    } else {
        $(`#${tableId}`).DataTable({
            columnDefs: [{
                "targets": 'no-sort',
                "orderable": false,
            }]
        }).draw();
    }
}

function beginDownload(blob, filename) {
    if (window.navigator.msSaveOrOpenBlob) {
        window.navigator.msSaveOrOpenBlob(blob, filename);
    } else {
        const a = document.createElement('a');
        document.body.append(a);
        const url = window.URL.createObjectURL(blob);
        a.href = url;
        a.download = filename;
        a.click();
        setTimeout(() => {
            Swal.close();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        }, 0);
    }
}

function createToolTip(element, ...params) {
    tippy(element, {
        content: `${params[0]}`,
    });
}

function updateTooltip(element, ...params) {
    (element)._tippy.setContent(`${params[0]}`);
}

function createToolTipMultiple(elementsClass = 'tippy-tooltip', elementsAttribute = 'data-tooltip') {
    tippy(`.${elementsClass}`, {
        content: (element) => {
            return element.getAttribute(elementsAttribute);
        },
    });
}

function initBootstrapTooltip() {
    $('[data-toggle="tooltip"]').tooltip({
        trigger: "manual"
    });

    $('[data-toggle="tooltip"]').on('mouseleave', function (e) {
        if(e.target.tagName.toLowerCase()==='i'){
            e.stopPropagation();
        }
        $(this).tooltip('hide');
    });

    $('[data-toggle="tooltip"]').on('mouseenter', function (e) {
        if(e.target.tagName.toLowerCase()==='i'){
            e.stopPropagation();
        }
        $(this).tooltip('show');
    });

    $('[data-toggle="tooltip"]').on('click', function () {
        $(this).tooltip('hide');
    });
}

function autoFillSelect(data, textKey, valueKey, element, selectedOption = false, selectedDefault = false) {
    element.innerHTML = "";
    let optionDefault = document.createElement('option');
    optionDefault.textContent = "Seleccione una opción...";
    optionDefault.value = "0";
    optionDefault.disabled = true;
    if (selectedDefault) {
        optionDefault.selected = true;
    }
    element.appendChild(optionDefault);
    for (let item of data) {
        let optionToInsert = document.createElement('option');
        optionToInsert.textContent = item[textKey];
        optionToInsert.value = item[valueKey];
        if (selectedOption) {
            if (selectedOption === item.id) {
                optionToInsert.selected = true;
            }
        }
        element.appendChild(optionToInsert);
    }
}

function copyToClipboard(content) {
    if (content) {
        navigator.clipboard.writeText(content)
            .then(() => {
                showMessage('Su enlace ha sido copiado exitosamente', 'success')
            })
            .catch(err => {
                showMessage("Ha ocurrido un error al copiar al portapeles.", "error");
            })
    }
}

function fullCustomConfirmModal(
    question = '¿Que acción desea realizar?',
    description = 'Recuerde que la acción que seleccione y apruebe no podrá ser revertida.',
    icon = 'question',
    firstButtonText = '',
    secondButtonText = '',
    thirdButtonText = '',
    fourthButtonText = '',
    firstButtonAction = () => {
    },
    secondButtonAction = () => {
    },
    thirdButtonAction = () => {
    },
    fourthButtonAction = () => {
    }
) {
    let buttonTemplate = '';
    if (firstButtonText !== '') {
        buttonTemplate += `<button type="button" class="mr-1 btn btn-first btn-primary">${firstButtonText}</button>`;
    }
    if (secondButtonText !== '') {
        buttonTemplate += `<button type="button" class="mr-1 btn btn-second btn-danger">${secondButtonText}</button>`;
    }
    if(thirdButtonText !==''){
        buttonTemplate += `<button type="button" class="mr-1 btn btn-third btn-success" >${thirdButtonText}</button>`;
    }
    if(fourthButtonText !==''){
        buttonTemplate += `<button type="button" class="mr-1 btn btn-fourth btn-secondary">${fourthButtonText}</button>`;
    }
    return new Promise(resolve => {
        Swal.fire({
            title: `<span>${question}</span>`,
            html: `<span>${description}</span>  
                   <br> <br> <br> 
                   ${buttonTemplate}
                    `,
            icon: icon,
            showCancelButton: false,
            showConfirmButton: false,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            color: 'white',
            showClass: {
                backdrop: 'swal2-with-backdrop'
            },
            didOpen: () => {
                if(firstButtonText!==''){
                    const first = document.querySelector('.btn-first')
                    first.addEventListener('click', () => {
                        firstButtonAction();
                    })
                }

                if(secondButtonText!==''){
                    const second = document.querySelector('.btn-second')
                    second.addEventListener('click', () => {
                        secondButtonAction();
                    })
                }

                if(thirdButtonText!==''){
                    const third = document.querySelector('.btn-third')
                    third.addEventListener('click', () => {
                        thirdButtonAction();
                    })
                }

                if(fourthButtonText!==''){
                    const fourth = document.querySelector('.btn-fourth')
                    fourth.addEventListener('click', () => {
                        fourthButtonAction();
                    })
                }

            }
        }).then((result) => {
            swal.close();
            resolve(!!result.value);
        })
    });
}