<?php

require_once MODX_CORE_PATH.'model/modx/modprocessor.class.php';
class CRUpdateFromGridProcessor extends modObjectUpdateProcessor {
	public $objectType = 'CRlist';
	public $classKey = 'CRlist';
	//public $primaryKeyField = 'resource_id';
	public $permission = '';

	public function initialize() {
		$data = $this->getProperty('data');
		if (empty($data)) {
			return $this->modx->lexicon('invalid_data');
		}
		$data = $this->modx->fromJSON($data);
		if (empty($data)) {
			return $this->modx->lexicon('invalid_data');
		}

		$data['valuerate'] =

		$this->setProperties($data);
		$this->unsetProperty('data');
		return parent::initialize();
	}

	public function fireAfterSaveEvent() {
		if (!empty($this->afterSaveEvent)) {

			/*$this->modx->invokeEvent($this->afterSaveEvent,array(
				'mode' => modSystemEvent::MODE_UPD,
				$this->primaryKeyField => $this->object->get($this->primaryKeyField),
				$this->objectType => &$this->object,
			));*/
		}
	}

}
return 'CRUpdateFromGridProcessor';