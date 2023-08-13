
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>GPS Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="" name="description" />
    <meta content="" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="">

    <!-- App css -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/app.min.css" rel="stylesheet" type="text/css" />

</head>

<body class="account-body accountbg">

<!-- Log In page -->
<div class="container">
    <div class="row vh-100 d-flex justify-content-center">
        <div class="col-12 align-self-center">
            <div class="row">
                <div class="col-lg-5 mx-auto">
                    <div class="card">
                        <div class="card-body p-0 auth-header-box">
                            <div class="text-center p-3">
                                <h4 class="mt-3 mb-1 fw-semibold text-white font-18">Dashboard de mi GPS</h4>
                                <p class="text-muted  mb-0">Inicie sesión para continuar al panel del gps.</p>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <ul class="nav-border nav nav-pills" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active fw-semibold" data-bs-toggle="tab" href="#LogIn_Tab" role="tab">Iniciar Sesión</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#Register_Tab" role="tab">Registrarse</a>
                                </li>
                            </ul>
                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div class="tab-pane active p-3" id="LogIn_Tab" role="tabpanel">
                                    <div class="form-horizontal auth-form" id="login">

                                        <div class="form-group mb-2">
                                            <label class="form-label" for="correo">Correo</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control required avoid-numeric" name="correo" id="correo" placeholder="Ingrese su correo">
                                            </div>
                                        </div><!--end form-group-->

                                        <div class="form-group mb-2">
                                            <label class="form-label" for="clave">Contraseña</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control required avoid-numeric" name="clave" id="clave" placeholder="Ingrese su contraseña">
                                            </div>
                                        </div><!--end form-group-->

                                        <div class="form-group mb-0 row">
                                            <div class="col-12">
                                                <button class="btn btn-primary w-100 waves-effect waves-light" type="button" onclick="iniciarSesion()">
                                                    Iniciar  Sesión <i class="fas fa-sign-in-alt ms-1"></i>
                                                </button>
                                            </div><!--end col-->
                                        </div> <!--end form-group-->
                                    </div><!--end form-->
                                </div>
                                <div class="tab-pane p-3" id="Register_Tab" role="tabpanel">
                                    <div class="form-horizontal auth-form" id="registro">

                                        <div class="form-group mb-2">
                                            <label class="form-label" for="nombres">Nombres</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control required avoid-numeric" name="nombres" id="nombres" placeholder="Ingrese sus nombres">
                                            </div>
                                        </div><!--end form-group-->

                                        <div class="form-group mb-2">
                                            <label class="form-label" for="apellidos">Apellidos</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control required avoid-numeric" name="apellidos" id="apellidos" placeholder="Ingrese sus apellidos">
                                            </div>
                                        </div><!--end form-group-->

                                        <div class="form-group mb-2">
                                            <label class="form-label" for="correo">Correo</label>
                                            <div class="input-group">
                                                <input type="email" class="form-control required avoid-numeric" name="correo" id="correo" placeholder="Ingrese su correo">
                                            </div>
                                        </div><!--end form-group-->

                                        <div class="form-group mb-2">
                                            <label class="form-label" for="clave">Contraseña</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control required avoid-numeric" name="clave" id="clave" placeholder="Ingrese una contraseña">
                                            </div>
                                        </div><!--end form-group-->

                                        <div class="form-group mb-2">
                                            <label class="form-label" for="clave2">Verificar Contraseña</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control required avoid-numeric" name="clave2" id="clave2" placeholder="Repita la contraseña">
                                            </div>
                                        </div><!--end form-group-->



                                        <div class="form-group mb-0 row">
                                            <div class="col-12">
                                                <button class="btn btn-primary w-100 waves-effect waves-light" type="button" onclick="registrarse()">Registrarse<i class="fas fa-sign-in-alt ms-1"></i></button>
                                            </div><!--end col-->
                                        </div> <!--end form-group-->
                                    </div><!--end form-->
                                </div>
                            </div>
                        </div><!--end card-body-->
                        <div class="card-body bg-light-alt text-center">
                                    <span class="text-muted d-none d-sm-inline-block">Unesum© <script>
                                        document.write(new Date().getFullYear())
                                    </script></span>
                        </div>
                    </div><!--end card-->
                </div><!--end col-->
            </div><!--end row-->
        </div><!--end col-->
    </div><!--end row-->
</div><!--end container-->
<!-- End Log In page -->




<!-- jQuery  -->
<script src="../assets/js/jquery.min.js"></script>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/waves.js"></script>
<script src="../assets/js/feather.min.js"></script>
<script src="../assets/js/simplebar.min.js"></script>
<script src="../assets/js/sweetAlert/sweetalert2.all.min.js"></script>

<!-- App js  -->
<script src="js/login.js"></script>
<script src="../js/config.js"></script>
<script src="../js/empiric_frontend_framework.js"></script>



</body>

</html>