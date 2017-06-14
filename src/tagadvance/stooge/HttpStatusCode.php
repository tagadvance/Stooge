<?php

namespace tagadvance\stooge;

/**
 * Some of the status codes were originally copied from <a href="http://www.peej.co.uk/tonic/api/source/src/tonic/response.php.html">Tonic\Response</a> for convenience.
 *
 * Documentation is copied from Wikipedia and is licensed under the <a href="https://en.wikipedia.org/wiki/Wikipedia:Text_of_Creative_Commons_Attribution-ShareAlike_3.0_Unported_License">Creative Commons Attribution-ShareAlike License</a>.
 * 
 * @see http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
 */
final class HttpStatusCode {
	
	/*
	 * 1xx Informational responses
	 * 
	 * An informational response indicates that the request was received and understood. It is issued on a provisional basis while request processing continues. It alerts the client to wait for a final response. The message consists only of the status line and optional header fields, and is terminated by an empty line. As the HTTP/1.0 standard did not define any 1xx status codes, servers must not[note 1] send a 1xx response to an HTTP/1.0 compliant client except under experimental conditions.
	 */
	
	/**
	 * 100 Continue
	 * The server has received the request headers and the client should proceed to send the request body (in the case of a request for which a body needs to be sent; for example, a POST request).
	 * Sending a large request body to a server after a request has been rejected for inappropriate headers would be inefficient. To have a server check the request's headers, a client must send Expect: 100-continue as a header in its initial request and receive a 100 Continue status code in response before sending the body. The response 417 Expectation Failed indicates the request should not be continued.
	 *
	 * @var integer
	 */
	const CONTINUE = 100;
	/**
	 * 101 Switching Protocols
	 * The requester has asked the server to switch protocols and the server has agreed to do so.
	 *
	 * @var integer
	 */
	const SWITCHING_PROTOCOLS = 101;
	
	/*
	 * 2xx Success
	 * 
	 * This class of status codes indicates the action requested by the client was received, understood, accepted, and processed successfully.
	 */
	
	/**
	 * 200 OK
	 * Standard response for successful HTTP requests.
	 * The actual response will depend on the request method used. In a GET request, the response will contain an entity corresponding to the requested resource. In a POST request, the response will contain an entity describing or containing the result of the action.
	 *
	 * @var integer
	 */
	const OK = 200;
	/**
	 * 201 Created
	 * The request has been fulfilled, resulting in the creation of a new resource.
	 *
	 * @var integer
	 */
	const CREATED = 201;
	/**
	 * 202 Accepted
	 * The request has been accepted for processing, but the processing has not been completed.
	 * The request might or might not be eventually acted upon, and may be disallowed when processing occurs.
	 *
	 * @var integer
	 */
	const ACCEPTED = 202;
	/**
	 * 203 Non-Authoritative Information (since HTTP/1.1)
	 * The server is a transforming proxy (e.g.
	 * a Web accelerator) that received a 200 OK from its origin, but is returning a modified version of the origin's response.
	 *
	 * @var integer
	 */
	const NONAUTHORATIVE_INFORMATION = 203;
	/**
	 * 204 No Content
	 * The server successfully processed the request and is not returning any content.
	 *
	 * @var integer
	 */
	const NO_CONTENT = 204;
	/**
	 * 205 Reset Content
	 * The server successfully processed the request, but is not returning any content.
	 * Unlike a 204 response, this response requires that the requester reset the document view.
	 *
	 * @var integer
	 */
	const RESET_CONTENT = 205;
	/**
	 * 206 Partial Content (RFC 7233)
	 * The server is delivering only part of the resource (byte serving) due to a range header sent by the client.
	 * The range header is used by HTTP clients to enable resuming of interrupted downloads, or split a download into multiple simultaneous streams.
	 *
	 * @var integer
	 */
	const PARTIAL_CONTENT = 206;
	/**
	 * 226 IM Used (RFC 3229)
	 * The server has fulfilled a request for the resource, and the response is a representation of the result of one or more instance-manipulations applied to the current instance.
	 *
	 * @var integer
	 */
	const IM_USED = 226;
	
	/*
	 * 3xx Redirection
	 * 
	 * This class of status code indicates the client must take additional action to complete the request. Many of these status codes are used in URL redirection.
	 * 
	 * A user agent may carry out the additional action with no user interaction only if the method used in the second request is GET or HEAD. A user agent may automatically redirect a request. A user agent should detect and intervene to prevent cyclical redirects.
	 */
	
