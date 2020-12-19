<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Backyard forms test.
 *
 * @package   backyard-foundation
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://sematico.com
 */

namespace Backyard\Tests;

use Backyard\Forms\Elements\Nonce;
use Backyard\Forms\Filters\SanitizeTextField;
use Backyard\Forms\Form;

class TestForms extends \WP_UnitTestCase {

	/**
	 * Form instance.
	 *
	 * @var Form
	 */
	protected $form;

	public function setUp() {
		$this->form = new ExampleForm( 'example_form' );
	}

	public function testFormSetup() {
		$this->assertInstanceOf( Form::class, $this->form );
	}

	public function testFormNonceNameSetup() {

		$this->assertNotEmpty( $this->form->getOption( 'nonce_name' ) );

		$formCustomNonce = new ExampleForm( 'example_form', [ 'nonce_name' => 'my_nonce' ] );

		$this->assertSame( $formCustomNonce->getOption( 'nonce_name' ), 'my_nonce' );

	}

	public function testFormHasNonceField() {
		$this->assertInstanceOf( Nonce::class, $this->form->get( 'example_form' ) );
	}

	public function testFiltersAreAutomaticallyApplied() {

		$filters = $this->form->getInputFilter();

		$exampleField = $filters->get( 'text' );
		$filtersList  = $exampleField->getFilterChain()->getFilters()->toArray();

		$this->assertInstanceOf( SanitizeTextField::class, $filtersList[0][0] );

	}

}

class ExampleForm extends Form {
	public function setupFields() {
		$this->add(
			[
				'type'    => 'text',
				'name'    => 'text',
				'options' => [
					'label' => 'Text field',
					'hint'  => 'Here goes the description',
				],
			]
		);
		$this->add(
			[
				'type'    => 'textarea',
				'name'    => 'mytextareafield',
				'options' => [
					'label' => 'Textarea field',
					'hint'  => 'Here goes the description',
				],
			]
		);
	}
}


