<?php

namespace Mepatek;

use Mepatek\Entity\File;

class FileSystem
{

	/** @var string */
	protected $cpFileSytem;

	/**
	 * FileSystem constructor.
	 *
	 * @param string $cpFileSytem
	 */
	public function __construct($cpFileSytem = "WINDOWS-1250")
	{
		$this->cpFileSytem = $cpFileSytem;
	}

	/**
	 * Get Mepatek\Entity\File new object
	 *
	 * @param string $filename (UTF8)
	 *
	 * @return File
	 */
	public function getFileUtf8($filename)
	{
		return new File($filename, $this);
	}

	/**
	 * Get Mepatek\Entity\File new object
	 *
	 * @param string $filename (CpFs)
	 *
	 * @return File
	 */
	public function getFile($filename)
	{
		return new File($filename, $this, true);
	}

	/**
	 * Convert string from codepage filesystem to utf8
	 *
	 * @param $string
	 *
	 * @return string
	 */
	public function cpFsToUtf8($string)
	{
		return iconv($this->cpFileSytem, "UTF-8//TRANSLIT", $string);
	}

	/**
	 * Convert string from codepage filesystem to utf8
	 *
	 * @param $string
	 *
	 * @return string
	 */
	public function utf8TocpFs($string)
	{
		return iconv("UTF-8", $this->cpFileSytem . "//TRANSLIT", $string);
	}
}