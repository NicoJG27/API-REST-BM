<?php
// config.php
//Credenciales BD
const DB_DSN  = 'mysql:host=db;dbname=burguermarina;charset=utf8mb4';
const DB_USER = 'root';
const DB_PASS = 'root';
// Clave secreta para firmar los tokens JWT
define("JWT_SECRET", "asperones");