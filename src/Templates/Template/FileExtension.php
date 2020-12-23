<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Determine file extension that should be used for template files.
 *
 * @package   backyard-framework
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://sematico.com
 */

namespace Backyard\Templates\Template;

/**
 * Template file extension handler.
 */
class FileExtension {

	/**
	 * Template file extension.
	 *
	 * @var string
	 */
	protected $fileExtension;

	/**
	 * Create new FileExtension instance.
	 *
	 * @param null|string $fileExtension
	 */
	public function __construct( $fileExtension = 'php' ) {
		$this->set( $fileExtension );
	}

	/**
	 * Set the template file extension.
	 *
	 * @param  null|string $fileExtension
	 * @return FileExtension
	 */
	public function set( $fileExtension ) {
		$this->fileExtension = $fileExtension;

		return $this;
	}

	/**
	 * Get the template file extension.
	 *
	 * @return string
	 */
	public function get() {
		return $this->fileExtension;
	}
}
