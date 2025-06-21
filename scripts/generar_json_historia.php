<?php

require_once __DIR__ . '/../vendor/autoload.php';

use League\CommonMark\CommonMarkConverter;

// --- Configuración y directorios ---
define('DIR_BASE_HTML', '../historia/'); // Ajusta según la ubicación real de tus HTML
define('DIR_OUTPUT', '/app/data/historia/'); // Usar ruta absoluta
define('DIR_OUTPUT_SUBPAGINAS', DIR_OUTPUT . 'subpaginas/');

// Crear directorios de salida si no existen (PHP intentará crearlos si no existen)
// Los permisos ya deberían estar establecidos por un paso anterior de bash.
if (!is_dir(DIR_OUTPUT)) {
    if (!mkdir(DIR_OUTPUT, 0777, true) && !is_dir(DIR_OUTPUT)) { // Comprueba !is_dir de nuevo por si acaso
        die("Error: No se pudo crear el directorio de salida principal: " . DIR_OUTPUT . "\n");
    }
}
if (!is_dir(DIR_OUTPUT_SUBPAGINAS)) {
    if (!mkdir(DIR_OUTPUT_SUBPAGINAS, 0777, true) && !is_dir(DIR_OUTPUT_SUBPAGINAS)) {
        die("Error: No se pudo crear el directorio de salida de subpáginas: " . DIR_OUTPUT_SUBPAGINAS . "\n");
    }
}

echo "Directorios de salida verificados/creados.\n";

// --- Funciones Auxiliares ---

/**
 * Limpia una cadena de texto: trim y reemplaza múltiples espacios/saltos de línea.
 *
 * @param string $texto
 * @return string
 */
function limpiar_texto(string $texto): string {
    $texto = trim($texto);
    $texto = preg_replace('/\s+/', ' ', $texto);
    return $texto;
}

/**
 * Extrae bloques de contenido de un nodo DOM padre.
 *
 * @param DOMNode $nodo_padre
 * @param DOMXPath $xpath
 * @return array
 */
function extraer_bloques_contenido(DOMNode $nodo_padre, DOMXPath $xpath): array {
    $bloques = [];
    if (!$nodo_padre) {
        return $bloques;
    }

    foreach ($nodo_padre->childNodes as $nodo_hijo) {
        if ($nodo_hijo->nodeType === XML_ELEMENT_NODE) {
            $tipo = '';
            $texto = '';
            $html_original = $nodo_hijo->ownerDocument->saveHTML($nodo_hijo);

            switch (strtolower($nodo_hijo->nodeName)) {
                case 'p':
                    $tipo = 'parrafo';
                    $texto = limpiar_texto($nodo_hijo->textContent);
                    break;
                case 'h1':
                    $tipo = 'encabezado_h1';
                    $texto = limpiar_texto($nodo_hijo->textContent);
                    break;
                case 'h2':
                    $tipo = 'encabezado_h2';
                    $texto = limpiar_texto($nodo_hijo->textContent);
                    break;
                case 'h3':
                    $tipo = 'encabezado_h3';
                    $texto = limpiar_texto($nodo_hijo->textContent);
                    break;
                case 'h4':
                    $tipo = 'encabezado_h4';
                    $texto = limpiar_texto($nodo_hijo->textContent);
                    break;
                case 'ul':
                    $tipo = 'lista_ul';
                    $items_texto = [];
                    $listItems = $xpath->query('./li', $nodo_hijo);
                    foreach ($listItems as $item) {
                        $items_texto[] = limpiar_texto($item->textContent);
                    }
                    $texto = implode("\n", $items_texto); // O concatenar de otra forma
                    break;
                case 'ol':
                    $tipo = 'lista_ol';
                    $items_texto = [];
                    $listItems = $xpath->query('./li', $nodo_hijo);
                    foreach ($listItems as $item) {
                        $items_texto[] = limpiar_texto($item->textContent);
                    }
                    $texto = implode("\n", $items_texto);
                    break;
                case 'blockquote':
                    $tipo = 'cita';
                    $texto = limpiar_texto($nodo_hijo->textContent);
                    break;
                case 'img':
                    $tipo = 'imagen';
                    $texto = $xpath->evaluate('string(./@alt)', $nodo_hijo);
                    // Podrías querer extraer también el src, etc.
                    // $src = $xpath->evaluate('string(./@src)', $nodo_hijo);
                    break;
                // Añadir más casos según sea necesario (table, figure, etc.)
                default:
                    // Si no es un tipo conocido, pero tiene texto, guardarlo como párrafo.
                    $texto_potencial = limpiar_texto($nodo_hijo->textContent);
                    if (!empty($texto_potencial) && strlen($texto_potencial) > 10) { // Evitar nodos vacíos o muy cortos
                         $tipo = 'desconocido_convertido_parrafo';
                         $texto = $texto_potencial;
                    } else if (!empty($texto_potencial)) {
                        $tipo = 'desconocido_corto';
                        $texto = $texto_potencial;
                    }
                    break;
            }

            if (!empty($tipo) && !empty($texto)) {
                $bloques[] = [
                    'tipo' => $tipo,
                    'texto' => $texto,
                    'html_original' => $html_original,
                ];
            } elseif (!empty($tipo) && $tipo === 'imagen') { // Caso especial para imágenes sin alt text pero con src
                 $bloques[] = [
                    'tipo' => $tipo,
                    'texto' => $texto, // puede estar vacío
                    'src' => $xpath->evaluate('string(./@src)', $nodo_hijo),
                    'html_original' => $html_original,
                ];
            }
        }
    }
    return $bloques;
}

