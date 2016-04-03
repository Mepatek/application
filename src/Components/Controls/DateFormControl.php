<?php

namespace Mepatek\Components\Controls;

use Grido\Components\Filters\Date;
use Nette,
	Nette\Utils\DateTime,
	Nette\Forms\Controls\TextInput;

class DateFormControl extends TextInput
{

	/**
	 * @param  string  label
	 * @param  int  maximum number of characters the user may enter
	 */
	public function __construct($label = null)
	{
		parent::__construct($label, 10);
		$this->control->type = 'date';
	}

	/**
	 * Generates control's HTML element.
	 * @return Nette\Utils\Html
	 */
	public function getControl()
	{
		$input = parent::getControl();
		$input->addAttributes(['class' => 'date-picker']);
		return $input;
	}


	/**
	 * Returns control's value.
	 * @return string
	 */
	public function getValue()
	{
		$value = parent::getValue();
		// convert to Nette\Utils\DateTime
		if ($value && is_string($value)) {
			$format = "d.m.Y";
			$value = DateTime::createFromFormat($format, $value);
		}
		return $value ? $value : NULL;
	}


	/**
	 * Sets control's value.
	 * @param  string
	 * @return self
	 */
	public function setValue($value = null)
	{

		if ($value instanceof \DateTime) {
			$value = $value->format("d.m.Y");
		}
		parent::setValue($value);
		return $this;

		if ($value instanceof \DateTime) {
			$this->value = $value;
			$this->submitedValue = null;
		} elseif ($value instanceof \DateInterval) {
			$this->value = \DateTime::createFromFormat(self::$formats[self::TYPE_TIME], $value->format("%H:%I:%S"));
			$this->submitedValue = null;
		} elseif (is_string($value)) {
			if ($value === '') {
				$this->value = null;
				$this->submitedValue = null;
			} else {
				$this->value = $this->parseValue($value);
				if ($this->value !== false) {
					$this->submitedValue = null;
				} else {
					$this->value = null;
					$this->submitedValue = $value;
				}
			}
		} else {
			$this->submitedValue = $value;
			throw new \InvalidArgumentException("Invalid type for \$value.");
		}
		return $this;
	}
}