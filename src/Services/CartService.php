<?php

namespace Services;

use Models\Cart;
use Repositories\CartRepository;

/**
 * Clase que recibe los datos de CartController
 * y se los pasa al repository
 */
class CartService {
    /**
     * Variable para establecer la conexion el repository
     */
    private CartRepository $repository;

    /**
     * Constructor que inicializa las variables
     */
    public function __construct() {
        $this->repository = new CartRepository();
    }

    /**
     * Metodo que llama al repositorio para añadir el 
     * carrito a la base de datos
     * @return void
     */
    public function addToCart(){
        return $this->repository->addToCart();
    }

    /**
     * Metodo que llama al repositorio para cargar el 
     * carrito de la base de datos
     * @return array
     */
    public function loadCart(){
        return $this->repository->loadCart();
    }   
    
    /**
     * Metodo que llama al repositorio para borrar el 
     * carrito de la base de datos
     * @var int con el id del usuario del cual borrar el
     * carrito
     * @return bool
     */
    public function deleteCart(int $userId): bool {
        return $this->repository->deleteCart($userId);
    }

    /**
     * Metodo que actualiza el precio total del carrito
     * @var int con el id del carrito al que actualizar el precio
     * @var float con el precio a actualizar
     * @return bool
     */
    public function updateTotalPrice(int $cartId, float $totalPrice): bool {
        return $this->repository->updateTotalPrice($cartId, $totalPrice);
    }

}