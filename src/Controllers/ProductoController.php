<?php

namespace Controllers;

use Lib\BaseDatos;
use Services\ProductosServicio;
use Services\CategoriaServicio;
use Lib\Pages;
use Lib\Utils;
use Models\Productos;

class ProductoController
{

    private pages $pages;
    private ProductosServicio $productosServicio;
    private CategoriaServicio $categoriasServicio;
    private Utils $utils;
    private Productos $productoModel;

    public function __construct()
    {
        $this->pages = new Pages();
        $this->productosServicio = new ProductosServicio();
        $this->categoriasServicio = new CategoriaServicio();
        $this->utils = new Utils();
        $this->productoModel = new Productos();
    }

    // Método para obtener todos los productos
    public function obtenerProductos(): void
    {
        $productos = $this->productosServicio->obtenerProductos();
        $this->pages->render(
            '/productos/index',
            [
                'productos' => $productos
            ]
        );
    }

    // Método para insertar un producto
    public function insertarProducto()
    {


        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!$this->utils->isAdmin()) {
                header("Location" . BASE_URL . "");
            } else {
                unset($_SESSION['guardado']);

                $categoriasServicio = $this->categoriasServicio->obtenerCategorias();


                $this->pages->render(
                    'productos/crear',
                    [
                        'categorias' => $categoriasServicio
                    ]
                );
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $imagenNombre = null;
            $rutaCarpeta = '../../public/IMG';
            $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];


