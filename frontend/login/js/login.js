async function iniciarSesion() {
    let jsonRQ = new jsonRequest('Authentication', 'login');
    jsonRQ.addAuto(false, async () => {
        showLoading('Iniciando sesión...');
        await jsonRQ.makeServerQuery((response) => {
            const token = getResponse(response, 'token');
            setCookie('token', token);
            setTimeout(() => {
                Swal.close();
                window.location.href = '../dashboard';
            }, 500);
        });
    }, 'login');
}

async function registrarse() {
    let jsonRQ = new jsonRequest('Authentication', 'register');
    jsonRQ.addAuto(false, async () => {
        showLoading('Registrando usuario...');
        await jsonRQ.makeServerQuery((response) => {
            setTimeout(() => {
                Swal.close();
                showMessage(response.message);
            }, 500);
        });
    }, "registro");
}
