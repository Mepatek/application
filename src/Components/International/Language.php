<?php
/**
 * Created by PhpStorm.
 * User: pepa
 * Date: 26.05.2017
 * Time: 17:58
 */

namespace Mepatek\Components\International;


class Language
{
	/** @var string iso 2 char id */
	public $id;
	/** @var string english name */
	public $name;
	/** @var string original name */
	public $originalName;
	/** @var string czech name */
	public $czechName;

	/**
	 * Language constructor.
	 *
	 * @param array $arrayData
	 */
	public function __construct($arrayData)
	{
		list($this->id, $this->czechName, $this->originalName, $this->name) = $arrayData;
	}
}
