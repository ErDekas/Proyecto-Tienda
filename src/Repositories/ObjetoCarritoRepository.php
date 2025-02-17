<?php

namespace Repositories;

use Lib\BaseDatos;
use Models\ObjetoCarrito;
use PDO;
use PDOException;

/**
 * Clase que realiza las consultas a la tabla carts
 */
class ObjetoCarritoRepository {
    /**
     * Variables para establecer la conexion con la base de datos
     */
    private BaseDatos $conexion;

    /**
     * Constructor que inicializa las variables
     */
    public function __construct() {
        $this->conexion = new BaseDatos();
    }


    
    /**
     * Metodo que guarda los productos de un carrito en la base de datos
     * @var int con el id del carrito del cual guardar el producto
     * @return bool|string
     */
    public function saveItemsCart(int $cartId): bool|string {
        try {

            $this->conexion->empezarTransaccion();

            $stmt = $this->conexion->prepare(
                "INSERT INTO cart_items (cart_id, product_id, quantity, price)
                 VALUES (:cartId, :productId, :quantity, :price)"
            );

            if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
                return false; // No hay productos en el carrito, no hacemos nada
            }

            foreach ($_SESSION['carrito'] as $producto) {
                $stmt->bindValue(':cartId', $cartId, PDO::PARAM_INT);
                $stmt->bindValue(':productId', $producto['id'], PDO::PARAM_INT);
                $stmt->bindValue(':quantity', $producto['cantidad'], PDO::PARAM_INT);
                $stmt->bindValue(':price', number_format($producto['precio'], 2, '.', ''), PDO::PARAM_STR);

                $stmt->execute();
            }

            $this->conexion->commit();

            return true;
        } 
        catch (PDOException $e) {
            $this->conexion->deshacer();
            return $e->getMessage();
        }
    }

    /**
     * Metodo que obtiene todas los productos del carrito del usuario de la base de datos
     * @var int con el id del carrito del cual obtener los productos
     * @return array
     */
    public function loadCartItems(int $id): array {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM cart_items WHERE cart_id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Error al obtener los productos del carrito: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Metodo que actualiza la cantidad de un producto del carrito en la base de datos
     * @var int con el carrito donde estan los productos
     * @var int con el id del producto a actualizar
     * @var int con la cantidad a actulizar
     * @return bool
     */
    public function updateCartItem(int $cartId, int $productId, int $quantity): bool {
        try {
            $stmt = $this->conexion->prepare(
                "UPDATE cart_items SET quantity = :quantity WHERE cart_id = :cartId AND product_id = :productId"
            );

            $stmt->bindValue(':quantity', $quantity, PDO::PARAM_INT);
            $stmt->bindValue(':cartId', $cartId, PDO::PARAM_INT);
            $stmt->bindValue(':productId', $productId, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Metodo que añade nuevos productos al carrito
     * @var int con el carrito al que añadir nuevos productos
     */
    public function addNewProductsToCart(int $cartId){
        try {
            // Transaction para que si falla alguna inserccion no se haga ninguna
            $this->conexion->empezarTransaccion();
    
            // Comprobar si el producto ya está en el carrito
            foreach ($_SESSION['carrito'] as $producto) {
                
                $stmt = $this->conexion->prepare("SELECT * FROM cart_items WHERE cart_id = :cartId AND product_id = :productId");
                $stmt->bindValue(':cartId', $cartId, PDO::PARAM_INT);
                $stmt->bindValue(':productId', $producto['id'], PDO::PARAM_INT);
                $stmt->execute();
    
                $existingProduct = $stmt->fetch(PDO::FETCH_ASSOC);
    
                if ($existingProduct) {
                    // Si el producto ya está se actualiza la cantidad
                    $stmt = $this->conexion->prepare(
                        "UPDATE cart_items SET quantity = :quantity WHERE cart_id = :cartId AND product_id = :productId"
                    );
                    $stmt->bindValue(':cartId', $cartId, PDO::PARAM_INT);
                    $stmt->bindValue(':productId', $producto['id'], PDO::PARAM_INT);
                    $stmt->bindValue(':quantity', $existingProduct['quantity'] + $producto['cantidad'], PDO::PARAM_INT);
                    $stmt->execute();
                } else {
                    // Si el producto no está en el carrito se inserta
                    $stmt = $this->conexion->prepare(
                        "INSERT INTO cart_items (cart_id, product_id, quantity, price) VALUES (:cartId, :productId, :quantity, :price)"
                    );
                    $stmt->bindValue(':cartId', $cartId, PDO::PARAM_INT);
                    $stmt->bindValue(':productId', $producto['id'], PDO::PARAM_INT);
                    $stmt->bindValue(':quantity', $producto['cantidad'], PDO::PARAM_INT);
                    $stmt->bindValue(':price', number_format($producto['precio'], 2, '.', ''), PDO::PARAM_STR);
                    $stmt->execute();
                }
            }
    
            $this->conexion->commit();
    
            return true;
        } catch (PDOException $e) {
            $this->conexion->deshacer();
            return $e->getMessage();
        }
    }


    /**
     * Metod que borrar un elemento del carrito en la base de datos
     * @var int con el id del carrito del cual borrar el elemento
     * @var int con el id del producto a borrar
     * @return bool|string
     */
    public function deleteItemFromCart(int $cartId, int $productId): bool|string {
        try {
            $stmt = $this->conexion->prepare(
                "DELETE FROM cart_items WHERE cart_id = :cartId AND product_id = :productId"
            );
            $stmt->bindValue(':cartId', $cartId, PDO::PARAM_INT);
            $stmt->bindValue(':productId', $productId, PDO::PARAM_INT);
            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            return $e->getMessage();
        }
}


}