/**
 * Extrae enlaces internos de un documento o un nodo específico.
 *
 * @param DOMXPath $xpath
 * @param DOMNode|null $contextNode El nodo desde el cual buscar enlaces. Si es null, busca en todo el documento.
 * @return array
 */
function extraer_enlaces_internos(DOMXPath $xpath, ?DOMNode $contextNode = null): array {
    $enlaces = [];
    $query = './/a[@href]'; // Busca todos los 'a' con atributo 'href'

    $nodelist = $contextNode ? $xpath->query($query, $contextNode) : $xpath->query($query);

    foreach ($nodelist as $nodo_enlace) {
        $url = $nodo_enlace->getAttribute('href');
        // Filtrar enlaces externos, anclas en la misma página, y javascript:
        if (!preg_match('/^(#|javascript:|mailto:|tel:|https?:\/\/)/i', $url)) {
            $enlaces[] = [
                'texto' => limpiar_texto($nodo_enlace->textContent),
                'url' => $url,
            ];
        }
    }
    return $enlaces;
}


/**
 * Guarda datos en un archivo JSON.
 *
 * @param string $ruta_archivo
 * @param mixed $datos
 * @return bool
 */
function guardar_json(string $ruta_archivo, $datos): bool {
    echo "Intentando guardar JSON en: " . $ruta_archivo . "\n"; // DEBUG
    $json_contenido = json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    $directorio_del_archivo = dirname($ruta_archivo);
    if (!is_dir($directorio_del_archivo)) {
        echo "Directorio no existe, intentando crear: " . $directorio_del_archivo . "\n"; // DEBUG
        if (!mkdir($directorio_del_archivo, 0777, true) && !is_dir($directorio_del_archivo)) { //Añadido !is_dir para robustez
            echo "Error: No se pudo crear el directorio para el archivo JSON: " . $directorio_del_archivo . "\n";
            return false;
        }
        echo "Directorio creado o ya existente: " . $directorio_del_archivo . "\n"; // DEBUG
    }

    if (file_put_contents($ruta_archivo, $json_contenido)) {
        echo "Generado exitosamente: " . $ruta_archivo . "\n";
        return true;
    } else {
        echo "Error: No se pudo escribir el archivo " . $ruta_archivo . "\n";
        return false;
    }
}

// --- Lógica Principal ---

$archivos_procesados = []; // Para llevar cuenta de qué se ha generado

