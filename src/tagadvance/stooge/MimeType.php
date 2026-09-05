<?php

namespace tagadvance\stooge;

/**
 * Common MIME types.
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types/Complete_list_of_MIME_types
 */
final class MimeType
{
    public const DEFAULT = 'application/octet-stream';

    public const CSS = 'text/css';

    public const HTML = 'text/html';

    public const JAVASCRIPT = 'application/javascript';

    public const JSON = 'application/json';

    public const TEXT = 'text/plain';

    public const XHTML = 'application/xhtml+xml';

    public const GIF = 'image/gif';

    public const JPEG = 'image/jpeg';

    public const PNG = 'image/png';

    private function __construct() {}

}
