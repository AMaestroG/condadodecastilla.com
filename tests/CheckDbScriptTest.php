<?php

use PHPUnit\Framework\TestCase;

class CheckDbScriptTest extends TestCase
{
    public function testScriptFailsWhenPgIsreadyFails(): void
    {
        $tmpDir = sys_get_temp_dir() . '/fakebin_' . uniqid('', true);
        mkdir($tmpDir);
        $fake = $tmpDir . '/pg_isready';
        file_put_contents($fake, "#!/bin/sh\nexit 1\n");
        chmod($fake, 0755);
        $env = [
            'PATH' => $tmpDir . ':' . getenv('PATH'),
            'CONDADO_DB_PASSWORD' => 'password'
        ];
        $cmd = 'bash ' . escapeshellarg(__DIR__.'/../scripts/check_db.sh');
        $proc = proc_open($cmd, [1 => ['pipe','w'], 2 => ['pipe','w']], $pipes, null, $env);
        $out = stream_get_contents($pipes[1]);
        $err = stream_get_contents($pipes[2]);
        $status = proc_close($proc);
        unlink($fake);
        rmdir($tmpDir);
        $this->assertNotSame(0, $status, $out . $err);
    }
}
