<?php

/**
 * Create an Item
 */
class CRCreateProcessor extends modObjectCreateProcessor {
	public $objectType = 'CRlist';
	public $classKey = 'CRlist';
	public $languageTopics = array('currencyrate');
	//public $permission = 'create';

	public function process() {

		$this->modx->log(1, print_r('deferfe' , 1));

		if (!$this->modx->currencyrate->rateIntoDb()) return $this->modx->currencyrate->error('pas_save_setting_err');
		return $this->modx->currencyrate->success('');
	}

}

return 'CRCreateProcessor';