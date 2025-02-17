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
        

        if ($_SERVER['REQUobtenerProductosEST_METHOD'] === 'GET'){

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
    public function deleteProduct (int $id){
        if(!$this->utils->isAdmin()){
            header("Location: " . BASE_URL ."");
        }
        else{
            $lines = $this->orderLineService->verLineasDePedido($id);
            if(!empty($lines)){
                $update = $this->productService->obtenerProductosPorCategoria($id);
                if ($update === true) {
                    die("he entrado 1");
                    header("Location: " . BASE_URL ."");
                } 
                else {
                    $_SESSION['falloDatos'] = 'fallo';
                    $this->pages->render('productos/productoInfo/$id');
                }
            }
            else{
                ob_start();
                $this->productApiController->destroy($id);
                $resultado = json_decode(ob_get_clean(), true);

                if (isset($resultado['mensaje'])) {
                    header("Location: " . BASE_URL ."");
                    exit;
                } 
                else {
                    $_SESSION['falloDatos'] = 'fallo';
                    $this->pages->render('productos/productoInfo/$id');
                }
            }

            
        }
                    
    }

    /**
     * Metodo que actualiza los datos de un producto
     * @var id id del producto aactualizar
     * @return void
     */
    public function updateProduct(int $id){
        if ($_SERVER['REQUEST_METHOD'] === 'GET'){

            if(!$this->utils->isAdmin()){
                header("Location: " . BASE_URL ."");
            }
            else{

                unset($_SESSION['actualizado']);

                $categorias = $this->categoryService->obtenerCategorias();
                $product = $this->productService->informacionProducto($id);


                $this->pages->render('productos/actualizar',
                [
                    'categorias' => $categorias,
                    'product' => $product
                ]);
            }
        }

        else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method']) && $_POST['_method'] === 'PUT') {
                $_SERVER['REQUEST_METHOD'] = 'PUT';
            }

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

            ob_start();
            $this->productApiController->show($id);
            $product = json_decode(ob_get_clean(), true);


            if($imagenNombre === ''){
                $imagenNombre = $product["data"]["imagen"];
            }

                $producto = new Productos(
                    null,
                    $_POST['categoria'],
                    $_POST['nombre'],
                    $_POST['descripcion'],
                    $_POST['precio'],
                    $_POST['stock'],
                    $_POST['oferta'],
                    "",
                    $imagenNombre
                );

                // Sanitizar datos
                $producto->sanitizarDatos();
                
                // Validar datos
                $errores = $producto->validarUpdate();

                if (empty($errores)) {
                    
                    $apiData = json_encode([
                        'categoria_id' => $producto->getCategoriaId(),
                        'nombre' => $producto->getNombre(),
                        'descripcion' => $producto->getDescripcion(),
                        'precio' => $producto->getPrecio(),
                        'stock' => $producto->getStock(),
                        'oferta' => $producto->getOferta(),
                        'fecha' => $producto->getFecha(),
                        'imagen' => $producto->getImagen(),
                    ]);

                    ob_start();
                    $this->productApiController->update($id, $apiData);
                    $resultado = json_decode(ob_get_clean(), true);
                    
                    if (isset($resultado['mensaje'])) {
                        $_SESSION['actualizado'] = true;
                        $this->pages->render('productos/actualizar');
                        exit;
                    } 
                    else {
                        $errores = $response['errores'] ?? ['db' => 'Error al actualizar el producto'];
                        $this->pages->render('productos/actualizar', ["errores" => $errores]);
                    }
                } 
                else {
                    $this->pages->render('productos/actualizar', ["errores" => $errores]);
                }
            
        }
    }

}
