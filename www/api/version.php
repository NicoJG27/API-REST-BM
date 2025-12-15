<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");

// Endpoint de versión de la API (público)

http_response_code(200);
echo json_encode([
    "version" => "1.0.0",
    "descripcion" => "API REST para gestión de platos de Burguer Marina",
    "fecha" => "2025-12-15"
]);
