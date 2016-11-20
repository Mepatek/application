<?php

namespace Mepatek\Mapper;

use Nette,
	Nette\Database\IRow,
	Nette\Database\Table\Selection;
use Webpatser\Uuid\Uuid;

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
 *
 * Entity must have property id
 * If id not set, generate Uuid
 *
 * Constructor must set neon, storage, neonFile and objectClass
 */
class AbstractNeonMapper extends AbstractMapper
{
	/** @var Nette\Neon\Neon */
	protected $neon;
	/** @var string */
	protected $neonFile;
	/** @var Nette\Caching\IStorage */
	protected $storage;
	/** @var string */
	protected $objectClass;

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
			// set id to data
			foreach ($this->neonData as $key => $value) {
				$this->neonData[$key]["id"] = $key;
			}
			$this->getCache()->save(
				$this->neonFile,
				$this->neonData,
				[
					Nette\Caching\Cache::FILES  => $this->neonFile,
					Nette\Caching\Cache::EXPIRE => "24 hours",
				]
			);
		}
		if ($this->neonData === null) {
			$this->neonData = [];
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
		$retArray = [];
		foreach ($this->getDataBy($values, $order, $limit, $offset) as $entityData) {
			$retArray[] = $this->dataToItem($entityData);
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
	 * Save entity
	 *
	 * @param object $item
	 *
	 * @return boolean
	 */
	public function save(&$item)
	{
		$id = $item->id;
		$this->neonData[$id] = $this->itemToData($item);;

		$this->persist();
		return true;
	}

	/**
	 * Find 1 entity by ID
	 *
	 * @param mixed $id
	 *
	 * @return object
	 */
	public function find($id)
	{
		$this->encodeNeonData();

		if (isset($this->neonData[$id])) {
			return $this->dataToItem($this->neonData[$id]);
		} else {
			return null;
		}
	}

	/**
	 * Find first entity by $values (key=>value)
	 *
	 * @param array $values
	 * @param array $order Order => column=>ASC/DESC
	 *
	 * @return object
	 */
	public function findOneBy(array $values, $order = null)
	{
		$items = $this->findBy($values, $order, 1);
		if (count($items) > 0) {
			return $items[0];
		} else {
			return null;
		}
	}

	/**
	 * Delete item
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		if (isset($this->neonData[$id])) {
			unset($this->neonData[$id]);
		}
		$this->persist();
		return true;
	}

	/**
	 * @param array $data
	 * @param array $values
	 *
	 * @return bool
	 */
	protected function isDataInFilter($data, array $values)
	{
		if (!is_array($data)) {
			return false;
		}
		if (count($values) === 0) {
			return true;
		}

		foreach ($values as $key => $value) {
			if (is_int($key) and is_array($value)) {
				throw new \Exception("Multiple value filter for NEON not supported");
			} else {
				$property = $key;
				if (isset($data[$property])) {
					if ($data[$property] == $value) {
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Helper for findBy
	 *
	 * @param array   $values (colum(=), column LIKE)
	 * @param array   $order Order => column=>ASC/DESC
	 * @param integer $limit Limit count
	 * @param integer $offset Limit offset
	 *
	 * @return Nette\Database\Table\Selection
	 */
	protected function getDataBy(array $values, $order = null, $limit = null, $offset = null)
	{
		$this->encodeNeonData();

		$valuesWithPermanentFilter = array_merge($values, $this->getPermanentlyFilter());
		$filteredData = array_filter(
			$this->neonData,
			function ($itemData) use ($valuesWithPermanentFilter) {
				return $this->isDataInFilter($itemData, $valuesWithPermanentFilter);
			}
		);


		// compose Order
		if ($order !== null) {
			if (count($order)>1) {
				throw new \Exception("Only one order column is supported for NEON");
			}
			foreach ($order as $property=>$ascdesc) {
				usort($filteredData, function ($a, $b) use ($property, $ascdesc) {
					if ($a[$property]==$b[$property]) {
						return 0;
					}
					if ($ascdesc=="DESC") {
						$sign = -1;
					} else {
						$sign = 1;
					}
					return $a[$property]<$b[$property] ? (-1 * $sign) : ($sign);
				});
			}
		}

		// Limit and offset
		if ($offset !== null or $limit !== null) {
			if ($offset === null) {
				$offset = 0;
			}
			$filteredData = array_slice($filteredData, $offset, $limit);
		}

		return $filteredData;
	}

	/**
	 * From data to item
	 *
	 * @param array $data
	 *
	 * @return mixed
	 */
	protected function dataToItem($data)
	{
		$item = new $this->objectClass;
		foreach ($data as $property => $value) {
			$item->$property = $value;
		}
		return $item;
	}


	/**
	 * From item to data
	 *
	 * @param object $data
	 *
	 * @return array
	 */
	protected function itemToData($item)
	{
		$data = [];
		foreach ($item as $property => $value) {
			$data[$property] = $value;
		}
		return $data;
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
		$filteredValues = $this->findBy($values);
		return count($filteredValues);
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
		$filteredValues = $this->findBy($values);
		$sum = 0.00;
		foreach ($filteredValues as $item) {
			$sum = $sum + (float)$item->$column;
		}
		return $sum;
	}

	/**
	 * Persist changes to neon file
	 */
	protected function persist()
	{
		$saveData = [];
		// remove id
		foreach ($this->neonData as $key=>$value) {
			if (isset($this->neonData[$key]["id"])) {
				$id = $this->neonData[$key]["id"];
				unset($value["id"]);
			} else {
				$id = Uuid::generate(4);
			}

			$saveData[$id] = $value;
		}
		$data = $this->neon->encode($saveData, Nette\Neon\Encoder::BLOCK);
		file_put_contents($this->neonFile, $data);
	}
}
