<?php

/**
 * Class currencyrateMainController
 */
abstract class currencyrateMainController extends modExtraManagerController {
	/** @var currencyrate $currencyrate */
	public $currencyrate;


	/**
	 * @return void
	 */
	public function initialize() {
		$version = $this->modx->getVersionData();
		$modx23 = !empty($version) && version_compare($version['full_version'], '2.3.0', '>=');
		if (!$modx23) {
			$this->addCss(MODX_ASSETS_URL . 'components/currencyrate/css/mgr/font-awesome.min.css');
		}

		$corePath = $this->modx->getOption('currencyrate_core_path', null, $this->modx->getOption('core_path') . 'components/currencyrate/');
		require_once $corePath . 'model/currencyrate/currencyrate.class.php';

		$this->currencyrate = new currencyrate($this->modx);
		$this->addCss($this->currencyrate->config['cssUrl'] . 'mgr/main.css');
		$this->addJavascript($this->currencyrate->config['jsUrl'] . 'mgr/currencyrate.js');
		$this->addHtml('
		<script type="text/javascript">
			MODx.modx23 = ' . (int)$modx23 . ';
			currencyrate.config = ' . $this->modx->toJSON($this->currencyrate->config) . ';
			currencyrate.config.connector_url = "' . $this->currencyrate->config['connectorUrl'] . '";
		</script>
		');

		parent::initialize();
	}


	/**
	 * @return array
	 */
	public function getLanguageTopics() {
		return array('currencyrate:default');
	}


	/**
	 * @return bool
	 */
	public function checkPermissions() {
		return true;
	}
}


/**
 * Class IndexManagerController
 */
class IndexManagerController extends currencyrateMainController {

	/**
	 * @return string
	 */
	public static function getDefaultController() {
		return 'home';
	}
}