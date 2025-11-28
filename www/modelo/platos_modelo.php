<?php
class PlatosModelo
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Obtener todos los platos.
     *
     * @return array Lista de platos.
     */
    public function obtenerTodos(int $pagina = 1, int $limite = 10): array

    {
        $offset = ($pagina - 1) * $limite;
        $sql = "SELECT id_plato, nombre, precio, descripcion, imagen, id_categoria 
                FROM Platos
                LIMIT :limite OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Insertar un nuevo plato.
     *
     * @return bool true si se ha insertado correctamente.
     */
    public function insertar(string $nombre, string $descripcion, float $precio, string $imagen, int $id_categoria): bool
    {
        $sql = "INSERT INTO Platos (nombre, descripcion, precio, imagen, id_categoria)
                VALUES (:nombre, :descripcion, :precio, :imagen, :id_categoria)";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':precio', $precio);
        $stmt->bindParam(':imagen', $imagen);
        $stmt->bindParam(':id_categoria', $id_categoria);

        return $stmt->execute();
    }

    /**
     * Actualizar un plato existente.
     *
     * @return bool|null
     */
    public function actualizar(int $id, string $nombre, string $descripcion, float $precio, string $imagen, int $id_categoria): ?bool
    {
        $sql = "UPDATE Platos 
                SET nombre = :nombre, 
                    descripcion = :descripcion, 
                    precio = :precio, 
                    imagen = :imagen, 
                    id_categoria = :id_categoria
                WHERE id_plato = :id";

        try {
            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":descripcion", $descripcion);
            $stmt->bindParam(":precio", $precio);
            $stmt->bindParam(":imagen", $imagen);
            $stmt->bindParam(":id_categoria", $id_categoria);
            $stmt->bindParam(":id", $id);

            $stmt->execute();

            $filas = $stmt->rowCount();

            if ($filas === 0) {
                return null; // No hubo cambios o no existe el ID
            }
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Eliminar un plato.
     */
    public function eliminar(int $id): ?bool
    {
        $sql = "DELETE FROM Platos WHERE id_plato = :id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();

            $filas = $stmt->rowCount();

            if ($filas === 0) {
                return null;
            }

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Obtener un único plato por id.
     */
    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT * FROM Platos WHERE id_plato = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $plato = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($plato === false) {
            return null;
        }

        return $plato;
    }

    /**
     * REQUISITO DEL PROYECTO: Endpoint Detallado con JOIN.
     * 
     */
    public function obtenerDetallado(int $id): ?array
    {
        $sql = "SELECT p.*, c.nombre as nombre_categoria 
                FROM Platos p
                INNER JOIN Categorias c ON p.id_categoria = c.id_categoria
                WHERE p.id_plato = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $plato = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($plato === false) {
            return null;
        }

        return $plato;
    }
}
