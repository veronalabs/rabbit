<?php // phpcs:ignore WordPress.Files.FileName
/**
 * Backyard forms nonce field.
 *
 * @package   backyard-framework
 * @author    Sematico LTD <hello@sematico.com>
 * @copyright 2020 Sematico LTD
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GPL-3.0-or-later
 * @link      https://sematico.com
 */

namespace Backyard\Forms\Elements;

use Laminas\Form\Element\Hidden;
use Laminas\InputFilter\InputProviderInterface;
use Laminas\Validator\ValidatorInterface;
use Laminas\Filter\StringTrim;

/**
 * Nonce field.
 */
class Nonce extends Hidden implements InputProviderInterface {

	/**
	 * @var ValidatorInterface
	 */
	protected $validator;

	/**
	 * Get a validator if none has been set.
	 *
	 * @return ValidatorInterface
	 */
	public function getValidator() {
		return $this->validator;
	}

	/**
	 * Sets the validator to use for this element
	 *
	 * @param  ValidatorInterface $validator
	 * @return self
	 */
	public function setValidator( ValidatorInterface $validator ) {
		$this->validator = $validator;
		return $this;
	}

	/**
	 * Provide default input rules for this element
	 *
	 * Attaches a phone number validator.
	 *
	 * @return array
	 */
	public function getInputSpecification() {
		return [
			'name'       => $this->getName(),
			'required'   => true,
			'filters'    => [
				[ 'name' => StringTrim::class ],
			],
			'validators' => [
				$this->getValidator(),
			],
		];
	}

}
