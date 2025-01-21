<?php

namespace Services;

use Exception;
use Models\Productos;
use Repositories\ProductosRepository;

class ProductosServicio
{
    private ProductosRepository $productosRepository;

    public function __construct()
    {
        $this->productosRepository = new ProductosRepository();
    }

    // Método que llama al repositorio para guardar un producto
    public function insertarProducto(array $datos): bool
    {
        try {
            $productoRepository = new Productos(
                null,                      // id (null for new products)
                $datos['categoria_id'],    // categoria_id
                $datos['nombre'],          // nombre
                $datos['descripcion'],     // descripcion
                $datos['precio'],          // precio
                $datos['stock'],           // stock
                $datos['oferta'],          // oferta
                $datos['fecha'],           // fecha
                $datos['imagen']           // imagen
            );

            return $this->productosRepository->insertarProducto($productoRepository);
        } catch (Exception $ex) {
            echo $ex->getMessage();
            return false;
        }
    }


    // Método que llama al repositorio para ver todos los productos
    public function obtenerProductos(): array
    {
        try {
            $productoRepository = new Productos();
            return $this->productosRepository->obtenerProductos($productoRepository);
        } catch (Exception $ex) {
            echo $ex->getMessage();
            return false;
        }
    }

    // Método que llama al repositorio para borrar un producto
    public function borrarProducto(int $id): bool
    {
        try {
            return $this->productosRepository->borrarProducto($id);
        } catch (Exception $ex) {
            error_log("Error al borrar producto: " . $ex->getMessage());
            return false;
        }
    }


    // Método que llama al repositorio para actualizar un producto
    public function actualizarProducto(array $datos, int $id): bool
    {
        try {
            // Creamos el modelo con el ID correcto
            $productoRepository = new Productos(
                $id,                       // Importante: pasar el ID primero
                $datos['categoria_id'],
                $datos['nombre'],
                $datos['descripcion'],
                $datos['precio'],
                $datos['stock'],
                $datos['oferta'],
                $datos['fecha'],
                $datos['imagen']
            );

            return $this->productosRepository->actualizarProducto($productoRepository);
        } catch (Exception $ex) {
            error_log("Error al actualizar producto: " . $ex->getMessage());
            return false;
        }
    }

    // Método que llama al repositorio para obtener los productos de una categoría
    public function obtenerProductosPorCategoria(int $id): array
    {
        try {
            return $this->productosRepository->obtenerProductosPorCategoria($id);
        } catch (Exception $ex) {
            echo $ex->getMessage();
            return false;
        }
    }

    // Método que llama al repositorio para obtener un producto
    public function obtenerProductoPorId(int $id): ?array
    {
        try {
            // Creamos el modelo solo con el ID
            $productoRepository = new Productos(
                $id,    // ID
                0,      // categoria_id
                '',     // nombre
                '',     // descripcion
                0,      // precio
                0,      // stock
                '',     // oferta
                '',     // fecha
                ''      // imagen
            );

            $resultado = $this->productosRepository->obtenerProductoPorId($productoRepository);
            return $resultado ?: null;
        } catch (Exception $ex) {
            error_log("Error al obtener producto: " . $ex->getMessage());
            return null;
        }
    }

    public function informacionProducto(int $id): array
    {
        return $this->productosRepository->informacionProducto($id);
    }

    // Método que llama al repositorio para obtener los productos en oferta
    public function obtenerProductosEnOferta(): array
    {
        try {
            $productoRepository = new Productos();
            return $this->productosRepository->obtenerProductosEnOferta($productoRepository);
        } catch (Exception $ex) {
            echo $ex->getMessage();
            return false;
        }
    }

    // Método que llama al repositorio para obtener los productos con stock
    public function obtenerProductosConStock(): array
    {
        try {
            $productoRepository = new Productos();
            return $this->productosRepository->obtenerProductosConStock($productoRepository);
        } catch (Exception $ex) {
            echo $ex->getMessage();
            return false;
        }
    }

    // Método que llama al repositorio para obtener el total de los productos
    public function obtenerTotalProductos(): int
    {
        try {
            $productoRepository = new Productos();
            return $this->productosRepository->obtenerTotalProductos($productoRepository);
        } catch (Exception $ex) {
            echo $ex->getMessage();
            return false;
        }
    }

    // Método que llama al repositorio para obtener los productos 
    public function obtenerProductosPorNombre(): array
    {
        try {
            $productoRepository = new Productos();
            return $this->productosRepository->obtenerProductosPorNombre($productoRepository);
        } catch (Exception $ex) {
            echo $ex->getMessage();
            return false;
        }
    }

    // Método que llama al repositorio para actualizar el stock de un producto después de confirmar su pedido
    public function actualizarStockProducto()
    {
        return $this->productosRepository->actualizarStockProducto();
    }

    // Método que llama al repositorio para obtener el total de los productos por categoria
    public function obtenerTotalProductosPorCategoria(): int
    {
        try {
            $productoRepository = new Productos();
            return $this->productosRepository->obtenerTotalProductosPorCategoria($productoRepository);
        } catch (Exception $ex) {
            echo $ex->getMessage();
            return false;
        }
    }
}
