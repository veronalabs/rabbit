<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Backyard plugin templates testing
 *
 * @package   backyard-foundation
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://sematico.com
 */

namespace Backyard\Tests\Templates\Template;

use Backyard\Templates\Template\FileExtension;

class TestFileExtension extends \WP_UnitTestCase {
	private $fileExtension;

	public function setUp(): void {
		$this->fileExtension = new FileExtension();
	}

	public function testCanCreateInstance() {
		$this->assertInstanceOf( FileExtension::class, $this->fileExtension );
	}

	public function testSetFileExtension() {
		$this->assertInstanceOf( FileExtension::class, $this->fileExtension->set( 'tpl' ) );
		$this->assertSame( 'tpl', $this->fileExtension->get() );
	}

	public function testSetNullFileExtension() {
		$this->assertInstanceOf( FileExtension::class, $this->fileExtension->set( null ) );
		$this->assertNull( $this->fileExtension->get() );
	}

	public function testGetFileExtension() {
		$this->assertSame( 'php', $this->fileExtension->get() );
	}
}
