<?php

namespace Mepatek\Components\Controls;

use Nette,
	Nette\Forms\Controls\TextInput;

class DateFormControl extends TextInput
{

	/**
	 * @param  string  label
	 * @param  int  maximum number of characters the user may enter
	 */
	public function __construct($label = NULL)
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
		$input->addAttributes('class', 'date-picker');
		dump($input);
		return $input;
	}
}