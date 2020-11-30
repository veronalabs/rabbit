<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Dom nodes class.
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
 * Dom nodes
 */
abstract class AbstractNode {

	/**
	 * Object child nodes
	 *
	 * @var array
	 */
	protected $childNodes = [];

	/**
	 * Indentation for formatting purposes
	 *
	 * @var string
	 */
	protected $indent = null;

	/**
	 * Child output
	 *
	 * @var string
	 */
	protected $output = null;

	/**
	 * Parent node
	 *
	 * @var AbstractNode
	 */
	protected $parent = null;

	/**
	 * Return the indent
	 *
	 * @return string
	 */
	public function getIndent() {
		return $this->indent;
	}

	/**
	 * Set the indent
	 *
	 * @param  string $indent
	 * @return mixed
	 */
	public function setIndent( $indent ) {
		$this->indent = $indent;
		return $this;
	}

	/**
	 * Return the parent node
	 *
	 * @return AbstractNode
	 */
	public function getParent() {
		return $this->parent;
	}

	/**
	 * Set the parent node
	 *
	 * @param  AbstractNode $parent
	 * @return AbstractNode
	 */
	public function setParent( AbstractNode $parent ) {
		$this->parent = $parent;
		return $this;
	}

	/**
	 * Add a child to the object
	 *
	 * @param  mixed $c
	 * @return mixed
	 */
	public function addChild( Child $c ) {
		$c->setParent( $this );
		$this->childNodes[] = $c;
		return $this;
	}

	/**
	 * Add children to the object
	 *
	 * @param  array|Child $children
	 * @throws Exception When wrong elements are added.
	 * @return mixed
	 */
	public function addChildren( $children ) {
		if ( is_array( $children ) ) {
			foreach ( $children as $child ) {
				$this->addChild( $child );
			}
		} elseif ( $children instanceof Child ) {
			$this->addChild( $children );
		} else {
			throw new Exception(
				'Error: The parameter passed must be an instance of Backyard\Dom\Child or an array of Backyard\Dom\Child instances.'
			);
		}

		return $this;
	}

	/**
	 * Get whether or not the child object has children
	 *
	 * @return boolean
	 */
	public function hasChildren() {
		return ( count( $this->childNodes ) > 0 );
	}

	/**
	 * Get whether or not the child object has children (alias)
	 *
	 * @return boolean
	 */
	public function hasChildNodes() {
		return ( count( $this->childNodes ) > 0 );
	}

	/**
	 * Get the child nodes of the object
	 *
	 * @param int $i
	 * @return Child
	 */
	public function getChild( $i ) {
		return ( isset( $this->childNodes[ (int) $i ] ) ) ? $this->childNodes[ (int) $i ] : null;
	}

	/**
	 * Get the child nodes of the object
	 *
	 * @return array
	 */
	public function getChildren() {
		return $this->childNodes;
	}

	/**
	 * Get the child nodes of the object (alias)
	 *
	 * @return array
	 */
	public function getChildNodes() {
		return $this->childNodes;
	}

	/**
	 * Remove all child nodes from the object
	 *
	 * @param  int $i
	 * @return void
	 */
	public function removeChild( $i ) {
		if ( isset( $this->childNodes[ $i ] ) ) {
			unset( $this->childNodes[ $i ] );
		}
	}

	/**
	 * Remove all child nodes from the object
	 *
	 * @return void
	 */
	public function removeChildren() {
		$this->childNodes = [];
	}

}