            if (!is_dir($rutaCarpeta)) {
                mkdir($rutaCarpeta, 0777, true);
            }

            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $tipoArchivo = mime_content_type($_FILES['imagen']['tmp_name']);
                if (!in_array($tipoArchivo, $tiposPermitidos)) {
                    $errores['imagen'] = "El archivo debe ser una formato válido (JPEG, PNG o GIF).";
                } else {
                    $imagenNombre = basename($_FILES['imagen']['name']);
                    $rutaArchivo = rtrim($rutaCarpeta, '/') . '/' . $imagenNombre;

                    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaArchivo)) {
                        $errores['imagen'] = "No se pudo guardar el archivo de la imagen.";
                    }
                }
            } else if (isset($_FILES['imagen']['error']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
                $errores['imagen'] = "Error al cargar la imagen: " . $_FILES['imagen']['error'];
            }


            $producto = new Productos(
                null,
                intval($_POST['categoria']),
                $_POST['nombre'],
                $_POST['descripcion'],
                floatval($_POST['precio']),
                intval($_POST['stock']),
                $_POST['oferta'],
                $_POST['fecha'],
                $imagenNombre
            );
            // Sanitizar datos
            $producto->sanitizarDatos();

            // Validar datos
            $errores = $producto->validarDatosProductos();

            if (empty($errores)) {

                $productData = [
                    'categoria_id' => $producto->getCategoriaId(),
                    'nombre' => $producto->getNombre(),
                    'descripcion' => $producto->getDescripcion(),
                    'precio' => $producto->getPrecio(),
                    'stock' => $producto->getStock(),
                    'oferta' => $producto->getOferta(),
                    'fecha' => $producto->getFecha(),
                    'imagen' => $producto->getImagen(),
                ];

                $resultado = $this->productosServicio->insertarProducto($productData);

                if ($resultado === true) {
                    $_SESSION['guardado'] = true;
                    $this->pages->render('productos/crear');
                    exit;
                } else {
                    $errores['db'] = "Error al guardar el producto: " . $resultado;
                    $this->pages->render('productos/crear', ["errores" => $errores]);
                }
            } else {
                $this->pages->render('productos/crear', ["errores" => $errores]);
            }
        }
    }

    // Método para borrar un producto
    public function actualizarProducto()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!$this->utils->isAdmin()) {
                header("Location" . BASE_URL . "");
            } else {
                unset($_SESSION['errores']);
                unset($_SESSION['actualizado']);

                $categoriasServicio = $this->categoriasServicio->obtenerCategorias();
                $productos = $this->productosServicio->obtenerProductos(); // Asume que existe este método

                $this->pages->render(
                    'productos/actualizar',
                    [
                        'categorias' => $categoriasServicio,
                        'productos' => $productos // Añadir productos aquí
                    ]
                );
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $imagenNombre = null;
            $rutaCarpeta = '../../public/IMG';
            $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];

            $cambiarIdProducto = $_POST['productoSeleccionado'];
            if (!is_dir($rutaCarpeta)) {
                mkdir($rutaCarpeta, 0777, true);
            }

            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $tipoArchivo = mime_content_type($_FILES['imagen']['tmp_name']);
                if (!in_array($tipoArchivo, $tiposPermitidos)) {
                    $errores['imagen'] = "El archivo debe ser una formato válido (JPEG, PNG o GIF).";
                } else {
                    $imagenNombre = basename($_FILES['imagen']['name']);
                    $rutaArchivo = rtrim($rutaCarpeta, '/') . '/' . $imagenNombre;

                    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaArchivo)) {
                        $errores['imagen'] = "No se pudo guardar el archivo de la imagen.";
                    }
                }
            } else if (isset($_FILES['imagen']['error']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
                $errores['imagen'] = "Error al cargar la imagen: " . $_FILES['imagen']['error'];
            }


            $producto = new Productos(
                $cambiarIdProducto,
                intval($_POST['categoria']),
                $_POST['nombre'],
                $_POST['descripcion'],
                floatval($_POST['precio']),
                intval($_POST['stock']),
                $_POST['oferta'],
                $_POST['fecha'],
                $imagenNombre
            );
            // Sanitizar datos
            $producto->sanitizarDatos();

            // Validar datos
            $errores = $producto->validarDatosProductos();

            if (empty($errores)) {

                $productData = [
                    'categoria_id' => intval($_POST['categoria']),
                    'nombre' => htmlspecialchars($_POST['nombre']),
                    'descripcion' => htmlspecialchars($_POST['descripcion']),
                    'precio' => floatval($_POST['precio']),
                    'stock' => intval($_POST['stock']),
                    'oferta' => htmlspecialchars($_POST['oferta']),
                    'fecha' => $_POST['fecha'],
                    'imagen' => $imagenNombre,
                ];

                $resultado = $this->productosServicio->actualizarProducto($productData, $cambiarIdProducto);

                if ($resultado === true) {
                    $_SESSION['actualizado'] = true;
                    $this->pages->render('productos/actualizar');
                    exit;
                } else {
                    $errores['db'] = "Error al actualizar el producto: " . $resultado;
                    $this->pages->render('productos/actualizar', ["errores" => $errores]);
                }
            } else {
                $this->pages->render('productos/actualizar', ["errores" => $errores]);
            }
        }
    }

    public function borrarProducto()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!$this->utils->isAdmin()) {
                header("Location" . BASE_URL . "");
            } else {
                unset($_SESSION['errores']);
                unset($_SESSION['borrado']);

                $productos = $this->productosServicio->obtenerProductos(); // Método para obtener productos
                $this->pages->render('productos/borrar', [
                    'productos' => $productos,
                    'errores' => $errores ?? null,
                ]);
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productoId = $_POST['productoSeleccionado'];

            $resultado = $this->productosServicio->borrarProducto($productoId);

            if ($resultado === true) {
                $_SESSION['borrado'] = true;
                $this->pages->render('productos/borrar');
                exit;
            } else {
                $_SESSION['errores'] = 'fallo';
                $this->pages->render('productos/borrar', ["errores" => ['db' => "Error al borrar el producto."]]);
            }
        }
    }

    public function informacionProducto(int $id)
    {
        $details = $this->productosServicio->informacionProducto($id);

        //die(var_dump(($details)));

        $this->pages->render(
            'productos/productoInfo',
            [
                'admin' => $this->utils->isAdmin(),
                'details' => $details
            ]
        );
    }
}
