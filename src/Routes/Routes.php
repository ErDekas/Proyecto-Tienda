<?php
namespace Routes;

use Lib\Router;
use Controllers\CategoriaController;
use Controllers\ProductoController;
use Controllers\ErrorController;
use Controllers\UsuarioController;
use Controllers\CarritoController;
use Controllers\PedidoController;
use Models\Pedido;

class Routes {
    public static function index() {
        Router::add('GET', '/', function() {
            (new CategoriaController())->obtenerCategorias();
        });

        // Rutas categorias

        Router::add('GET', 'categorias/index', function() {
            (new CategoriaController())->obtenerCategorias();
        });

        Router::add('GET', '/categorias/crear', function() {
            (new CategoriaController())->insertarCategoria();
        });

        Router::add('GET', '/categorias/actualizar', function() {
            (new CategoriaController())->actualizarCategoria();
        });

        Router::add('GET', '/categorias/borrar', function() {
            (new CategoriaController())->eliminarCategorias();
        });

        Router::add('GET','/productos/index/:id',function(int $id){
            (new CategoriaController())->productosPorCategoria($id);
        });

        Router::add('POST', '/categorias/crear', function() {
            (new CategoriaController())->insertarCategoria();
        });

        Router::add('POST', '/categorias/actualizar', function() {
            (new CategoriaController())->actualizarCategoria();
        });

        Router::add('POST', '/categorias/borrar', function() {
            (new CategoriaController())->eliminarCategorias();
        });

        
        // Rutas productos

        Router::add('GET', 'productos/index', function() {
            (new ProductoController())->obtenerProductos();
        });
        Router::add('GET', '/productos/crear', function() {
            (new ProductoController())->insertarProducto();
        });
        Router::add('POST', '/productos/crear', function() {
            (new ProductoController())->insertarProducto();
        });
        Router::add('GET', '/productos/actualizar', function() {
            (new ProductoController())->actualizarProducto();
        });
        Router::add('POST', '/productos/actualizar', function() {
            (new ProductoController())->actualizarProducto();
        });
        Router::add('GET', '/productos/borrar', function() {
            (new ProductoController())->borrarProducto();
        });
        Router::add('POST', '/productos/borrar', function() {
            (new ProductoController())->borrarProducto();
        });
        Router::add('GET', '/productos/productoInfo:id', function(int $id) {
            (new ProductoController())->informacionProducto($id);
        });


        // Rutas Usuarios
        Router::add('GET','/usuarios/registrar',function(){
            (new UsuarioController())->registrar();
        });

        Router::add('POST','/usuarios/registrar',function(){
            (new UsuarioController())->registrar();
        });

        Router::add('GET','/usuarios/iniciarSesion',function(){
            (new UsuarioController())->iniciarSesion();
        });

        Router::add('POST','/usuarios/iniciarSesion',function(){
            (new UsuarioController())->iniciarSesion();
        });

        Router::add('GET','/usuarios/cerrarSesion',function(){
            (new UsuarioController())->logout();
        });

        Router::add('GET','/usuarios/datosUsuario',function(){
            (new UsuarioController())->verTusDatos();
        });


        // Rutas carrito

        Router::add('GET','carrito/cargarCarrito',function(){
            (new CarritoController())->cargarCarrito();
        });

        Router::add('GET', 'carrito/anadirProducto/:id', function(int $id) {
            (new CarritoController())->anadirProducto($id);
        });

        Router::add('GET', 'carrito/eliminarProducto/:id', function(int $id) {
            (new CarritoController())->eliminarProducto($id);
        });

        Router::add('GET', 'carrito/limpiarCarrito', function() {
            (new CarritoController())->limpiarCarrito();
        });

        Router::add('GET', 'carrito/bajarMonto/:id', function(int $id) {
            (new CarritoController())->bajarMonto($id);
        });

        Router::add('GET', 'carrito/aumentarMonto/:id', function(int $id) {
            (new CarritoController())->aumentarMonto($id);
        }); 


        // Rutas Pedidos
        Router::add('GET', 'pedido/autenticarPedido', function(){
            (new PedidoController())->autenticarPedido();
        });
        Router::add('GET', 'pedido/guardarPedido', function(){
            (new PedidoController())->guardarPedido();
        });
        Router::add('POST', 'pedido/guardarPedido', function(){
            (new PedidoController())->guardarPedido();
        });
        Router::add('GET', 'pedido/pedidos', function(){
            (new PedidoController())->verPedidos();
        });

        Router::add('GET','/error404',function(){
            ErrorController::error404();
        });


        Router::dispatch();
    }
}
