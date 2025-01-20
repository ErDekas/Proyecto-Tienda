<?php

namespace Services;

use Exception;
use Models\Categorias;
use Repositories\CategoriasRepository;

class CategoriaServicio
{
    private CategoriasRepository $categoriaRepository;

    public function __construct()
    {
        $this->categoriaRepository = new CategoriasRepository();
    }

    // Método que llama al repositorio para guardar una categoria
    public function insertarCategoria(array $datos): bool
    {
        try {
            $categoriaRepository = new Categorias(
                null,
                $datos['nombre']
            );

            return $this->categoriaRepository->insertarCategoria(($categoriaRepository));
        } catch (Exception $ex) {
            echo $ex->getMessage();
            return false;
        }
    }

    // Método que llama al repositorio para ver todas las categorias
    public function obtenerCategorias(): array
    {
        try {
            $categoriaRepository = new Categorias();
            return $this->categoriaRepository->obtenerCategorias($categoriaRepository);
        } catch (Exception $ex) {
            echo $ex->getMessage();
            return false;
        }
    }

    // Método que llama al repositorio para borrar una categoria
    public function eliminarCategorias(int $id): bool
    {
        try {
            $categoriaRepository = new Categorias($id);
            return $this->categoriaRepository->eliminarCategoria($categoriaRepository);
        } catch (Exception $ex) {
            echo $ex->getMessage();
            return false;
        }
    }

    // Método que llama al repositorio para actualizar una categoria
    public function actualizarCategoria(array $datos, int $id): bool
    {
        try {
            $categoriaRepository = new Categorias(
                null,
                $datos['nombre']
            );

            return $this->categoriaRepository->actualizarCategoria($categoriaRepository, $id);
        } catch (Exception $ex) {
            echo $ex->getMessage();
            return false;
        }
    }
}
