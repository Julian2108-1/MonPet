<?php
declare(strict_types=1);

/*
 * Catálogo PHP de muestra. Los archivos entregados no definen la tabla ni
 * los campos de mascotas; reemplaza este arreglo por la consulta real cuando
 * esa estructura esté disponible.
 */
function catalogo_esc($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$mascotas = [
    ['name' => 'Buddy', 'type' => 'perros', 'breed' => 'Labrador Retriever', 'gender' => 'macho', 'age' => 2, 'age_label' => '2 años', 'size' => 'grande', 'size_label' => 'Grande', 'city' => 'bogota', 'city_label' => 'Bogotá, Cundinamarca', 'image' => 'https://images.unsplash.com/photo-1552053831-71594a27632d?w=400&auto=format&fit=crop', 'created' => '2026-11-20'],
    ['name' => 'Luna', 'type' => 'gatos', 'breed' => 'Gato doméstico', 'gender' => 'hembra', 'age' => 1, 'age_label' => '1 año', 'size' => 'mediano', 'size_label' => 'Mediano', 'city' => 'bogota', 'city_label' => 'Bogotá, Cundinamarca', 'image' => 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=400&auto=format&fit=crop', 'created' => '2026-11-18'],
    ['name' => 'Max', 'type' => 'perros', 'breed' => 'Criollo', 'gender' => 'macho', 'age' => 3, 'age_label' => '3 años', 'size' => 'grande', 'size_label' => 'Grande', 'city' => 'soacha', 'city_label' => 'Soacha, Cundinamarca', 'image' => 'https://images.unsplash.com/photo-1537151608828-ea2b11777ee8?w=400&auto=format&fit=crop', 'created' => '2026-11-16'],
    ['name' => 'Mía', 'type' => 'gatos', 'breed' => 'Gata doméstica', 'gender' => 'hembra', 'age' => 8 / 12, 'age_label' => '8 meses', 'size' => 'pequeno', 'size_label' => 'Pequeño', 'city' => 'bogota', 'city_label' => 'Bogotá, Cundinamarca', 'image' => 'https://images.unsplash.com/photo-1533738363-b7f9aef128ce?w=400&auto=format&fit=crop', 'created' => '2026-11-14'],
    ['name' => 'Rocky', 'type' => 'perros', 'breed' => 'Pastor Alemán', 'gender' => 'macho', 'age' => 4 / 12, 'age_label' => '4 meses', 'size' => 'mediano', 'size_label' => 'Mediano', 'city' => 'bogota', 'city_label' => 'Bogotá, Cundinamarca', 'image' => 'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?w=400&auto=format&fit=crop', 'created' => '2026-11-12'],
    ['name' => 'Nala', 'type' => 'gatos', 'breed' => 'Gato doméstico', 'gender' => 'hembra', 'age' => 2, 'age_label' => '2 años', 'size' => 'mediano', 'size_label' => 'Mediano', 'city' => 'chia', 'city_label' => 'Chía, Cundinamarca', 'image' => 'https://images.unsplash.com/photo-1573865526739-10659fec78a5?w=400&auto=format&fit=crop', 'created' => '2026-11-10'],
    ['name' => 'Toby', 'type' => 'perros', 'breed' => 'Criollo', 'gender' => 'macho', 'age' => 1, 'age_label' => '1 año', 'size' => 'mediano', 'size_label' => 'Mediano', 'city' => 'soacha', 'city_label' => 'Soacha, Cundinamarca', 'image' => 'https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=400&auto=format&fit=crop', 'created' => '2026-11-08'],
    ['name' => 'Simba', 'type' => 'gatos', 'breed' => 'Gato doméstico', 'gender' => 'macho', 'age' => 3 / 12, 'age_label' => '3 meses', 'size' => 'pequeno', 'size_label' => 'Pequeño', 'city' => 'bogota', 'city_label' => 'Bogotá, Cundinamarca', 'image' => 'https://images.unsplash.com/photo-1574158622682-e40e69881006?w=400&auto=format&fit=crop', 'created' => '2026-11-06'],
];

$tipo = (string) ($_GET['tipo'] ?? '');
if (!in_array($tipo, ['perros', 'gatos'], true)) {
    $tipo = '';
}

$tamanos = $_GET['size'] ?? [];
$tamanos = is_array($tamanos) ? array_values(array_intersect($tamanos, ['pequeno', 'mediano', 'grande'])) : [];
$sexos = $_GET['gender'] ?? [];
$sexos = is_array($sexos) ? array_values(array_intersect($sexos, ['macho', 'hembra'])) : [];

$ciudad = (string) ($_GET['city'] ?? '');
if (!in_array($ciudad, ['', 'bogota', 'soacha', 'chia'], true)) {
    $ciudad = '';
}
$edadMaxima = filter_var($_GET['edad_max'] ?? 10, FILTER_VALIDATE_FLOAT);
$edadMaxima = $edadMaxima === false ? 10.0 : max(0.0, min(10.0, (float) $edadMaxima));
$edadMinima = filter_var($_GET['edad_min'] ?? 0, FILTER_VALIDATE_FLOAT);
$edadMinima = $edadMinima === false ? 0.0 : max(0.0, min(10.0, (float) $edadMinima));
if ($edadMinima > $edadMaxima) {
    $edadMinima = $edadMaxima;
}
$orden = (string) ($_GET['orden'] ?? 'recientes');
if (!in_array($orden, ['recientes', 'antiguos', 'nombre'], true)) {
    $orden = 'recientes';
}

$mascotasFiltradas = array_values(array_filter($mascotas, static function (array $mascota) use ($tipo, $tamanos, $sexos, $ciudad, $edadMaxima): bool {
    return ($tipo === '' || $mascota['type'] === $tipo)
        && ($tamanos === [] || in_array($mascota['size'], $tamanos, true))
        && ($sexos === [] || in_array($mascota['gender'], $sexos, true))
        && ($ciudad === '' || $mascota['city'] === $ciudad)
        && $mascota['age'] >= $edadMinima
        && $mascota['age'] <= $edadMaxima;
}));

usort($mascotasFiltradas, static function (array $a, array $b) use ($orden): int {
    if ($orden === 'nombre') {
        return strcasecmp($a['name'], $b['name']);
    }
    $comparison = strcmp($a['created'], $b['created']);
    return $orden === 'antiguos' ? $comparison : -$comparison;
});

ob_start();
foreach ($mascotasFiltradas as $mascota):
    $nombre = catalogo_esc($mascota['name']);
    $sexo = $mascota['gender'];
    $sexoIcono = $sexo === 'macho' ? 'mars' : 'venus';
    $sexoClase = $sexo === 'macho' ? 'male' : 'female';
?>
<article class="pet-card" data-type="<?= catalogo_esc($mascota['type']) ?>" data-size="<?= catalogo_esc($mascota['size']) ?>" data-gender="<?= catalogo_esc($sexo) ?>" data-age="<?= catalogo_esc($mascota['age']) ?>" data-city="<?= catalogo_esc($mascota['city']) ?>" data-name="<?= strtolower($nombre) ?>" data-created="<?= catalogo_esc($mascota['created']) ?>">
  <div class="card-image-container">
    <img src="<?= catalogo_esc($mascota['image']) ?>" alt="<?= $nombre ?> - <?= catalogo_esc($mascota['breed']) ?>" loading="lazy">
    <span class="status-badge">Disponible</span>
    <button class="favorite-btn" type="button" aria-label="Guardar a <?= $nombre ?> en favoritos" aria-pressed="false"><i class="fa-regular fa-heart" aria-hidden="true"></i></button>
  </div>
  <div class="card-content">
    <div class="pet-title"><h3><?= $nombre ?></h3><span class="gender-icon <?= $sexoClase ?>"><i class="fa-solid fa-<?= $sexoIcono ?>" aria-hidden="true"></i></span></div>
    <p class="pet-breed"><?= catalogo_esc($mascota['breed']) ?></p>
    <div class="pet-details"><span><i class="fa-regular fa-calendar" aria-hidden="true"></i> <?= catalogo_esc($mascota['age_label']) ?></span><span><i class="fa-solid fa-paw" aria-hidden="true"></i> <?= catalogo_esc($mascota['size_label']) ?></span></div>
    <p class="pet-location"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> <?= catalogo_esc($mascota['city_label']) ?></p>
  </div>
</article>
<?php
endforeach;
$tarjetasHtml = ob_get_clean();

$template = file_get_contents(__DIR__ . '/catalogo_mascotas.html');
if ($template === false) {
    http_response_code(500);
    exit('No se pudo cargar la plantilla del catálogo.');
}

$inicio = '<!-- PHP_PET_CARDS_START -->';
$fin = '<!-- PHP_PET_CARDS_END -->';
$posInicio = strpos($template, $inicio);
$posFin = strpos($template, $fin);
if ($posInicio !== false && $posFin !== false && $posFin >= $posInicio) {
    $contenidoInicio = $posInicio + strlen($inicio);
    $template = substr_replace($template, $tarjetasHtml, $contenidoInicio, $posFin - $contenidoInicio);
}

$template = str_replace('<strong>8</strong>', '<strong>' . count($mascotasFiltradas) . '</strong>', $template);
echo $template;

?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MonPet - Catálogo de Mascotas</title>
  <!-- Hoja de estilos principal -->
  <link rel="stylesheet" href="../css/estilo_catalogo_mascotas.css">
  <!-- Carga de iconos SVG simples usando la librería FontAwesome (opcional para renderizar íconos) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

  <!-- ========================================== -->
  <!-- ENCABEZADO Y NAVEGACIÓN PRINCIPAL (HEADER) -->
  <!-- ========================================== -->
  <header class="main-header">
    <div class="header-container">
      <!-- Logotipo de la aplicación -->
      <div class="logo-container">
        <div class="logo-icon">
          <i class="fa-solid fa-paw"></i>
        </div>
        <div class="logo-text">
          <h1>MonPet</h1>
          <span>Amor que cambia vidas <i class="fa-solid fa-heart"></i></span>
        </div>
      </div>

      <!-- Menú de navegación principal -->
      <nav class="main-nav">
        <ul>
          <li><a href="#">Inicio</a></li>
          <li><a href="#" class="active">Adopción <i class="fa-solid fa-chevron-down"></i></a></li>
          <li><a href="#">Reportes</a></li>
          <li><a href="#">Refugios</a></li>
          <li><a href="#">Veterinarios</a></li>
          <li><a href="#">Jornadas</a></li>
          <li><a href="#">Nosotros</a></li>
        </ul>
      </nav>

      <!-- Acciones del usuario (Notificaciones y Perfil) -->
      <div class="user-actions">
        <button class="notification-btn" title="Notificaciones">
          <i class="fa-regular fa-bell"></i>
          <span class="badge">2</span>
        </button>
        <div class="user-profile">
          <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&auto=format&fit=crop"
            alt="Foto de perfil de Laura García">
          <span class="user-name">Laura García</span>
          <i class="fa-solid fa-chevron-down"></i>
        </div>
      </div>
    </div>
  </header>

  <!-- ========================================== -->
  <!-- CONTENIDO PRINCIPAL (MAIN)                 -->
  <!-- ========================================== -->
  <main class="main-content">

    <!-- Migas de pan (Breadcrumbs) para guiar la navegación del usuario -->
    <nav class="breadcrumbs">
      <a href="#"><i class="fa-solid fa-house"></i> Inicio</a>
      <span>/</span>
      <a href="#">Adopción</a>
      <span>/</span>
      <span class="current">Catálogo de mascotas</span>
    </nav>

    <!-- Sección del Encabezado de la página y Banner Promocional -->
    <section class="page-header-section">
      <div class="page-title-container">
        <div class="title-icon">
          <i class="fa-solid fa-paw"></i>
        </div>
        <div>
          <h2>Catálogo de mascotas</h2>
          <p>Encuentra a tu nuevo mejor amigo</p>
        </div>
      </div>

      <!-- Banner promocional de adopción -->
      <div class="banner-card">
        <div class="banner-images">
          <img src="https://images.unsplash.com/photo-1543466835-00a7907e9de1?w=150&auto=format&fit=crop"
            alt="Perro feliz">
          <img src="https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=150&auto=format&fit=crop"
            alt="Gato doméstico">
        </div>
        <div class="banner-text">
          <h3>Adopta, no compres <i class="fa-solid fa-heart"></i></h3>
          <p>Miles de mascotas esperan por un hogar lleno de amor.</p>
        </div>
        <div class="banner-watermark">
          <i class="fa-solid fa-paw"></i>
        </div>
      </div>
    </section>

    <!-- Contenedor general que divide Filtros (Izquierda) y Catálogo (Derecha) -->
    <div class="catalog-layout">

      <!-- ========================================== -->
      <!-- PANEL LATERAL DE FILTROS                   -->
      <!-- ========================================== -->
      <aside class="filters-sidebar">
        <div class="filters-header">
          <h3><i class="fa-solid fa-sliders"></i> Filtros</h3>
          <button id="clear-filters-btn" class="clear-btn">Limpiar todo</button>
        </div>

        <form id="filters-form" action="catalogo_mascotas.php" method="get">
          <input type="hidden" id="pet-type-filter" name="tipo" value="">

          <!-- Filtro: Tipo de mascota -->
          <div class="filter-group">
            <label class="filter-label">Tipo de mascota</label>
            <div class="pet-type-selector">
              <button type="button" class="type-btn active" data-type="perros">
                <i class="fa-solid fa-dog"></i> Perros
              </button>
              <button type="button" class="type-btn" data-type="gatos">
                <i class="fa-solid fa-cat"></i> Gatos
              </button>
            </div>
          </div>

          <!-- Filtro: Tamaño -->
          <div class="filter-group">
            <label class="filter-label">Tamaño</label>
            <label class="checkbox-container">
              <input type="checkbox" name="size[]" value="pequeno">
              <span class="checkmark"></span>
              Pequeño
            </label>
            <label class="checkbox-container">
              <input type="checkbox" name="size[]" value="mediano">
              <span class="checkmark"></span>
              Mediano
            </label>
            <label class="checkbox-container">
              <input type="checkbox" name="size[]" value="grande">
              <span class="checkmark"></span>
              Grande
            </label>
          </div>

          <!-- Filtro: Sexo -->
          <div class="filter-group">
            <label class="filter-label">Sexo</label>
            <label class="checkbox-container">
              <input type="checkbox" name="gender[]" value="macho">
              <span class="checkmark"></span>
              Macho
            </label>
            <label class="checkbox-container">
              <input type="checkbox" name="gender[]" value="hembra">
              <span class="checkmark"></span>
              Hembra
            </label>
          </div>

          <!-- Filtro: Rango de Edad -->
          <div class="filter-group">
            <label class="filter-label">Edad</label>
            <div class="range-slider">
              <input type="range" min="0" max="10" value="0" name="edad_min" class="slider age-min" id="ageMin" aria-label="Edad mínima">
              <input type="range" min="0" max="10" value="10" name="edad_max" class="slider age-max" id="ageRange" aria-label="Edad máxima">
              <div class="range-labels">
                <span id="age-min-label">0 meses</span>
                <span>10+ años</span>
              </div>
            </div>
          </div>

          <!-- Filtro: Ubicación -->
          <div class="filter-group">
            <label class="filter-label">Ubicación</label>
            <select class="select-input" id="city-filter" name="city">
              <option value="" disabled selected>Selecciona una ciudad</option>
              <option value="bogota">Bogotá, Cundinamarca</option>
              <option value="soacha">Soacha, Cundinamarca</option>
              <option value="chia">Chía, Cundinamarca</option>
            </select>
          </div>

          <!-- Botón de aplicar filtros -->
          <button type="submit" class="apply-filters-btn">
            <i class="fa-solid fa-magnifying-glass"></i> Aplicar filtros
          </button>
        </form>
      </aside>

      <!-- ========================================== -->
      <!-- SECCIÓN DEL CATÁLOGO DE TARJETAS          -->
      <!-- ========================================== -->
      <section class="pets-catalog">

        <!-- Barra superior de resultados y ordenamiento -->
        <div class="catalog-top-bar">
          <span class="results-count" id="results-count"><strong>8</strong> mascotas disponibles</span>

          <div class="sort-and-view">
            <label for="sort">Ordenar por:</label>
            <select id="sort" class="select-input sort-select" aria-label="Ordenar mascotas">
              <option value="recientes">Más recientes</option>
              <option value="antiguos">Más antiguos</option>
              <option value="nombre">Por nombre</option>
            </select>

            <!-- Toggle de vista (Grilla vs Lista) -->
            <div class="view-toggle">
              <button class="view-btn active" title="Vista en cuadrícula"><i
                  class="fa-solid fa-border-all"></i></button>
              <button class="view-btn" title="Vista en lista"><i class="fa-solid fa-list"></i></button>
            </div>
          </div>
        </div>

        <!-- Grilla que contiene las tarjetas de mascotas -->
        <div class="pets-grid" id="pets-grid">

          <!-- PHP_PET_CARDS_START -->
          <!-- TARJETA 1: Buddy -->
          <article class="pet-card">
            <div class="card-image-container">
              <img src="https://images.unsplash.com/photo-1552053831-71594a27632d?w=400&auto=format&fit=crop"
                alt="Buddy - Labrador Retriever">
              <span class="status-badge">Disponible</span>
              <button class="favorite-btn" aria-label="Guardar en favoritos"><i
                  class="fa-regular fa-heart"></i></button>
            </div>
            <div class="card-content">
              <div class="pet-title">
                <h3>Buddy</h3>
                <span class="gender-icon male"><i class="fa-solid fa-mars"></i></span>
              </div>
              <p class="pet-breed">Labrador Retriever</p>
              <div class="pet-details">
                <span><i class="fa-regular fa-calendar"></i> 2 años</span>
                <span><i class="fa-solid fa-paw"></i> Grande</span>
              </div>
              <p class="pet-location"><i class="fa-solid fa-location-dot"></i> Bogotá, Cundinamarca</p>
            </div>
          </article>

          <!-- TARJETA 2: Luna -->
          <article class="pet-card">
            <div class="card-image-container">
              <img src="https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=400&auto=format&fit=crop"
                alt="Luna - Gato doméstico">
              <span class="status-badge">Disponible</span>
              <button class="favorite-btn" aria-label="Guardar en favoritos"><i
                  class="fa-regular fa-heart"></i></button>
            </div>
            <div class="card-content">
              <div class="pet-title">
                <h3>Luna</h3>
                <span class="gender-icon female"><i class="fa-solid fa-venus"></i></span>
              </div>
              <p class="pet-breed">Gato doméstico</p>
              <div class="pet-details">
                <span><i class="fa-regular fa-calendar"></i> 1 año</span>
                <span><i class="fa-solid fa-paw"></i> Mediano</span>
              </div>
              <p class="pet-location"><i class="fa-solid fa-location-dot"></i> Bogotá, Cundinamarca</p>
            </div>
          </article>

          <!-- TARJETA 3: Max -->
          <article class="pet-card">
            <div class="card-image-container">
              <img src="https://images.unsplash.com/photo-1537151608828-ea2b11777ee8?w=400&auto=format&fit=crop"
                alt="Max - Criollo">
              <span class="status-badge">Disponible</span>
              <button class="favorite-btn" aria-label="Guardar en favoritos"><i
                  class="fa-regular fa-heart"></i></button>
            </div>
            <div class="card-content">
              <div class="pet-title">
                <h3>Max</h3>
                <span class="gender-icon male"><i class="fa-solid fa-mars"></i></span>
              </div>
              <p class="pet-breed">Criollo</p>
              <div class="pet-details">
                <span><i class="fa-regular fa-calendar"></i> 3 años</span>
                <span><i class="fa-solid fa-paw"></i> Grande</span>
              </div>
              <p class="pet-location"><i class="fa-solid fa-location-dot"></i> Soacha, Cundinamarca</p>
            </div>
          </article>

          <!-- TARJETA 4: Mía -->
          <article class="pet-card">
            <div class="card-image-container">
              <img src="https://images.unsplash.com/photo-1533738363-b7f9aef128ce?w=400&auto=format&fit=crop"
                alt="Mía - Gata doméstica">
              <span class="status-badge">Disponible</span>
              <button class="favorite-btn" aria-label="Guardar en favoritos"><i
                  class="fa-regular fa-heart"></i></button>
            </div>
            <div class="card-content">
              <div class="pet-title">
                <h3>Mía</h3>
                <span class="gender-icon female"><i class="fa-solid fa-venus"></i></span>
              </div>
              <p class="pet-breed">Gata doméstica</p>
              <div class="pet-details">
                <span><i class="fa-regular fa-calendar"></i> 8 meses</span>
                <span><i class="fa-solid fa-paw"></i> Pequeño</span>
              </div>
              <p class="pet-location"><i class="fa-solid fa-location-dot"></i> Bogotá, Cundinamarca</p>
            </div>
          </article>

          <!-- TARJETA 5: Rocky -->
          <article class="pet-card">
            <div class="card-image-container">
              <img src="https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?w=400&auto=format&fit=crop"
                alt="Rocky - Pastor Alemán">
              <span class="status-badge">Disponible</span>
              <button class="favorite-btn" aria-label="Guardar en favoritos"><i
                  class="fa-regular fa-heart"></i></button>
            </div>
            <div class="card-content">
              <div class="pet-title">
                <h3>Rocky</h3>
                <span class="gender-icon male"><i class="fa-solid fa-mars"></i></span>
              </div>
              <p class="pet-breed">Pastor Alemán</p>
              <div class="pet-details">
                <span><i class="fa-regular fa-calendar"></i> 4 meses</span>
                <span><i class="fa-solid fa-paw"></i> Mediano</span>
              </div>
              <p class="pet-location"><i class="fa-solid fa-location-dot"></i> Bogotá, Cundinamarca</p>
            </div>
          </article>

          <!-- TARJETA 6: Nala -->
          <article class="pet-card">
            <div class="card-image-container">
              <img src="https://images.unsplash.com/photo-1573865526739-10659fec78a5?w=400&auto=format&fit=crop"
                alt="Nala - Gato doméstico">
              <span class="status-badge">Disponible</span>
              <button class="favorite-btn" aria-label="Guardar en favoritos"><i
                  class="fa-regular fa-heart"></i></button>
            </div>
            <div class="card-content">
              <div class="pet-title">
                <h3>Nala</h3>
                <span class="gender-icon female"><i class="fa-solid fa-venus"></i></span>
              </div>
              <p class="pet-breed">Gato doméstico</p>
              <div class="pet-details">
                <span><i class="fa-regular fa-calendar"></i> 2 años</span>
                <span><i class="fa-solid fa-paw"></i> Mediano</span>
              </div>
              <p class="pet-location"><i class="fa-solid fa-location-dot"></i> Chía, Cundinamarca</p>
            </div>
          </article>

          <!-- TARJETA 7: Toby -->
          <article class="pet-card">
            <div class="card-image-container">
              <img src="https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=400&auto=format&fit=crop"
                alt="Toby - Criollo">
              <span class="status-badge">Disponible</span>
              <button class="favorite-btn" aria-label="Guardar en favoritos"><i
                  class="fa-regular fa-heart"></i></button>
            </div>
            <div class="card-content">
              <div class="pet-title">
                <h3>Toby</h3>
                <span class="gender-icon male"><i class="fa-solid fa-mars"></i></span>
              </div>
              <p class="pet-breed">Criollo</p>
              <div class="pet-details">
                <span><i class="fa-regular fa-calendar"></i> 1 año</span>
                <span><i class="fa-solid fa-paw"></i> Mediano</span>
              </div>
              <p class="pet-location"><i class="fa-solid fa-location-dot"></i> Soacha, Cundinamarca</p>
            </div>
          </article>

          <!-- TARJETA 8: Simba -->
          <article class="pet-card">
            <div class="card-image-container">
              <img src="https://images.unsplash.com/photo-1574158622682-e40e69881006?w=400&auto=format&fit=crop"
                alt="Simba - Gato doméstico">
              <span class="status-badge">Disponible</span>
              <button class="favorite-btn" aria-label="Guardar en favoritos"><i
                  class="fa-regular fa-heart"></i></button>
            </div>
            <div class="card-content">
              <div class="pet-title">
                <h3>Simba</h3>
                <span class="gender-icon male"><i class="fa-solid fa-mars"></i></span>
              </div>
              <p class="pet-breed">Gato doméstico</p>
              <div class="pet-details">
                <span><i class="fa-regular fa-calendar"></i> 3 meses</span>
                <span><i class="fa-solid fa-paw"></i> Pequeño</span>
              </div>
              <p class="pet-location"><i class="fa-solid fa-location-dot"></i> Bogotá, Cundinamarca</p>
            </div>
          </article>

          <!-- PHP_PET_CARDS_END -->
        </div>

        <p id="empty-state" class="empty-state" hidden>No encontramos mascotas con esos filtros. Prueba cambiando tu búsqueda.</p>

        <!-- Paginación inferior -->
        <div class="pagination">
          <button class="page-btn nav-btn" type="button" disabled aria-label="Página anterior"><i class="fa-solid fa-chevron-left"></i></button>
          <button class="page-btn active" type="button" aria-current="page">1</button>
          <button class="page-btn nav-btn" type="button" disabled aria-label="Página siguiente"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
      </section>

    </div>
  </main>

  <!-- ========================================== -->
  <!-- PIE DE PÁGINA (FOOTER)                    -->
  <!-- ========================================== -->
  <footer class="main-footer">
    <div class="footer-container">
      <!-- Columna 1: Información general y redes sociales -->
      <div class="footer-col brand-col">
        <div class="logo-container white">
          <div class="logo-icon">
            <i class="fa-solid fa-paw"></i>
          </div>
          <div class="logo-text">
            <h2>MonPet</h2>
            <span>Amor que cambia vidas <i class="fa-solid fa-heart"></i></span>
          </div>
        </div>
        <p>Conectamos corazones para que cada mascota encuentre un hogar lleno de amor y cuidado.</p>
        <div class="social-links">
          <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
          <a href="#" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>
          <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
        </div>
      </div>

      <!-- Columna 2: Enlaces Rápidos -->
      <div class="footer-col">
        <h4>Enlaces rápidos</h4>
        <ul>
          <li><a href="#">Inicio</a></li>
          <li><a href="#">Adopción</a></li>
          <li><a href="#">Reportes</a></li>
          <li><a href="#">Refugios</a></li>
          <li><a href="#">Veterinarios</a></li>
          <li><a href="#">Jornadas</a></li>
          <li><a href="#">Nosotros</a></li>
        </ul>
      </div>

      <!-- Columna 3: Ayuda -->
      <div class="footer-col">
        <h4>Ayuda</h4>
        <ul>
          <li><a href="#">Preguntas frecuentes</a></li>
          <li><a href="#">Cómo adoptar</a></li>
          <li><a href="#">Consejos de cuidado</a></li>
          <li><a href="#">Contáctanos</a></li>
        </ul>
      </div>

      <!-- Columna 4: Información Legal -->
      <div class="footer-col">
        <h4>Información legal</h4>
        <ul>
          <li><a href="#">Términos y condiciones</a></li>
          <li><a href="#">Política de privacidad</a></li>
          <li><a href="#">Aviso legal</a></li>
        </ul>
      </div>

      <!-- Columna 5: Datos de Contacto -->
      <div class="footer-col">
        <h4>Contáctanos</h4>
        <ul class="contact-info">
          <li><i class="fa-solid fa-location-dot"></i> Bogotá, Colombia</li>
          <li><i class="fa-regular fa-envelope"></i> hola@monpet.com</li>
          <li><i class="fa-solid fa-phone"></i> +57 300 123 4567</li>
          <li><i class="fa-regular fa-clock"></i> Lun - Vie: 8:00 a.m. - 6:00 p.m.<br>Sáb: 8:00 a.m. - 2:00 p.m.</li>
        </ul>
      </div>
    </div>

    <!-- Copyright final -->
    <div class="footer-bottom">
      <p>&copy; 2026 MonPet. Todos los derechos reservados. <i class="fa-solid fa-paw"></i></p>
    </div>
  </footer>

  <!-- Script JS al final para no bloquear la carga visual -->
  <script src="../js/script_catalogo_mascotas.js" defer></script>
</body>

</html>
