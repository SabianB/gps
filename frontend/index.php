<?php
chdir(__DIR__);
if (isset($_COOKIE['token'])) {
    chdir(__DIR__);
    header('Location: dashboard');
} else {
    chdir(__DIR__);
    header('Location: login');
}
