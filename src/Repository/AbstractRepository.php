<?php

namespace Mepatek\Repository;

use Nette\Object;

/**
 * Class AbstractRepository
 * @package Mepatek\Repository
 */
abstract class AbstractRepository extends Object implements IRepository
{

	/** @var \Mepatek\Mapper\IMapper */
	protected $mapper;

	/**
	 * Delete
	 *
	 * @param mixed $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		return $this->mapper->delete($id);
	}

	/**
	 * Find items by values
	 *
	 * @param array $values
	 * @param array $order
	 * @param int   $limit
	 * @param int   $offset
	 *
	 * @return array of items
	 */
	public function findBy(array $values, $order = null, $limit = null, $offset = null)
	{
		return $this->mapper->findBy($values, $order, $limit, $offset);
	}

	/**
	 * Count items by $values
	 *
	 * @param array $values
	 *
	 * @return integer
	 */
	public function countBy(array $values)
	{
		return $this->mapper->countBy($values);
	}

	/**
	 * Set Permanently filter for all functions includes find!
	 *
	 * @param array $values
	 */
	public function setPermanentlyFilter(array $values = [])
	{
		return $this->mapper->setPermanentlyFilter($values);
	}

	/**
	 * Get Permanently filter
	 *
	 * @return array
	 */
	public function getPermanentlyFilter()
	{
		return $this->mapper->getPermanentlyFilter();
	}

}
