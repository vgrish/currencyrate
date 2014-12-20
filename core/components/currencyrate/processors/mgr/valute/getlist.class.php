<?php

/**
 * Get a list of Items
 */
class CRGetListProcessor extends modObjectGetListProcessor {
	public $objectType = 'CRlist';
	public $classKey = 'CRlist';
	public $defaultSortField = 'id';
	public $defaultSortDirection = 'ASC';

	/**
	 * @param xPDOQuery $c
	 *
	 * @return xPDOQuery
	 */
/*	public function prepareQueryBeforeCount(xPDOQuery $c) {
		$query = trim($this->getProperty('query'));
		if ($query) {
			$c->where(array(
				'name:LIKE' => "%{$query}%",
				'OR:description:LIKE' => "%{$query}%",
			));
		}

		return $c;
	}*/


	/**
	 * @param xPDOObject $object
	 *
	 * @return array
	 */
	public function prepareRow(xPDOObject $object) {
		$array = $object->toArray();

		return $array;
	}

}

return 'CRGetListProcessor';