<?php

namespace tagadvance\stooge;

/**
 * Temporary file helper for cookie jars and other short-lived request state.
 *
 * @author Tag <tagadvance+stooge@gmail.com>
 */
class File
{
    /**
     * Hidden constructor
     */
    private function __construct() {}

    /**
     * The file is unlinked by a shutdown function, so the path it returns is
     * only valid for the lifetime of the current script.
     *
     * @param string $fileName
     *            A prefix for the generated name, not the name itself.
     * @param ?string $directory
     *            Defaults to the system temporary directory.
     * @throws CurlException when the file could not be created.
     */
    public static function createTempFile($fileName, $directory = null): \SplFileInfo
    {
        if ($directory === null) {
            $directory = sys_get_temp_dir();
        }

        $temp = tempnam($directory, $fileName);
        if ($temp === false) {
            $message = 'temporary file could not be created';
            throw new CurlException($message);
        }

        $deleteOnExit = function () use ($temp) {
            unlink($temp);
        };
        register_shutdown_function($deleteOnExit);

        return new \SplFileInfo($temp);
    }

}
