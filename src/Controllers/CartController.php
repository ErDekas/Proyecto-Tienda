<?php

namespace Controllers;

use Lib\Pages;
use Models\Product;
use Lib\Utils;
use Models\Cart;
use Services\ProductosServicio;
use Services\CategoriaServicio;
use Services\CartService;
use Services\CartItemService;

/**
 * Clase para controlar el carrito
 */
class CartController
{

    /**
     * Variables privadas del controlador
     */
    private Pages $pages;
    private Utils $utils;
    private ProductosServicio $productService;
    private CartItemService $cartItemService;
    private CartService $cartService;

    /**
     * Constructor para inicializar las variables
     */
    public function __construct()
    {
        $this->pages = new Pages();
        $this->utils = new Utils();
        $this->productService = new ProductosServicio();
        $this->cartService = new CartService();
        $this->cartItemService = new CartItemService();
    }

    /**
     * Método que renderiza la vista del carrito
     * @return void
     */
    public function loadCart()
    {
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        $total = $this->priceTotal();

        $_SESSION['totalCost'] = $total;

        if (isset($_SESSION['usuario'])) {
            $cartUser = $this->loadFromDatabase();
            if (empty($cartUser)) {
                $this->addToDatabase();
            }
        }

        $this->pages->render('Cart/cart');
    }

