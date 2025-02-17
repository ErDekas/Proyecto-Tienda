<?php
namespace API;

use Lib\BaseDatos;
use API\APIProduct;
use PDO;
use PDOException;

class APIProductRepository {
   private BaseDatos $conexion;

   public function __construct() {
       $this->conexion = new BaseDatos();
   }

   public function guardarProductos(APIProduct $producto): bool|string {
       try {
           $stmt = $this->conexion->prepare(
               "INSERT INTO productos (categoria_id, nombre, descripcion, precio, stock, oferta, fecha, imagen)
                VALUES (:categoria_id, :nombre, :descripcion, :precio, :stock, :oferta, :fecha, :imagen)"
           );

           $stmt->bindValue(':categoria_id', $producto->getCategoriaId(), PDO::PARAM_INT);
           $stmt->bindValue(':nombre', $producto->getNombre(), PDO::PARAM_STR);
           $stmt->bindValue(':descripcion', $producto->getDescripcion(), PDO::PARAM_STR);
           $stmt->bindValue(':precio', number_format($producto->getPrecio(), 2, '.', ''), PDO::PARAM_STR);
           $stmt->bindValue(':stock', $producto->getStock(), PDO::PARAM_INT);
           $stmt->bindValue(':oferta', $producto->getOferta(), PDO::PARAM_STR);
           $stmt->bindValue(':fecha', $producto->getFecha(), PDO::PARAM_STR);
           $stmt->bindValue(':imagen', $producto->getImagen(), PDO::PARAM_STR);

           $stmt->execute();
           return true;
       } 
       catch (PDOException $e) {
           error_log("Error al guardar producto: " . $e->getMessage());
           return $e->getMessage();
       }
   }

   public function findAll(): array {
       try {
           $stmt = $this->conexion->prepare("SELECT * FROM productos");
           $stmt->execute();
           return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
       } catch (PDOException $e) {
           error_log("Error al obtener productos: " . $e->getMessage());
           return [];
       }
   }

   public function findById(int $id): ?array {
       try {
           $stmt = $this->conexion->prepare("SELECT * FROM productos WHERE id = :id");
           $stmt->bindValue(':id', $id, PDO::PARAM_INT);
           $stmt->execute();
           $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
           return $resultado ?: null;
       } catch (PDOException $e) {
           error_log("Error al obtener producto: " . $e->getMessage());
           return null;
       }
   }

   public function update(APIProduct $producto, int $id): bool {
       try {
           $stmt = $this->conexion->prepare(
               "UPDATE productos SET categoria_id = :categoria_id, nombre = :nombre, 
               descripcion = :descripcion, precio = :precio, stock = :stock, 
               oferta = :oferta, imagen = :imagen WHERE id = :id"
           );

           $stmt->bindValue(':categoria_id', $producto->getCategoriaId(), PDO::PARAM_INT);
           $stmt->bindValue(':nombre', $producto->getNombre(), PDO::PARAM_STR);
           $stmt->bindValue(':descripcion', $producto->getDescripcion(), PDO::PARAM_STR);
           $stmt->bindValue(':precio', $producto->getPrecio(), PDO::PARAM_STR);
           $stmt->bindValue(':stock', $producto->getStock(), PDO::PARAM_INT);
           $stmt->bindValue(':oferta', $producto->getOferta(), PDO::PARAM_STR);
           $stmt->bindValue(':imagen', $producto->getImagen(), PDO::PARAM_STR);
           $stmt->bindValue(':id', $id, PDO::PARAM_INT);

           return $stmt->execute();
       } 
       catch (PDOException $e) {
           error_log("Error al actualizar producto: " . $e->getMessage());
           return false;
       }
   }

   public function delete(int $id): bool {
       try {
           $stmt = $this->conexion->prepare("DELETE FROM productos WHERE id = :id");
           $stmt->bindValue(':id', $id, PDO::PARAM_INT);
           return $stmt->execute();
       } 
       catch (PDOException $e) {
           error_log("Error al eliminar producto: " . $e->getMessage());
           return false;
       }
   }
}