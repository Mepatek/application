<?php

namespace Mepatek\Components\Grido\DataSources;

use Grido\Exception,
	Grido\Components\Filters\Condition,
	Grido\DataSources,
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
class RepositorySource extends \Nette\Object implements DataSources\IDataSource
{
    /** @var IRepository */
    protected $repository;
	/** @var integer */
	protected $limit;
	/** @var integer */
	protected $offset;
	/** @var array */
	protected $filterValues=array();
	/** @var array */
	protected $order=array();

    /**
     * @param IRepository $repository
     */
    public function __construct($repository)
    {
        $this->repository = $repository;
    }

	/**
	 * @param Condition $condition
	 */
	protected function makeWhere(Condition $condition)
	{
		if ($condition->callback) {
			//callback($condition->callback)->invokeArgs(array($condition->value, $selection));
		} else {
			$condarray = $condition->__toArray();
			$this->filterValues[$condarray[0]] = $condarray[1];
			//call_user_func_array(array($selection, 'where'), $condition->__toArray());
		}
	}

    /**
     * @param string $actual
     * @param string $condition
     * @param mixed $expected
     * @throws Exception
     * @return bool
     */
    public function compare($actual, $condition, $expected)
    {
        $expected = (array) $expected;
        $expected = current($expected);
        $cond = str_replace(' ?', '', $condition);

        if ($cond === 'LIKE') {
            $actual = Strings::toAscii($actual);
            $expected = Strings::toAscii($expected);

            $pattern = str_replace('%', '(.|\s)*', preg_quote($expected, '/'));
            return (bool) preg_match("/^{$pattern}$/i", $actual);

        } elseif ($cond === '=') {
            return $actual == $expected;

        } elseif ($cond === '<>') {
            return $actual != $expected;

        } elseif ($cond === 'IS NULL') {
            return $actual === NULL;

        } elseif ($cond === 'IS NOT NULL') {
            return $actual !== NULL;

        } elseif (in_array($cond, array('<', '<=', '>', '>='))) {
            $actual = (int) $actual;
            return eval("return {$actual} {$cond} {$expected};");

        } else {
            throw new Exception("Condition '$condition' not implemented yet.");
        }
    }

    /*********************************** interface IDataSource ************************************/

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->repository->countBy($this->filterValues);
    }

    /**
     * @return array
     */
    public function getData()
    {
		return $this->repository->findBy($this->filterValues, $this->order, $this->limit, $this->offset);
    }

    /**
     * @param array $conditions
     */
    public function filter(array $conditions)
    {
		$this->filterValues = array();
        foreach ($conditions as $condition) {
            $this->makeWhere($condition);
        }
    }

    /**
     * @param int $offset
     * @param int $limit
     */
    public function limit($offset, $limit)
    {
        $this->limit = $limit;
		$this->offset = $offset;
    }

    /**
     * @param array $sorting
     * @throws Exception
     */
    public function sort(array $sorting)
    {
        if (count($sorting) > 1) {
            throw new Exception('Multi-column sorting is not implemented yet.');
        }

		$this->order = array();

        foreach ($sorting as $column => $sort) {
			$this->order[$column] = $sort;
        }
    }

    /**
     * @param mixed $column
     * @param array $conditions
     * @param int $limit
     * @return array
     * @throws Exception
     */
    public function suggest($column, array $conditions, $limit)
    {
        $data = $this->data;
        foreach ($conditions as $condition) {
            $data = $this->makeWhere($condition, $data);
        }

        array_slice($data, 1, $limit);

        $items = array();
        foreach ($data as $row) {
            if (is_string($column)) {
                $value = (string) $row[$column];
            } elseif (is_callable($column)) {
                $value = (string) $column($row);
            } else {
                $type = gettype($column);
                throw new Exception("Column of suggestion must be string or callback, $type given.");
            }

            $items[$value] = Nette\Templating\Helpers::escapeHtml($value);
        }

        sort($items);
        return array_values($items);
    }
}
