<?php

/**
 * Get a list of Items
 */
class CRGetListProcessor extends modObjectGetListProcessor {
	public $objectType = 'CRlist';
	public $classKey = 'CRlist';
	public $defaultSortField = 'rank';
	public $defaultSortDirection  = 'asc';

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