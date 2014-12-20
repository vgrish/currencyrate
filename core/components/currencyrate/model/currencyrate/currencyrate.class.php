<?php

/**
 * The base class for currencyrate.
 */
class currencyrate
{
	/* @var modX $modx */
	public $modx;
	public $namespace = 'currencyrate';
	public $cache = null;
	public $config = array();

	protected $list = array();

	/**
	 * @param modX $modx
	 * @param array $config
	 */
	function __construct(modX &$modx, array $config = array())
	{
		$this->modx =& $modx;

		$this->namespace = $this->getOption('currencyrate', $config, 'currencyrate');
		$corePath = $this->modx->getOption('currencyrate_core_path', $config, $this->modx->getOption('core_path') . 'components/currencyrate/');
		$assetsUrl = $this->modx->getOption('currencyrate_assets_url', $config, $this->modx->getOption('assets_url') . 'components/currencyrate/');
		$connectorUrl = $assetsUrl . 'connector.php';

		$this->config = array_merge(array(
			'assetsUrl' => $assetsUrl,
			'cssUrl' => $assetsUrl . 'css/',
			'jsUrl' => $assetsUrl . 'js/',
			'imagesUrl' => $assetsUrl . 'images/',
			'connectorUrl' => $connectorUrl,

			'corePath' => $corePath,
			'modelPath' => $corePath . 'model/',
			'chunksPath' => $corePath . 'elements/chunks/',
			'templatesPath' => $corePath . 'elements/templates/',
			'chunkSuffix' => '.chunk.tpl',
			'snippetsPath' => $corePath . 'elements/snippets/',
			'processorsPath' => $corePath . 'processors/'
		), $config);

		$this->modx->addPackage('currencyrate', $this->config['modelPath']);
		$this->modx->lexicon->load('currencyrate:default');
	}

	/**
	 * @param $key
	 * @param array $config
	 * @param null $default
	 * @return mixed|null
	 */
	public function getOption($key, $config = array(), $default = null)
	{
		$option = $default;
		if (!empty($key) && is_string($key)) {
			if ($config != null && array_key_exists($key, $config)) {
				$option = $config[$key];
			} elseif (array_key_exists($key, $this->config)) {
				$option = $this->config[$key];
			} elseif (array_key_exists("{$this->namespace}.{$key}", $this->modx->config)) {
				$option = $this->modx->getOption("{$this->namespace}.{$key}");
			}
		}
		return $option;
	}

	/**
	 * формируем массив валют
	 *
	 * @return bool
	 */
	public function loadRate()
	{
		$xml = new DOMDocument();
		$url = $this->modx->getOption('currencyrate_url', '', 'http://www.cbr.ru/scripts/XML_daily.asp?date_req=') . date('d/m/Y');
		/*		if (function_exists('curl_init')) {
					$timeout = $this->modx->getOption('currencyrate_timeout', '', 5);
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $request);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
					curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
					$result = curl_exec($ch);
					curl_close ($ch);
				} else {
					$result = file_get_contents($request);
				}*/
		$this->modx->log(1, print_r($url , 1));

		if (@$xml->load($url)) {
			$this->list = array();
			$root = $xml->documentElement;
			$items = $root->getElementsByTagName('Valute');

			foreach ($items as $item) {
				$numcode = $item->getElementsByTagName('NumCode')->item(0)->nodeValue;
				$charcode = $item->getElementsByTagName('CharCode')->item(0)->nodeValue;
				$nominal = $item->getElementsByTagName('Nominal')->item(0)->nodeValue;
				$name = $item->getElementsByTagName('Name')->item(0)->nodeValue;
				$value = $item->getElementsByTagName('Value')->item(0)->nodeValue;
				$value = floatval(str_replace(',', '.', $value));

				$this->list[] = array(
					'numcode' => $numcode,
					'charcode' => $charcode,
					'nominal' => $nominal,
					'name' => $name,
					'value' => $value,
				);
			}


			$this->modx->log(1, print_r($this->list , 1));

			return true;
		} else
			return false;
	}

	public function rateIntoDb()
	{
		if ($this->loadRate()) {

			$this->modx->log(1, print_r('run' , 1));

			foreach($this->list as $item) {

				$this->modx->log(1, print_r($item , 1));
			}
		}
		else {
			$this->modx->log(1, print_r('NO run' , 1));
		}

	}

}