// 1. Procesamiento de historia/subpaginas_indice.html
function procesar_indice_subpaginas(DOMXPath $xpath, DOMDocument $doc, string $ruta_html_original): array {
    global $archivos_procesados;
    echo "Procesando: " . $ruta_html_original . "\n";

    $datos_indice = [
        'id_pagina' => 'indice_detallado',
        'titulo_pagina' => '',
        'ruta_html_original' => $ruta_html_original,
        'descripcion_general' => '',
        'temas_detallados' => []
    ];

    // Extraer título de la página (<h1> o <title>)
    $titulo_h1 = $xpath->query('//h1')->item(0);
    if ($titulo_h1) {
        $datos_indice['titulo_pagina'] = limpiar_texto($titulo_h1->textContent);
    } else {
        $titulo_title = $xpath->query('//title')->item(0);
        if ($titulo_title) {
            $datos_indice['titulo_pagina'] = limpiar_texto($titulo_title->textContent);
        }
    }

    // Extraer descripción general (primer <p> después de <h1> o un <p class="intro"> específico)
    // Esto es una suposición, puede necesitar ajuste.
    $descripcion_p = $xpath->query('//h1/following-sibling::p[1]')->item(0);
     if (!$descripcion_p) { // Intenta buscar un P con clase intro o similar si no lo encuentra
        $descripcion_p = $xpath->query('//p[contains(@class, "intro") or contains(@class, "introduction") or contains(@class, "lead")]')->item(0);
    }
    if ($descripcion_p) {
        $datos_indice['descripcion_general'] = limpiar_texto($descripcion_p->textContent);
    }

    $lista_ul = $xpath->query('//ul[contains(@class, "subpage-index-list")]')->item(0);
    if ($lista_ul) {
        $items_li = $xpath->query('./li', $lista_ul);
        foreach ($items_li as $item_li) {
            $titulo_tema_nodo = $xpath->query('.//h3', $item_li)->item(0);
            $descripcion_corta_nodo = $xpath->query('.//p[1]', $item_li)->item(0); // Primer P dentro del LI
            $enlace_nodo = $xpath->query('.//a[contains(@class, "read-more")]', $item_li)->item(0);

            if ($titulo_tema_nodo && $descripcion_corta_nodo && $enlace_nodo) {
                $ruta_html_tema = $enlace_nodo->getAttribute('href');
                $nombre_archivo_tema = basename($ruta_html_tema, '.html');
                $id_tema = preg_replace('/[^a-z0-9_]+/', '_', strtolower($nombre_archivo_tema)); // Limpiar id

                $datos_indice['temas_detallados'][] = [
                    'id_tema' => $id_tema,
                    'titulo_tema' => limpiar_texto($titulo_tema_nodo->textContent),
                    'descripcion_corta' => limpiar_texto($descripcion_corta_nodo->textContent),
                    'ruta_html_original' => $ruta_html_tema, // Guardar la ruta relativa original
                    'ruta_json_contenido' => DIR_OUTPUT_SUBPAGINAS . $id_tema . '.json'
                ];
            }
        }
    } else {
        echo "Advertencia: No se encontró la lista <ul class=\"subpage-index-list\"> en " . $ruta_html_original . "\n";
    }

    $ruta_json_salida = DIR_OUTPUT . 'indice_detallado.json';
    echo "Preparando para guardar indice_detallado.json en: " . $ruta_json_salida . "\n"; // DEBUG
    guardar_json($ruta_json_salida, $datos_indice);
    $archivos_procesados['indice_detallado'] = 'data/historia/indice_detallado.json';
    return $datos_indice['temas_detallados'];
}


