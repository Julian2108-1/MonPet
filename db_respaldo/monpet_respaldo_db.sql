

CREATE DATABASE IF NOT EXISTS monpet_taller3
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE monpet_taller3;

-- 1. CATÁLOGOS
CREATE TABLE rol (
    rol_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE tipo_documento (
    tipo_documento_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE especie (
    id_especie INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE tipo_aporte (
    tipo_apor_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_aporte VARCHAR(100) NOT NULL UNIQUE,
    descripcion_apor VARCHAR(200),
    estado VARCHAR(50) NOT NULL DEFAULT 'activo'
) ENGINE=InnoDB;

CREATE TABLE tipo_vivienda (
    tipo_vivienda_id INT AUTO_INCREMENT PRIMARY KEY,
    descripcion_vivienda VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE administrador (
    documento VARCHAR(50) PRIMARY KEY,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    telefono VARCHAR(20),
    direccion VARCHAR(150)
) ENGINE=InnoDB;

-- 2. REPRESENTANTE LEGAL, REFUGIO Y REDES SOCIALES
CREATE TABLE representante_legal (
    documento_repre_legal VARCHAR(50) PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    direccion VARCHAR(150),
    email VARCHAR(100),
    telefono VARCHAR(20),
    estado VARCHAR(50) NOT NULL DEFAULT 'activo',
    fecha_nacimiento DATE,
    anos_experiencia INT,
    tipo_documento_id INT,
    CONSTRAINT fk_repre_tipo_documento
      FOREIGN KEY (tipo_documento_id)
      REFERENCES tipo_documento(tipo_documento_id)
      ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT chk_repre_experiencia CHECK (anos_experiencia IS NULL OR anos_experiencia >= 0)
) ENGINE=InnoDB;

CREATE TABLE refugio (
    nit VARCHAR(50) PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    ciudad VARCHAR(100),
    direccion VARCHAR(150),
    telefono VARCHAR(20),
    email VARCHAR(100),
    capacidad INT,
    estado VARCHAR(50) NOT NULL DEFAULT 'activo',
    horario_atencion VARCHAR(100),
    documento_repre_legal VARCHAR(50),
    CONSTRAINT fk_refugio_representante
      FOREIGN KEY (documento_repre_legal)
      REFERENCES representante_legal(documento_repre_legal)
      ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT chk_refugio_capacidad CHECK (capacidad IS NULL OR capacidad >= 0)
) ENGINE=InnoDB;

-- Una fila por red social. El refugio puede tener cero, una o muchas redes.
-- Se evita la relación circular refugio <-> redes_sociales_refugio del original.
CREATE TABLE redes_sociales_refugio (
    red_social_id INT AUTO_INCREMENT PRIMARY KEY,
    nit VARCHAR(50) NOT NULL,
    plataforma VARCHAR(100) NOT NULL,
    username VARCHAR(150),
    url VARCHAR(255),
    CONSTRAINT uq_red_social_refugio UNIQUE (nit, plataforma, username),
    CONSTRAINT fk_red_social_refugio
      FOREIGN KEY (nit) REFERENCES refugio(nit)
      ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE estudios_repre (
    tp_estudios_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_estudio VARCHAR(100) NOT NULL,
    certificados VARCHAR(255),
    area_estudio VARCHAR(100),
    documento_repre_legal VARCHAR(50) NOT NULL,
    CONSTRAINT fk_estudio_representante
      FOREIGN KEY (documento_repre_legal)
      REFERENCES representante_legal(documento_repre_legal)
      ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 3. VOLUNTARIOS Y HOGARES DE PASO
CREATE TABLE voluntario (
    documento_voluntario_id VARCHAR(50) PRIMARY KEY,
    nit VARCHAR(50),
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    telefono VARCHAR(20),
    direccion VARCHAR(150),
    disponibilidad VARCHAR(50),
    estado VARCHAR(50) NOT NULL DEFAULT 'activo',
    fecha_registro DATE,
    fecha_fin DATE,
    tipo_documento_id INT,
    CONSTRAINT fk_voluntario_tipo_documento
      FOREIGN KEY (tipo_documento_id)
      REFERENCES tipo_documento(tipo_documento_id)
      ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_voluntario_refugio
      FOREIGN KEY (nit) REFERENCES refugio(nit)
      ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT chk_voluntario_fechas CHECK (fecha_fin IS NULL OR fecha_registro IS NULL OR fecha_fin >= fecha_registro)
) ENGINE=InnoDB;

CREATE TABLE hogar_paso (
    hogar_paso_id INT AUTO_INCREMENT PRIMARY KEY,
    documento_responsable VARCHAR(50),
    nombre_responsable VARCHAR(100) NOT NULL,
    direccion VARCHAR(150),
    email VARCHAR(100),
    telefono VARCHAR(20),
    capacidad INT,
    estado VARCHAR(50) NOT NULL DEFAULT 'activo',
    fecha_vinculacion DATE,
    nit VARCHAR(50),
    CONSTRAINT fk_hogar_refugio
      FOREIGN KEY (nit) REFERENCES refugio(nit)
      ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT chk_hogar_capacidad CHECK (capacidad IS NULL OR capacidad >= 0)
) ENGINE=InnoDB;

-- 1FN: las especies admitidas se registran en filas, no como texto separado por comas.
CREATE TABLE hogar_paso_especie (
    hogar_paso_id INT NOT NULL,
    id_especie INT NOT NULL,
    PRIMARY KEY (hogar_paso_id, id_especie),
    CONSTRAINT fk_hpe_hogar FOREIGN KEY (hogar_paso_id)
      REFERENCES hogar_paso(hogar_paso_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_hpe_especie FOREIGN KEY (id_especie)
      REFERENCES especie(id_especie) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 4. RAZAS, MASCOTAS Y ESTADÍAS
CREATE TABLE raza (
    raza_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    id_especie INT NOT NULL,
    CONSTRAINT uq_raza_especie UNIQUE (id_especie, nombre),
    CONSTRAINT fk_raza_especie FOREIGN KEY (id_especie)
      REFERENCES especie(id_especie) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE veterinario (
    veterinario_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    telefono VARCHAR(20),
    tarjeta_profesional VARCHAR(50) UNIQUE
) ENGINE=InnoDB;

-- 3FN: catálogo de especialidades separado de los veterinarios.
CREATE TABLE especialidad (
    especialidad_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE veterinario_especialidad (
    veterinario_id INT NOT NULL,
    especialidad_id INT NOT NULL,
    PRIMARY KEY (veterinario_id, especialidad_id),
    CONSTRAINT fk_ve_veterinario FOREIGN KEY (veterinario_id)
      REFERENCES veterinario(veterinario_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_ve_especialidad FOREIGN KEY (especialidad_id)
      REFERENCES especialidad(especialidad_id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE mascota (
    mascota_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    senas VARCHAR(255),
    altura DECIMAL(5,2),
    tamano VARCHAR(50),
    color VARCHAR(50),
    foto VARCHAR(255),
    estado VARCHAR(50) NOT NULL DEFAULT 'en refugio',
    pelaje VARCHAR(50),
    longitud DECIMAL(5,2),
    fecha_nacimiento DATE,
    fecha_ingreso DATE,
    nit VARCHAR(50),
    hogar_paso_id INT,
    raza_id INT,
    documento_voluntario_id VARCHAR(50),
    veterinario_id INT,
    CONSTRAINT fk_mascota_refugio FOREIGN KEY (nit)
      REFERENCES refugio(nit) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_mascota_hogar FOREIGN KEY (hogar_paso_id)
      REFERENCES hogar_paso(hogar_paso_id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_mascota_raza FOREIGN KEY (raza_id)
      REFERENCES raza(raza_id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_mascota_voluntario FOREIGN KEY (documento_voluntario_id)
      REFERENCES voluntario(documento_voluntario_id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_mascota_veterinario FOREIGN KEY (veterinario_id)
      REFERENCES veterinario(veterinario_id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT chk_mascota_altura CHECK (altura IS NULL OR altura >= 0),
    CONSTRAINT chk_mascota_longitud CHECK (longitud IS NULL OR longitud >= 0)
) ENGINE=InnoDB;

CREATE TABLE estadia (
    estadia_id INT AUTO_INCREMENT PRIMARY KEY,
    mascota_id INT NOT NULL,
    telefono VARCHAR(20),
    motivo_salida VARCHAR(255),
    tiempo_estadia INT,
    fecha_ingreso_hogar DATE,
    fecha_salida_hogar DATE,
    hogar_paso_id INT,
    CONSTRAINT fk_estadia_mascota FOREIGN KEY (mascota_id)
      REFERENCES mascota(mascota_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_estadia_hogar FOREIGN KEY (hogar_paso_id)
      REFERENCES hogar_paso(hogar_paso_id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT chk_estadia_dias CHECK (tiempo_estadia IS NULL OR tiempo_estadia >= 0),
    CONSTRAINT chk_estadia_fechas CHECK (fecha_salida_hogar IS NULL OR fecha_ingreso_hogar IS NULL OR fecha_salida_hogar >= fecha_ingreso_hogar)
) ENGINE=InnoDB;

-- 5. PADRINOS Y APADRINAMIENTOS
CREATE TABLE padrino (
    documento_identidad VARCHAR(50) PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    telefono VARCHAR(20),
    direccion VARCHAR(150),
    pais VARCHAR(100),
    ciudad VARCHAR(100),
    fecha_nacimiento DATE,
    fecha_registro DATE,
    tipo_documento_id INT,
    CONSTRAINT fk_padrino_tipo_documento FOREIGN KEY (tipo_documento_id)
      REFERENCES tipo_documento(tipo_documento_id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE apadrinamiento (
    apadrinamiento_id INT AUTO_INCREMENT PRIMARY KEY,
    documento_identidad VARCHAR(50) NOT NULL,
    mascota_id INT NOT NULL,
    monto_aporte DECIMAL(10,2) NOT NULL,
    frecuencia_pago VARCHAR(50),
    estado VARCHAR(50) NOT NULL DEFAULT 'activo',
    fecha_inicio DATE,
    fecha_fin DATE,
    CONSTRAINT fk_apadrinamiento_padrino FOREIGN KEY (documento_identidad)
      REFERENCES padrino(documento_identidad) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_apadrinamiento_mascota FOREIGN KEY (mascota_id)
      REFERENCES mascota(mascota_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT chk_apadrinamiento_monto CHECK (monto_aporte >= 0),
    CONSTRAINT chk_apadrinamiento_fechas CHECK (fecha_fin IS NULL OR fecha_inicio IS NULL OR fecha_fin >= fecha_inicio)
) ENGINE=InnoDB;

-- 6. HISTORIA VETERINARIA, ENFERMEDADES, TRATAMIENTOS Y COSTOS
CREATE TABLE enfermedades (
    enfermedad_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_enfermedad VARCHAR(150) NOT NULL UNIQUE,
    descripcion VARCHAR(200),
    contagioso BOOLEAN,
    estado VARCHAR(100),
    nivel_gravedad VARCHAR(100)
) ENGINE=InnoDB;

CREATE TABLE tratamientos (
    tratamiento_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_tratamiento VARCHAR(100) NOT NULL UNIQUE,
    descripcion_tratamiento TEXT
) ENGINE=InnoDB;

CREATE TABLE consulta_veterinaria (
    consulta_veterinaria_id INT AUTO_INCREMENT PRIMARY KEY,
    mascota_id INT NOT NULL,
    veterinario_id INT NOT NULL,
    fecha_consulta DATETIME NOT NULL,
    diagnostico TEXT,
    proxima_consulta DATE,
    observaciones TEXT,
    CONSTRAINT fk_consulta_mascota FOREIGN KEY (mascota_id)
      REFERENCES mascota(mascota_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_consulta_veterinario FOREIGN KEY (veterinario_id)
      REFERENCES veterinario(veterinario_id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Una consulta puede incluir varios tratamientos y un tratamiento puede usarse en muchas consultas.
CREATE TABLE consulta_tratamiento (
    consulta_veterinaria_id INT NOT NULL,
    tratamiento_id INT NOT NULL,
    indicaciones TEXT,
    PRIMARY KEY (consulta_veterinaria_id, tratamiento_id),
    CONSTRAINT fk_ct_consulta FOREIGN KEY (consulta_veterinaria_id)
      REFERENCES consulta_veterinaria(consulta_veterinaria_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_ct_tratamiento FOREIGN KEY (tratamiento_id)
      REFERENCES tratamientos(tratamiento_id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE costos_atencion (
    costos_id INT AUTO_INCREMENT PRIMARY KEY,
    consulta_veterinaria_id INT NOT NULL,
    tratamiento_id INT,
    concepto VARCHAR(255) NOT NULL,
    valor DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    estado_pago VARCHAR(50) NOT NULL DEFAULT 'en proceso',
    CONSTRAINT fk_costo_consulta FOREIGN KEY (consulta_veterinaria_id)
      REFERENCES consulta_veterinaria(consulta_veterinaria_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_costo_tratamiento FOREIGN KEY (tratamiento_id)
      REFERENCES tratamientos(tratamiento_id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT chk_costo_valor CHECK (valor >= 0)
) ENGINE=InnoDB;

CREATE TABLE mascota_enfermedad (
    mascota_enfermedad_id INT AUTO_INCREMENT PRIMARY KEY,
    mascota_id INT NOT NULL,
    enfermedad_id INT NOT NULL,
    consulta_veterinaria_id INT,
    fecha_diagnostico DATE,
    observaciones VARCHAR(150),
    estado VARCHAR(100),
    CONSTRAINT fk_me_mascota FOREIGN KEY (mascota_id)
      REFERENCES mascota(mascota_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_me_enfermedad FOREIGN KEY (enfermedad_id)
      REFERENCES enfermedades(enfermedad_id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Se agrega con ALTER TABLE como ejemplo de intervención sobre una tabla ya creada.
ALTER TABLE mascota_enfermedad
  ADD CONSTRAINT fk_me_consulta FOREIGN KEY (consulta_veterinaria_id)
  REFERENCES consulta_veterinaria(consulta_veterinaria_id)
  ON DELETE SET NULL ON UPDATE CASCADE;

-- 7. USUARIOS Y ROLES. contrasena_hash debe contener un hash, nunca texto plano.
CREATE TABLE usuario (
    documento_usuario VARCHAR(50) PRIMARY KEY,
    tipo_documento_id INT,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    pais VARCHAR(100),
    ciudad VARCHAR(100),
    direccion VARCHAR(150),
    correo VARCHAR(100) NOT NULL UNIQUE,
    contrasena_hash VARCHAR(255) NOT NULL,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    rol_id INT,
    CONSTRAINT fk_usuario_tipo_documento FOREIGN KEY (tipo_documento_id)
      REFERENCES tipo_documento(tipo_documento_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_usuario_rol FOREIGN KEY (rol_id)
      REFERENCES rol(rol_id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 8. APORTES VOLUNTARIOS
CREATE TABLE aporte_voluntario (
    aporte_id INT AUTO_INCREMENT PRIMARY KEY,
    nit VARCHAR(50),
    descripcion TEXT,
    cantidad DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    tipo_apor_id INT,
    fecha_aporte DATE,
    documento_usuario VARCHAR(50),
    CONSTRAINT fk_aporte_usuario FOREIGN KEY (documento_usuario)
      REFERENCES usuario(documento_usuario) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_aporte_refugio FOREIGN KEY (nit)
      REFERENCES refugio(nit) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_aporte_tipo FOREIGN KEY (tipo_apor_id)
      REFERENCES tipo_aporte(tipo_apor_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT chk_aporte_cantidad CHECK (cantidad >= 0)
) ENGINE=InnoDB;

-- 9. REPORTES
CREATE TABLE reporte (
    reporte_id INT AUTO_INCREMENT PRIMARY KEY,
    documento_reporte VARCHAR(50),
    tipo_reporte VARCHAR(100) NOT NULL,
    estado VARCHAR(50) NOT NULL DEFAULT 'pendiente',
    descripcion TEXT,
    foto VARCHAR(255),
    ubicacion VARCHAR(255),
    documento_usuario VARCHAR(50) NOT NULL,
    CONSTRAINT fk_reporte_usuario FOREIGN KEY (documento_usuario)
      REFERENCES usuario(documento_usuario) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 10. SOLICITUDES DE ADOPCIÓN. Los archivos se guardan en tabla de detalle (1FN).
CREATE TABLE solicitud_adopcion (
    soli_adopcion_id INT AUTO_INCREMENT PRIMARY KEY,
    documento_usuario VARCHAR(50) NOT NULL,
    comuna VARCHAR(50),
    sector VARCHAR(50),
    direccion VARCHAR(100),
    tipo_vivienda_id INT,
    sustento_economico TEXT,
    observaciones TEXT,
    mascota_id INT,
    estado VARCHAR(50) NOT NULL DEFAULT 'pendiente',
    fecha_solicitud DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_solicitud_usuario FOREIGN KEY (documento_usuario)
      REFERENCES usuario(documento_usuario) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_solicitud_vivienda FOREIGN KEY (tipo_vivienda_id)
      REFERENCES tipo_vivienda(tipo_vivienda_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_solicitud_mascota FOREIGN KEY (mascota_id)
      REFERENCES mascota(mascota_id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE solicitud_archivo (
    solicitud_archivo_id INT AUTO_INCREMENT PRIMARY KEY,
    soli_adopcion_id INT NOT NULL,
    ruta_archivo VARCHAR(500) NOT NULL,
    tipo_archivo VARCHAR(100),
    fecha_carga DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_archivo_solicitud FOREIGN KEY (soli_adopcion_id)
      REFERENCES solicitud_adopcion(soli_adopcion_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- FIN DEL ESQUEMA NORMALIZADO.
-- Nota: si el proyecto debe seguir usando la base de datos "monpet", hacer
-- respaldo primero y cambiar monpet_taller3 por monpet en este script.
