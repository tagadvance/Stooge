<?php

namespace tagadvance\stooge;

use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function testCreateTempFileUsesTheSystemTempDirectory()
    {
        $file = File::createTempFile('foo');

        $this->assertFileExists($file->getPathname());
        $this->assertSame(realpath(sys_get_temp_dir()), $file->getPath());
    }

    public function testCreateTempFileUsesTheGivenDirectory()
    {
        $directory = sys_get_temp_dir() . '/' . uniqid('stooge-file-test-');
        mkdir($directory);

        $file = File::createTempFile('foo', $directory);
        // Shutdown functions run in registration order, so this runs after the
        // unlink that createTempFile() registered.
        register_shutdown_function(fn() => rmdir($directory));

        $this->assertFileExists($file->getPathname());
        $this->assertSame($directory, $file->getPath());
    }

}
