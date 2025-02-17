<?php

namespace Controllers;

use Lib\Pages;
use Models\Productos;
use Lib\Utils;
use Services\ProductosServicio;
use Services\CategoryService;
use Services\OrderLineService;
use API\APIProductController;
use Services\CategoriaServicio;
use Services\LineaDePedidoServicio;

/**
 * Clase para controlar los productos
 */
class ProductoController {

    /**
     * Variables de los productos
     */
    private Pages $pages;
    private Utils $utils;
    private APIProductController $productApiController;
    private ProductosServicio $productService;
    private LineaDePedidoServicio $orderLineService;
    private CategoriaServicio $categoryService;
    

    /**
     * Constructor que inicializa las variables
     */
    public function __construct() {
        $this->pages = new Pages();
        $this->utils = new Utils();
        $this->productApiController = new APIProductController();
        $this->productService = new ProductosServicio();
        $this->orderLineService = new LineaDePedidoServicio();
        $this->categoryService = new CategoriaServicio();
    }

    /**
     * Metodo que saca los productos y los renderiza a la vista
     * @return void
     */
    public function gestion(){

        if ($this->utils->isAdmin()){
            $admin = true;
        }
        else{
            $admin = false;
        }


        //$this->productApiController->index();
        ob_start();
        $this->productApiController->index();
        $response = json_decode(ob_get_clean(), true);

        $productos = $response['data'] ?? [];
        //die(print_r($productos));
        $this->pages->render('productos/index', 
        [
            'admin' => $admin,
            'productos' => $productos    
        ]);    
    }

    /**
     * Metodo que guardar los productos en caso de no haber errores
     * y renderiza la vista
     * @return void
     */
    public function guardarProductos() {
        

        if ($_SERVER['REQUEST_METHOD'] === 'GET'){

            if(!$this->utils->isAdmin()){
                header("Location: " . BASE_URL ."");
            }
            else{
                unset($_SESSION['guardado']);

                $categorias = $this->categoryService->obtenerCategorias();


                $this->pages->render('productos/crear',
                [
                    'categorias' => $categorias
                ]);
            }
        }

        else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $imagenNombre = '';
            $rutaCarpeta = '../../public/img';
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
                $_POST['categoria'],
                $_POST['nombre'],
                $_POST['descripcion'],
                $_POST['precio'],
                $_POST['stock'],
                $_POST['oferta'],
                $_POST['fecha'],
                $imagenNombre
            );
    
                // Sanitizar datos
                $producto->sanitizarDatos();

                // Validar datos
                $errores = $producto->validarDatosProductos();
                if (empty($errores)) {
                    
                    $apiData = json_encode([
                        'categoria_id' => $_POST['categoria'],
                        'nombre' => $_POST['nombre'],
                        'descripcion' => $_POST['descripcion'],
                        'precio' => $_POST['precio'],
                        'stock' => $_POST['stock'],
                        'oferta' => $_POST['oferta'] ?? '',
                        'fecha' => $_POST['fecha'] ?? date('Y-m-d'),
                        'imagen' => $imagenNombre
                    ]);

                    
                    ob_start();
                    $this->productApiController->store($apiData);
                    $response = json_decode(ob_get_clean(), true);
                    //die(var_dump($response));

                    if (isset($response['mensaje'])) {
                        $_SESSION['guardado'] = true;
                        $this->pages->render('productos/crear');
                        exit;
                    } 
                    else {
                        $errores = $response['errores'] ?? ['db' => 'Error al guardar el producto'];
                        $this->pages->render('productos/crear', ["errores" => $errores]);
                    }
                } 
                else {
                    $this->pages->render('productos/crear', ["errores" => $errores]);
                }
            
        }
    }

    /**
     * Metodo que obtiene los detalles de un producto
     * @var id id del producto al que obtener los detalles
     * @return void
     */
    public function detailProduct(int $id){
        ob_start();
        $this->productApiController->show($id);
        $response = json_decode(ob_get_clean(), true);

        $details = [];
        if (isset($response['data'])) {
            if (isset($response['data'][0])) {
                $details = $response['data'];
            } 
            else if (is_array($response['data'])) {
                $details = [$response['data']];
            }
        }

        //die(var_dump(($details)));

        $this->pages->render('productos/productoInfo', 
        [
            'admin' => $this->utils->isAdmin(),
            'details' => $details    
        ]); 
    }

    /**
     * Metodo que borrar  un producto
     * @var id id del producto a borrar
     * @return void
     */
    public function borrarProducto()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!$this->utils->isAdmin()) {
                header("Location" . BASE_URL . "");
            } else {
                unset($_SESSION['errores']);
                unset($_SESSION['borrado']);

                $productos = $this->productService->obtenerProductos(); // Método para obtener productos
                $this->pages->render('productos/borrar', [
                    'productos' => $productos,
                    'errores' => $errores ?? null,
                ]);
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productoId = $_POST['productoSeleccionado'];

            $resultado = $this->productService->borrarProducto($productoId);

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

    /**
     * Metodo que actualiza los datos de un producto
     * @var id id del producto aactualizar
     * @return void
     */
    public function actualizarProducto()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!$this->utils->isAdmin()) {
                header("Location" . BASE_URL . "");
            } else {
                unset($_SESSION['errores']);
                unset($_SESSION['actualizado']);

                $categoriasServicio = $this->categoryService->obtenerCategorias();
                $productos = $this->productService->obtenerProductos(); // Asume que existe este método

                $this->pages->render(
                    'productos/actualizar',
                    [
                        'categorias' => $categoriasServicio,
                        'productos' => $productos
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

                $resultado = $this->productService->actualizarProducto($productData, $cambiarIdProducto);

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

}
