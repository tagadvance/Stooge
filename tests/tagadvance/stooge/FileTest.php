<?php

namespace tagadvance\stooge;

use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function testCreateTempFileIsNotNull()
    {
        $name = 'foo';
        $file = File::createTempFile($name);
        $this->assertNotNull($file);
    }

    public function testCreateTempFileWithDirectoryIsNotNull()
    {
        $name = 'foo';
        $directory = '/tmp';
        $file = File::createTempFile($name, $directory);
        $this->assertNotNull($file);
    }

}
