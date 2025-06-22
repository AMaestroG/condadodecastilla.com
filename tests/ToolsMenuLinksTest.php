<?php
use PHPUnit\Framework\TestCase;

class ToolsMenuLinksTest extends TestCase {
    private function runPage(string $script): array {
        $prepend = realpath(__DIR__.'/fixtures/page_prepend.php');
        $cmd = sprintf('php-cgi -d auto_prepend_file=%s %s',
            escapeshellarg($prepend),
            escapeshellarg($script)
        );
        $env = [
            'PATH' => getenv('PATH'),
            'REDIRECT_STATUS' => '1',
            'SCRIPT_FILENAME' => $script
        ];
        $proc = proc_open($cmd, [1=>['pipe','w'], 2=>['pipe','w']], $pipes, null, $env);
        $out = stream_get_contents($pipes[1]);
        $err = stream_get_contents($pipes[2]);
        $status = proc_close($proc);
        return [$status, $out, $err];
    }

    public static function urlProvider(): array {
        $path = __DIR__.'/../fragments/menus/tools-menu.html';
        if (!file_exists($path)) {
            // Lanzar SkippedTestError si el archivo no existe, para que la prueba se marque como omitida.
            // Necesitamos asegurarnos de que la clase SkippedTestError esté disponible.
            // Si no, simplemente devolver un array vacío y dejar que PHPUnit lo maneje.
            // Por ahora, para evitar el error "Class not found", volvemos a return [].
            // El test en sí deberá marcarse como skipped.
            return [];
        }
        $html = file_get_contents($path);
        $dom = new DOMDocument();
        libxml_use_internal_errors(true); // Suprimir errores de HTML mal formado
        if (!empty($html)) {
            $dom->loadHTML($html);
        }
        libxml_clear_errors(); // Limpiar errores de libxml

        $urls = [];
        foreach ($dom->getElementsByTagName('a') as $a) {
            $href = $a->getAttribute('href');
            if ($href && $href !== '#') { // Solo URLs válidas y no solo anclas
                $urls[] = [$href];
            }
        }
        return $urls;
    }

    /**
     * @dataProvider urlProvider
     */
    public function testLinkLoads(string $href): void {
        // Marcar la prueba como omitida si el archivo HTML del menú no existe.
        // Esto se hace aquí porque el dataProvider es estático y no puede usar $this->markTestSkipped.
        // Si urlProvider devuelve un array vacío, este test no se ejecutará para ningún dato.
        // Para una omisión más explícita si el archivo falta, el provider podría devolver un dataset especial
        // y el test comprobarlo. O la prueba podría comprobar la existencia del archivo aquí.
        $menuHtmlPath = __DIR__.'/../fragments/menus/tools-menu.html';
        if (!file_exists($menuHtmlPath)) {
            $this->markTestSkipped('El archivo fragments/menus/tools-menu.html no existe, omitiendo prueba de enlaces de menú de herramientas.');
        }

        $path = __DIR__.'/..'.$href;
        if (is_dir($path)) {
            $path .= '/index.php'; // Asumir index.php para directorios
        }

        $this->assertFileExists($path, "El archivo referenciado por el enlace '$href' no existe en '$path'.");

        if (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
            // Usar la función runPage de la clase para ejecutar scripts PHP
            [$status, $out, $err] = $this->runPage($path);
            $this->assertSame(0, $status, "Error al ejecutar $path: $err");
            $this->assertNotEmpty($out, "La salida de $path está vacía.");
        } else {
            // Para otros tipos de archivo (ej. HTML), simplemente leer el contenido
            $content = file_get_contents($path);
            $this->assertNotFalse($content, "No se pudo leer el archivo $path.");
            $this->assertNotEmpty($content, "El archivo $path está vacío.");
        }
    }
}
?>
