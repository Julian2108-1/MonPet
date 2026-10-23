DROP DATABASE IF EXISTS monpet;
CREATE DATABASE monpet CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE monpet;

-- ==========================
-- TABLA ROLES
-- ==========================

CREATE TABLE roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(30) NOT NULL UNIQUE
);

INSERT INTO roles (nombre_rol) VALUES
('Administrador'),
('Veterinario'),
('Voluntario'),
('Adoptante');

-- ==========================
-- TABLA ESTADOS
-- ==========================

CREATE TABLE estados (
    id_estado INT AUTO_INCREMENT PRIMARY KEY,
    nombre_estado VARCHAR(20) NOT NULL UNIQUE
);

INSERT INTO estados (nombre_estado) VALUES
('Activo'),
('Inactivo');

-- ==========================
-- TABLA TIPOS DE DOCUMENTO
-- ==========================

CREATE TABLE tipos_documento (
    id_tipo_documento INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO tipos_documento (nombre) VALUES
('Cédula de ciudadanía'),
('Tarjeta de identidad'),
('Cédula de extranjería'),
('Pasaporte');

-- ==========================
-- TABLA USUARIOS
-- ==========================

CREATE TABLE usuarios (

    id_usuario INT AUTO_INCREMENT PRIMARY KEY,

    nombres VARCHAR(50) NOT NULL,

    apellidos VARCHAR(50) NOT NULL,

    id_tipo_documento INT NOT NULL,

    numero_documento VARCHAR(20) NOT NULL UNIQUE,

    fecha_nacimiento DATE NOT NULL,

    genero ENUM('Masculino', 'Femenino', 'Otro') NOT NULL,

    correo VARCHAR(100) NOT NULL UNIQUE,

    telefono VARCHAR(20),

    direccion VARCHAR(150) NOT NULL,

    ciudad VARCHAR(30) NOT NULL,

    contrasena VARCHAR(255) NOT NULL,

    id_rol INT NOT NULL DEFAULT 4,

    id_estado INT NOT NULL DEFAULT 1,

    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_usuario_documento
        FOREIGN KEY (id_tipo_documento)
        REFERENCES tipos_documento(id_tipo_documento),

    CONSTRAINT fk_usuario_rol
        FOREIGN KEY (id_rol)
        REFERENCES roles(id_rol),

    CONSTRAINT fk_usuario_estado
        FOREIGN KEY (id_estado)
        REFERENCES estados(id_estado)

);

-- ==========================
-- ADMINISTRADOR INICIAL
-- Contraseña: Admin123*
-- ==========================

