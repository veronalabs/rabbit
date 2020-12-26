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

use Backyard\Templates\Template\Functions;

class TestFunctions extends \WP_UnitTestCase {
	private $functions;

	public function setUp(): void {
		$this->functions = new Functions();
	}

	public function testCanCreateInstance() {
		$this->assertInstanceOf( Functions::class, $this->functions );
	}

	public function testAddAndGetFunction() {
		$this->assertInstanceOf( Functions::class, $this->functions->add( 'upper', 'strtoupper' ) );
		$this->assertSame( 'strtoupper', $this->functions->get( 'upper' )->getCallback() );
	}

	public function testAddFunctionConflict() {
		// The template function name "upper" is already registered.
		$this->expectException( \LogicException::class );
		$this->functions->add( 'upper', 'strtoupper' );
		$this->functions->add( 'upper', 'strtoupper' );
	}

	public function testGetNonExistentFunction() {
		// The template function "foo" was not found.
		$this->expectException( \LogicException::class );
		$this->functions->get( 'foo' );
	}

	public function testRemoveFunction() {
		$this->functions->add( 'upper', 'strtoupper' );
		$this->assertTrue( $this->functions->exists( 'upper' ) );
		$this->functions->remove( 'upper' );
		$this->assertFalse( $this->functions->exists( 'upper' ) );
	}

	public function testRemoveNonExistentFunction() {
		// The template function "foo" was not found.
		$this->expectException( \LogicException::class );
		$this->functions->remove( 'foo' );
	}

	public function testFunctionExists() {
		$this->assertFalse( $this->functions->exists( 'upper' ) );
		$this->functions->add( 'upper', 'strtoupper' );
		$this->assertTrue( $this->functions->exists( 'upper' ) );
	}
}
