<?php

namespace Mepatek\Components;

use Tomaj\Form\Renderer\BootstrapInlineRenderer;
use Tomaj\Form\Renderer\BootstrapRenderer;
use Tomaj\Form\Renderer\BootstrapVerticalRenderer;

/**
 * Class FormBootstrap
 * @package App\Components
 */
class FormBootstrap extends Form
{
	/** @var null|string */
	protected $type = null;

	/**
	 * FormBootstrap constructor.
	 *
	 * @param null|ITranslator $translator
	 * @param null|string      $type null, inline, vertical
	 */
	public function __construct($translator, $type = null)
	{
		if ($type == "inline" or $type == "vertical") {
			$this->type = $type;
		}
		parent::__construct($translator);
	}

	/**
	 * Render bootstrap form
	 */
	public function render(...$args)
	{
		switch ($this->type) {
			case "inline":
				$renderer = new BootstrapInlineRenderer();
				break;
			case "vertical":
				$renderer = new BootstrapVerticalRenderer();
				break;
			default:
				$renderer = new BootstrapRenderer;
				break;
		}
		$renderer->wrappers["label"]["container"] = 'div class="col-sm-2 control-label"';
		$this->setRenderer($renderer);

		parent::render(...$args);
	}

}