	/**
	 * 300 Multiple Choices
	 * Indicates multiple options for the resource from which the client may choose (via agent-driven content negotiation).
	 * For example, this code could be used to present multiple video format options, to list files with different filename extensions, or to suggest word-sense disambiguation.
	 *
	 * @var integer
	 */
	const MULTIPLE_CHOICES = 300;
	/**
	 * 301 Moved Permanently
	 * This and all future requests should be directed to the given URI.
	 *
	 * @var integer
	 */
	const MOVED_PERMANENTLY = 301;
	/**
	 * 302 Found
	 * This is an example of industry practice contradicting the standard.
	 * The HTTP/1.0 specification (RFC 1945) required the client to perform a temporary redirect (the original describing phrase was "Moved Temporarily"), but popular browsers implemented 302 with the functionality of a 303 See Other. Therefore, HTTP/1.1 added status codes 303 and 307 to distinguish between the two behaviours. However, some Web applications and frameworks use the 302 status code as if it were the 303.
	 *
	 * @var integer
	 */
	const FOUND = 302;
	/**
	 * 303 See Other (since HTTP/1.1)
	 * The response to the request can be found under another URI using a GET method.
	 * When received in response to a POST (or PUT/DELETE), the client should presume that the server has received the data and should issue a redirect with a separate GET message.
	 *
	 * @var integer
	 */
	const SEE_OTHER = 303;
	/**
	 * 304 Not Modified (RFC 7232)
	 * Indicates that the resource has not been modified since the version specified by the request headers If-Modified-Since or If-None-Match.
	 * In such case, there is no need to retransmit the resource since the client still has a previously-downloaded copy.
	 *
	 * @var integer
	 */
	const NOT_MODIFIED = 304;
	/**
	 * 305 Use Proxy (since HTTP/1.1)
	 * The requested resource is available only through a proxy, the address for which is provided in the response.
	 * Many HTTP clients (such as Mozilla and Internet Explorer) do not correctly handle responses with this status code, primarily for security reasons.
	 *
	 * @var integer
	 */
	const USE_PROXY = 305;
	/**
	 * 306 Switch Proxy
	 * No longer used.
	 * Originally meant "Subsequent requests should use the specified proxy."
	 *
	 * @var integer
	 */
	const SWITCH_PROXY = 306;
	/**
	 * 307 Temporary Redirect (since HTTP/1.1)
	 * In this case, the request should be repeated with another URI; however, future requests should still use the original URI.
	 * In contrast to how 302 was historically implemented, the request method is not allowed to be changed when reissuing the original request. For example, a POST request should be repeated using another POST request.
	 *
	 * @var integer
	 */
	const TEMPORARY_REDIRECT = 307;
	/**
	 * 308 Permanent Redirect (RFC 7538)
	 * The request and all future requests should be repeated using another URI.
	 * 307 and 308 parallel the behaviors of 302 and 301, but do not allow the HTTP method to change. So, for example, submitting a form to a permanently redirected resource may continue smoothly.
	 *
	 * @var integer
	 */
	const PERMANENT_REDIRECT = 308;
	
	/*
	 * 4xx Client errors
	 *
	 * The 4xx class of status codes is intended for situations in which the client seems to have erred. Except when responding to a HEAD request, the server should include an entity containing an explanation of the error situation, and whether it is a temporary or permanent condition. These status codes are applicable to any request method. User agents should display any included entity to the user.
	 */
	
