<?php
$host = $_SERVER['HTTP_HOST'];
$protocol = isset($_SERVER["HTTPS"]) ? 'https' : 'http';
$folderStructure = "gps";
$config = [
    'serverApi' => "$protocol://$host/$folderStructure/backend/api.php",
    'resources' => "$protocol://$host/$folderStructure/backend/media.php?code=",
    'dashboard' => "$protocol://$host/$folderStructure/frontend/dashboard",
    'login' =>"$protocol://$host/$folderStructure/frontend/login/",
];