<?php

namespace Repositories;

use Lib\BaseDatos;
use Models\Cart;
use Models\ObjetoCarrito;
use PDO;
use PDOException;
use Repositories\ObjetoCarritoRepository;

/**
 * Clase que realiza las consultas a la tabla carts
 */
class CarritoRepository {
    /**
     * Variables para establecer la conexion con la base de datos
     */
    private BaseDatos $conexion;
    private ObjetoCarritoRepository $cartItemsRepository;

    /**
     * Constructor que inicializa las variables
     */
    public function __construct() {
        $this->conexion = new BaseDatos();
        $this->cartItemsRepository = new ObjetoCarritoRepository();
    }

    /**
     * Metodo que añade un carrito a la base de datos
     * junto con sus productos
     */
    public function addToCart(){
        try {

            $stmt = $this->conexion->prepare(
                "INSERT INTO carts (user_id, total_price) VALUES (:user, :total)");

            $stmt->bindValue(':user', $_SESSION['usuario']['id'], PDO::PARAM_STR);
            $stmt->bindValue(':total', $_SESSION['totalCost'], PDO::PARAM_STR);

            $stmt->execute();

            $cartID = $this->conexion->ultimoIDInsertado();

            $this->cartItemsRepository->saveItemsCart($cartID);

            return true;
        } 
        catch (PDOException $e) {
            return $e->getMessage();
        }
    }

     /**
     * Metodo que obtiene el carrito del usuario de la base de datos
     * @return array
     */
    public function loadCart(): array {
        try {
            $stmt = $this->conexion->prepare("SELECT * FROM carts WHERE user_id = :id");

            $stmt->bindValue(':id', $_SESSION['usuario']['id'], PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Error al obtener el carrito: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Metodo que borra carrito de la base de datos
     * @var int con el id del usuario al que borrar el carrito
     * @return bool
     */
    public function deleteCart(int $userId): bool {
        try {
            $stmt = $this->conexion->prepare("DELETE FROM carts WHERE user_id = :id");
            $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }


    /**
     * Metodo que actualiza el precio total del carrito en la base de datos
     * @var int con el carrito del cual actualizar el precio
     * @var float con el precio a actualizar
     * @return bool
     */
    public function updateTotalPrice(int $cartId, float $totalPrice): bool {
        try {
         $stmt = $this->conexion->prepare(
                "UPDATE carts SET total_price = :totalPrice WHERE id = :cartId"
            );
            $stmt->bindValue(':cartId', $cartId, PDO::PARAM_INT);
            $stmt->bindValue(':totalPrice', number_format($totalPrice, 2, '.', ''), PDO::PARAM_STR);
            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            return false;
        }
}

    


}