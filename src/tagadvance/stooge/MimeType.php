<?php

namespace tagadvance\stooge;

/**
 * Common MIME types.
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types/Complete_list_of_MIME_types
 */
final class MimeType {

    const DEFAULT = 'application/octet-stream';

    const CSS = 'text/css';

    const HTML = 'text/html';

    const JAVASCRIPT = 'application/javascript';

    const JSON = 'application/json';

    const TEXT = 'text/plain';

    const XHTML = 'application/xhtml+xml';

    const GIF = 'image/gif';

    const JPEG = 'image/jpeg';

    const PNG = 'image/png';

    private function __construct() {}

}