<?php

namespace Controllers;

use Services\CategoriaServicio;
use Lib\Pages;
use Lib\Utils;
use Models\Categorias;
use Services\ProductosServicio;

class CategoriaController
{

    private pages $pages;
    private CategoriaServicio $categoriaServicio;
    private Utils $utils;
    private ProductosServicio $productoServicios;
    private Categorias $categoriaModel;

    public function __construct()
    {
        $this->pages = new Pages();
        $this->categoriaServicio = new CategoriaServicio();
        $this->utils = new Utils();
        $this->categoriaModel = new Categorias();
        $this->productoServicios = new ProductosServicio();
    }

    public function obtenerCategorias(): void
    {
        if ($this->utils->isAdmin()) {
            $admin = true;
        } else {
            $admin = false;
        }
        $categorias = $this->categoriaServicio->obtenerCategorias();
        $this->pages->render(
            '/categorias/index',
            [
                'admin' => $admin,
                'categorias' => $categorias
            ]
        );
    }

    public function insertarCategoria(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!$this->utils->isAdmin()) {
                header("Location: " . BASE_URL . "");
            } else {
                $this->pages->render('categorias/crear');
                unset($_SESSION['errores']);
                unset($_SESSION['creado']);
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $_POST['categorias'];

            $categorias = $this->categoriaModel = Categorias::fromArray($datos);

            // Sanitizamos los datos
            $categorias->sanitizarDatos();

            // Validamos los datos
            $errores = $categorias->validarDatos();

            if (empty($errores)) {
                $datosCategoria = [
                    'nombre' => $categorias->getNombre(),
                ];

                $resultado = $this->categoriaServicio->insertarCategoria($datosCategoria);

                if ($resultado === true) {
                    $this->obtenerCategorias();
                    exit;
                } else {
                    $errores['db'] = 'Hubo un error al guardar la categoria';
                    $this->pages->render(
                        'categorias/crear',
                        [
                            'errores' => $errores
                        ]
                    );
                }
            } else {
                $this->pages->render(
                    'categorias/crear',
                    [
                        'errores' => $errores
                    ]
                );
            }
        }
        $this->pages->render('categorias/crear');
    }

    public function actualizarCategoria(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!$this->utils->isAdmin()) {
                header("Location: " . BASE_URL . "");
            } else {
                unset($_SESSION['errores']);
                unset($_SESSION['actualizado']);

                $categorias = $this->categoriaServicio->obtenerCategorias();

                $this->pages->render(
                    'categorias/actualizar',
                    [
                        'categorias' => $categorias
                    ]
                );
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_POST['categorias']) {
                $datos = $_POST['categorias'];

                $categorias = $this->categoriaModel = Categorias::fromArray($datos);

                $cambiarIdCategoria = $_POST['categoriaSeleccionada'];

                // Sanitizamos los datos
                $categorias->sanitizarDatos();

                // Validamos los datos
                $errores = $categorias->validarDatos();

                if (empty($errores)) {
                    $datosCategoria = [
                        'nombre' => $categorias->getNombre(),
                    ];

                    $resultado = $this->categoriaServicio->actualizarCategoria($datosCategoria, $cambiarIdCategoria);

                    if ($resultado === true) {
                        $_SESSION['actualizado'] = true;
                        $this->pages->render('categorias/actualizar');
                        exit;
                    } else {
                        $errores['db'] = 'Hubo un error al actualizar la categoria';
                        $this->pages->render(
                            'categorias/actualizar',
                            [
                                'errores' => $errores
                            ]
                        );
                    }
                } else {
                    $this->pages->render(
                        'categorias/actualizar',
                        [
                            'errores' => $errores
                        ]
                    );
                }
            }
        } else {
            $_SESSION['errores'] = 'fallo';
            $this->pages->render('categorias/actualizar');
        }
    }

    public function eliminarCategorias(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!$this->utils->isAdmin()) {
                header("Location" . BASE_URL . "");
            } else {
                unset($_SESSION['errores']);
                unset($_SESSION['borrado']);

                $categorias = $this->categoriaServicio->obtenerCategorias();

                $this->pages->render(
                    '/categorias/borrar',
                    [
                        'categorias' => $categorias
                    ]
                );
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_POST['categorias']) {
                $datos = $_POST['categorias'];

                $categorias = $this->categoriaModel = Categorias::fromArray($datos);

                $borrarIdCategoria = $_POST['categoriaSeleccionada'];

                // Sanitizamos los datos
                $categorias->sanitizarBorrado();

                // Validamos los datos
                $errores = $categorias->validarBorrado($borrarIdCategoria);

                if (empty($errores)) {
                    $resultado = $this->categoriaServicio->eliminarCategorias($borrarIdCategoria);

                    if ($resultado === true) {
                        $_SESSION['borrado'] = true;
                        $this->pages->render('/categorias/borrar');
                        exit;
                    } else {
                        $errores['db'] = 'Hubo un error al borrar la categoria';
                        $this->pages->render(
                            '/categorias/borrar',
                            [
                                'errores' => $errores
                            ]
                        );
                    }
                } else {
                    $this->pages->render(
                        'categorias/borrar',
                        [
                            'errores' => $errores
                        ]
                    );
                }
            }
        } else {
            $_SESSION['errores'] = 'fallo';
            $this->pages->render('/categorias/borrar');
        }
    }

    public function productosPorCategoria(int $id)
    {
        $productos = $this->productoServicios->obtenerProductosPorCategoria($id);


        $this->pages->render(
            'productos/index',
            [
                'admin' => $this->utils->isAdmin(),
                'productos' => $productos
            ]
        );
    }
}
