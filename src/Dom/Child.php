<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Dom child class.
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
 * Dom element
 */
class Child extends AbstractNode {

	/**
	 * Child element node name
	 *
	 * @var string
	 */
	protected $nodeName = null;

	/**
	 * Child element node value
	 *
	 * @var string
	 */
	protected $nodeValue = null;

	/**
	 * Child element node value CDATA flag
	 *
	 * @var boolean
	 */
	protected $cData = false;

	/**
	 * Flag to render children before node value or not
	 *
	 * @var boolean
	 */
	protected $childrenFirst = false;

	/**
	 * Child element attributes
	 *
	 * @var array
	 */
	protected $attributes = [];

	/**
	 * Flag to preserve whitespace
	 *
	 * @var boolean
	 */
	protected $preserveWhiteSpace = true;

	/**
	 * Constructor
	 *
	 * Instantiate the DOM element object
	 *
	 * @param  string $name
	 * @param  string $value
	 * @param  array  $options
	 */
	public function __construct( $name, $value = null, array $options = [] ) {
		$this->nodeName  = $name;
		$this->nodeValue = $value;

		if ( isset( $options['cData'] ) ) {
			$this->cData = (bool) $options['cData'];
		}
		if ( isset( $options['childrenFirst'] ) ) {
			$this->childrenFirst = (bool) $options['childrenFirst'];
		}
		if ( isset( $options['indent'] ) ) {
			$this->indent = (string) $options['indent'];
		}
		if ( isset( $options['attributes'] ) ) {
			$this->setAttributes( $options['attributes'] );
		}
		if ( isset( $options['whitespace'] ) ) {
			$this->preserveWhiteSpace( $options['whitespace'] );
		}
	}

	/**
	 * Static factory method to create a child object
	 *
	 * @param  string $name
	 * @param  string $value
	 * @param  array  $options
	 * @return Child
	 */
	public static function create( $name, $value = null, array $options = [] ) {
		return new self( $name, $value, $options );
	}

	/**
	 * Static method to parse an XML/HTML string
	 *
	 * @param  string $string
	 * @return Child|array
	 */
	public static function parseString( $string ) {
		$doc = new \DOMDocument();
		$doc->loadHTML( $string );

		$dit = new \RecursiveIteratorIterator(
			new DomIterator( $doc ),
			\RecursiveIteratorIterator::SELF_FIRST
		);

		$parent     = null;
		$child      = null;
		$lastDepth  = 0;
		$endElement = null;
		$partial    = ( ( stripos( $string, '<html' ) === false ) || ( stripos( $string, '<body' ) === false ) );

		foreach ( $dit as $node ) {
			if ( ( $node->nodeType === XML_ELEMENT_NODE ) || ( $node->nodeType === XML_TEXT_NODE ) ) {
				$attribs = [];
				if ( null !== $node->attributes ) {
					for ( $i = 0; $i < $node->attributes->length; $i++ ) {
						$name             = $node->attributes->item( $i )->name;
						$attribs[ $name ] = $node->getAttribute( $name );
					}
				}
				if ( null === $parent ) {
					$parent = new Child( $node->nodeName );
				} else {
					if ( ( $node->nodeType === XML_TEXT_NODE ) && ( null !== $child ) ) {
						$nodeValue = trim( $node->nodeValue );
						if ( ! empty( $nodeValue ) ) {
							if ( ( $endElement ) && ( null !== $child->getParent() ) && ( null !== $node->previousSibling ) ) {
								$prev = $node->previousSibling->nodeName;
								$par  = $child->getParent();
								while ( ( null !== $par ) && ( $prev != $par->getNodeName() ) ) {
									$par = $par->getParent();
								}
								if ( null === $par ) {
									$par = $child->getParent();
								} else {
									$par = $par->getParent();
								}
								$par->addChild( new Child( '#text', $nodeValue ) );
							} else {
								$child->setNodeValue( $nodeValue );
								$endElement = true;
							}
						}
					} else {
						// down
						if ( $dit->getDepth() > $lastDepth ) {
							if ( null !== $child ) {
								$parent = $child;
							}
							$child = new Child( $node->nodeName );
							$parent->addChild( $child );
							$endElement = false;
							// up
						} elseif ( $dit->getDepth() < $lastDepth ) {
							while ( $parent->getNodeName() !== $node->parentNode->nodeName ) {
								$parent = $parent->getParent();
							}
							$child = new Child( $node->nodeName );
							$parent->addChild( $child );
							$endElement = false;
							// next (sibling)
						} elseif ( $dit->getDepth() == $lastDepth ) {
							$child = new Child( $node->nodeName );
							$parent->addChild( $child );
							$endElement = false;
						}
						if ( ! empty( $attribs ) ) {
							$child->setAttributes( $attribs );
						}
						$lastDepth = $dit->getDepth();
					}
				}
			}
		}
		while ( null !== $parent->getParent() ) {
			$parent = $parent->getParent();
		}

		if ( $partial ) {
			$parent = $parent->getChild( 0 );
			if ( strtolower( $parent->getNodeName() ) == 'body' ) {
				$parent = $parent->getChildNodes();
			}
		}

		return $parent;
	}

