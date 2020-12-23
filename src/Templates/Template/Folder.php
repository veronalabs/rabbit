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
	 * The folder priority status.
	 *
	 * @var int
	 */
	protected $priority;

	/**
	 * Create a new Folder instance.
	 *
	 * @param string  $name
	 * @param string  $path
	 * @param boolean $priority
	 */
	public function __construct( $name, $path, $priority ) {
		$this->setName( $name );
		$this->setPath( $path );
		$this->setPriority( $priority );
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
	 * @throws LogicException When path does not exist.
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
	 * Set the folder priority status.
	 *
	 * @param  boolean $priority
	 * @return Folder
	 */
	public function setPriority( $priority ) {
		$this->priority = $priority;

		return $this;
	}

	/**
	 * Get the folder priority status.
	 *
	 * @return int
	 */
	public function getPriority() {
		return $this->priority;
	}
}
