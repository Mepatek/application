<?php

namespace Mepatek\Components\UI;

use Grido,
	Grido\Grid,
	Mepatek\Components\Grido\DataSources\RepositorySource,
	Mepatek\Repository\IRepository,
	Nette\Database\Table\Selection;

/**
 * Class GridFactory
 * @package App\Components\UI
 */
class GridFactory
{
	/**
	 * Create grid UI component
	 *
	 * @param array|IRepository|Selection $data
	 * @param string                      $primaryKey
	 * @param integer                     $perPage
	 *
	 * @return Grid
	 */
	public function create($data = null, $primaryKey = null, $perPage = null)
	{
		$grid = new Grid();

		// set data model
		if ($data) {
			if ($data instanceof IRepository) {
				$dataModel = new RepositorySource($data);
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
		$grid->filterRenderType = Grido\Components\Filters\Filter::RENDER_INNER;
		$grid->translator->lang = 'en';
		$grid->getTablePrototype()->class("table table-striped table-hover table-bordered dataTable");

		//$grid->setEditableColumns();
		//$grid->setRememberState(true);


		// set item per page
		if ($perPage) {
			$grid->setDefaultPerPage($perPage);
		}

		return $grid;
	}
}