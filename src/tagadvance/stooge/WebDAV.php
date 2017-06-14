<?php

namespace tagadvance\stooge;

/**
 * Documentation is copied from Wikipedia and is licensed under the <a href="https://en.wikipedia.org/wiki/Wikipedia:Text_of_Creative_Commons_Attribution-ShareAlike_3.0_Unported_License">Creative Commons Attribution-ShareAlike License</a>.
 * 
 * @see http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
 */
final class WebDAV {
	
	/**
	 * 102 Processing (WebDAV; RFC 2518)
	 * A WebDAV request may contain many sub-requests involving file operations, requiring a long time to complete the request.
	 * This code indicates that the server has received and is processing the request, but no response is available yet. This prevents the client from timing out and assuming the request was lost.
	 */
	const PROCESSING = 102;
	/**
	 * 207 Multi-Status (WebDAV; RFC 4918)
	 * The message body that follows is an XML message and can contain a number of separate response codes, depending on how many sub-requests were made.
	 *
	 * @var integer
	 */
	const MULTI_STATUS = 207;
	/**
	 * 208 Already Reported (WebDAV; RFC 5842)
	 * The members of a DAV binding have already been enumerated in a preceding part of the (multistatus) response, and are not being included again.
	 *
	 * @var integer
	 */
	const ALREADY_REPORTED = 208;
	/**
	 * 422 Unprocessable Entity (WebDAV; RFC 4918)
	 * The request was well-formed but was unable to be followed due to semantic errors.
	 *
	 * @var integer
	 */
	const UNPROCESSABLE_ENTITY = 422;
	/**
	 * 423 Locked (WebDAV; RFC 4918)
	 * The resource that is being accessed is locked.
	 *
	 * @var integer
	 */
	const LOCKED = 423;
	/**
	 * 424 Failed Dependency (WebDAV; RFC 4918)
	 * The request failed due to failure of a previous request (e.g., a PROPPATCH).
	 *
	 * @var integer
	 */
	const FAILED_DEPENDENCY = 424;
	/**
	 * 507 Insufficient Storage (WebDAV; RFC 4918)
	 * The server is unable to store the representation needed to complete the request.
	 *
	 * @var integer
	 */
	const INSUFFICIENT_STORAGE = 507;
	/**
	 * 508 Loop Detected (WebDAV; RFC 5842)
	 * The server detected an infinite loop while processing the request (sent in lieu of 208 Already Reported).
	 *
	 * @var integer
	 */
	const LOOP_DETECTED = 508;
	
	/**
	 * Hidden constructor.
	 */
	private function __construct() {
	}
	
}