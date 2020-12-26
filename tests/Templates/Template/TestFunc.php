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

use Backyard\Contracts\TemplatesEngineExtensionInterface;
use Backyard\Templates\Engine;
use Backyard\Templates\Template\Func;

class TestFunc extends \WP_UnitTestCase {
	private $function;

	public function setUp(): void {
		$this->function = new Func(
			'uppercase',
			function ( $string ) {
				return strtoupper( $string );
			}
		);
	}

	public function testCanCreateInstance() {
		$this->assertInstanceOf( Func::class, $this->function );
	}

	public function testSetAndGetName() {
		$this->assertInstanceOf( Func::class, $this->function->setName( 'test' ) );
		$this->assertSame( 'test', $this->function->getName() );
	}

	public function testSetInvalidName() {
		// Not a valid function name.
		$this->expectException( \LogicException::class );
		$this->function->setName( 'invalid-function-name' );
	}

	public function testSetAndGetCallback() {
		$this->assertInstanceOf( Func::class, $this->function->setCallback( 'strtolower' ) );
		$this->assertSame( 'strtolower', $this->function->getCallback() );
	}

	public function testSetInvalidCallback() {
		// Not a valid function callback.
		$this->expectException( \LogicException::class );
		$this->function->setCallback( null );
	}

	public function testFunctionCall() {
		$this->assertSame( 'JONATHAN', $this->function->call( null, array( 'Jonathan' ) ) );
	}

	public function testExtensionFunctionCall() {
		$extension = new class() implements TemplatesEngineExtensionInterface {
			public function register( Engine $engine ) {
			}
			public function foo(): string {
				return 'bar';
			}
		};
		$this->function->setCallback( array( $extension, 'foo' ) );
		$this->assertSame( 'bar', $this->function->call( null ) );
	}
}
