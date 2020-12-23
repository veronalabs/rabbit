<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Templates engine single folder representation.
 *
 * @package   backyard-framework
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://sematico.com
 */

namespace Backyard\Templates\Template;

use LogicException;

/**
 * Templates engine single folder representation.
 */
class Folder {

	/**
	 * The folder name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The folder path.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * The folder fallback status.
	 *
	 * @var boolean
	 */
	protected $fallback;

	/**
	 * Create a new Folder instance.
	 *
	 * @param string  $name
	 * @param string  $path
	 * @param boolean $fallback
	 */
	public function __construct( $name, $path, $fallback = false ) {
		$this->setName( $name );
		$this->setPath( $path );
		$this->setFallback( $fallback );
	}

	/**
	 * Set the folder name.
	 *
	 * @param  string $name
	 * @return Folder
	 */
	public function setName( $name ) {
		$this->name = $name;

		return $this;
	}

	/**
	 * Get the folder name.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Set the folder path.
	 *
	 * @param  string $path
	 * @return Folder
	 */
	public function setPath( $path ) {
		if ( ! is_dir( $path ) ) {
			throw new LogicException( 'The specified directory path "' . $path . '" does not exist.' );
		}

		$this->path = $path;

		return $this;
	}

	/**
	 * Get the folder path.
	 *
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * Set the folder fallback status.
	 *
	 * @param  boolean $fallback
	 * @return Folder
	 */
	public function setFallback( $fallback ) {
		$this->fallback = $fallback;

		return $this;
	}

	/**
	 * Get the folder fallback status.
	 *
	 * @return boolean
	 */
	public function getFallback() {
		return $this->fallback;
	}
}
