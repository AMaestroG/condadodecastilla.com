<?php
use PHPUnit\Framework\TestCase;

class ContactFormTest extends TestCase {
    private function runScript(array $env): array {
        $script = realpath(__DIR__.'/../contacto/submit.php');
        $prepend = realpath(__DIR__.'/fixtures/contact_prepend.php');
        $cmd = sprintf('php-cgi -d auto_prepend_file=%s %s',
            escapeshellarg($prepend),
            escapeshellarg($script)
        );
        $env = array_merge([
            'PATH' => getenv('PATH'),
            'REDIRECT_STATUS' => '1',
            'SCRIPT_FILENAME' => $script,
            'REQUEST_METHOD' => 'POST'
        ], $env);
        $proc = proc_open($cmd, [1=>['pipe','w'], 2=>['pipe','w']], $pipes, null, $env);
        $out = stream_get_contents($pipes[1]);
        $err = stream_get_contents($pipes[2]);
        $status = proc_close($proc);
        return [$status, $out, $err];
    }

    public function testSuccessfulSubmission(): void {
        [$status, $out, $err] = $this->runScript([
            'CONTACT_NOMBRE' => 'Juan',
            'CONTACT_EMAIL' => 'juan@example.com',
            'CONTACT_ASUNTO' => 'Hola',
            'CONTACT_MENSAJE' => 'Prueba'
        ]);
        $this->assertSame(0, $status, $err);
        $this->assertStringContainsString('Mensaje enviado correctamente', $out);
    }
}
?>
