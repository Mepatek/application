<?php

namespace Mepatek\Components\Controls;

use Grido\Components\Filters\Date;
use Nette,
	Nette\Utils\DateTime,
	Nette\Forms\Controls\TextInput;

class DateTimeFormControl extends TextInput
{

	/**
	 * @param  string  label
	 * @param  int  maximum number of characters the user may enter
	 */
	public function __construct($label = null)
	{
		parent::__construct($label, 16);
		$this->control->type = 'datetime';
	}

	/**
	 * Generates control's HTML element.
	 * @return Nette\Utils\Html
	 */
	public function getControl()
	{
		$input = parent::getControl();
		if (!is_array($input->class)) {
			if ($input->class) {
				$input->class[] = $input->class;
			} else {
				$input->class = [];
			}
		}
		$input->class[] = 'datetime-picker';
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
			$format = "d.m.Y H:i:s";
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
			$value = $value->format("d.m.Y H:i:s");
		}
		parent::setValue($value);
		return $this;
	}
}