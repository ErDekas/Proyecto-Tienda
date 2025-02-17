<?php

namespace Repositories;

use Lib\BaseDatos;
use Models\Categorias;
use PDO;
use PDOException;
use Exception;

class CategoriasRepository
{
    private BaseDatos $conexion;

    public function __construct()
    {
        $this->conexion = new BaseDatos();
    }

    // Método para obtener todas las categorías
    public function obtenerCategorias(): array
    {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM categorias");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    // Método para obtener una categoría por su ID
    public function obtenerCategoriaPorId(Categorias $categoria): array
    {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM categorias WHERE id = :id");
            $stmt->bindParam(":id", $categoria->getId(), PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    // Método para insertar una categoría
    public function insertarCategoria(Categorias $categoria): bool
    {
        try {
            $stmt = $this->conexion->prepare("INSERT INTO categorias (nombre) VALUES (:nombre)");
            $stmt->bindValue(":nombre", $categoria->getNombre(), PDO::PARAM_STR);
            $stmt->execute();
            return true;
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    // Método para actualizar una categoría
    public function actualizarCategoria(Categorias $categoria, int $id): bool
    {
        try {
            $stmt = $this->conexion->prepare("UPDATE categorias SET nombre = :nombre WHERE id = :id");
            $stmt->bindValue(":nombre", $categoria->getNombre(), PDO::PARAM_STR);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $ex) {
            echo $ex->getMessage();
            return false;
        }
    }

    // Método para eliminar una categoría
    public function eliminarCategoria(Categorias $categoria): bool
    {
        try {
            $this->conexion->empezarTransaccion();

            // Verificar si hay productos en esta categoría
            $checkStmt = $this->conexion->prepare(
                "SELECT COUNT(*) FROM productos WHERE categoria_id = :id"
            );
            $checkStmt->bindValue(":id", $categoria->getId(), PDO::PARAM_INT);
            $checkStmt->execute();

            if ($checkStmt->fetchColumn() > 0) {
                // Buscar la categoría "Sin existencias"
                $sinExistenciasStmt = $this->conexion->prepare(
                    "SELECT id FROM categorias WHERE nombre = 'Sin existencias' LIMIT 1"
                );
                $sinExistenciasStmt->execute();

                $sinExistenciasId = $sinExistenciasStmt->fetchColumn();

                if (!$sinExistenciasId) {
                    throw new Exception("No existe la categoría 'Sin existencias'.");
                }

                // Actualizar los productos a la categoría "Sin existencias"
                $updateStmt = $this->conexion->prepare(
                    "UPDATE productos 
                    SET categoria_id = :new_id 
                    WHERE categoria_id = :old_id"
                );
                $updateStmt->bindValue(":new_id", $sinExistenciasId, PDO::PARAM_INT);
                $updateStmt->bindValue(":old_id", $categoria->getId(), PDO::PARAM_INT);
                $updateStmt->execute();
            }

            // Proceder con la eliminación de la categoría
            $deleteStmt = $this->conexion->prepare("DELETE FROM categorias WHERE id = :id");
            $deleteStmt->bindValue(":id", $categoria->getId(), PDO::PARAM_INT);
            $deleteStmt->execute();

            $this->conexion->commit();
            return true;
        } catch (PDOException $ex) {
            $this->conexion->deshacer();
            echo $ex->getMessage();
            return false;
        } catch (Exception $ex) {
            $this->conexion->deshacer();
            echo $ex->getMessage();
            return false;
        }
    }

    // Método para obtener listado de categorías por su nombre
    public function listadoCategoriasPorNombre(Categorias $categoria): array|bool
    {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM categorias WHERE nombre = :nombre");
            $stmt->bindParam(":nombre", $categoria->getNombre(), PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            echo $ex->getMessage();
            return false;
        }
    }
}
