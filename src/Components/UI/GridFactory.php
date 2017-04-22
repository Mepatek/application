<?php

namespace Mepatek\Components\UI;

use Doctrine\ORM\QueryBuilder;
use Nette,
	Grido,
	Grido\Grid,
	Ublaboo\DataGrid\DataGrid,
	Ublaboo\DataGrid\Exception\DataGridException,
	Mepatek,
	Mepatek\Repository\IRepository,
	Nette\Database\Table\Selection;
use Nette\Localization\ITranslator;
use Ublaboo\DataGrid\DataSource\ArrayDataSource;
use Ublaboo\DataGrid\DataSource\DoctrineDataSource;
use Ublaboo\DataGrid\DataSource\NetteDatabaseTableDataSource;

/**
 * Class GridFactory
 * @package App\Components\UI
 */
class GridFactory
{

	/** @var ITranslator */
	private $translator;

	/** @var string */
	private $defaultGrid;

	/**
	 * GridFactory constructor.
	 *
	 * @param ITranslator $translator
	 */
	public function __construct($translator, $defaultGrid = "Ublaboo")
	{
		$this->translator = $translator;
		$this->defaultGrid = $defaultGrid;
	}


	/**
	 * Create grid UI component (default is Grido)
	 *
	 * @param array|IRepository|Selection          $data
	 * @param string                               $primaryKey
	 * @param integer                              $perPage
	 * @param array                                $permanentlyFilter
	 * @param Nette\ComponentModel\IContainer|null $parent
	 * @param null|string                          $name
	 *
	 * @return Grid|DataGrid
	 */
	public function create($data = null, $primaryKey = null, $perPage = null, $permanentlyFilter = [], $parent = null, $name = null)
	{
		switch ($this->defaultGrid) {
			case "Grido":
				return $this->createGrido($data, $primaryKey, $perPage, $permanentlyFilter);
				break;
			case "Ublaboo":
				return $this->createUblaboo($data, $primaryKey, $perPage, $permanentlyFilter, $parent, $name);
				break;
		}
	}

	/**
	 * Create grid UI component Grido\Grid
	 *
	 * @param array|IRepository|Selection $data
	 * @param string                      $primaryKey
	 * @param integer                     $perPage
	 * @param array                       $permanentlyFilter
	 *
	 * @return Grid
	 */
	public function createGrido($data = null, $primaryKey = null, $perPage = null, $permanentlyFilter = [])
	{
		$grid = new Grid();

		// set data model
		if ($data) {
			if ($data instanceof IRepository) {
				$dataModel = new Mepatek\Components\Grido\DataSources\RepositorySource($data);
				$dataModel->setPermanentlyFilter($permanentlyFilter);
			} elseif ($data instanceof Selection) {
				$dataModel = new Grido\DataSources\NetteDatabase($data);
			} else {
				$dataModel = new Grido\DataSources\ArraySource($data);
			}
			$grid->setModel($dataModel);
		} else {
			$dataModel = new Grido\DataSources\ArraySource([]);
			$grid->setModel($dataModel);
		}
		// set primary key
		if ($primaryKey) {
			$grid->setPrimaryKey($primaryKey);
		}

		// set properties of grido
		$grid->filterRenderType = \Grido\Components\Filters\Filter::RENDER_INNER;
		if ($this->translator) {
			$grid->setTranslator($this->translator);
		}
		$grid->getTablePrototype()->class("table table-striped table-hover table-bordered dataTable");


		// set item per page
		if ($perPage) {
			$grid->setDefaultPerPage($perPage);
		}

		return $grid;
	}

	/**
	 * Create grid UI component (Ublaboo/DataGrid
	 *
	 * @param array|QueryBuilder|IRepository|Selection $data
	 * @param string                                   $primaryKey
	 * @param integer                                  $perPage
	 * @param array                                    $permanentlyFilter
	 * @param Nette\ComponentModel\IContainer|null     $parent
	 * @param string|null                              $name
	 *
	 * @return DataGrid
	 * @throws DataGridException
	 */
	public function createUblaboo($data = null, $primaryKey = null, $perPage = null, $permanentlyFilter = [], $parent = null, $name = null)
	{
		$grid = new DataGrid($parent, $name);

		// set primary key
		if ($primaryKey) {
			$grid->setPrimaryKey($primaryKey);
		}

		// set data source
		if ($data) {
			if ($data instanceof QueryBuilder) {
				$dataSource = new DoctrineDataSource($data, $primaryKey);
			} elseif ($data instanceof IRepository) {
				$dataSource = new Mepatek\Components\Ublaboo\DataSources\RepositorySource($data, $primaryKey);
				$dataSource->setPermanentlyFilter($permanentlyFilter);
			} elseif ($data instanceof Selection) {
				$dataSource = new NetteDatabaseTableDataSource($data, $primaryKey);
			} else {
				$dataSource = new ArrayDataSource($data);
			}
			$grid->setDataSource($dataSource);
		} else {
			$dataSource = new ArrayDataSource([]);
			$grid->setDataSource($dataSource);
		}

		// set properties of grid
		if ($this->translator) {
			$grid->setTranslator($this->translator);
		}

		//$grid->setEditableColumns();

		$grid->setRememberState(true);


		// set item per page
		if ($perPage) {
			$grid->per_page = $perPage;
		}

		return $grid;
	}
}
