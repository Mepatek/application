<?php

namespace Mepatek\Mapper;

use Nette,
	Nette\Database\Context,
	Nette\Database\IRow,
	Nette\Database\Table\Selection;

/**
 * Class AbstractNetteDatabaseMapper
 * @package Mepatek\Mapper
 */
class AbstractNetteDatabaseMapper extends AbstractMapper
{
	/** @var Context */
	protected $database;
	/** @var array */
	protected $permanentlyFilter = [];

	/**
	 * Find entities by $values (key=>value)
	 *
	 * @param array   $values
	 * @param array   $order Order => column=>ASC/DESC
	 * @param integer $limit Limit count
	 * @param integer $offset Limit offset
	 *
	 * @return array
	 */
	public function findBy(array $values, $order = null, $limit = null, $offset = null)
	{
		$selection = $this->selectionBy($values, $order, $limit, $offset);
		$retArray = [];
		foreach ($selection as $row) {
			$retArray[] = $this->dataToItem($row);
		}
		return $retArray;
	}

	/**
	 * Set Permanently filter for all functions includes find!
	 *
	 * @param array $values
	 */
	public function setPermanentlyFilter(array $values = [])
	{
		$this->permanentlyFilter = $values;
	}

	/**
	 * Get Permanently filter
	 *
	 * @return array
	 */
	public function getPermanentlyFilter()
	{
		return $this->permanentlyFilter;
	}

	/**
	 * Helper for findBy, countBy
	 *
	 * @param array   $values
	 * @param array   $order Order => column=>ASC/DESC
	 * @param integer $limit Limit count
	 * @param integer $offset Limit offset
	 *
	 * @return Nette\Database\Table\Selection
	 */
	protected function selectionBy(array $values, $order = null, $limit = null, $offset = null)
	{
		$selection = $this->getTable();
		// compose Where
		foreach ($values as $key => $value) {
			// translate property name to SQL column name
			$keyTranslate = $this->translatePropertyToColumnSQL($key);
			if (is_int($key) and is_array($value)) {
				// multiple parameters in array must be first condition like (col = ? OR col = ? OR col2 = ?) and next is parameters
				$value[0] = $this->translatePropertyToColumnSQL($value[0]);
				call_user_func_array([$selection, "where"], $value);
			} else {
				$selection->where($keyTranslate, $value);
			}
		}
		// compose permanently filter
		foreach ($this->getPermanentlyFilter() as $key => $value) {
			// translate property name to SQL column name
			$keyTranslate = $this->translatePropertyToColumnSQL($key);
			if (is_int($key) and is_array($value)) {
				// multiple parameters in array must be first condition like (col = ? OR col = ? OR col2 = ?) and next is parameters
				$value[0] = $this->translatePropertyToColumnSQL($value[0]);
				call_user_func_array([$selection, "where"], $value);
			} else {
				$selection->where($keyTranslate, $value);
			}
		}
		// compose Order
		if ($order !== null) {
			$orderString = "";
			foreach ($order as $column => $ascdesc) {
				// translate properties to SQL column name
				$columnTranslate = $this->translatePropertyToColumnSQL($column);
				$orderString .= ($orderString ? "," : "") . $columnTranslate . (strtolower(
						$ascdesc
					) == "desc" ? " DESC" : "");
			}
			if ($orderString) {
				$selection->order($orderString);
			} else {
			}
		}
		// compose Limit
		if ($limit !== null) {
			if ($offset !== null) {
				$selection->limit((int)$limit, (int)$offset);
			} else {
				$selection->limit((int)$limit);
			}
		}
		if ($limit !== null and !$order) {
			// is MS SQL? need order for OFFSET
			if ($this->database->getConnection()->getSupplementalDriver()
				instanceof Nette\Database\Drivers\SqlsrvDriver
			) {
				$selection->order($selection->getPrimary());
			}
		}

		return $selection;
	}

	/**
	 * Get table object
	 *
	 * @return Selection
	 */
	protected function getTable()
	{
		return null;
	}

	/**
	 * Translate property name in string to SQl column name
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	protected function translatePropertyToColumnSQL($string)
	{
		return strtr($string, $this->mapItemPropertySQLNames());
	}

	/**
	 * Get array map of item property vs SQL columns name
	 * For overwrite
	 *
	 * @return array
	 */
	protected function mapItemPropertySQLNames()
	{
		return [];
	}

	/**
	 * From data to item
	 * For overwrite
	 *
	 * @param IRow $data
	 *
	 * @return mixed
	 */
	protected function dataToItem($data)
	{
		return iterator_to_array($data);
	}

	/**
	 * Count entities by $values (key=>value)
	 *
	 * @param array $values
	 *
	 * @return integer
	 */
	public function countBy(array $values)
	{
		return $this->selectionBy($values)->count();
	}

	/**
	 * Sum column (property) by $values (key=>value)
	 *
	 * @param array $values
	 * @param       $column
	 *
	 * @return integer
	 */
	public function sumBy(array $values, $column)
	{
		return $this->selectionBy($values)->sum($this->translatePropertyToColumnSQL($column));
	}

}