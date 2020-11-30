<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Elements interator class.
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

/**
 * Dom Elements iterator
 */
class DomIterator implements \RecursiveIterator {

	/**
	 * Current position
	 *
	 * @var int
	 */
	protected $position;

	/**
	 * Node List
	 *
	 * @var \DOMNodeList
	 */
	protected $nodeList;

	/**
	 * Constructor
	 *
	 * Instantiate the DOM iterator object
	 *
	 * @param \DOMNode $domNode
	 */
	public function __construct( \DOMNode $domNode ) {
		$this->position = 0;
		$this->nodeList = $domNode->childNodes;
	}

	/**
	 * Get current method
	 *
	 * @return \DOMNode
	 */
	public function current() {
		return $this->nodeList->item( $this->position );
	}

	/**
	 * Get children method
	 *
	 * @return DomIterator
	 */
	public function getChildren() {
		return new self( $this->current() );
	}

	/**
	 * Has children method
	 *
	 * @return bool
	 */
	public function hasChildren() {
		return $this->current()->hasChildNodes();
	}

	/**
	 * Key method
	 *
	 * @return int
	 */
	public function key() {
		return $this->position;
	}

	/**
	 * Next method
	 */
	public function next() {
		$this->position++;
	}

	/**
	 * Rewind method
	 */
	public function rewind() {
		$this->position = 0;
	}

	/**
	 * Is valid method
	 *
	 * @return bool
	 */
	public function valid() {
		return $this->position < $this->nodeList->length;
	}

}
