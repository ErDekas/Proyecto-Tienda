<?php

namespace Repositories;

use Lib\BaseDatos;
use Models\Productos;
use PDO;
use PDOException;
use InvalidArgumentException;
use Exception;

class ProductosRepository
{
    private BaseDatos $conexion;

    public function __construct()
    {
        $this->conexion = new BaseDatos();
    }

    // Método para obtener todos los productos
    public function obtenerProductos(): array
    {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM productos");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    // Método para obtener un producto por su ID
    public function obtenerProductoPorId(Productos $productos): array
    {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM productos WHERE id = :id");
            $stmt->bindParam(":id", $productos->getId(), PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    // Método para insertar un producto
    public function insertarProducto(Productos $productos): bool
    {
        try {
            $stmt = $this->conexion->prepare("INSERT INTO productos (nombre, descripcion, precio, categoria_id, stock, oferta, fecha, imagen) VALUES (:nombre, :descripcion, :precio, :categoria_id, :stock, :oferta, :fecha, :imagen)");
            $stmt->bindParam(":nombre", $productos->getNombre(), PDO::PARAM_STR);
            $stmt->bindParam(":descripcion", $productos->getDescripcion(), PDO::PARAM_STR);
            $stmt->bindParam(":precio", $productos->getPrecio(), PDO::PARAM_STR);
            $stmt->bindParam(":categoria_id", $productos->getCategoriaId(), PDO::PARAM_INT);
            $stmt->bindParam(":stock", $productos->getStock(), PDO::PARAM_INT);
            $stmt->bindParam(":oferta", $productos->getOferta(), PDO::PARAM_STR);
            $stmt->bindParam(":fecha", $productos->getFecha(), PDO::PARAM_STR);
            $stmt->bindParam(":imagen", $productos->getImagen(), PDO::PARAM_STR);
            $stmt->execute();
            return true;
        } catch (PDOException $ex) {
            echo $ex->getMessage();
            return false;
        }
    }

    // Método para actualizar un producto
    public function actualizarProducto(Productos $productos): bool
    {
        try {
            $stmt = $this->conexion->prepare("UPDATE productos SET nombre = :nombre, descripcion = :descripcion, precio = :precio, categoria_id = :categoria_id, stock = :stock, oferta = :oferta, fecha = :fecha, imagen = :imagen WHERE id = :id");
            $stmt->bindValue(":nombre", $productos->getNombre(), PDO::PARAM_STR);
            $stmt->bindValue(":descripcion", $productos->getDescripcion(), PDO::PARAM_STR);
            $stmt->bindValue(":precio", $productos->getPrecio(), PDO::PARAM_STR);
            $stmt->bindValue(":categoria_id", $productos->getCategoriaId(), PDO::PARAM_INT);
            $stmt->bindValue(":stock", $productos->getStock(), PDO::PARAM_INT);
            $stmt->bindValue(":oferta", $productos->getOferta(), PDO::PARAM_STR);
            $stmt->bindValue(":fecha", $productos->getFecha(), PDO::PARAM_STR);
            $stmt->bindValue(":imagen", $productos->getImagen(), PDO::PARAM_STR);
            $stmt->bindValue(":id", $productos->getId(), PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $ex) {
            error_log("Error al actualizar producto: " . $ex->getMessage());
            return false;
        }
    }

    // Método para borrar un producto
    public function borrarProducto(int $id): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException("El ID debe ser mayor que 0");
        }

        try {
            $this->conexion->empezarTransaccion();

            // Verificamos si el producto existe
            $checkStmt = $this->conexion->prepare("SELECT id FROM productos WHERE id = :id");
            $checkStmt->bindValue(":id", $id, PDO::PARAM_INT);
            $checkStmt->execute();

            if (!$checkStmt->fetch()) {
                throw new Exception("El producto no existe.");
            }

            // Primero eliminamos las referencias en lineas_pedidos
            $lineasStmt = $this->conexion->prepare("DELETE FROM lineas_pedidos WHERE producto_id = :id");
            $lineasStmt->bindValue(":id", $id, PDO::PARAM_INT);
            $lineasStmt->execute();

            // Después borramos el producto
            $deleteStmt = $this->conexion->prepare("DELETE FROM productos WHERE id = :id");
            $deleteStmt->bindValue(":id", $id, PDO::PARAM_INT);
            $deleteStmt->execute();

            if ($deleteStmt->rowCount() > 0) {
                $this->conexion->commit();
                return true;
            } else {
                $this->conexion->deshacer();
                return false;
            }
        } catch (PDOException $ex) {
            $this->conexion->deshacer();
            error_log("Error al ejecutar el borrado: " . $ex->getMessage());
            return false;
        }
    }


    // Método para obtener los productos de una categoría
    public function obtenerProductosPorCategoria(int $categoria_id): array
    {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM productos WHERE categoria_id = :categoria_id");
            $stmt->bindParam(":categoria_id", $categoria_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            echo $ex->getMessage();
            return [];
        }
    }

    // Método para obtener los productos en oferta
    public function obtenerProductosEnOferta(): array
    {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM productos WHERE oferta = 'si'");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    // Método para obtener los productos con stock
    public function obtenerProductosConStock(): array
    {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM productos WHERE stock > 0");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    // Método para obtener el total de productos
    public function obtenerTotalProductos(): int
    {
        try {
            $stmt = $this->conexion->prepare("SELECT COUNT(*) FROM productos");
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    // Método para obtener los productos paginados
    public function obtenerProductosPaginados(int $inicio, int $productosPorPagina): array
    {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM productos LIMIT :inicio, :productosPorPagina");
            $stmt->bindParam(":inicio", $inicio, PDO::PARAM_INT);
            $stmt->bindParam(":productosPorPagina", $productosPorPagina, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    // Método para obtener el número de productos que tiene una categoria
    public function obtenerTotalProductosPorCategoria(Productos $productos): int
    {
        try {
            $stmt = $this->conexion->prepare("SELECT COUNT(*) FROM productos WHERE categoria_id = :categoria_id");
            $stmt->bindParam(":categoria_id", $productos->getCategoriaId(), PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    // Método para obtener los productos por nombre
    public function obtenerProductosPorNombre(Productos $productos): array
    {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM productos WHERE nombre LIKE :nombre");
            $stmt->bindParam(":nombre", $productos->getNombre(), PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    public function obtenerTodosLosProductos(): array
    {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM productos");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            error_log("Error al obtener productos: " . $ex->getMessage());
            return [];
        }
    }

    public function informacionProducto(int $id): array
    {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM productos WHERE id = :productID");
            $stmt->bindValue(':productID', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Error al obtener los datos del producto: " . $e->getMessage());
            return [];
        }
    }

    public function actualizarStockProducto()
    {
        try {
            $this->conexion->empezarTransaccion();

            $stmt = $this->conexion->prepare("UPDATE productos SET stock = stock - :cantidad where id = :id");

            foreach ($_SESSION['carrito'] as $producto) {
                $stmt->bindValue(':cantidad', $producto['cantidad'], PDO::PARAM_INT);
                $stmt->bindValue(':id', $producto['id'], PDO::PARAM_INT);
                $stmt->execute();
            }

            $this->conexion->commit();
            return true;
        } catch (PDOException $e) {
            $this->conexion->deshacer();
            return $e->getMessage();
        }
    }
}
