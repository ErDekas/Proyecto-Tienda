<?php

namespace Controllers;

use Lib\Pages;
use Models\Product;
use Lib\Utils;
use Models\Carrito;
use Services\ProductosServicio;
use Services\CategoriaServicio;
use Services\CarritoServicio;
use Services\CarritoObjetoServicio;

/**
 * Clase para controlar el carrito
 */
class CarritoController {

    /**
     * Variables privadas del controlador
     */
    private Pages $pages;
    private Utils $utils;
    private ProductosServicio $productService;
    private CarritoObjetoServicio $cartItemService;
    private CarritoServicio $cartService;
    
    /**
     * Constructor para inicializar las variables
     */
    public function __construct() {
        $this->pages = new Pages();
        $this->utils = new Utils();
        $this->productService = new ProductosServicio();
        $this->cartService = new CarritoServicio();
        $this->cartItemService = new CarritoObjetoServicio();
        $this->initializeCart();
    }
    private function initializeCart(): void {
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = []; 
        }
    }
    private function renderCartView(array $params = []): void {
        $total = $this->precioTotal();
        $_SESSION['precio'] = $total;
        $this->pages->render('carrito/carrito', $params);
    }

    /**
     * Método que renderiza la vista del carrito
     * @return void
     */
    public function cargarCarrito(): void {
        $this->initializeCart();

        if(isset($_SESSION['usuario'])){
            $cartUser = $this->loadFromDatabase();
            if(empty($cartUser)){
                $this->addToDatabase();
            }
        }

        $this->renderCartView();
    }

    /**
     * Metodo que calcula el precio total del carrito
     * @return int $total -> Varaible con el precio total del carrito
     */
    private function precioTotal(): float {
        $total = 0;
        if(isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])){
            foreach($_SESSION['carrito'] as $item){
                $total += $item['precio'] * $item['cantidad'];
            }
        }
        return $total;
    }


    /**
     * Metodo que añade un produco al carrito si no esta creado y si lo esta
     * aumenta su cantidad y despues renderiza la vista
     * @var id id del producto a añadir al carrito
     * @return void
     */
    public function anadirProducto(int $id): void {
        $productCart = $this->productService->informacionProducto($id);
        
        if (!$productCart) {
            $this->renderCartView(['error' => 'Producto no encontrado']);
            return;
        }

        if (isset($_SESSION['carrito'][$id])) {
            if($_SESSION['carrito'][$id]['cantidad'] === $_SESSION['carrito'][$id]['stock']){
                $this->renderCartView(['error' => 'Stock máximo alcanzado']);
                return;
            }
            $_SESSION['carrito'][$id]['cantidad']++; 
        } else {
            $_SESSION['carrito'][$id] = [
                'id' => $productCart[0]['id'],
                'imagen' => $productCart[0]['imagen'],
                'nombre' => $productCart[0]['nombre'],
                'precio' => $productCart[0]['precio'],
                'stock' => $productCart[0]['stock'],
                'cantidad' => 1
            ];
        }

        if(isset($_SESSION['usuario'])){
            $cart = $this->cartService->loadCart();
            if($cart){
                $this->cartItemService->addNewProductsToCart($cart['id']);
                $total = $this->precioTotal();
                $this->cartService->updateTotalPrice($cart['id'], $total);
            }
        }

        $this->renderCartView();
    }

    /**
     * Metodo que vacia el carrito y redirije a la vista
     * @return void
     */
    public function limpiarCarrito(): void {
        unset($_SESSION['carrito']);
        $this->initializeCart();

        if (isset($_SESSION['usuario'])) {
            $this->cartService->deleteCart($_SESSION['usuario']['id']);
        }

        $this->renderCartView();
    }

    /**
     * Metodo que elimina un producto del carrito y tras eso renderiza la vista
     * @var id id del producto a quitar del carrito´
     * @return void
     */
    public function eliminarProducto(int $id): void {
        if(isset($_SESSION['carrito'][$id])){
            unset($_SESSION['carrito'][$id]);

            if (isset($_SESSION['usuario'])) {
                $cart = $this->cartService->loadCart();
                if ($cart) {
                    $this->cartItemService->deleteItemFromCart($cart['id'], $id);
                    $total = $this->precioTotal();
                    $this->cartService->updateTotalPrice($cart['id'], $total);
                }
            }
            $this->renderCartView();
        } else {
            $this->renderCartView(['errorRemove' => 'Error al borrar el producto']);
        }
    }

    /**
     * Metod que decrementa la cantidad de un producto y renderiza la vista
     * @var id id del produto a decrementar su cantidad
     * @return void
     */
    public function bajarMonto(int $id): void {
        if(!isset($_SESSION['carrito'][$id])){
            $this->renderCartView(['error' => 'Producto no encontrado en el carrito']);
            return;
        }

        $_SESSION['carrito'][$id]['cantidad']--;

        if($_SESSION['carrito'][$id]['cantidad'] <= 0){
            unset($_SESSION['carrito'][$id]);
        }

        if (isset($_SESSION['usuario'])) {
            $cart = $this->cartService->loadCart();
            if ($cart) {
                if (isset($_SESSION['carrito'][$id])) {
                    $this->cartItemService->updateCartItem($cart['id'], $id, $_SESSION['carrito'][$id]['cantidad']);
                } else {
                    $this->cartItemService->deleteItemFromCart($cart['id'], $id);
                }
                $total = $this->precioTotal();
                $this->cartService->updateTotalPrice($cart['id'], $total);
            }
        }

        $this->renderCartView();
    }

    /**
     * Metod que aumenta la cantidad de un producto y renderiza la vista
     * @var id id del produto a aumentar su cantidad
     * @return void
     */
    public function aumentarMonto(int $id): void {
        if(!isset($_SESSION['carrito'][$id])){
            $this->renderCartView(['error' => 'Producto no encontrado en el carrito']);
            return;
        }

        if($_SESSION['carrito'][$id]['cantidad'] >= $_SESSION['carrito'][$id]['stock']){
            $this->renderCartView(['error' => 'Stock máximo alcanzado']);
            return;
        }

        $_SESSION['carrito'][$id]['cantidad']++;

        if (isset($_SESSION['usuario'])) {
            $cart = $this->cartService->loadCart();
            if ($cart) {
                $this->cartItemService->updateCartItem($cart['id'], $id, $_SESSION['carrito'][$id]['cantidad']);
                $total = $this->precioTotal();
                $this->cartService->updateTotalPrice($cart['id'], $total);
            }
        }

        $this->renderCartView();
    }

    /**
     * Metodo que añade el carrito a la base de datos
     * @return void
     */
    public function addToDatabase(){
        $this->cartService->addToCart();
    }

    /**
     * Metodo que carga el carrito de la base de datos
     * @return void
     */
    public function loadFromDatabase(){
        $cart = $this->cartService->loadCart();

        if (empty($cart)) {
            return null;
        }

        $cartItems = $this->cartItemService->loadCartItems($cart['id']);

        //die(var_dump($cartItems));

        if (!empty($cartItems)) {  
    
            foreach ($cartItems as $item) {
                $products = $this->productService->informacionProducto($item['product_id']);
                //die(var_dump($products));
                $_SESSION['carrito'][$item['product_id']] = [
                    'id' => $item['product_id'],
                    'imagen' => $products[0]['imagen'],  
                    'nombre' => $products[0]['nombre'],  
                    'precio' => $item['price'],
                    'cantidad' => $item['quantity'],
                    'stock' => $products[0]['stock']
                ];
            }
        }

        return $cart;
    }

    /**
     * Metodo que actualiza la cantidad de un producto del carrito
     * en la base de datos
     * @var int con el id del producto a actualizar
     * @var int con la cantidad a actualizar
     * @return void
     */
    public function updateProductQuantity(int $id, int $quantity) {
        if (isset($_SESSION['carrito'][$id])) {
            $_SESSION['carrito'][$id]['cantidad'] = $quantity;
    
            if (isset($_SESSION['usuario'])) {
                $cart = $this->cartService->loadCart();
                if ($cart) {
                    $this->cartItemService->updateCartItem($cart['id'], $id, $quantity);
                }
            }
        }
    
        header("Location: " . BASE_URL . "carrito/carrito");
    }
    
}