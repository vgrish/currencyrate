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