<?php
// www/generar.php
require 'vendor/autoload.php';

use OpenApi\Annotations as OA;

// 1. Creamos el objeto principal VACÍO
$openapi = new OA\OpenApi([]);

// 2. Metemos la Información General a mano
$openapi->openapi = '3.0.0';
$openapi->info = new OA\Info([
    'title' => 'API Burguer Marina',
    'version' => '1.0.0',
    'description' => 'Documentación forzada manualmente'
]);

$openapi->servers = [
    new OA\Server(['url' => 'http://localhost:8080/api', 'description' => 'Docker Local'])
];

// 3. Metemos la Seguridad a mano
$openapi->components = new OA\Components([
    'securitySchemes' => [
        new OA\SecurityScheme([
            'securityScheme' => 'bearerAuth',
            'type' => 'http',
            'scheme' => 'bearer',
            'bearerFormat' => 'JWT'
        ])
    ]
]);

// 4. AQUÍ ESTÁ LA CLAVE: Metemos el endpoint /platos A MANO
// Pasamos de leer platos.php. Lo definimos aquí y punto.
$pathPlatos = new OA\PathItem(['path' => '/platos']);
$getPlatos = new OA\Get([
    'tags' => ['Platos'],
    'summary' => 'Listar platos',
    'security' => [], // Público
    'responses' => [
        new OA\Response([
            'response' => 200, 
            'description' => 'Lista de platos recuperada'
        ])
    ]
]);
$pathPlatos->get = $getPlatos;

// Añadimos el path al objeto principal
$openapi->paths = [
    $pathPlatos
];

// 5. Guardamos
file_put_contents(__DIR__ . '/openapi.json', $openapi->toJson());

echo "✅ JSON CREADO A LA FUERZA. SI ESTO FALLA ME RETIRO.\n";
?>