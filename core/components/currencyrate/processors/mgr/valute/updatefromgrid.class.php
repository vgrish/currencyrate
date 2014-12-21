<?php

require_once MODX_CORE_PATH.'model/modx/modprocessor.class.php';
class CRUpdateFromGridProcessor extends modObjectUpdateProcessor {
	public $objectType = 'CRlist';
	public $classKey = 'CRlist';
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
		$data = $this->modx->currencyrate->calcData($data);
		$this->setProperties($data);
		$this->unsetProperty('data');
		return parent::initialize();
	}

}
return 'CRUpdateFromGridProcessor';