// 2. Procesamiento de cada Subpágina Detallada
function procesar_subpagina_detallada(string $ruta_html_subpagina, string $id_tema, string $ruta_json_salida_esperada, DOMXPath $xpath_sub, DOMDocument $doc_sub) {
    global $archivos_procesados;
    echo "Procesando subpágina: " . $ruta_html_subpagina . "\n";

    $datos_subpagina = [
        'id_pagina' => $id_tema,
        'titulo_pagina' => '',
        'ruta_html_original' => $ruta_html_subpagina,
        'contenido_principal' => [],
        'enlaces_internos' => [],
        'metadatos' => (object)[] // JSON object, not array
    ];

    // Extraer título de la página (<h1> o <title>)
    $titulo_h1 = $xpath_sub->query('//h1')->item(0);
    if ($titulo_h1) {
        $datos_subpagina['titulo_pagina'] = limpiar_texto($titulo_h1->textContent);
    } else {
        $titulo_title = $xpath_sub->query('//title')->item(0);
        if ($titulo_title) {
            $datos_subpagina['titulo_pagina'] = limpiar_texto($titulo_title->textContent);
        }
    }

    // Identificar el contenedor principal del contenido
    // Pruebo varias clases comunes para artículos. Ajustar si es necesario.
    $contenedor_articulo = $xpath_sub->query('//div[contains(@class, "article-content")]')->item(0);
    if (!$contenedor_articulo) {
        $contenedor_articulo = $xpath_sub->query('//main[contains(@class, "content")]')->item(0);
    }
    if (!$contenedor_articulo) {
        $contenedor_articulo = $xpath_sub->query('//article')->item(0);
    }
     if (!$contenedor_articulo) {
        // Como último recurso, si no hay un contenedor claro, tomar el body
        // pero esto podría traer mucho ruido (nav, footer, etc.)
        // Sería mejor tener selectores más precisos.
        $contenedor_articulo = $xpath_sub->query('//body')->item(0);
        echo "Advertencia: No se encontró un contenedor de artículo claro para " . $ruta_html_subpagina . ". Usando 'body' como fallback.\n";
    }


    if ($contenedor_articulo) {
        $datos_subpagina['contenido_principal'] = extraer_bloques_contenido($contenedor_articulo, $xpath_sub);
        $datos_subpagina['enlaces_internos'] = extraer_enlaces_internos($xpath_sub, $contenedor_articulo);
    } else {
        echo "Advertencia: No se encontró el contenedor de contenido principal en " . $ruta_html_subpagina . "\n";
    }

    // Para el caso específico de origenes_castilla, buscar autor
    if ($id_tema === 'origenes_castilla') {
        // Intentar encontrar "Iván García Blanco". Esto es muy específico.
        // Podrías buscar en párrafos, o en elementos con clase "author" o "autor"
        $posible_autor = $xpath_sub->query("//*[contains(text(), 'Iván García Blanco')]")->item(0);
        if ($posible_autor) {
            // Verificar que no sea parte de un texto más grande de forma accidental
            if (strpos(limpiar_texto($posible_autor->textContent), 'Iván García Blanco') !== false ) {
                 $datos_subpagina['metadatos']->autor_investigacion = 'Iván García Blanco';
            }
        }
        // Si hay un elemento específico con la clase autor
        $autor_element = $xpath_sub->query("//*[@class='author' or @class='autor']")->item(0);
        if($autor_element){
            $datos_subpagina['metadatos']->autor_investigacion = limpiar_texto($autor_element->textContent);
        }
    }


    guardar_json($ruta_json_salida_esperada, $datos_subpagina); // $ruta_json_salida_esperada ya se basa en DIR_OUTPUT
    // Para $archivos_procesados, guardaremos la ruta relativa esperada en el JSON final
    $archivos_procesados[$id_tema] = 'data/historia/subpaginas/' . $id_tema . '.json';
    echo "Preparando para guardar subpágina JSON para id_tema '".$id_tema."' en: " . $ruta_json_salida_esperada . "\n"; // DEBUG
}

