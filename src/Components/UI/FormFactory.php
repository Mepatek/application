<?php

namespace Mepatek\Components\UI;

use Mepatek\Components\Form,
	Mepatek\Components\FormBootstrap;
use Nette\Localization\ITranslator;

/**
 * Class FormFactory
 * @package Mepatek\Components\UI
 */
class FormFactory
{

	/** @var ITranslator */
	private $translator;

	/**
    * Create form UI component
    *
    * @return Form
    */
    public function create()
    {
        $form = new Form();
		if ($this->translator) {
			$form->setTranslator($this->translator);
		}
		return $form;
   }

	/**
	 * Create form bootstrap UI component
	 *
	 * @param string $type null=standard, inline = inline form, vertical = vertical form
	 *
	 * @return FormBootstrap
	 */
	public function createBootstrap($type=null)
	{
		$form = new FormBootstrap($type);
		if ($this->translator) {
			$form->setTranslator($this->translator);
		}
		return $form;
	}

	/**
	 * @return ITranslator
	 */
	public function getTranslator()
	{
		return $this->translator;
	}

	/**
	 * @param ITranslator $translator
	 */
	public function setTranslator(ITranslator $translator)
	{
		$this->translator = $translator;
	}


}
