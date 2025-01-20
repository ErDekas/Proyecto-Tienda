<?php

namespace Controllers;

use Lib\Pages;
use Models\Productos;
use Lib\Utils;
use Services\ProductosServicio;
use Services\CategoriaServicio;


class CarritoController
{


    private Pages $pages;
    private Utils $utils;
    private ProductosServicio $productosServicio;
    private CategoriaServicio $categoriaServicio;


    public function __construct()
    {
        $this->pages = new Pages();
        $this->utils = new Utils();
        $this->productosServicio = new ProductosServicio();
        $this->categoriaServicio = new CategoriaServicio();
    }

    // Metodo que carga la vista del carrito
    public function cargarCarrito()
    {
        $total = $this->precioTotal();

        $_SESSION['costeTotal'] = $total;

        $this->pages->render('carrito/carrito');
    }

    // Metodo que calcula el precio total del carrito
    public function precioTotal()
    {
        $total = 0;


        if (isset($_SESSION['carrito']) || !empty($_SESSION['carrito'])) {
            foreach ($_SESSION['carrito'] as $item) {
                $total += $item['precio'] * $item['cantidad'];
            }
        }

        return $total;
    }


    // Metodo que añade un producto al carrito  
    public function anadirProducto(int $id)
    {

        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = array();
        }


        $productCart = $this->productosServicio->informacionProducto($id);

        if (isset($_SESSION['carrito'][$id])) {
            $_SESSION['carrito'][$id]['cantidad'] += 1;
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

        $this->cargarCarrito();
    }

    // Metodo que limpia el carrito
    public function limpiarCarrito()
    {
        unset($_SESSION['carrito']);

        header("Location: " . BASE_URL . "carrito/cargarCarrito");
    }

    // Metodo que borra un objeto del carrito
    public function eliminarProducto(int $id)
    {
        if (isset($_SESSION['carrito'][$id])) {
            unset($_SESSION['carrito'][$id]);

            header("Location: " . BASE_URL . "carrito/cargarCarrito");
        } else {
            $errorRemove = 'Error al borrar el producto';
            $total = $this->precioTotal();

            $this->pages->render('carrito/carrito', ['errorRemove' => $errorRemove, 'total' => $total]);
        }
    }

    // Metodo que disminuye la cantidad de un producto y renderiza la vista
    public function bajarMonto(int $id)
    {
        if (isset($_SESSION['carrito'][$id])) {
            $_SESSION['carrito'][$id]['cantidad'] -= 1;

            if ($_SESSION['carrito'][$id]['cantidad'] === 0) {
                unset($_SESSION['carrito'][$id]);
            }

            header("Location: " . BASE_URL . "carrito/cargarCarrito");
        } else {
            $error = 'Error al quitar unidades';
            $total = $this->precioTotal();

            $this->pages->render('carrito/carrito', ['error' => $error, 'total' => $total]);
        }
    }

    // Metodo que aumenta la cantidad de un producto y renderiza la vista   
    public function aumentarMonto(int $id)
    {
        if (isset($_SESSION['carrito'][$id])) {
            if ($_SESSION['carrito'][$id]['cantidad'] === $_SESSION['carrito'][$id]['stock']) {
                header("Location: " . BASE_URL . "carrito/cargarCarrito");
            } else {
                $_SESSION['carrito'][$id]['cantidad'] += 1;
                header("Location: " . BASE_URL . "carrito/cargarCarrito");
            }
        } else {
            $error = 'Error al añadir unidades';
            $total = $this->precioTotal();

            $this->pages->render('carrito/carrito', ['error' => $error, 'total' => $total]);
        }
    }
}