	/**
	 * Static method to parse an XML/HTML string from a file
	 *
	 * @param  string $file
	 * @throws Exception When file does not exist.
	 * @return Child
	 */
	public static function parseFile( $file ) {
		if ( ! file_exists( $file ) ) {
			throw new Exception( 'Error: That file does not exist.' );
		}
		return self::parseString( file_get_contents( $file ) );
	}

	/**
	 * Return the child node name
	 *
	 * @return string
	 */
	public function getNodeName() {
		return $this->nodeName;
	}

	/**
	 * Return the child node value
	 *
	 * @return string
	 */
	public function getNodeValue() {
		return $this->nodeValue;
	}

	/**
	 * Return the child node content, including tags, etc
	 *
	 * @param  boolean $ignoreWhiteSpace
	 * @return string
	 */
	public function getNodeContent( $ignoreWhiteSpace = false ) {
		$content = $this->render( 0, null, true );
		if ( $ignoreWhiteSpace ) {
			$content = preg_replace( '/\s+/', ' ', str_replace( [ "\n", "\r", "\t" ], [ '', '', '' ], trim( $content ) ) );
			$content = preg_replace( '/\s*\.\s*/', '. ', $content );
			$content = preg_replace( '/\s*\?\s*/', '? ', $content );
			$content = preg_replace( '/\s*\!\s*/', '! ', $content );
			$content = preg_replace( '/\s*,\s*/', ', ', $content );
			$content = preg_replace( '/\s*\:\s*/', ': ', $content );
			$content = preg_replace( '/\s*\;\s*/', '; ', $content );
		}
		return $content;
	}

	/**
	 * Return the child node content, including tags, etc
	 *
	 * @param  boolean $ignoreWhiteSpace
	 * @return string
	 */
	public function getTextContent( $ignoreWhiteSpace = false ) {
		$content = wp_strip_all_tags( $this->render( 0, null, true ) );

		if ( $ignoreWhiteSpace ) {
			$content = preg_replace( '/\s+/', ' ', str_replace( [ "\n", "\r", "\t" ], [ '', '', '' ], trim( $content ) ) );
			$content = preg_replace( '/\s*\.\s*/', '. ', $content );
			$content = preg_replace( '/\s*\?\s*/', '? ', $content );
			$content = preg_replace( '/\s*\!\s*/', '! ', $content );
			$content = preg_replace( '/\s*,\s*/', ', ', $content );
			$content = preg_replace( '/\s*\:\s*/', ': ', $content );
			$content = preg_replace( '/\s*\;\s*/', '; ', $content );
		}
		return $content;
	}

	/**
	 * Set the child node name
	 *
	 * @param  string $name
	 * @return Child
	 */
	public function setNodeName( $name ) {
		$this->nodeName = $name;
		return $this;
	}

	/**
	 * Set the child node value
	 *
	 * @param  string $value
	 * @return Child
	 */
	public function setNodeValue( $value ) {
		$this->nodeValue = $value;
		return $this;
	}

	/**
	 * Add to the child node value
	 *
	 * @param  string $value
	 * @return Child
	 */
	public function addNodeValue( $value ) {
		$this->nodeValue .= $value;
		return $this;
	}

	/**
	 * Set the child node value as CDATA
	 *
	 * @param  boolean $cData
	 * @return Child
	 */
	public function setAsCData( $cData = true ) {
		$this->cData = (bool) $cData;
		return $this;
	}

	/**
	 * Determine if the child node value is CDATA
	 *
	 * @return boolean
	 */
	public function isCData() {
		return $this->cData;
	}

	/**
	 * Set an attribute for the child element object
	 *
	 * @param  string $a
	 * @param  string $v
	 * @return Child
	 */
	public function setAttribute( $a, $v ) {
		$this->attributes[ $a ] = $v;
		return $this;
	}

	/**
	 * Set an attribute or attributes for the child element object
	 *
	 * @param  array $a
	 * @return Child
	 */
	public function setAttributes( array $a ) {
		foreach ( $a as $name => $value ) {
			$this->attributes[ $name ] = $value;
		}
		return $this;
	}

	/**
	 * Determine if the child object has an attribute
	 *
	 * @param  string $name
	 * @return boolean
	 */
	public function hasAttribute( $name ) {
		return ( isset( $this->attributes[ $name ] ) );
	}

	/**
	 * Get the attribute of the child object
	 *
	 * @param  string $name
	 * @return string
	 */
	public function getAttribute( $name ) {
		return ( isset( $this->attributes[ $name ] ) ) ? $this->attributes[ $name ] : null;
	}

	/**
	 * Get the attributes of the child object
	 *
	 * @return array
	 */
	public function getAttributes() {
		return $this->attributes;
	}

	/**
	 * Remove an attribute from the child element object
	 *
	 * @param  string $a
	 * @return Child
	 */
	public function removeAttribute( $a ) {
		if ( isset( $this->attributes[ $a ] ) ) {
			unset( $this->attributes[ $a ] );
		}
		return $this;
	}

