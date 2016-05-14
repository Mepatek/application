<?php

namespace Mepatek\Components\Ublaboo\DataSources;

use Ublaboo\DataGrid\DataSource\IDataSource,
	Ublaboo\DataGrid\DataSource\FilterableDataSource,
	Nette\Database\Table\Selection,
	Ublaboo\DataGrid\Filter,
	Ublaboo\DataGrid\Utils\Sorting,
	Nette,
	Nette\Utils\Strings,
	Mepatek\Repository\IRepository;

/**
 * Repository data source.
 *
 * @package     Grido
 * @subpackage  DataSources
 * @author      Josef Dohnal <josef.dohnal@mepatek.cz>
 *
 */
class RepositorySource extends FilterableDataSource implements IDataSource
{
	/** @var IRepository */
	protected $repository;
	/** @var array */
	protected $filter=[];
	/** @var array */
	protected $order=[];
	/** @var null|int */
	protected $limit=null;
	/** @var null|int */
	protected $offset=null;

	/**
	 * @var array
	 */
	protected $data = [];

	/**
	 * @var string
	 */
	protected $primary_key;


	/**
	 * @param IRepository $repository
	 * @param string      $primary_key
	 */
	public function __construct(IRepository $repository, $primary_key)
	{
		$this->repository = $repository;
		$this->primary_key = $primary_key;
	}


	/********************************************************************************
	 *                          IDataSource implementation                          *
	 ********************************************************************************/


	/**
	 * Get count of data
	 * @return int
	 */
	public function getCount()
	{
		return $this->repository->countBy($this->filter);
	}


	/**
	 * Get the data
	 * @return array
	 */
	public function getData()
	{
		return $this->repository->findBy($this->filter,$this->order,$this->limit, $this->offset);
	}


	/**
	 * Filter data - get one row
	 *
	 * @param array $condition
	 *
	 * @return static
	 */
	public function filterOne(array $condition)
	{
		$this->limit = 1;
		$this->filter = $condition;
		return $this;
	}


	/**
	 * Filter by date
	 *
	 * @param  Filter\FilterDate $filter
	 *
	 * @return void
	 */
	public function applyFilterDate(Filter\FilterDate $filter)
	{
		$conditions = $filter->getCondition();

		$date = \DateTime::createFromFormat($filter->getPhpFormat(), $conditions[$filter->getColumn()]);

		$this->filter["DATE({$filter->getColumn()}) = ?"] = $date->format('Y-m-d');
	}


	/**
	 * Filter by date range
	 *
	 * @param  Filter\FilterDateRange $filter
	 *
	 * @return void
	 */
	public function applyFilterDateRange(Filter\FilterDateRange $filter)
	{
		$conditions = $filter->getCondition();

		$value_from = $conditions[$filter->getColumn()]['from'];
		$value_to = $conditions[$filter->getColumn()]['to'];

		if ($value_from) {
			$date_from = \DateTime::createFromFormat($filter->getPhpFormat(), $value_from);
			$date_from->setTime(0, 0, 0);

			$this->filter["DATE({$filter->getColumn()}) >= ?"] = $date_from->format('Y-m-d');
		}

		if ($value_to) {
			$date_to = \DateTime::createFromFormat($filter->getPhpFormat(), $value_to);
			$date_to->setTime(23, 59, 59);

			$this->filter["DATE({$filter->getColumn()}) <= ?"] = $date_to->format('Y-m-d');
		}
	}


	/**
	 * Filter by range
	 *
	 * @param  Filter\FilterRange $filter
	 *
	 * @return void
	 */
	public function applyFilterRange(Filter\FilterRange $filter)
	{
		$conditions = $filter->getCondition();

		$value_from = $conditions[$filter->getColumn()]['from'];
		$value_to = $conditions[$filter->getColumn()]['to'];

		if ($value_from) {
			$this->filter["{$filter->getColumn()} >= ?"] = $value_from;
		}

		if ($value_to) {
			$this->filter["{$filter->getColumn()} <= ?"] = $value_to;
		}
	}


	/**
	 * Filter by keyword
	 *
	 * @param  Filter\FilterText $filter
	 *
	 * @return void
	 */
	public function applyFilterText(Filter\FilterText $filter)
	{
		$or = [];
		$args = [];
		$big_or = '(';
		$big_or_args = [];
		$condition = $filter->getCondition();

		foreach ($condition as $column => $value) {
			$words = explode(' ', $value);

			$like = '(';
			$args = [];

			foreach ($words as $word) {
				$like .= "$column LIKE ? OR ";
				$args[] = "%$word%";
			}

			$like = substr($like, 0, strlen($like) - 4) . ')';

			$or[] = $like;
			$big_or .= "$like OR ";
			$big_or_args = array_merge($big_or_args, $args);
		}

		if (sizeof($or) > 1) {
			$big_or = substr($big_or, 0, strlen($big_or) - 4) . ')';

			$query = array_merge([$big_or], $big_or_args);
			$this->filter[] = $query;
		} else {
			$query = array_merge($or, $args);
			$this->filter[] = $query;
		}
	}


	/**
	 * Filter by multi select value
	 *
	 * @param  Filter\FilterMultiSelect $filter
	 *
	 * @return void
	 */
	public function applyFilterMultiSelect(Filter\FilterMultiSelect $filter)
	{
		$condition = $filter->getCondition();
		$values = $condition[$filter->getColumn()];
		$or = '(';

		if (sizeof($values) > 1) {
			$length = sizeof($values);
			$i = 1;

			foreach ($values as $value) {
				if ($i == $length) {
					$or .= $filter->getColumn() . ' = ?)';
				} else {
					$or .= $filter->getColumn() . ' = ? OR ';
				}

				$i++;
			}

			array_unshift($values, $or);

			$this->filter[] = $values;
		} else {
			$this->filter[$filter->getColumn() . ' = ?'] = reset($values);
		}
	}


	/**
	 * Filter by select value
	 *
	 * @param  Filter\FilterSelect $filter
	 *
	 * @return void
	 */
	public function applyFilterSelect(Filter\FilterSelect $filter)
	{
		$this->filter = array_merge($this->filter, $filter->getCondition());
	}


	/**
	 * Apply limit and offset on data
	 *
	 * @param int $offset
	 * @param int $limit
	 *
	 * @return static
	 */
	public function limit($offset, $limit)
	{
		$this->limit = $limit;
		$this->offset = $offset;

		return $this;
	}


	/**
	 * Sort data
	 *
	 * @param  Sorting $sorting
	 *
	 * @return static
	 */
	public function sort(Sorting $sorting)
	{
		if (is_callable($sorting->getSortCallback())) {
			call_user_func(
				$sorting->getSortCallback(),
				$this->repository,
				$sorting->getSort()
			);

			return $this;
		}

		$sort = $sorting->getSort();

		if (!empty($sort)) {
			$this->order = [];

			foreach ($sort as $column => $order) {
				$this->order[$column] = $order;
			}
		}

		return $this;
	}
}
