<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Dom document hanlder class.
 *
 * @package   backyard-framework
 * @author    Nick Sagona, III <dev@nolainteractive.com>
 * @copyright Copyright (c) 2009-2020 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://sematico.com
 */

namespace Backyard\Dom;

use Exception;

/**
 * Document handler
 */
class Document extends AbstractNode {

	/**
	 * Constant to use the XML doctype
	 *
	 * @var string
	 */
	const XML = 'XML';

	/**
	 * Constant to use the HTML doctype
	 *
	 * @var string
	 */
	const HTML = 'HTML';

	/**
	 * Constant to use the XML doctype, RSS content-type
	 *
	 * @var string
	 */
	const RSS = 'RSS';

	/**
	 * Constant to use the XML doctype, Atom content-type
	 *
	 * @var string
	 */
	const ATOM = 'ATOM';

	/**
	 * Document type
	 *
	 * @var string
	 */
	protected $doctype = 'XML';

	/**
	 * Document content type
	 *
	 * @var string
	 */
	protected $contentType = 'application/xml';

	/**
	 * Document charset
	 *
	 * @var string
	 */
	protected $charset = 'utf-8';

	/**
	 * Document doctypes
	 *
	 * @var array
	 */
	protected static $doctypes = [
		'XML'  => "<?xml version=\"1.0\" encoding=\"[{charset}]\"?>\n",
		'HTML' => "<!DOCTYPE html>\n",
	];

	/**
	 * Constructor
	 *
	 * Instantiate the document object
	 *
	 * @param  string $doctype
	 * @param  Child  $childNode
	 * @param  string $indent
	 */
	public function __construct( $doctype = 'XML', Child $childNode = null, $indent = null ) {
		$this->setDoctype( $doctype );

		if ( null !== $childNode ) {
			$this->addChild( $childNode );
		}
		if ( null !== $indent ) {
			$this->setIndent( $indent );
		}
	}

	/**
	 * Return the document type.
	 *
	 * @return string
	 */
	public function getDoctype() {
		return str_replace( '[{charset}]', $this->charset, self::$doctypes[ $this->doctype ] );
	}

	/**
	 * Return the document charset
	 *
	 * @return string
	 */
	public function getCharset() {
		return $this->charset;
	}

	/**
	 * Return the document charset
	 *
	 * @return string
	 */
	public function getContentType() {
		return $this->contentType;
	}

	/**
	 * Set the document type
	 *
	 * @param  string $doctype
	 * @throws Exception When incorrect document type.
	 * @return Document
	 */
	public function setDoctype( $doctype ) {
		$doctype = strtoupper( $doctype );

		if ( ( $doctype !== self::XML ) && ( $doctype !== self::HTML ) && ( $doctype !== self::RSS ) && ( $doctype !== self::ATOM ) ) {
			throw new Exception( 'Error: Incorrect document type' );
		}

		switch ( $doctype ) {
			case 'XML':
				$this->doctype     = self::XML;
				$this->contentType = 'application/xml';
				break;
			case 'HTML':
				$this->doctype     = self::HTML;
				$this->contentType = 'text/html';
				break;
			case 'RSS':
				$this->doctype     = self::XML;
				$this->contentType = 'application/rss+xml';
				break;
			case 'ATOM':
				$this->doctype     = self::XML;
				$this->contentType = 'application/atom+xml';
				break;
		}

		return $this;
	}

	/**
	 * Set the document charset
	 *
	 * @param  string $char
	 * @return Document
	 */
	public function setCharset( $char ) {
		$this->charset = $char;
		return $this;
	}

	/**
	 * Set the document charset
	 *
	 * @param  string $content
	 * @return Document
	 */
	public function setContentType( $content ) {
		$this->contentType = $content;
		return $this;
	}

	/**
	 * Render the document and its child elements
	 *
	 * @return string
	 */
	public function render() {
		$this->output = null;

		if ( null !== $this->doctype ) {
			$this->output .= str_replace( '[{charset}]', $this->charset, self::$doctypes[ $this->doctype ] );
		}

		foreach ( $this->childNodes as $child ) {
			$this->output .= $child->render( 0, $this->indent );
		}

		return $this->output;
	}

	/**
	 * Render Dom object to string
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->render();
	}

}
