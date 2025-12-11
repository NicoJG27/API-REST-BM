<?php
// www/api/apidocs.php

// 1. ESTO TIENE QUE IR LO PRIMERO (Esta fue la clave del error anterior)
use OpenApi\Annotations as OA;

/**
 * 2. Configuración General
 * @OA\Info(
 * title="API Burguer Marina",
 * version="1.0.0",
 * description="¡Por fin funciona!"
 * )
 * @OA\Server(
 * url="http://localhost:8080/api",
 * description="Servidor Docker"
 * )
 * @OA\SecurityScheme(
 * securityScheme="bearerAuth",
 * type="http",
 * scheme="bearer",
 * bearerFormat="JWT"
 * )
 */

/**
 * 3. Definición del Endpoint (GET /platos)
 * @OA\Get(
 * path="/platos",
 * summary="Obtener lista de platos",
 * tags={"Platos"},
 * @OA\Response(
 * response=200,
 * description="Lista devuelta correctamente"
 * )
 * )
 */
class ApiDocs {} 
// Clase vacía necesaria para que el escáner detecte el archivo
?>