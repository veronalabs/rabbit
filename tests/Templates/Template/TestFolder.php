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

use Backyard\Templates\Template\Folder;
use org\bovigo\vfs\vfsStream;

class TestFolder extends \WP_UnitTestCase {
	private $folder;

	public function setUp(): void {
		vfsStream::setup( 'templates' );

		$this->folder = new Folder( 'folder', vfsStream::url( 'templates' ), 1 );
	}

	public function testCanCreateInstance() {
		$this->assertInstanceOf( Folder::class, $this->folder );
	}

	public function testSetAndGetName() {
		$this->folder->setName( 'name' );
		$this->assertSame( 'name', $this->folder->getName() );
	}

	public function testSetAndGetPath() {
		vfsStream::create(
			array(
				'folder' => array(),
			)
		);

		$this->folder->setPath( vfsStream::url( 'templates/folder' ) );
		$this->assertSame( vfsStream::url( 'templates/folder' ), $this->folder->getPath() );
	}

	public function testSetInvalidPath() {
		// The specified directory path "vfs://does/not/exist" does not exist.
		$this->expectException( \LogicException::class );
		$this->folder->setPath( vfsStream::url( 'does/not/exist' ) );
	}

	public function testSetAndGetPriority() {

		$this->assertTrue( $this->folder->getPriority() === 1 );

		$this->folder->setPriority( 2 );

		$this->assertTrue( $this->folder->getPriority() === 2 );

	}
}
