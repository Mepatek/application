<?php

namespace Mepatek\Mapper;

use Nette,
	Nette\Object,
	Mepatek\Logger;

/**
 * Class AbstractMapper
 * @package Mepatek\Mapper
 */
class AbstractMapper extends Object
{
	/** @var Logger */
	protected $logger;

	/**
	 * Log after update/save
	 *
	 * @param string               $mapper
	 * @param array | \Traversable $oldvalues
	 * @param array | \Traversable $newvalues
	 * @param string               $addMessage additional message
	 */
	protected function logSave($mapper, $oldvalues = [], $newvalues = [], $addMessage = "")
	{
		$msg = "Save ($mapper)\nOld values:" . $this->traversableToString(
				$oldvalues, ", "
			) . "\nNew values:" . $this->traversableToString(
				$newvalues, ", "
			) . ($addMessage ? "\n" . $addMessage : "");
		$this->log(
			$msg,
			[
				"function" => substr($mapper, -50),
			]
		);
	}

	/**
	 * Helper : array or \Traversable to string
	 *
	 * @param array | \Traversable $values
	 * @param string               $delimiter
	 *
	 * @return string
	 */
	protected function traversableToString($values, $delimiter = ", ")
	{
		$str = "";
		foreach ($values as $name => $value) {
			if (is_array($value) or is_object($value)) {
				$value = serialize($value);
			}
			$str .= ($str ? $delimiter : "") . "$name:$value";
		}
		return $str;
	}

	/**
	 * Log message - helper for logInsert/logSave/logDelete
	 *
	 * @param       $msg
	 * @param array $context
	 */
	protected function log($msg, $context)
	{
		if ($this->logger) {
			$this->logger->info($msg, $context);
		}
	}

	/**
	 * Log after insert
	 *
	 * @param string               $mapper
	 * @param array | \Traversable $newvalues
	 * @param string               $addMessage additional message
	 */
	protected function logInsert($mapper, $newvalues = [], $addMessage = "")
	{
		$msg = "New ($mapper)\nNew values:" . $this->traversableToString(
				$newvalues, ", "
			) . ($addMessage ? "\n" . $addMessage : "");
		$this->log(
			$msg,
			[
				"function" => substr($mapper, -50),
			]
		);
	}

	/**
	 * Log after delete
	 *
	 * @param string               $mapper
	 * @param array | \Traversable $oldvalues
	 * @param string               $addMessage additional message
	 */
	protected function logDelete($mapper, $oldvalues = [], $addMessage = "")
	{
		$msg = "Delete ($mapper)\nDeleted values:" . $this->traversableToString(
				$oldvalues, ", "
			) . ($addMessage ? "\n" . $addMessage : "");
		$this->log(
			$msg,
			[
				"function" => substr($mapper, -50),
			]
		);
	}

}