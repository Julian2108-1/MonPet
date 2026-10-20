<?php
require_once("../db/conexion.php");
$tipos = mysqli_query(
    $conexion,
    "SELECT * FROM tipos_documento"
);

?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.0/css/all.min.css"
        integrity="sha512-ApSLB1Pd3/bZN8fWB/RG9YhN/7bd9Hkf3AGaE2mPfebjrxagjuBtx2GcgdqIlJkUzwylBo61r9Xa9NmgBI0swA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Registro - MonPet</title>
    <link rel="stylesheet" href="../css/estilo_registro.css">
</head>

<body>
    <header class="menu">
        <div class="contenedor-logo">
            <div class="icono-logo">
                <i class="fa-solid fa-paw"></i>
            </div>
            <div class="texto-logo">
                <h1><span class="texto-titulo-1">Mon</span><span class="texto-titulo-2">Pet</span></h1>
                <p class="slogan-banner">Amor que cambia vidas <i class="fa-solid fa-heart"></i></p>
            </div>
        </div>
        <nav class="links-navegacion">
            <ul>
                <li><a href="#" class="active">Inicio</a></li>
                <li><a href="#">Adopción</a></li>
                <li><a href="#">Reportes</a></li>
                <li><a href="#">Refugios</a></li>
                <li><a href="#">Jornadas</a></li>
                <li><a href="#">Nosotros</a></li>
            </ul>
        </nav>
        <div class="accion-navegacion">
            <a href="#" class="boton-secundario">Volver al Inicio</a>
        </div>
    </header>
    <main class="contenido-registro">

        <section class="panel-bienvenida">
            <span class="etiqueta-comunidad"> Únete a nuestra comunidad
            </span>
            <h2 class="titulo-bienvenida">Crea tu cuenta y<br>se parte <span>del cambio</span></h2>
            <p class="descripcion-bienvenida">Regístrate para comenzar a ayudar,<br>adoptar y transformar vidas.</p>

            <div class="ilustracion-mascotas">
                <img src="../img/fondo_2.png" alt="Mascotas">
            </div>

            <article class="tarjeta-estadisticas">
                <h3>Juntos hacemos la diferencia</h3>

                <div class="estadisticas-comunidad">

                    <div class="estadistica-adopcion">
                        <span class="icono-estadistica"><i class="fa-solid fa-paw icono-huella"></i></span>
                        <strong>+1,200</strong>
                        <p>Mascotas en adopción</p>
                    </div>

                    <div class="estadistica-adopcion">
                        <span class="icono-estadistica"><i
                                class="fa-solid fa-heart-circle-bolt icono-adopcion"></i></span>
                        <strong>980</strong>
                        <p>Adopciones exitosas</p>
                    </div>

                    <div class="estadistica-adopcion">
                        <span class="icono-estadistica"><i class="fa-solid fa-people-roof icono-refugio"></i></span>
                        <strong>45</strong>
                        <p>Refugios aliados</p>
                    </div>

                    <div class="estadistica-adopcion">
                        <span class="icono-estadistica"><i class="fa-solid fa-users icono-usuario"></i></span>
                        <strong>320</strong>
                        <p>Voluntarios activos</p>
                    </div>
                </div>
            </article>
        </section>

        <section class="panel-formulario-registro">
            <header class="encabezado-formulario">
                <div class="icono-formulario">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <div class="informacion-formulario">
                    <h2>Crear cuenta</h2>
                    <p>Completa tus datos para registrarte en MonPet.</p>
                </div>
            </header>

            <div class="pasos-registro">

                <div class="paso-registro paso-seleccionado">
                    <span class="numero-paso">1</span>
                    <p>Información personal</p>
                </div>

                <div class="paso-registro">
                    <span class="numero-paso">2</span>
                    <p>Información de contacto</p>
                </div>

                <div class="paso-registro">
                    <span class="numero-paso">3</span>
                    <p>Seguridad</p>
                </div>

            </div>

            <form action="../admin/crear_cuenta/registrar_usuario.php" method="POST" id="form-crear"
                class="formulario-registro">

                <!-- =========================
         PASO 1 - INFORMACIÓN PERSONAL
    ========================== -->
                <div class="paso-formulario activo" id="paso-1">

                    <div class="fila-campos">

                        <div class="campo-formulario">
                            <label>Nombre(s)</label>
                            <div class="icono-input">
                                <i class="fa-regular fa-user"></i>
                                <input type="text" name="nombre" id="nombre" placeholder="Ingresa tu nombre">
                            </div>
                        </div>

                        <div class="campo-formulario">
                            <label>Apellidos</label>
                            <div class="icono-input">
                                <i class="fa-regular fa-user"></i>
                                <input type="text" name="apellido" id="apellido" placeholder="Ingresa tus apellidos">
                            </div>
                        </div>

                    </div>


                    <div class="fila-campos">

                        <div class="campo-formulario">

                            <label>Tipo de documento</label>

                            <div class="icono-input">

                                <i class="fa-regular fa-id-card"></i>

                                <select name="tipo_documento" id="tipo_documento">

                                    <option value="">Seleccione...</option>

                                    <?php while ($fila = mysqli_fetch_assoc($tipos)) { ?>

                                        <option value="<?php echo $fila["id_tipo_documento"]; ?>">
                                            <?php echo $fila["nombre"]; ?>
                                        </option>

                                    <?php } ?>

                                </select>

                            </div>

                        </div>


                        <div class="campo-formulario">

                            <label>Número de documento</label>

                            <div class="icono-input">

                                <i class="fa-regular fa-address-card"></i>

                                <input type="text" name="documento" id="documento"
                                    placeholder="Ingresa tu número de documento">

                            </div>

                        </div>

                    </div>


                    <div class="fila-campos">

                        <div class="campo-formulario">

                            <label>Fecha de nacimiento</label>

                            <div class="icono-input">

                                <i class="fa-regular fa-calendar-days"></i>

                                <input type="date" name="fecha_nacimiento" id="fecha_nacimiento">

                            </div>

                        </div>


                        <div class="campo-formulario">

                            <label>Género</label>

                            <div class="icono-input">

                                <i class="fa-solid fa-venus-mars"></i>

                                <select name="genero" id="genero">

                                    <option value="">Seleccionar</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                    <option value="Otro">Otro</option>

                                </select>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- =========================
         PASO 2 - INFORMACIÓN DE CONTACTO
    ========================== -->
                <div class="paso-formulario" id="paso-2">

                    <div class="fila-campos">

                        <div class="campo-formulario">

                            <label>Correo electrónico</label>

                            <div class="icono-input">

                                <i class="fa-regular fa-envelope"></i>

                                <input type="email" name="correo" id="correo" placeholder="ejemplo@correo.com">

                            </div>

                        </div>


                        <div class="campo-formulario">

                            <label>Número de teléfono</label>

                            <div class="icono-input">

                                <i class="fa-solid fa-phone"></i>

                                <input type="tel" name="telefono" id="telefono" placeholder="Ingresa tu número">

                            </div>

                        </div>

                    </div>


                    <div class="fila-campos">

                        <div class="campo-formulario">

                            <label>Dirección</label>

                            <div class="icono-input">

                                <i class="fa-solid fa-location-dot"></i>

                                <input type="text" name="direccion" id="direccion" placeholder="Ingresa tu dirección">

                            </div>

                        </div>


                        <div class="campo-formulario">

                            <label>Ciudad</label>

                            <div class="icono-input">

                                <i class="fa-solid fa-city"></i>

                                <input type="text" name="ciudad" id="ciudad" placeholder="Ingresa tu ciudad">

                            </div>

                        </div>

                    </div>

                </div>


                <!-- =========================
         PASO 3 - SEGURIDAD
    ========================== -->
                <div class="paso-formulario" id="paso-3">

                    <div class="campo-formulario">

                        <label>Contraseña</label>

                        <div class="icono-input">

                            <i class="fa-solid fa-lock"></i>

                            <input type="password" name="contrasena" id="contrasena" placeholder="Crea una contraseña">

                        </div>

                    </div>


                    <div class="campo-formulario">

                        <label>Confirmar contraseña</label>

                        <div class="icono-input">

                            <i class="fa-solid fa-lock"></i>

                            <input type="password" name="confirmar_contrasena" id="confirmar_contrasena"
                                placeholder="Repite tu contraseña">

                        </div>

                    </div>


                    <article class="aviso-privacidad">

                        <div class="icono-seguridad">

                            <i class="fa-solid fa-user-shield"></i>

                        </div>

                        <div class="contenido-aviso">

                            <h3>Tu información está protegida</h3>

                            <p>
                                En MonPet cuidamos tus datos y nunca los compartimos
                                con terceros.
                                Consulta nuestra
                                <a href="#">Política de Privacidad.</a>
                            </p>

                        </div>

                        <div class="icono-candado">

                            <i class="fa-solid fa-lock"></i>

                        </div>

                    </article>

                </div>


                <!-- BOTONES -->

                <div class="acciones-formulario">

                    <button type="button" class="boton-cancelar" id="boton-anterior">
                        Anterior
                    </button>

                    <button type="button" class="boton-siguiente" id="boton-siguiente">
                        Siguiente
                    </button>

                </div>

            </form>

            <footer class="pie-formulario">
                <p>¿Ya tienes una cuenta?
                    <a href="#">Iniciar sesión</a>
                </p>
            </footer>
        </section>
    </main>

    <script src="../js/registro.js"></script>
</body>

</html>