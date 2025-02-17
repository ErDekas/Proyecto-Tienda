CREATE DATABASE tienda;
SET NAMES UTF8;
CREATE DATABASE IF NOT EXISTS tienda;
USE tienda;

DROP TABLE IF EXISTS usuarios;
CREATE TABLE IF NOT EXISTS usuarios( 
id              int(255) auto_increment not null,
nombre          varchar(100) not null,
apellidos       varchar(255),
email           varchar(255) not null,
password        varchar(255) not null,
rol             varchar(20),
confirmado      tinyint(1) DEFAULT 0,
token           varchar(255),
token_exp       datetime,
CONSTRAINT pk_usuarios PRIMARY KEY(id),
CONSTRAINT uq_email UNIQUE(email)  
)ENGINE=InnoDb DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS categorias;
CREATE TABLE IF NOT EXISTS categorias(
id              int(255) auto_increment not null,
nombre          varchar(100) not null,
CONSTRAINT pk_categorias PRIMARY KEY(id) 
)ENGINE=InnoDb DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS productos;
CREATE TABLE IF NOT EXISTS productos(
id              int(255) auto_increment not null,
categoria_id    int(255) not null,
nombre          varchar(100) not null,
descripcion     text,
precio          float(100,2) not null,
stock           int(255) not null,
oferta          varchar(2),
fecha           date not null,
imagen          varchar(255),
CONSTRAINT pk_categorias PRIMARY KEY(id),
CONSTRAINT fk_producto_categoria FOREIGN KEY(categoria_id) REFERENCES categorias(id)
)ENGINE=InnoDb DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS pedidos;
CREATE TABLE IF NOT EXISTS pedidos(
id              int(255) auto_increment not null,
usuario_id      int(255) not null,
provincia       varchar(100) not null,
localidad       varchar(100) not null,
direccion       varchar(255) not null,
coste           float(200,2) not null,
estado          varchar(20) not null,
fecha           date,
hora            time,
CONSTRAINT pk_pedidos PRIMARY KEY(id),
CONSTRAINT fk_pedido_usuario FOREIGN KEY(usuario_id) REFERENCES usuarios(id)
)ENGINE=InnoDb DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS lineas_pedidos;
CREATE TABLE IF NOT EXISTS lineas_pedidos(
id              int(255) auto_increment not null,
pedido_id       int(255) not null,
producto_id     int(255) not null,
unidades        int(255) not null,
CONSTRAINT pk_lineas_pedidos PRIMARY KEY(id),
CONSTRAINT fk_linea_pedido FOREIGN KEY(pedido_id) REFERENCES pedidos(id),
CONSTRAINT fk_linea_producto FOREIGN KEY(producto_id) REFERENCES productos(id)
)ENGINE=InnoDb DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS carts;
CREATE TABLE carts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_price DECIMAL(10, 2),
    CONSTRAINT fk_UsuarioCarrito FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

DROP TABLE IF EXISTS cart_items;
CREATE TABLE cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cart_id INT,
    product_id INT,
    quantity INT DEFAULT 1,
    price DECIMAL(10, 2) NOT NULL,
    CONSTRAINT fk_carritoID FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
    CONSTRAINT fk_Producto FOREIGN KEY (product_id) REFERENCES productos(id) ON DELETE CASCADE
);


/* La contraseña es esta: Admin.42 */
INSERT INTO usuarios (nombre, apellidos, email, password, rol, confirmado, token, token_exp)
VALUES 
('Admin', '42', 'admin@gmail.com', '$2y$10$tWdWmBNDtceabU5UQ2y3xOkprvU8OVrL9wuCOPYoGG0FOtaEwnU42', 'admin', 1, NULL, NULL);