// 3. Procesamiento de historia/historia.html (Línea de Tiempo)
function procesar_linea_tiempo(DOMXPath $xpath, DOMDocument $doc, string $ruta_html_original) {
    global $archivos_procesados;
    echo "Procesando: " . $ruta_html_original . "\n";

    $datos_linea_tiempo = [
        'id_pagina' => 'linea_tiempo',
        'titulo_pagina' => '',
        'ruta_html_original' => $ruta_html_original,
        'introduccion' => '',
        'hitos' => [],
        'enlaces_internos' => [] // Otros enlaces de la página
    ];

    // Extraer título de la página
    $titulo_h1 = $xpath->query('//h1')->item(0);
    if ($titulo_h1) {
        $datos_linea_tiempo['titulo_pagina'] = limpiar_texto($titulo_h1->textContent);
    } else {
        $titulo_title = $xpath->query('//title')->item(0);
        if ($titulo_title) {
            $datos_linea_tiempo['titulo_pagina'] = limpiar_texto($titulo_title->textContent);
        }
    }

    // Extraer introducción
    $intro_p = $xpath->query('//p[contains(@class, "timeline-intro")]')->item(0);
    if ($intro_p) {
        $datos_linea_tiempo['introduccion'] = limpiar_texto($intro_p->textContent);
    } else {
         // Fallback: primer P después del H1 o P principal de la página si no hay clase específica
        $intro_p_fallback = $xpath->query('//h1/following-sibling::p[1]')->item(0);
        if ($intro_p_fallback) {
            $datos_linea_tiempo['introduccion'] = limpiar_texto($intro_p_fallback->textContent);
        } else {
            echo "Advertencia: No se encontró introducción para la línea de tiempo en " . $ruta_html_original . "\n";
        }
    }


    $timeline_ul = $xpath->query('//ul[contains(@class, "timeline")]')->item(0);
    if ($timeline_ul) {
        $items_li = $xpath->query('./li[contains(@class, "timeline-item")]', $timeline_ul);
        foreach ($items_li as $item_li) {
            $hito = [
                'titulo_hito' => '',
                'texto_descripcion' => '',
                'fecha_hito' => '', // Intentar extraer fecha si existe
                'enlace_relacionado' => null
            ];

            $titulo_hito_nodo = $xpath->query('.//h3', $item_li)->item(0);
            if ($titulo_hito_nodo) {
                $hito['titulo_hito'] = limpiar_texto($titulo_hito_nodo->textContent);
            }

            $texto_descripcion_nodo = $xpath->query('.//p', $item_li)->item(0); // Asume que la descripción está en el primer <p>
            if ($texto_descripcion_nodo) {
                $hito['texto_descripcion'] = limpiar_texto($texto_descripcion_nodo->textContent);
            }

            // Intentar extraer fecha (ej. de un <span class="timeline-date"> o similar)
            $fecha_nodo = $xpath->query('.//*[contains(@class, "date") or contains(@class, "fecha") or contains(@class, "time")]', $item_li)->item(0);
            if ($fecha_nodo) {
                $hito['fecha_hito'] = limpiar_texto($fecha_nodo->textContent);
            }


            $enlace_nodo = $xpath->query('.//a', $item_li)->item(0); // Primer enlace dentro del hito
            if ($enlace_nodo) {
                $hito['enlace_relacionado'] = [
                    'texto' => limpiar_texto($enlace_nodo->textContent),
                    'url' => $enlace_nodo->getAttribute('href')
                ];
            }
            $datos_linea_tiempo['hitos'][] = $hito;
        }
        // Extraer otros enlaces de la página (fuera de la lista de hitos)
        // Podríamos pasar el nodo $timeline_ul como contexto para excluir sus enlaces si es necesario,
        // o buscar en un contenedor padre si la timeline está dentro de un <article> o <main>
        $datos_linea_tiempo['enlaces_internos'] = extraer_enlaces_internos($xpath, $doc->documentElement);


    } else {
        echo "Advertencia: No se encontró la lista <ul class=\"timeline\"> en " . $ruta_html_original . "\n";
    }

    $ruta_json_salida = DIR_OUTPUT . 'linea_tiempo.json';
    echo "Preparando para guardar linea_tiempo.json en: " . $ruta_json_salida . "\n"; // DEBUG
    guardar_json($ruta_json_salida, $datos_linea_tiempo);
    $archivos_procesados['linea_tiempo'] = 'data/historia/linea_tiempo.json';
}


// --- Ejecución Principal ---

$libxml_previous_state = libxml_use_internal_errors(true); // Para manejar errores de HTML mal formado

// Procesar el índice de subpáginas primero para obtener la lista de temas
$ruta_indice_subpaginas_html = DIR_BASE_HTML . 'subpaginas_indice.html';
$temas_detallados_a_procesar = [];

if (file_exists($ruta_indice_subpaginas_html)) {
    $doc_indice = new DOMDocument();
    // Usar @ para suprimir warnings de HTML mal formado, libxml_use_internal_errors los capturará.
    if (@$doc_indice->loadHTMLFile($ruta_indice_subpaginas_html)) {
        $xpath_indice = new DOMXPath($doc_indice);
        $temas_detallados_a_procesar = procesar_indice_subpaginas($xpath_indice, $doc_indice, 'historia/subpaginas_indice.html');
    } else {
        echo "Error: No se pudo parsear el archivo HTML: " . $ruta_indice_subpaginas_html . "\n";
        foreach (libxml_get_errors() as $error) {
            echo "\tError XML: " . $error->message . " en línea " . $error->line . "\n";
        }
        libxml_clear_errors();
    }
} else {
    echo "Error: No se encontró el archivo " . $ruta_indice_subpaginas_html . "\n";
}


