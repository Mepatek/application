<?php

namespace Mepatek\Components\UI\Dashboard;

use Nette\Application\UI\Control;

class StatisticBoxProgress extends Control
{

	/** @var string */
	protected $color = "default";
	/** @var string */
	protected $icon = "fa fa fa-bar-chart-o";
	/** @var int|float */
	protected $number = 0;
	/** @var string */
	protected $prefix = null;
	/** @var string */
	protected $suffix = null;
	/** @var string */
	protected $description = "";
	/** @var string */
	protected $progressDescription = "";
	/** @var int */
	protected $progressPercent = 0;
	/** @var string */
	protected $link=null;


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
	 * @return float|int
	 */
	public function getNumber()
	{
		return $this->number;
	}

	/**
	 * @param float|int $number
	 */
	public function setNumber($number)
	{
		$this->number = $number;
	}

	/**
	 * @return string
	 */
	public function getPrefix()
	{
		return $this->prefix;
	}

	/**
	 * @param string $prefix
	 */
	public function setPrefix($prefix)
	{
		$this->prefix = $prefix;
	}

	/**
	 * @return string
	 */
	public function getSuffix()
	{
		return $this->suffix;
	}

	/**
	 * @param string $suffix
	 */
	public function setSuffix($suffix)
	{
		$this->suffix = $suffix;
	}

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param string $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}

	/**
	 * @return string
	 */
	public function getProgressDescription()
	{
		return $this->progressDescription;
	}

	/**
	 * @param string $progressDescription
	 */
	public function setProgressDescription($progressDescription)
	{
		$this->progressDescription = $progressDescription;
	}

	/**
	 * @return int
	 */
	public function getProgressPercent()
	{
		return $this->progressPercent;
	}

	/**
	 * @param int $progressPercent
	 */
	public function setProgressPercent($progressPercent)
	{
		$this->progressPercent = (int)$progressPercent;
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
