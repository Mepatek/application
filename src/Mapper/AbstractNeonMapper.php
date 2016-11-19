<?php

namespace Mepatek\Mapper;

use Nette,
	Nette\Database\IRow,
	Nette\Database\Table\Selection;

/**
 * Class AbstractNeonMapper
 * @package Mepatek\Mapper
 *
 * Store and load entities to/from neon files
 * Structure is
 *    id1:
 *        property_1: value_1
 *        property_n: value_n
 *    id2:
 *        property_1: value_1
 *        property_n: value_n
 */
class AbstractNeonMapper extends AbstractMapper
{
	/** @var Nette\Neon\Neon */
	protected $neon;
	/** @var string */
	protected $neonFile;
	/** @var Nette\Caching\IStorage */
	protected $storage;

	/** @var Nette\Caching\Cache */
	protected $cache = null;

	/** @var mixed */
	protected $neonData = null;
	/** @var array */
	protected $permanentlyFilter = [];

	/**
	 * Get Cache object
	 *
	 * @return Nette\Caching\Cache
	 */
	protected function getCache()
	{
		if ($this->cache === null) {
			$this->cache = new Nette\Caching\Cache($this->storage, "MepatekNeonMapper");
		}
		return $this->cache;
	}

	/**
	 * load data from cache or encode data to neonData
	 *
	 * @return void
	 */
	protected function encodeNeonData()
	{
		$this->neonData = $this->getCache()->load($this->neonFile);
		if ($this->neonData === null) {
			$this->neonData = $this->neon->decode(
				file_get_contents($this->neonFile)
			);
			$this->getCache()->save(
				$this->neonFile,
				$this->neonData,
				[
					Nette\Caching\Cache::FILES  => $this->neonFile,
					Nette\Caching\Cache::EXPIRE => "24 hours",
				]
			);
		}
	}

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
		$this->encodeNeonData();

		$retArray = [];
		foreach ($this->neonData as $entity) {
			$retArray[] = $this->dataToItem($entity);
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
