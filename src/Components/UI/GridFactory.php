<?php

namespace Mepatek\Components\UI;

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
use Ublaboo\DataGrid\DataSource\NetteDatabaseTableDataSource;

/**
 * Class GridFactory
 * @package App\Components\UI
 */
class GridFactory
{

	/** @var ITranslator */
	private $translator;

	/**
	 * GridFactory constructor.
	 *
	 * @param ITranslator $translator
	 */
	public function __construct(ITranslator $translator, $defaultGrid = "Grido")
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
	 * @param Nette\ComponentModel\IContainer|null $parent
	 * @param null|string                          $name
	 *
	 * @return Grid|DataGrid
	 */
	public function create($data = null, $primaryKey = null, $perPage = null, $parent = null, $name = null)
	{
		switch ($this->defaultGrid) {
			case "Grido":
				return $this->createGrido($data, $primaryKey, $perPage);
				break;
			case "Ublaboo":
				return $this->createUblaboo($parent, $name, $data, $primaryKey, $perPage);
				break;
		}
	}

	/**
	 * Create grid UI component Grido\Grid
	 *
	 * @param array|IRepository|Selection $data
	 * @param string                      $primaryKey
	 * @param integer                     $perPage
	 *
	 * @return Grid
	 */
	public function createGrido($data = null, $primaryKey = null, $perPage = null)
	{
		$grid = new Grid();

		// set data model
		if ($data) {
			if ($data instanceof IRepository) {
				$dataModel = new Mepatek\Components\Grido\DataSources\RepositorySource($data);
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
	 * @param array|IRepository|Selection          $data
	 * @param string                               $primaryKey
	 * @param integer                              $perPage
	 * @param Nette\ComponentModel\IContainer|null $parent
	 * @param string|null                          $name
	 *
	 * @return DataGrid
	 * @throws DataGridException
	 */
	public function createUblaboo($data = null, $primaryKey = null, $perPage = null, $parent = null, $name = null)
	{
		$grid = new DataGrid($parent, $name);

		// set primary key
		if ($primaryKey) {
			$grid->setPrimaryKey($primaryKey);
		}

		// set data source
		if ($data) {
			if ($data instanceof IRepository) {
				$dataSource = new Mepatek\Components\Ublaboo\DataSources\RepositorySource($data, $primaryKey);
			} elseif ($data instanceof Selection) {
				$dataSource = new NetteDatabaseTableDataSource($data);
			} else {
				$dataSource = new ArrayDataSource($data);
			}
			$grid->setDataSource($dataSource);
		} else {
			$dataSource = new Grido\DataSources\ArrayDataSource([]);
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