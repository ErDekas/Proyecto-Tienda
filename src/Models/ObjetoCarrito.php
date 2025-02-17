<?php

namespace Models;

class ObjetoCarrito {

    public function __construct(
        private ?int $id = null,
        private int $cart_id = 0,
        private int $product_id = 0,
        private int $quantity = 0,
        private float $price = 0,
    ) {
    }

    // Getters and setters
    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getCartId(): int {
        return $this->cart_id;
    }

    public function setCartId(int $CartId): void {
        $this->cart_id = $CartId;
    }

    public function getProductId(): int {
        return $this->product_id;
    }

    public function setProductId(int $productId): void {
        $this->product_id = $productId;
    }

    public function getQuantity(): int {
        return $this->quantity;
    }
    
    public function setQuantity(int $quantity): void {
        $this->quantity = $quantity;
    }

    public function getPrice(): float {
        return $this->price;
    }

    public function setPrice(float $price): void {
        $this->price = $price;
    }
}