// Procesar cada subpágina detallada obtenida del índice
if (!empty($temas_detallados_a_procesar)) {
    foreach ($temas_detallados_a_procesar as $tema) {
        // La ruta_html_original en $tema es relativa al directorio 'historia/'
        // o a la raíz del sitio si así están en el HTML.
        // Necesitamos construir la ruta completa desde DIR_BASE_HTML
        $ruta_html_completa_tema = DIR_BASE_HTML . $tema['ruta_html_original'];

        // Si la ruta ya es absoluta o contiene 'historia/', ajustamos
        if (strpos($tema['ruta_html_original'], 'historia/') === 0) {
             // Ejemplo: $tema['ruta_html_original'] es 'historia/subpaginas/archivo.html'
             // DIR_BASE_HTML es '../historia/'
             // Necesitamos '../' . $tema['ruta_html_original'] -> '../historia/subpaginas/archivo.html'
             // O si DIR_BASE_HTML ya es el directorio correcto que contiene 'historia/subpaginas_indice.html'
             // entonces $ruta_html_completa_tema = DIR_BASE_HTML . str_replace('historia/', '', $tema['ruta_html_original']);
             // La clave es que $tema['ruta_html_original'] debe ser interpretable desde la raíz del proyecto o desde DIR_BASE_HTML
             // Si $tema['ruta_html_original'] es algo como 'subpaginas/archivo.html', entonces
             // $ruta_html_completa_tema = DIR_BASE_HTML . 'subpaginas/' . basename($tema['ruta_html_original']);
             // Es importante asegurar que $ruta_html_completa_tema sea la ruta correcta al archivo HTML.
             // Por simplicidad, asumiré que las rutas en $tema['ruta_html_original'] son relativas a DIR_BASE_HTML
        }


        if (file_exists($ruta_html_completa_tema)) {
            $doc_sub = new DOMDocument();
            if (@$doc_sub->loadHTMLFile($ruta_html_completa_tema)) {
                $xpath_sub = new DOMXPath($doc_sub);
                procesar_subpagina_detallada(
                    $tema['ruta_html_original'], // Guardar la ruta relativa original
                    $tema['id_tema'],
                    $tema['ruta_json_contenido'],
                    $xpath_sub,
                    $doc_sub
                );
            } else {
                echo "Error: No se pudo parsear el archivo HTML de subpágina: " . $ruta_html_completa_tema . "\n";
                 foreach (libxml_get_errors() as $error) {
                    echo "\tError XML: " . $error->message . " en línea " . $error->line . "\n";
                }
                libxml_clear_errors();
            }
        } else {
            echo "Error: No se encontró el archivo de subpágina: " . $ruta_html_completa_tema . " (ID: " . $tema['id_tema'] .")\n";
        }
    }
}


// Procesar historia/historia.html (Línea de Tiempo)
$ruta_linea_tiempo_html = DIR_BASE_HTML . 'historia.html';
if (file_exists($ruta_linea_tiempo_html)) {
    $doc_lt = new DOMDocument();
    if (@$doc_lt->loadHTMLFile($ruta_linea_tiempo_html)) {
        $xpath_lt = new DOMXPath($doc_lt);
        procesar_linea_tiempo($xpath_lt, $doc_lt, 'historia/historia.html');
    } else {
        echo "Error: No se pudo parsear el archivo HTML: " . $ruta_linea_tiempo_html . "\n";
        foreach (libxml_get_errors() as $error) {
            echo "\tError XML: " . $error->message . " en línea " . $error->line . "\n";
        }
        libxml_clear_errors();
    }
} else {
    echo "Error: No se encontró el archivo " . $ruta_linea_tiempo_html . "\n";
}

// Procesar docs/historia_ampliada_nuevo4.md
// Este se procesará como una subpágina detallada, pero con un id_tema y ruta_json_salida específicos
$ruta_nuestra_historia_html = '../docs/historia_ampliada_nuevo4.md';
$id_tema_nuestra_historia = 'origenes_castilla'; // Según el punto 7 del requerimiento
// $ruta_json_nuestra_historia debe usar la nueva DIR_OUTPUT para la escritura temporal
$ruta_json_escritura_nuestra_historia = DIR_OUTPUT . $id_tema_nuestra_historia . '.json';

