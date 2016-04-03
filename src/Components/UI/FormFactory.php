<?php

namespace Mepatek\Components\UI;

use Mepatek\Components\Form,
	Mepatek\Components\FormBootstrap;
use Nette\Localization\ITranslator;

class FormFactory
{

	/** @var ITranslator */
	private $translator;

	/**
	 * FormFactory constructor.
	 *
	 * @param ITranslator $translator
	 */
	public function __construct(ITranslator $translator)
	{
		$this->translator = $translator;
	}

	/**
    * Create form UI component
    * 
    * @return \App\Components\Form
    */
    public function create()
    {
        $form = new Form();
		return $form;
   }

	/**
	 * Create form bootstrap UI component
	 *
	 * @param string $type null=standard, inline = inline form, vertical = vertical form
	 * @return \App\Components\FormBootstrap
	 */
	public function createBootstrap($type=null)
	{
		$form = new FormBootstrap($this->translator, $type);
		return $form;
	}

}