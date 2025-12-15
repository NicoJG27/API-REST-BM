# API REST Burguer Marina 🍔

API REST en PHP sin framework para gestión de platos y pedidos de hamburguesería con autenticación JWT.

---

## 📋 Tabla de Contenidos

- [Descripción](#descripción)
- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Autenticación JWT](#autenticación-jwt)
- [Endpoints](#endpoints)
- [Ejemplos de Uso](#ejemplos-de-uso)
- [Base de Datos](#base-de-datos)
- [Mejoras Implementadas](#mejoras-implementadas)
- [Testing](#testing)

---

## Descripción

API REST para gestión de platos de una hamburguesería. Incluye:
- **CRUD completo** de platos
- **Autenticación JWT** con roles (admin/user)
- **Búsqueda y filtrado** avanzado
- **Paginación** estable
- **Relación con categorías** mediante `?include=categorias`
- **Estadísticas** de platos
- **Endpoint de perfil** autenticado

---

## Requisitos

- **PHP 7.4+**
- **Apache** (con mod_rewrite habilitado)
- **MySQL/MariaDB**
- **Composer** (para autoload)
- **Firebase JWT** (incluido en vendor/)

---

## Instalación

### 1. Clonar o descargar el proyecto

```bash
git clone <tu-repo>
cd API-REST-BM
```

### 2. Configurar base de datos

Ejecutar el SQL de inicialización (ver [Base de Datos](#base-de-datos)):

```bash
mysql -u root -p < setup.sql
```

### 3. Configurar credenciales

Editar `www/config.php`:

```php
define('JWT_SECRET', 'tu_clave_secreta_aqui');
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'password');
define('DB_NAME', 'burger_marina');
```

Editar `www/database.php` si es necesario.

### 4. Instalar dependencias

```bash
cd www
composer install
```

### 5. Iniciar servidor

**Opción A: PHP built-in**
```bash
php -S localhost:8000 -t www/
```

**Opción B: Docker** (si existe Dockerfile)
```bash
docker-compose up -d
```

### 6. Verificar instalación

```
GET http://localhost:8000/api/platos
```

Deberías recibir un JSON con lista de platos.

---

## Estructura del Proyecto

```
API-REST-BM/
├── www/
│   ├── api/
│   │   ├── .htaccess           # Rutas amigables
│   │   ├── platos.php          # Endpoint CRUD platos
│   │   ├── login.php           # Autenticación (genera JWT)
│   │   ├── me.php              # Perfil del usuario autenticado
│   │   ├── auth.php            # Función requireAuth()
│   │   ├── logger.php           # Logging
│   │   ├── tester.html         # Cliente web para probar
│   │   └── platos_count.php    # Endpoint de conteo
│   ├── modelo/
│   │   └── platos_modelo.php   # Lógica BD (queries)
│   ├── controlador/
│   │   └── platos_controlador.php # Lógica de negocio
│   ├── config.php              # Configuración (JWT_SECRET, DB)
│   ├── database.php            # Conexión PDO
│   ├── openapi.yaml            # Documentación Swagger
│   ├── composer.json           # Dependencias (Firebase JWT)
│   └── vendor/                 # Dependencias (autoload, JWT)
├── README.md                   # Este archivo
├── Dockerfile                  # Contenedor Docker
└── docker-compose.yml          # Orquestación Docker
```

---

## Autenticación JWT

### Flujo

1. **Login**: `POST /api/login` con email + password
2. **Token**: API devuelve JWT válido por 1 hora
3. **Uso**: Incluir en header `Authorization: Bearer <token>`
4. **Validación**: `requireAuth()` valida y extrae datos del token

### Roles

- **admin**: Puede crear, editar y eliminar platos
- **user**: Solo puede crear platos
- **público**: Puede leer (GET) sin token

### Ejemplo

```bash
# 1. Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@test.com","password":"1234"}'

# Respuesta:
# {
#   "mensaje": "Login exitoso",
#   "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
#   "usuario": {"id": 1, "nombre": "Admin", "rol": "admin"}
# }

# 2. Usar token
curl -X GET http://localhost:8000/api/platos \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc..."
```

---

## Endpoints

### Platos (CRUD)

#### Listar platos (público)
```
GET /api/platos?page=1&limit=10&search=hamburguesa&order=precio&dir=ASC
```
**Parámetros**: `page`, `limit`, `search`, `order`, `dir`

#### Obtener detalle (público)
```
GET /api/platos/{id}
GET /api/platos/{id}?include=categorias
```

#### Crear plato (autenticado: user/admin)
```
POST /api/platos
Content-Type: application/json
Authorization: Bearer <token>

{
  "nombre": "Hamburguesa Doble",
  "precio": 12.50,
  "descripcion": "Doble carne, queso cheddar",
  "imagen": "burger.jpg",
  "id_categoria": 1
}
```

#### Actualizar plato (solo admin)
```
PUT /api/platos/{id}
Authorization: Bearer <token>

{
  "nombre": "Hamburguesa Deluxe",
  "precio": 14.99,
  "id_categoria": 1
}
```

#### Eliminar plato (solo admin)
```
DELETE /api/platos/{id}
Authorization: Bearer <token>
```

### Estadísticas

#### Total de platos
```
GET /api/platos/count
```
**Respuesta**: `{"total": 25}`

### Autenticación

#### Login
```
POST /api/login
Content-Type: application/json

{
  "email": "admin@test.com",
  "password": "1234"
}
```

#### Mi perfil (autenticado)
```
GET /api/me
Authorization: Bearer <token>
```
**Respuesta**: `{"id": 1, "email": "admin@test.com", "rol": "admin"}`

---

## Ejemplos de Uso

### Con Postman

1. **Importar colección**:
   - Abrir `File > Import`
   - Buscar `API-REST-BM-collection.json` (si existe)
   - O crear manualmente

2. **Crear variable de entorno**:
   - Token: `{{token}}`
   - URL base: `{{base_url}}` = `http://localhost:8000/api`

3. **Flujo típico**:
   - POST /login → obtener token
   - Guardar token en variable
   - GET /platos → listar
   - POST /platos → crear (con token)
   - PUT /platos/1 → editar
   - DELETE /platos/1 → eliminar

### Con curl

```bash
# Listar
curl http://localhost:8000/api/platos

# Con búsqueda
curl "http://localhost:8000/api/platos?search=hamburguesa&limit=5"

# Crear (requiere token)
curl -X POST http://localhost:8000/api/platos \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{"nombre":"Hamburguesa","precio":9.99,"id_categoria":1}'
```

### Con navegador

Abrir `http://localhost:8000/api/tester.html`:
- Cliente web interactivo
- Buttons rápidos: Listar, Conteo, Mi perfil
- Testing completo sin Postman

---

## Base de Datos

### SQL de inicialización

```sql
-- Crear base de datos
CREATE DATABASE IF NOT EXISTS burger_marina;
USE burger_marina;

-- Tabla de usuarios
CREATE TABLE Usuarios (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de categorías
CREATE TABLE Categorias (
    id_categoria INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

-- Tabla de platos
CREATE TABLE Platos (
    id_plato INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    descripcion TEXT,
    imagen VARCHAR(255),
    id_categoria INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_categoria) REFERENCES Categorias(id_categoria)
);

-- Datos de ejemplo
INSERT INTO Usuarios (nombre, email, password, rol) VALUES
('Admin', 'admin@test.com', '$2y$10$...', 'admin'),
('Usuario', 'user@test.com', '$2y$10$...', 'user');

INSERT INTO Categorias (nombre) VALUES
('Hamburguesas'),
('Bebidas'),
('Acompañamientos');

INSERT INTO Platos (nombre, precio, descripcion, id_categoria) VALUES
('Hamburguesa Clásica', 9.99, 'Carne, lechuga, tomate', 1),
('Coca Cola', 2.50, 'Refresco 33cl', 2),
('Papas Fritas', 3.50, 'Acompañamiento', 3);
```

**Nota**: Los passwords deben estar hasheados con `password_hash()`:
```php
$password = password_hash('1234', PASSWORD_BCRYPT);
```

---

## Mejoras Implementadas

Se han añadido **6 de 12** propuestas de mejora:

| # | Mejora | Estado | Descripción |
|---|--------|--------|-------------|
| 1 | 🔍 Búsqueda | ✅ | `?search=palabra` filtra por nombre/descripción |
| 2 | 🔢 Ordenación | ✅ | `?order=precio&dir=DESC` ordena resultados |
| 3 | 🔢 Conteo | ✅ | `GET /api/platos/count` → `{"total": n}` |
| 4 | 👤 Mi perfil | ✅ | `GET /api/me` devuelve datos del usuario JWT |
| 5 | 📝 Logs | ✅ | Fichero `logs/api.log` con registro de acciones |
| 6 | 🏷️ Versión API | ✅ | `GET /api/version` → `{"version": "1.0.0"}` |

---

## Testing

### Casos de prueba manuales

| # | Endpoint | Método | Auth | Esperado | Estado |
|---|----------|--------|------|----------|--------|
| T1 | /login | POST | ❌ | 200 + token | ✅ |
| T2 | /platos | GET | ❌ | 200 + array | ✅ |
| T3 | /platos/1 | GET | ❌ | 200 + objeto | ✅ |
| T4 | /platos/999 | GET | ❌ | 404 | ✅ |
| T5 | /platos | POST | ✅ user | 201 | ✅ |
| T6 | /platos | POST | ❌ | 401 | ✅ |
| T7 | /platos/1 | PUT | ✅ admin | 200 | ✅ |
| T8 | /platos/1 | PUT | ✅ user | 403 | ✅ |
| T9 | /platos/1 | DELETE | ✅ admin | 200 | ✅ |
| T10 | /platos/1 | DELETE | ✅ user | 403 | ✅ |
| T11 | /platos?search=ham | GET | ❌ | 200 + filtered | ✅ |
| T12 | /platos?order=precio&dir=DESC | GET | ❌ | 200 + sorted | ✅ |
| T13 | /platos/count | GET | ❌ | 200 + {"total": n} | ✅ |
| T14 | /me | GET | ✅ | 200 + user data | ✅ |
| T15 | /me | GET | ❌ | 401 | ✅ |

### Herramientas recomendadas

- **Postman**: Colección importable
- **Swagger UI**: `http://localhost:8000/api/openapi.yaml`
- **curl**: Terminal
- **tester.html**: Navegador (cliente web)

---

## Documentación

- **OpenAPI**: `www/openapi.yaml` (compatible con Swagger UI)
- **Código**: Comentarios en cada archivo
- **Rúbrica**: Ver `RUBRICA.md` (si existe)

---

## Autor

Proyecto API REST - Curso de Desarrollo Web

**Fecha**: Diciembre 2025  
**Estado**: Completado ✅  
**Versión**: 1.0.0
