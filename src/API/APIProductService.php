<?php
namespace API;

use API\APIProduct;
use API\APIProductRepository;

class APIProductService {
   private APIProductRepository $repository;

   public function __construct() {
       $this->repository = new APIProductRepository();
   }

   public function guardarProductos(array $productData): bool|string {
       try {
           $producto = new APIProduct(
               null,
               $productData['categoria_id'],
               $productData['nombre'],
               $productData['descripcion'],
               $productData['precio'],
               $productData['stock'],
               $productData['oferta'] ?? '',
               $productData['fecha'] ?? date('Y-m-d'),
               $productData['imagen'] ?? ''
           );

           return $this->repository->guardarProductos($producto);
       } 
       catch (\Exception $e) {
           error_log("Error al guardar el producto: " . $e->getMessage());
           return false;
       }
   }

   public function mostrarProductos(): array {
       return $this->repository->findAll();
   }

   public function detailProduct(int $id): ?array {
       return $this->repository->findById($id);
   }

   public function deleteProduct(int $id): bool {
       return $this->repository->delete($id);
   }

   public function updateProduct(array $productData, int $id): bool {
       try {
           $producto = new APIProduct(
               null,
               $productData['categoria_id'],
               $productData['nombre'],
               $productData['descripcion'],
               $productData['precio'],
               $productData['stock'],
               $productData['oferta'] ?? '',
               $productData['fecha'] ?? '',
               $productData['imagen'] ?? ''
           );

           return $this->repository->update($producto, $id);
       } 
       catch (\Exception $e) {
           error_log("Error al actualizar el producto: " . $e->getMessage());
           return false;
       }
   }
}