	/**
	 * Determine if child nodes render first, before the node value
	 *
	 * @return boolean
	 */
	public function isChildrenFirst() {
		return $this->childrenFirst;
	}

	/**
	 * Set whether child nodes render first, before the node value
	 *
	 * @param  bool $first
	 * @return Child
	 */
	public function setChildrenFirst( $first = true ) {
		$this->childrenFirst = (bool) $first;
		return $this;
	}

	/**
	 * Set whether to preserve whitespace
	 *
	 * @param  bool $preserve
	 * @return Child
	 */
	public function preserveWhiteSpace( $preserve = true ) {
		$this->preserveWhiteSpace = (bool) $preserve;
		return $this;
	}

	/**
	 * Render the child and its child nodes.
	 *
	 * @param  int     $depth
	 * @param  string  $indent
	 * @param  boolean $inner
	 * @return mixed
	 */
	public function render( $depth = 0, $indent = null, $inner = false ) {
		// Initialize child object properties and variables.
		$this->output = '';
		$this->indent = ( null === $this->indent ) ? str_repeat( '    ', $depth ) : $this->indent;
		$attribs      = '';
		$attribAry    = [];

		if ( $this->cData ) {
			$this->nodeValue = '<![CDATA[' . $this->nodeValue . ']]>';
		}

		// Format child attributes, if applicable.
		if ( count( $this->attributes ) > 0 ) {
			foreach ( $this->attributes as $key => $value ) {
				$attribAry[] = $key . '="' . $value . '"';
			}
			$attribs = ' ' . implode( ' ', $attribAry );
		}

		// Initialize the node.
		if ( $this->nodeName === '#text' ) {
			$this->output .= ( ( ! $this->preserveWhiteSpace ) ?
					'' : "{$indent}{$this->indent}" ) . $this->nodeValue . ( ( ! $this->preserveWhiteSpace ) ? '' : "\n" );
		} else {
			if ( ! $inner ) {
				$this->output .= ( ( ! $this->preserveWhiteSpace ) ?
						'' : "{$indent}{$this->indent}" ) . "<{$this->nodeName}{$attribs}";
			}

			if ( ( null === $indent ) && ( null !== $this->indent ) ) {
				$indent     = $this->indent;
				$origIndent = $this->indent;
			} else {
				$origIndent = $indent . $this->indent;
			}

			// If current child element has child nodes, format and render.
			if ( count( $this->childNodes ) > 0 ) {
				if ( ! $inner ) {
					$this->output .= '>';
					if ( $this->preserveWhiteSpace ) {
						$this->output .= "\n";
					}
				}
				$newDepth = $depth + 1;

				// Render node value before the child nodes.
				if ( ! $this->childrenFirst ) {
					if ( null !== $this->nodeValue ) {
						$this->output .= ( ( ! $this->preserveWhiteSpace ) ?
								'' : str_repeat( '    ', $newDepth ) . "{$indent}" ) . "{$this->nodeValue}\n";
					}
					foreach ( $this->childNodes as $child ) {
						$this->output .= $child->render( $newDepth, $indent );
					}
					if ( ! $inner ) {
						if ( ! $this->preserveWhiteSpace ) {
							$this->output .= "</{$this->nodeName}>";
						} else {
							$this->output .= "{$origIndent}</{$this->nodeName}>\n";
						}
					}
					// Else, render child nodes first, then node value.
				} else {
					foreach ( $this->childNodes as $child ) {
						$this->output .= $child->render( $newDepth, $indent );
					}
					if ( ! $inner ) {
						if ( null !== $this->nodeValue ) {
							$this->output .= ( ( ! $this->preserveWhiteSpace ) ?
									'' : str_repeat( '    ', $newDepth ) . "{$indent}" ) .
								"{$this->nodeValue}" . ( ( ! $this->preserveWhiteSpace ) ?
									'' : "\n{$origIndent}" ) . "</{$this->nodeName}>" . ( ( $this->preserveWhiteSpace ) ? '' : "\n" );
						} else {
							$this->output .= ( ( ! $this->preserveWhiteSpace ) ?
									'' : "{$origIndent}" ) . "</{$this->nodeName}>" . ( ( ! $this->preserveWhiteSpace ) ? '' : "\n" );
						}
					}
				}
				// Else, render the child node.
			} else {
				if ( ! $inner ) {
					if ( ( null !== $this->nodeValue ) || ( $this->nodeName == 'textarea' ) ) {
						$this->output .= '>';
						$this->output .= "{$this->nodeValue}</{$this->nodeName}>" . ( ( ! $this->preserveWhiteSpace ) ? '' : "\n" );
					} else {
						$this->output .= ' />';
						if ( $this->preserveWhiteSpace ) {
							$this->output .= "\n";
						}
					}
				} elseif ( ! empty( $this->nodeValue ) ) {
					$this->output .= $this->nodeValue;
				}
			}
		}

		return $this->output;
	}

	/**
	 * Render Dom child object to string
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->render();
	}

}
