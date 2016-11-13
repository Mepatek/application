<?php

namespace Mepatek\Components\UI\Dashboard;


use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI\Control;

class FlotChart extends Control
{

	/** @var string */
	protected $color = "default";
	/** @var string */
	protected $icon = "fa fa-bar-chart-o";
	/** @var string */
	protected $caption;
	/** @var string */
	protected $captionHelper;

	/** @var array */
	protected $buttons = [];

	/**
	 * Data format
	 *
	 * https://github.com/flot/flot/blob/master/API.md
	 *
	 * @var array
	 */
	protected $data = [];

	/**
	 * Plot Options
	 *
	 * https://github.com/flot/flot/blob/master/API.md
	 *
	 * @var array
	 */
	protected $options = [];

	/**
	 * Send data by AJAX
	 */
	public function handleGetData()
	{
		$data = $this->getData();
		$options = $this->getOptions();

		$this->presenter->sendResponse(
			new JsonResponse(
				[
					"data"    => $data,
					"options" => $options,
				]
			)
		);
	}

	/**
	 * render control
	 */
	public function render()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/' . basename(__FILE__, ".php") . '.latte');
		// vložíme do šablony nějaké parametry
		$template->control = $this;
		// a vykreslíme ji
		$template->render();
	}

	/**
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * @param array $data
	 */
	public function setData($data)
	{
		$this->data = $data;
	}

	/**
	 * @return array
	 */
	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * @param array $options
	 */
	public function setOptions($options)
	{
		$this->options = $options;
	}


	/**
	 * @return array
	 */
	public function getButtons()
	{
		return $this->buttons;
	}

	/**
	 */
	public function addButton($caption, $url = "", $icon = "", $color = null)
	{
		$button = new \stdClass();
		$button->caption = $caption;
		$button->url = $url;
		$button->color = $color ? $color : $this->getColor();
		$button->icon = $icon;
		$this->buttons[] = $button;
	}

	/**
	 * @param array $buttons
	 */
	public function setButtons($buttons)
	{
		$this->buttons = $buttons;
	}


	/**
	 * @return string
	 */
	public function getColor()
	{
		return $this->color;
	}

	/**
	 * @param string $color
	 */
	public function setColor($color)
	{
		$this->color = $color;
	}

	/**
	 * @return string
	 */
	public function getIcon()
	{
		return $this->icon;
	}

	/**
	 * @param string $icon
	 */
	public function setIcon($icon)
	{
		$this->icon = $icon;
	}

	/**
	 * @return string
	 */
	public function getCaption()
	{
		return $this->caption;
	}

	/**
	 * @param string $caption
	 */
	public function setCaption($caption)
	{
		$this->caption = $caption;
	}

	/**
	 * @return mixed
	 */
	public function getCaptionHelper()
	{
		return $this->captionHelper;
	}

	/**
	 * @param mixed $captionHelper
	 */
	public function setCaptionHelper($captionHelper)
	{
		$this->captionHelper = $captionHelper;
	}


	/************ set DATA and OPTIONS helpers *************/

	/**
	 * Add linear data
	 *
	 * @param array  $data
	 * @param string $label
	 * @param array  $options
	 */
	public function addSeries(array $data, $label = "", array $options = [])
	{
		if ($label) {
			$options["label"] = $label;
		}

		$this->data[] = array_merge(
			["data" => $data],
			$options
		);
	}
}