	/**
	 * 400 Bad Request
	 * The server cannot or will not process the request due to an apparent client error (e.g., malformed request syntax, size too large, invalid request message framing, or deceptive request routing).
	 *
	 * @var integer
	 */
	const BAD_REQUEST = 400;
	/**
	 * 401 Unauthorized (RFC 7235)
	 * Similar to 403 Forbidden, but specifically for use when authentication is required and has failed or has not yet been provided.
	 * The response must include a WWW-Authenticate header field containing a challenge applicable to the requested resource. See Basic access authentication and Digest access authentication. 401 semantically means "unauthenticated", i.e. the user does not have the necessary credentials.
	 * Note: Some sites issue HTTP 401 when an IP address is banned from the website (usually the website domain) and that specific address is refused permission to access a website.
	 *
	 * @var integer
	 */
	const UNAUTHORIZED = 401;
	/**
	 * 402 Payment Required
	 * Reserved for future use.
	 * The original intention was that this code might be used as part of some form of digital cash or micropayment scheme, but that has not happened, and this code is not usually used. Google Developers API uses this status if a particular developer has exceeded the daily limit on requests.
	 *
	 * @var integer
	 */
	const PAYMENT_REQUIRED = 402;
	/**
	 * 403 Forbidden
	 * The request was valid, but the server is refusing action.
	 * The user might not have the necessary permissions for a resource.
	 *
	 * @var integer
	 */
	const FORBIDDEN = 403;
	/**
	 * 404 Not Found
	 * The requested resource could not be found but may be available in the future.
	 * Subsequent requests by the client are permissible.
	 *
	 * @var integer
	 */
	const NOT_FOUND = 404;
	/**
	 * 405 Method Not Allowed
	 * A request method is not supported for the requested resource; for example, a GET request on a form that requires data to be presented via POST, or a PUT request on a read-only resource.
	 *
	 * @var integer
	 */
	const METHOD_NOT_ALLOWED = 405;
	/**
	 * 406 Not Acceptable
	 * The requested resource is capable of generating only content not acceptable according to the Accept headers sent in the request. See Content negotiation.
	 *
	 * @var integer
	 */
	const NOT_ACCEPTABLE = 406;
	/**
	 * 407 Proxy Authentication Required (RFC 7235)
	 * The client must first authenticate itself with the proxy.
	 *
	 * @var integer
	 */
	const PROXY_AUTHENTICATION_REQUIRED = 407;
	/**
	 * 408 Request Timeout
	 * The server timed out waiting for the request.
	 * According to HTTP specifications: "The client did not produce a request within the time that the server was prepared to wait. The client MAY repeat the request without modifications at any later time."
	 *
	 * @var integer
	 */
	const REQUEST_TIMEOUT = 408;
	/**
	 * 409 Conflict
	 * Indicates that the request could not be processed because of conflict in the request, such as an edit conflict between multiple simultaneous updates.
	 *
	 * @var integer
	 */
	const CONFLICT = 409;
	/**
	 * 410 Gone
	 * Indicates that the resource requested is no longer available and will not be available again.
	 * This should be used when a resource has been intentionally removed and the resource should be purged. Upon receiving a 410 status code, the client should not request the resource in the future. Clients such as search engines should remove the resource from their indices. Most use cases do not require clients and search engines to purge the resource, and a "404 Not Found" may be used instead.
	 *
	 * @var integer
	 */
	const GONE = 410;
	/**
	 * 411 Length Required
	 * The request did not specify the length of its content, which is required by the requested resource.
	 *
	 * @var integer
	 */
	const LENGTH_REQUIRED = 411;
	/**
	 * 412 Precondition Failed (RFC 7232)
	 * The server does not meet one of the preconditions that the requester put on the request.
	 *
	 * @var integer
	 */
	const PRECONDITION_FAILED = 412;
	/**
	 * 413 Payload Too Large (RFC 7231)
	 * The request is larger than the server is willing or able to process.
	 * Previously called "Request Entity Too Large".
	 *
	 * @var integer
	 */
	const REQUEST_ENTITY_TOO_LARGE = 413;
	/**
	 * 414 URI Too Long (RFC 7231)
	 * The URI provided was too long for the server to process.
	 * Often the result of too much data being encoded as a query-string of a GET request, in which case it should be converted to a POST request. Called "Request-URI Too Long" previously.
	 *
	 * @var integer
	 */
	const REQUEST_URI_TOO_LONG = 414;
	/**
	 * 415 Unsupported Media Type
	 * The request entity has a media type which the server or resource does not support.
	 * For example, the client uploads an image as image/svg+xml, but the server requires that images use a different format.
	 *
	 * @var integer
	 */
	const UNSUPPORTED_MEDIA_TYPE = 415;
	/**
	 * 416 Range Not Satisfiable (RFC 7233)
	 * The client has asked for a portion of the file (byte serving), but the server cannot supply that portion.
	 * For example, if the client asked for a part of the file that lies beyond the end of the file. Called "Requested Range Not Satisfiable" previously.
	 *
	 * @var integer
	 */
	const REQUESTED_RANGE_NOT_SATISFIABLE = 416;
	/**
	 * 417 Expectation Failed
	 * The server cannot meet the requirements of the Expect request-header field.
	 *
	 * @var integer
	 */
	const EXPECTATION_FAILED = 417;
	/**
	 * 418 I'm a teapot (RFC 2324)
	 * This code was defined in 1998 as one of the traditional IETF April Fools' jokes, in RFC 2324, Hyper Text Coffee Pot Control Protocol, and is not expected to be implemented by actual HTTP servers.
	 * The RFC specifies this code should be returned by teapots requested to brew coffee. This HTTP status is used as an Easter egg in some websites, including Google.com.
	 *
	 * @var integer
	 */
	const IM_A_TEAPOT = 418;
	/**
	 * 421 Misdirected Request (RFC 7540)
	 * The request was directed at a server that is not able to produce a response (for example because a connection reuse).
	 *
	 * @var integer
	 */
	const MISDIRECTED_REQUEST = 421;
	/**
	 * 426 Upgrade Required
	 * The client should switch to a different protocol such as TLS/1.0, given in the Upgrade header field.
	 *
	 * @var integer
	 */
	const UPGRADE_REQUIRED = 426;
	/**
	 * 428 Precondition Required (RFC 6585)
	 * The origin server requires the request to be conditional.
	 * Intended to prevent the 'lost update' problem, where a client GETs a resource's state, modifies it, and PUTs it back to the server, when meanwhile a third party has modified the state on the server, leading to a conflict."
	 *
	 * @var integer
	 */
	const PRECONDITION_REQUIRED = 428;
	/**
	 * 429 Too Many Requests (RFC 6585)
	 * The user has sent too many requests in a given amount of time.
	 * Intended for use with rate-limiting schemes.
	 *
	 * @var integer
	 */
	const TOO_MANY_REQUESTS = 429;
	/**
	 * 431 Request Header Fields Too Large (RFC 6585)
	 * The server is unwilling to process the request because either an individual header field, or all the header fields collectively, are too large.
	 *
	 * @var integer
	 */
	const REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
	/**
	 * 451 Unavailable For Legal Reasons (RFC 7725)
	 * A server operator has received a legal demand to deny access to a resource or to a set of resources that includes the requested resource. The code 451 was chosen as a reference to the novel Fahrenheit 451.
	 *
	 * @var integer
	 */
	const UNAVAILABLE_FOR_LEGAL_REASONS = 451;
	
