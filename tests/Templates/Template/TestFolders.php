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
use Backyard\Templates\Template\Folders;
use org\bovigo\vfs\vfsStream;

class TestFolders extends \WP_UnitTestCase {

	private $folders;

	public function setUp(): void {
		vfsStream::setup( 'templates' );

		$this->folders = new Folders();
	}

	public function testCanCreateInstance() {
		$this->assertInstanceOf( Folders::class, $this->folders );
	}

	public function testAddFolder() {
		$this->assertInstanceOf( Folders::class, $this->folders->add( 'name', vfsStream::url( 'templates' ) ) );
		$this->assertSame( 'vfs://templates', $this->folders->get( 'name' )->getPath() );
	}

	public function testAddFolderWithNamespaceConflict() {
		// The template folder "name" is already being used.
		$this->expectException( \LogicException::class );
		$this->folders->add( 'name', vfsStream::url( 'templates' ) );
		$this->folders->add( 'name', vfsStream::url( 'templates' ) );
	}

	public function testAddFolderWithInvalidDirectory() {
		// The specified directory path "vfs://does/not/exist" does not exist.
		$this->expectException( \LogicException::class );
		$this->folders->add( 'name', vfsStream::url( 'does/not/exist' ) );
	}

	public function testRemoveFolder() {
		$this->folders->add( 'folder', vfsStream::url( 'templates' ) );
		$this->assertTrue( $this->folders->exists( 'folder' ) );
		$this->assertInstanceOf( Folders::class, $this->folders->remove( 'folder' ) );
		$this->assertFalse( $this->folders->exists( 'folder' ) );
	}

	public function testRemoveFolderWithInvalidDirectory() {
		// The template folder "name" was not found.
		$this->expectException( \LogicException::class );
		$this->folders->remove( 'name' );
	}

	public function testGetFolder() {
		$this->folders->add( 'name', vfsStream::url( 'templates' ) );
		$this->assertInstanceOf( Folder::class, $this->folders->get( 'name' ) );
		$this->assertSame( vfsStream::url( 'templates' ), $this->folders->get( 'name' )->getPath() );
	}

	public function testGetNonExistentFolder() {
		// The template folder "name" was not found.
		$this->expectException( \LogicException::class );
		$this->assertInstanceOf( Folder::class, $this->folders->get( 'name' ) );
	}

	public function testFolderExists() {
		$this->assertFalse( $this->folders->exists( 'name' ) );
		$this->folders->add( 'name', vfsStream::url( 'templates' ) );
		$this->assertTrue( $this->folders->exists( 'name' ) );
	}

}