    /**
     * Metodo que calcula el precio total del carrito
     * @return int $total -> Varaible con el precio total del carrito
     */
    public function priceTotal()
    {
        $total = 0;


        if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
            foreach ($_SESSION['carrito'] as $item) {
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
    public function addProduct(int $id)
    {

        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = array();
        }


        $productCart = $this->productService->informacionProducto($id);

        if (isset($_SESSION['carrito'][$id])) {
            if ($_SESSION['carrito'][$id]['cantidad'] === $_SESSION['carrito'][$id]['stock']) {
                //$this->loadCart(); 
                header("Location: " . BASE_URL . "Cart/loadCart");
                exit;
            } else {
                $_SESSION['carrito'][$id]['cantidad'] += 1;
            }
        } else {
            $_SESSION['carrito'][$id] = array(
                'id' => $productCart[0]['id'],
                'imagen' => $productCart[0]['imagen'],
                'nombre' => $productCart[0]['nombre'],
                'precio' => $productCart[0]['precio'],
                'stock' => $productCart[0]['stock'],
                'cantidad' => 1
            );
        }


        if (isset($_SESSION['usuario'])) {
            $cart = $this->cartService->loadCart();

            if ($cart) {
                $this->cartItemService->addNewProductsToCart($cart['id']);

                $total = $this->priceTotal();
                $this->cartService->updateTotalPrice($cart['id'], $total);
            }
        }
        $this->loadCart();
    }

    /**
     * Metodo que vacia el carrito y redirije a la vista
     * @return void
     */
    public function clearCart()
    {
        unset($_SESSION['carrito']);

        if (isset($_SESSION['usuario'])) {
            $this->cartService->deleteCart($_SESSION['usuario']['id']);
        }
        header("Location: " . BASE_URL . "Cart/loadCart");
    }

    /**
     * Metodo que elimina un producto del carrito y tras eso renderiza la vista
     * @var id id del producto a quitar del carrito´
     * @return void
     */
    public function removeItem(int $id)
    {
        if (isset($_SESSION['carrito'][$id])) {
            unset($_SESSION['carrito'][$id]);

            if (isset($_SESSION['usuario'])) {
                $cart = $this->cartService->loadCart();

                if ($cart) {
                    $this->cartItemService->deleteItemFromCart($cart['id'], $id);
                }
            }

            $total = $this->priceTotal();

            $this->cartService->updateTotalPrice($cart['id'], $total);

            //$this->loadCart();
            header("Location: " . BASE_URL . "Cart/loadCart");
        } else {
            $errorRemove = 'Error al borrar el producto';
            $total = $this->priceTotal();

            $this->pages->render('Cart/cart', ['errorRemove' => $errorRemove, 'total' => $total]);
        }
    }

    /**
     * Metod que decrementa la cantidad de un producto y renderiza la vista
     * @var id id del produto a decrementar su cantidad
     * @return void
     */
    public function downAmount(int $id)
    {
        if (isset($_SESSION['carrito'][$id])) {
            $_SESSION['carrito'][$id]['cantidad'] -= 1;


            /* Actualizar cantidad en la base de datos si existe el producto en el
            carrito del usuario y borrar el producto si es 0 */
            if (isset($_SESSION['usuario'])) {
                $cart = $this->cartService->loadCart();
                if ($cart) {
                    if ($_SESSION['carrito'][$id]['cantidad'] === 0) {
                        $this->cartItemService->deleteItemFromCart($cart['id'], $id);
                    } else {
                        $this->cartItemService->updateCartItem($cart['id'], $id, $_SESSION['carrito'][$id]['cantidad']);
                    }
                }
            }

            if ($_SESSION['carrito'][$id]['cantidad'] === 0) {
                unset($_SESSION['carrito'][$id]);
            }

            $total = $this->priceTotal();

            $this->cartService->updateTotalPrice($cart['id'], $total);

            header("Location: " . BASE_URL . "Cart/loadCart");
        } else {
            $error = 'Error al quitar unidades';
            $total = $this->priceTotal();


            $this->pages->render('Cart/cart', ['error' => $error, 'total' => $total]);
        }
    }

    /**
     * Metod que aumenta la cantidad de un producto y renderiza la vista
     * @var id id del produto a aumentar su cantidad
     * @return void
     */
    public function upAmount(int $id)
    {
        if (isset($_SESSION['carrito'][$id])) {
            if ($_SESSION['carrito'][$id]['cantidad'] === $_SESSION['carrito'][$id]['stock']) {
                //$this->loadCart(); 
                header("Location: " . BASE_URL . "Cart/loadCart");
            } else {
                $_SESSION['carrito'][$id]['cantidad'] += 1;
                /* Actualizar cantidad en la base de datos si existe el producto en el
                carrito del usuario */
                if (isset($_SESSION['usuario'])) {
                    $cart = $this->cartService->loadCart();
                    if ($cart) {
                        $this->cartItemService->updateCartItem($cart['id'], $id, $_SESSION['carrito'][$id]['cantidad']);

                        $total = $this->priceTotal();

                        $this->cartService->updateTotalPrice($cart['id'], $total);
                    }
                }
                header("Location: " . BASE_URL . "Cart/loadCart");
            }
        } else {
            $error = 'Error al añadir unidades';
            $total = $this->priceTotal();

            $this->pages->render('Cart/cart', ['error' => $error, 'total' => $total]);
        }
    }

    /**
     * Metodo que añade el carrito a la base de datos
     * @return void
     */
    public function addToDatabase()
    {
        $this->cartService->addToCart();
    }

    /**
     * Metodo que carga el carrito de la base de datos
     * @return void
     */
    public function loadFromDatabase()
    {
        $cart = $this->cartService->loadCart();

        if (empty($cart)) {
            return null;
        }

        $cartItems = $this->cartItemService->loadCartItems($cart['id']);

        //die(var_dump($cartItems));

        if (!empty($cartItems)) {

            foreach ($cartItems as $item) {
                $products = $this->productService->informacionProducto($item['product_id']);

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
    public function updateProductQuantity(int $id, int $quantity)
    {
        if (isset($_SESSION['carrito'][$id])) {
            $_SESSION['carrito'][$id]['cantidad'] = $quantity;

            if (isset($_SESSION['usuario'])) {
                $cart = $this->cartService->loadCart();
                if ($cart) {
                    $this->cartItemService->updateCartItem($cart['id'], $id, $quantity);
                }
            }
        }

        header("Location: " . BASE_URL . "Cart/loadCart");
    }

    /**
     * Método para sincronizar el carrito después del login
     * Si existe un carrito en BD con total_price > 0, se mantiene ese
     * Si no existe carrito en BD o total_price = 0, se usa el de sesión
     * @return void
     */
    public function syncCartAfterLogin() {
        // Verificar si hay un carrito existente en la base de datos
        $existingCart = $this->cartService->loadCart();

        // Si no hay carrito en sesión, no hay nada que sincronizar
        if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
            return;
        }

        // Caso 1: No hay carrito en BD o el total es 0
        if (empty($existingCart) || $existingCart['total_price'] == 0) {
            $this->syncSessionCartToDatabase();
        }
        // Caso 2: Hay carrito en BD con total > 0
        else if ($existingCart['total_price'] > 0) {
            $this->loadDatabaseCartToSession($existingCart['id']);
        }
    }

    /**
     * Sincroniza el carrito de sesión a la base de datos
     * @return void
     */
    private function syncSessionCartToDatabase() {
        // Calcular el total del carrito de sesión
        $total = $this->priceTotal();
        $_SESSION['totalCost'] = $total;

        // Crear nuevo carrito en BD
        $success = $this->cartService->addToCart();
        
        if ($success === true) {
            // El carrito se creó exitosamente, ahora sincronizamos los items
            $newCart = $this->cartService->loadCart();
            if (!empty($newCart)) {
                $this->cartItemService->addNewProductsToCart($newCart['id']);
            }
        }
    }

    /**
     * Carga el carrito de la base de datos a la sesión
     * @param int $cartId ID del carrito en la base de datos
     * @return void
     */
    private function loadDatabaseCartToSession(int $cartId) {
        // Limpiar el carrito de sesión actual
        unset($_SESSION['carrito']);
        $_SESSION['carrito'] = [];

        // Cargar items del carrito de la BD
        $cartItems = $this->cartItemService->loadCartItems($cartId);

        // Reconstruir el carrito en la sesión
        foreach ($cartItems as $item) {
            $productInfo = $this->productService->informacionProducto($item['product_id']);
            if (!empty($productInfo)) {
                $_SESSION['carrito'][$item['product_id']] = [
                    'id' => $item['product_id'],
                    'imagen' => $productInfo[0]['imagen'],
                    'nombre' => $productInfo[0]['nombre'],
                    'precio' => $item['price'],
                    'cantidad' => $item['quantity'],
                    'stock' => $productInfo[0]['stock']
                ];
            }
        }

        // Actualizar el total en la sesión
        $_SESSION['totalCost'] = $this->priceTotal();
    }
}