	/*
	 * 5xx Server error
	 * 
	 * The server failed to fulfil an apparently valid request.
	 * 
	 * Response status codes beginning with the digit "5" indicate cases in which the server is aware that it has encountered an error or is otherwise incapable of performing the request. Except when responding to a HEAD request, the server should include an entity containing an explanation of the error situation, and indicate whether it is a temporary or permanent condition. Likewise, user agents should display any included entity to the user. These response codes are applicable to any request method.
	 */
	
	/**
	 * 500 Internal Server Error
	 * A generic error message, given when an unexpected condition was encountered and no more specific message is suitable.
	 *
	 * @var integer
	 */
	const INTERNAL_SERVER_ERROR = 500;
	/**
	 * 501 Not Implemented
	 * The server either does not recognize the request method, or it lacks the ability to fulfil the request.
	 * Usually this implies future availability (e.g., a new feature of a web-service API).
	 *
	 * @var integer
	 */
	const NOT_IMPLEMENTED = 501;
	/**
	 * 502 Bad Gateway
	 * The server was acting as a gateway or proxy and received an invalid response from the upstream server.
	 *
	 * @var integer
	 */
	const BAD_GATEWAY = 502;
	/**
	 * 503 Service Unavailable
	 * The server is currently unavailable (because it is overloaded or down for maintenance).
	 * Generally, this is a temporary state.
	 *
	 * @var integer
	 */
	const SERVICE_UNAVAILABLE = 503;
	/**
	 * 504 Gateway Timeout
	 * The server was acting as a gateway or proxy and did not receive a timely response from the upstream server.
	 *
	 * @var integer
	 */
	const GATEWAY_TIMEOUT = 504;
	/**
	 * 505 HTTP Version Not Supported
	 * The server does not support the HTTP protocol version used in the request.
	 *
	 * @var integer
	 */
	const HTTP_VERSION_NOT_SUPPORTED = 505;
	/**
	 * 506 Variant Also Negotiates (RFC 2295)
	 * Transparent content negotiation for the request results in a circular reference.
	 *
	 * @var integer
	 */
	const VARIANT_ALSO_NEGOTIATES = 506;
	/**
	 * 510 Not Extended (RFC 2774)
	 * Further extensions to the request are required for the server to fulfil it.
	 *
	 * @var integer
	 */
	const NOT_EXTENDED = 510;
	/**
	 * 511 Network Authentication Required (RFC 6585)
	 * The client needs to authenticate to gain network access.
	 * Intended for use by intercepting proxies used to control access to the network (e.g., "captive portals" used to require agreement to Terms of Service before granting full Internet access via a Wi-Fi hotspot).
	 *
	 * @var integer
	 */
	const NETWORK_AUTHENTICATION_REQUIRED = 511;
	
	/**
	 * Hidden constructor.
	 */
	private function __construct() {
	}
	
	/**
	 *
	 * @param integer $code        	
	 * @throws InvalidArgumentException
	 * @return string
	 */
	static function valueOf($code): string {
		$classes = [ 
				__CLASS__,
				WebDAV::class 
		];
		foreach ( $classes as $class ) {
			$r = new \ReflectionClass ( $class );
			$constants = $r->getConstants ();
			foreach ( $constants as $key => $value ) {
				if ($code === $value) {
					return $key;
				}
			}
		}
		
		throw new \InvalidArgumentException ( $code );
	}
	
}