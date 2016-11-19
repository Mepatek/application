<?php
namespace Mepatek\Components\UI\Dashboard;


use Nette\Application\UI\Control;

class EasyPieChart extends Control
{
	/** @var string */
	protected $class = "";
	/** @var string */
	protected $color = "";
	/** @var string */
	protected $title = "";
	/** @var int|float */
	protected $percent = 0;
	/** @var string */
	protected $link = null;

	/**
	 * render control
	 */
	public function render()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/' . basename(__FILE__,".php") . '.latte');
		// vložíme do šablony nějaké parametry
		$template->control = $this;
		// a vykreslíme ji
		$template->render();
	}

	/**
	 * @return string
	 */
	public function getClass()
	{
		return $this->class;
	}

	/**
	 * @param string $class
	 */
	public function setClass($class)
	{
		$this->class = $class;
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
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * @return float|int
	 */
	public function getPercent()
	{
		return $this->percent;
	}

	/**
	 * @param float|int $percent
	 */
	public function setPercent($percent)
	{
		$this->percent = $percent;
	}

	/**
	 * @return string
	 */
	public function getLink()
	{
		return $this->link;
	}

	/**
	 * @param string $link
	 */
	public function setLink($link)
	{
		$this->link = $link;
	}


}
