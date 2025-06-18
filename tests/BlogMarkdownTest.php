<?php
use PHPUnit\Framework\TestCase;

class BlogMarkdownTest extends TestCase {
    private function runPage(string $query): array {
        $prepend = realpath(__DIR__.'/fixtures/page_prepend.php');
        $cmd = sprintf('php-cgi -d auto_prepend_file=%s %s',
            escapeshellarg($prepend),
            escapeshellarg(__DIR__.'/../blog.php')
        );
        $env = [
            'PATH' => getenv('PATH'),
            'REDIRECT_STATUS' => '1',
            'SCRIPT_FILENAME' => realpath(__DIR__.'/../blog.php'),
            'QUERY_STRING' => $query
        ];
        $proc = proc_open($cmd, [1=>['pipe','w'], 2=>['pipe','w']], $pipes, null, $env);
        $out = stream_get_contents($pipes[1]);
        $err = stream_get_contents($pipes[2]);
        $status = proc_close($proc);
        return [$status, $out, $err];
    }

    public function testBlogPostRendering(): void {
        $slug = '01_por-desgracia-para-mi-no-puedo-usar-chat';
        [$status, $out, $err] = $this->runPage('post=' . $slug);
        $this->assertSame(0, $status, $err);
        $this->assertStringContainsString('<h1>Por desgracia para mi no puedo usar Chat GPT desde Paraguay.... pero use</h1>', $out);
        $this->assertStringContainsString('<p>esta otra IA Chatsonic', $out);
    }
}
?>
