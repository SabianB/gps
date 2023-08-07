<?php
session_destroy();
setcookie("token", "", time() - 3600, '/');
header('Location: ../../login/');