if (file_exists($ruta_nuestra_historia_html)) {
    $markdown_nh = file_get_contents($ruta_nuestra_historia_html);
    $converter_nh = new CommonMarkConverter();
    $temp_html_nh = tempnam(sys_get_temp_dir(), 'origenes_castilla') . '.html';
    file_put_contents($temp_html_nh, $converter_nh->convert($markdown_nh)->getContent());

    $doc_nh = new DOMDocument();
    if (@$doc_nh->loadHTMLFile($temp_html_nh)) {
        $xpath_nh = new DOMXPath($doc_nh);
        // Llamamos a procesar_subpagina_detallada directamente
        echo "Preparando para procesar y guardar origenes_castilla.json (origenes_castilla) en: " . $ruta_json_escritura_nuestra_historia . "\n"; // DEBUG
        procesar_subpagina_detallada(
            'docs/historia_ampliada_nuevo4.md',
            $id_tema_nuestra_historia,
            $ruta_json_escritura_nuestra_historia,
            $xpath_nh,
            $doc_nh
        );
        // Actualizar $archivos_procesados con la ruta final esperada
        $archivos_procesados[$id_tema_nuestra_historia] = 'data/historia/' . $id_tema_nuestra_historia . '.json';

    } else {
        echo "Error: No se pudo parsear el archivo HTML: " . $ruta_nuestra_historia_html . "\n";
        foreach (libxml_get_errors() as $error) {
            echo "\tError XML: " . $error->message . " en línea " . $error->line . "\n";
        }
        libxml_clear_errors();
    }
} else {
    echo "Error: No se encontró el archivo " . $ruta_nuestra_historia_html . "\n";
}


// 8. Generación del Índice Principal (data/historia/historia_indice.json)
echo "Generando índice principal: data/historia/historia_indice.json\n";
$indice_principal_contenido = [
    "titulo_general" => "Índice Principal de Historia",
    "descripcion_general" => "Este índice recopila los diferentes apartados históricos disponibles en formato JSON.",
    "secciones" => []
];

if (isset($archivos_procesados['linea_tiempo'])) {
    $indice_principal_contenido['secciones'][] = [
        "id_seccion" => "linea_tiempo",
        "titulo_seccion" => "Línea de Tiempo Histórica",
        "descripcion" => "Una cronología de los eventos más importantes.",
        "ruta_json" => $archivos_procesados['linea_tiempo'] // Ya tiene la ruta final correcta
    ];
}
if (isset($archivos_procesados['origenes_castilla'])) {
     $indice_principal_contenido['secciones'][] = [
        "id_seccion" => "origenes_castilla",
        "titulo_seccion" => "Orígenes de Castilla y la Historia de la Región",
        "descripcion" => "Investigación sobre los orígenes de Castilla y la historia temprana de la comarca.",
        "ruta_json" => $archivos_procesados['origenes_castilla'] // Ya tiene la ruta final correcta
    ];
}
if (isset($archivos_procesados['indice_detallado'])) {
    $indice_principal_contenido['secciones'][] = [
        "id_seccion" => "temas_detallados_indice",
        "titulo_seccion" => "Índice de Temas Históricos Detallados",
        "descripcion" => "Un índice que enlaza a diversos temas históricos específicos y sus contenidos.",
        "ruta_json" => $archivos_procesados['indice_detallado'] // Ya tiene la ruta final correcta
    ];
    // Podríamos añadir también enlaces directos a cada subpágina si fuera necesario
    // foreach ($temas_detallados_a_procesar as $tema) {
    //     if (isset($archivos_procesados[$tema['id_tema']])) {
    //         $indice_principal_contenido['secciones'][] = [
    //             "id_seccion" => "subpagina_" . $tema['id_tema'],
    //             "titulo_seccion" => "Subpágina: " . $tema['titulo_tema'],
    //             "descripcion" => $tema['descripcion_corta'],
    //             "ruta_json" => str_replace(DIR_OUTPUT_SUBPAGINAS, 'data/historia/subpaginas/', $archivos_procesados[$tema['id_tema']])
    //         ];
    //     }
    // }
}

echo "Preparando para guardar historia_indice.json en: " . (DIR_OUTPUT . 'historia_indice.json') . "\n"; // DEBUG
guardar_json(DIR_OUTPUT . 'historia_indice.json', $indice_principal_contenido);


libxml_use_internal_errors($libxml_previous_state); // Restaurar el manejo de errores
echo "Proceso completado.\n";

?>
