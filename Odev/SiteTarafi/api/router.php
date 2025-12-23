<?php

header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: https://okanhoca.online");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

require_once __DIR__ . "/db.php";
require_once __DIR__ . "/response.php";

/*
  action hem GET hem POST’tan okunur
  Böylece eski JS kodun da bozulmaz
*/
$action = $_GET["action"]
       ?? $_POST["action"]
       ?? "";

switch ($action) {

    case "giris":
        require __DIR__ . "/giris.php";
        break;

    case "oda":
        require __DIR__ . "/oda.php";
        break;

    case "hamle":
        require __DIR__ . "/hamle.php";
        break;

    default:
        error("Geçersiz API çağrısı");
}
