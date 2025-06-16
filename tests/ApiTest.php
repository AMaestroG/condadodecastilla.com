<?php
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase {
    private function runScript(string $script, array $server): array {
        $env = [
            'REQUEST_METHOD' => $server['REQUEST_METHOD'],
            'REQUEST_URI' => $server['REQUEST_URI'],
            'HTTP_HOST' => 'localhost',
        ];
        $prepend = realpath(__DIR__.'/fixtures/prepend.php');
        $cmd = sprintf('php-cgi -d auto_prepend_file=%s %s',
            escapeshellarg($prepend),
            escapeshellarg($script)
        );
        $env['PATH'] = getenv('PATH');
        $env['REDIRECT_STATUS'] = '1';
        $env['SCRIPT_FILENAME'] = $script;
        $descriptor = [1 => ['pipe','w'], 2 => ['pipe','w']];
        $proc = proc_open($cmd, $descriptor, $pipes, null, $env);
        $output = stream_get_contents($pipes[1]);
        $err = stream_get_contents($pipes[2]);
        $status = proc_close($proc);
        return [$status, $output, $err];
    }

    public function testMuseoGet(): void {
        [$status, $out, $err] = $this->runScript(__DIR__.'/../api_museo.php', [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/api/museo/piezas'
        ]);
        $this->assertSame(0, $status, $err);
        $data = json_decode($out, true);
        $this->assertIsArray($data);
        $this->assertSame('pieza1', $data[0]['titulo']);
    }

    public function testGaleriaGet(): void {
        [$status, $out, $err] = $this->runScript(__DIR__.'/../api_galeria.php', [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/api/galeria/fotos'
        ]);
        $this->assertSame(0, $status, $err);
        $data = json_decode($out, true);
        $this->assertIsArray($data);
        $this->assertSame('foto1', $data[0]['titulo']);
    }

    public function testTiendaGet(): void {
        [$status, $out, $err] = $this->runScript(__DIR__.'/../api_tienda.php', [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/api/tienda/productos'
        ]);
        $this->assertSame(0, $status, $err);
        $data = json_decode($out, true);
        $this->assertIsArray($data);
        $this->assertSame('prod1', $data[0]['nombre']);
    }
}
