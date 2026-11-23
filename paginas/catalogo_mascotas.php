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
    ['name' => 'Buddy', 'type' => 'perros', 'breed' => 'Labrador Retriever', 'gender' => 'macho', 'age' => 2, 'age_label' => '2 años', 'size' => 'grande', 'size_label' => 'Grande', 'city' => 'compartir', 'city_label' => 'Compartir, Soacha', 'image' => 'https://images.unsplash.com/photo-1552053831-71594a27632d?w=400&auto=format&fit=crop', 'created' => '2026-11-20'],
    ['name' => 'Luna', 'type' => 'gatos', 'breed' => 'Gato doméstico', 'gender' => 'hembra', 'age' => 1, 'age_label' => '1 año', 'size' => 'mediano', 'size_label' => 'Mediano', 'city' => 'ciudad-latina', 'city_label' => 'Ciudad Latina, Soacha', 'image' => 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=400&auto=format&fit=crop', 'created' => '2026-11-18'],
    ['name' => 'Max', 'type' => 'perros', 'breed' => 'Criollo', 'gender' => 'macho', 'age' => 3, 'age_label' => '3 años', 'size' => 'grande', 'size_label' => 'Grande', 'city' => 'ciudadela-sucre', 'city_label' => 'Ciudadela Sucre, Soacha', 'image' => 'https://images.unsplash.com/photo-1537151608828-ea2b11777ee8?w=400&auto=format&fit=crop', 'created' => '2026-11-16'],
    ['name' => 'Mía', 'type' => 'gatos', 'breed' => 'Gata doméstica', 'gender' => 'hembra', 'age' => 8 / 12, 'age_label' => '8 meses', 'size' => 'pequeno', 'size_label' => 'Pequeño', 'city' => 'leon-xiii', 'city_label' => 'León XIII, Soacha', 'image' => 'https://images.unsplash.com/photo-1533738363-b7f9aef128ce?w=400&auto=format&fit=crop', 'created' => '2026-11-14'],
    ['name' => 'Rocky', 'type' => 'perros', 'breed' => 'Pastor Alemán', 'gender' => 'macho', 'age' => 4 / 12, 'age_label' => '4 meses', 'size' => 'mediano', 'size_label' => 'Mediano', 'city' => 'ducales', 'city_label' => 'Ducales, Soacha', 'image' => 'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?w=400&auto=format&fit=crop', 'created' => '2026-11-12'],
    ['name' => 'Nala', 'type' => 'gatos', 'breed' => 'Gato doméstico', 'gender' => 'hembra', 'age' => 2, 'age_label' => '2 años', 'size' => 'mediano', 'size_label' => 'Mediano', 'city' => 'la-florida', 'city_label' => 'La Florida, Soacha', 'image' => 'https://images.unsplash.com/photo-1573865526739-10659fec78a5?w=400&auto=format&fit=crop', 'created' => '2026-11-10'],
    ['name' => 'Toby', 'type' => 'perros', 'breed' => 'Criollo', 'gender' => 'macho', 'age' => 1, 'age_label' => '1 año', 'size' => 'mediano', 'size_label' => 'Mediano', 'city' => 'san-nicolas', 'city_label' => 'San Nicolás, Soacha', 'image' => 'https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=400&auto=format&fit=crop', 'created' => '2026-11-08'],
    ['name' => 'Simba', 'type' => 'gatos', 'breed' => 'Gato doméstico', 'gender' => 'macho', 'age' => 3 / 12, 'age_label' => '3 meses', 'size' => 'pequeno', 'size_label' => 'Pequeño', 'city' => 'danubio', 'city_label' => 'Danubio, Soacha', 'image' => 'https://images.unsplash.com/photo-1574158622682-e40e69881006?w=400&auto=format&fit=crop', 'created' => '2026-11-06'],
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
if (!in_array($ciudad, ['', 'compartir', 'ciudad-latina', 'ducales', 'danubio', 'el-carmen', 'leon-xiii', 'olivos', 'ciudadela-sucre', 'luis-carlos-galan', 'la-florida', 'la-pradera', 'san-nicolas'], true)) {
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

$mascotasFiltradas = array_values(array_filter($mascotas, static function (array $mascota) use ($tipo, $tamanos, $sexos, $ciudad, $edadMinima, $edadMaxima): bool {
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
