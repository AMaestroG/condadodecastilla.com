<?php

$pdo = new PDO('sqlite::memory:');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec("CREATE TABLE museo_piezas (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    titulo TEXT,
    descripcion TEXT,
    autor TEXT,
    imagen_nombre TEXT,
    fecha_subida TEXT,
    notas_adicionales TEXT,
    pos_x REAL,
    pos_y REAL,
    pos_z REAL,
    escala REAL,
    rotacion_y REAL
);");
$pdo->exec("CREATE TABLE fotos_galeria (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    titulo TEXT,
    descripcion TEXT,
    autor TEXT,
    imagen_nombre TEXT,
    fecha_subida TEXT
);");
$pdo->exec("CREATE TABLE tienda_productos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nombre TEXT,
    descripcion TEXT,
    precio REAL,
    imagen_nombre TEXT,
    stock INTEGER,
    created_at TEXT
);");
$pdo->exec("CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE,
    password_hash TEXT,
    role TEXT
);");
$pdo->exec("INSERT INTO museo_piezas (titulo, descripcion, autor, imagen_nombre, fecha_subida) VALUES ('pieza1','desc','autor','img.jpg','2024-01-01 00:00:00');");
$pdo->exec("INSERT INTO fotos_galeria (titulo, descripcion, autor, imagen_nombre, fecha_subida) VALUES ('foto1','desc','autor','img.jpg','2024-01-01 00:00:00');");
$pdo->exec("INSERT INTO tienda_productos (nombre, descripcion, precio, imagen_nombre, stock, created_at) VALUES ('prod1','desc',10.5,'img.jpg',5,'2024-01-01 00:00:00');");
$hash = password_hash('secret', PASSWORD_DEFAULT);
$pdo->exec("INSERT INTO users (username, password_hash, role) VALUES ('admin', '{$hash}', 'admin');");
