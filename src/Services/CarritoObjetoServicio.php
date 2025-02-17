<?php

namespace Services;

use Models\ObjetoCarrito;
use Repositories\ObjetoCarritoRepository;

/**
 * Clase que recibe los datos de CartController
 * y se los pasa al repository
 */
class CarritoObjetoServicio {
    /**
     * Variable para establecer la conexion el repository
     */
    private ObjetoCarritoRepository $repository;

    /**
     * Constructor que inicializa las variables
     */
    public function __construct() {
        $this->repository = new ObjetoCarritoRepository();
    }

    /**
     * Metodo que llama al repositorio para cargar los productos del 
     * carrito de la base de datos
     * @var int con el id del carrito del cual cargar los productos
     * @return array
     */
    public function loadCartItems(int $id){
        return $this->repository->loadCartItems($id);
    }    

    /**
     * Metodo que actualiza la cantidad de un producto del carrito en la base de datos
     * @var int con el id del carrito donde esta el producto
     * @var int con el id del producto a actualizar
     * @var int con la cantidad que se va a actualizar
     * @return bool
     */
    public function updateCartItem(int $cartId, int $productId, int $quantity): bool {
        return $this->repository->updateCartItem($cartId, $productId, $quantity);
    }
    
    /**
     * Metodo que añade un nuevo producto al carrito
     * @var int con el carrito al que añadir el producto
     * @return bool|string
     */
    public function addNewProductsToCart(int $cartId): bool|string {
        return $this->repository->addNewProductsToCart($cartId);
    }

    /**
     * Metodo que llama al repository para eliminar un producto
     * del carrito
     * @var int con el id del carrito donde esta el producto
     * @var int con el id del producto a eliminar
     * @return bool|string
     */
    public function deleteItemFromCart(int $cartId, int $productId): bool|string {
        return $this->repository->deleteItemFromCart($cartId, $productId);
    }

}