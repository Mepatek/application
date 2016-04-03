<?php

namespace Mepatek\Components\UI;

use Nette\Application\UI\Control,
	Mepatek\Entity\AbstractEntity;

class RenderEntity extends Control
{

	/**
	 * @param string $class
	 * @return \Nette\Templating\FileTemplate
	 * @internal
	 */
	public function createTemplate($class = NULL)
	{
		$template = parent::createTemplate($class);
		$template->setFile(__DIR__ . '/RenderEntity.latte');
		//$template->registerHelper('translate', callback($this->getTranslator(), 'translate'));

		return $template;
	}

	/**
	 *
	 */
	public function render($entity)
	{
		$fields = [];
		if (is_array($entity)) {
			$aFields = $entity[1];
			$entity = $entity[0];
			if ($entity) {
				foreach($aFields as $field=>$label) {
					$fields[$label] = $entity->$field;
				}
			}
		} else {
			$fields = iterator_to_array($entity);
		}
		if ($entity) {
			// vložíme do šablony parametry
			$this->template->entity = $entity;
			$this->template->fields = $fields;
			// a vykreslíme ji
			$this->template->render();
		}
	}
}