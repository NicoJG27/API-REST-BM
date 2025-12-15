<?php
class PlatosControlador
{
    private PlatosModelo $modelo;

    public function __construct(PlatosModelo $modelo)
    {
        $this->modelo = $modelo;
    }

    // GET /api/platos
    public function listar(int $pagina, int $limite, ?string $busqueda, string $orden, string $dir): array
    {
        // Aquí solo delegamos en el modelo.
        // Por defecto, código HTTP 200 OK.
        return $this->modelo->obtenerTodos($pagina, $limite, $busqueda, $orden, $dir);
    }

    // POST /api/platos
    public function crear(array $data, ?int $userId = null): array
    {
        // Validar campos obligatorios (Nombre, Precio y Categoría son vitales)
        if (empty($data["nombre"]) || empty($data["precio"]) || empty($data["id_categoria"])) {
            // Error del cliente → 400 Bad Request
            http_response_code(400);
            logApi('WARN', 'platos', "POST fallido: datos incompletos", $userId);
            return ["error" => "Nombre, precio e id_categoria son obligatorios"];
        }

        // Preparamos los datos (si no envían descripción o imagen, ponemos cadena vacía)
        $descripcion = $data["descripcion"] ?? "";
        $imagen      = $data["imagen"] ?? "";

        // Intentar insertar en la base de datos
        if ($this->modelo->insertar($data["nombre"], $descripcion, $data["precio"], $imagen, $data["id_categoria"])) {
            // Recurso creado → 201 Created
            http_response_code(201);
            logApi('INFO', 'platos', "POST: plato '{$data["nombre"]}' creado", $userId);
            return ["mensaje" => "Plato creado correctamente"];
        }

        // Algo ha fallado en el servidor → 500 Internal Server Error
        http_response_code(500);
        logApi('ERROR', 'platos', "POST: error al insertar plato", $userId);
        return ["error" => "No se pudo insertar el plato"];
    }

    // PUT /api/platos?id=5
    public function actualizar(?int $id, array $data, ?int $userId = null): array
    {
        // Validar que se ha pasado un id
        if ($id === null || $id <= 0) {
            http_response_code(400); // Petición incorrecta
            logApi('WARN', 'platos', "PUT fallido: id inválido", $userId);
            return ["error" => "Debe indicar un id de plato válido"];
        }

        // Validar datos mínimos
        if (empty($data["nombre"]) || empty($data["precio"]) || empty($data["id_categoria"])) {
            http_response_code(400); // Datos incompletos
            logApi('WARN', 'platos', "PUT $id fallido: datos incompletos", $userId);
            return ["error" => "Nombre, precio e id_categoria son obligatorios para actualizar"];
        }

        $descripcion = $data["descripcion"] ?? "";
        $imagen      = $data["imagen"] ?? "";

        // Llamamos al modelo
        $actualizado = $this->modelo->actualizar($id, $data["nombre"], $descripcion, $data["precio"], $imagen, $data["id_categoria"]);

        if ($actualizado === true) {
            // Actualización correcta → 200 OK
            http_response_code(200);
            logApi('INFO', 'platos', "PUT $id: actualizado", $userId);
            return ["mensaje" => "Plato actualizado correctamente"];
        }

        if ($actualizado === null) {
            // No existe ese id
            http_response_code(404); // No encontrado
            logApi('WARN', 'platos', "PUT $id: no encontrado", $userId);
            return ["error" => "Plato no encontrado"];
        }

        // Error interno
        http_response_code(500);
        logApi('ERROR', 'platos', "PUT $id: error al actualizar", $userId);
        return ["error" => "No se pudo actualizar el plato"];
    }

    // DELETE /api/platos?id=5
    public function eliminar(?int $id, ?int $userId = null): array
    {
        // Validar id
        if ($id === null || $id <= 0) {
            http_response_code(400); // Petición incorrecta
            logApi('WARN', 'platos', "DELETE fallido: id inválido", $userId);
            return ["error" => "Debe indicar un id de plato válido"];
        }

        // Delegamos en el modelo
        $eliminado = $this->modelo->eliminar($id);

        if ($eliminado === true) {
            // Eliminado correctamente
            http_response_code(200);
            logApi('INFO', 'platos', "DELETE $id: eliminado", $userId);
            return ["mensaje" => "Plato eliminado correctamente"];
        }

        if ($eliminado === null) {
            // Plato no encontrado
            http_response_code(404);
            logApi('WARN', 'platos', "DELETE $id: no encontrado", $userId);
            return ["error" => "Plato no encontrado"];
        }

        // Error interno
        http_response_code(500);
        logApi('ERROR', 'platos', "DELETE $id: error al eliminar", $userId);
        return ["error" => "No se pudo eliminar el plato"];
    }

    // GET /api/platos/5
    public function ver(?int $id): array
    {
        // Validar el id
        if ($id === null || $id <= 0) {
            http_response_code(400);
            return ["error" => "Debe indicar un id de plato válido"];
        }

        // Pedir el plato al modelo
        $plato = $this->modelo->obtenerPorId($id);

        if ($plato === null) {
            http_response_code(404);
            return ["error" => "Plato no encontrado"];
        }

        // Plato encontrado
        http_response_code(200);
        return $plato;
    }

    // GET /api/platos/5/detallado (REQUISITO PROYECTO)
    public function verDetallado(?int $id): array
    {
        if ($id === null || $id <= 0) {
            http_response_code(400);
            return ["error" => "Debe indicar un id válido"];
        }

        // Usamos el método con JOIN del modelo
        $plato = $this->modelo->obtenerDetallado($id);

        if ($plato === null) {
            http_response_code(404);
            return ["error" => "Plato no encontrado"];
        }

        http_response_code(200);
        return $plato;
    }

    //total de platos
    public function estadisticas(): array
    {
        $total = $this->modelo->contarTodos();
        http_response_code(200);
        return ['total' => (int)$total];
    }
}
