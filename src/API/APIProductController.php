<?php
namespace API;

use API\APIProduct;
use API\APIProductService;
use Services\CategoriaServicio;

class APIProductController {
    private APIProductService $productService;
    private CategoriaServicio $categoryService;

    public function __construct() {
        $this->productService = new APIProductService();
        $this->categoryService = new CategoriaServicio();
    }

    public function index() {
        $productos = $this->productService->mostrarProductos();
        //header('Content-Type: application/json');
        echo json_encode(['data' => $productos]);
    }

    public function store($productoData) {
        if (empty($productoData)) {
            http_response_code(400);
            echo json_encode(['error' => 'No se recibieron datos válidos']);
            return;
        }

        $productoData = json_decode($productoData, true);
    
        $producto = new APIProduct(
            null,
            $productoData['categoria_id'],
            $productoData['nombre'],
            $productoData['descripcion'],
            $productoData['precio'],
            $productoData['stock'],
            $productoData['oferta'] ?? '',
            $productoData['fecha'] ?? date('Y-m-d'),
            $productoData['imagen'] ?? ''
        );
    
        // Validar los datos del producto
        /*$errores = $producto->validarDatosProductos();
        if (!empty($errores)) {
            http_response_code(400);
            return json_encode(['errores' => $errores]);
        }*/
    
        // Llamada al servicio para guardar el producto
        $resultado = $this->productService->guardarProductos([
            'categoria_id' => $producto->getCategoriaId(),
            'nombre' => $producto->getNombre(),
            'descripcion' => $producto->getDescripcion(),
            'precio' => $producto->getPrecio(),
            'stock' => $producto->getStock(),
            'oferta' => $producto->getOferta(),
            'fecha' => $producto->getFecha(),
            'imagen' => $producto->getImagen()
        ]);
    
        if ($resultado === true) {
            http_response_code(201);
            echo json_encode(['mensaje' => 'Producto creado exitosamente']);
            return;
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'No se pudo crear el producto']);
            return;
        }
    }

    public function show(int $id) {
        $details = $this->productService->detailProduct($id);
        
        //header('Content-Type: application/json');
        if (!empty($details)) {
            echo json_encode(['data' => $details]);
            return;
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Producto no encontrado']);
            return;
        }
    }

    public function update(int $id, $productoData) {
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        $productoData = json_decode($productoData, true);
        //die(var_dump($productoData));
        $producto = new APIProduct(
            null,
            $productoData['categoria_id'],
            $productoData['nombre'],
            $productoData['descripcion'],
            $productoData['precio'],
            $productoData['stock'],
            $productoData['oferta'] ?? '',
            $productoData['fecha'] ?? '',
            $productoData['imagen'] ?? ''
        );

        //$errores = $producto->validateUpdate();
        
        if (!empty($errores)) {
            http_response_code(400);
            echo json_encode(['errores' => $errores]);
            return;
        }

        $resultado = $this->productService->updateProduct([
            'categoria_id' => $producto->getCategoriaId(),
            'nombre' => $producto->getNombre(),
            'descripcion' => $producto->getDescripcion(),
            'precio' => $producto->getPrecio(),
            'stock' => $producto->getStock(),
            'oferta' => $producto->getOferta(),
            'fecha' => $producto->getFecha(),
            'imagen' => $producto->getImagen()
        ], $id);

        //header('Content-Type: application/json');
        if ($resultado === true) {
            echo json_encode(['mensaje' => 'Producto actualizado exitosamente']);
            return;
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'No se pudo actualizar el producto']);
            return;
        }
    }

    public function destroy(int $id) {
        $resultado = $this->productService->deleteProduct($id);

        //header('Content-Type: application/json');
        if ($resultado) {
            echo json_encode(['mensaje' => 'Producto eliminado exitosamente']);
            return;
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'No se pudo eliminar el producto']);
            return;
        }
    }
}