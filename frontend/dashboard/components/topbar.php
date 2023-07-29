<?php
global $nombres;
?>


<!-- Top Bar Start -->
<div class="topbar">
    <!-- Navbar -->
    <nav class="navbar-custom">
        <ul class="list-unstyled topbar-nav float-end mb-0">
            <li class="dropdown">
                <a class="nav-link dropdown-toggle waves-effect waves-light nav-user" data-bs-toggle="dropdown"
                   href="#" role="button"
                   aria-haspopup="false" aria-expanded="false">
                    <span class="ms-1 nav-user-name hidden-sm"><?= $nombres ?> </span>
                    <i class="fas fa-angle-down"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="#">
                        <i data-feather="user" class="align-self-center icon-xs icon-dual me-1"></i>
                        Perfíl
                    </a>

                    <div class="dropdown-divider mb-0">

                    </div>
                    <a class="dropdown-item" href="#login">
                        <i data-feather="power" class="align-self-center icon-xs icon-dual me-1">

                        </i>
                        Cerrar Sesión
                    </a>
                </div>
            </li>
        </ul><!--end topbar-nav-->

        <ul class="list-unstyled topbar-nav mb-0">
            <li>
                <button class="nav-link button-menu-mobile">
                    <i data-feather="menu" class="align-self-center topbar-icon"></i>
                </button>
            </li>
        </ul>
    </nav>
    <!-- end navbar-->
</div>
<!-- Top Bar End -->