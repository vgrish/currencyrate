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
	public $active = false;
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
		$this->active = $this->modx->getOption('currencyrate_active', $config, false);

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
			return true;
		} else
			return false;
	}

	/**
	 * @return bool
	 */
	public function rateIntoDb()
	{
		if ($this->loadRate()) {
			foreach($this->list as $item) {
				if(!$itemFromDb = $this->modx->getObject('CRlist', array('numcode' => $item['numcode']))) {
					$itemFromDb = $this->modx->newObject('CRlist');
				}
				$item['rate'] = $itemFromDb->get('rate');
				$item = $this->calcData($item);
				$itemFromDb->fromArray($item);
				if(!$itemFromDb->save()) $this->modx->log(1, print_r('[CR:Error] save to db for charcode - '.$item['charcode'] , 1));
			}
			return true;
		}
		$this->modx->log(1, print_r('[CR:Error] NO loadRate()', 1));
		return false;
	}

	/**
	 * @param $sp
	 */
	public function OnHandleRequest($sp)
	{
		$list = $this->getList();
		$this->modx->setPlaceholders($list, '+');
	}

	/**
	 * @param $sp
	 */
	public function OnBeforeCacheUpdate($sp) {
		$cacheOptions = array(xPDO::OPT_CACHE_KEY => 'crlist');
		$this->modx->cacheManager->clean($cacheOptions);
		$this->modx->log(modX::LOG_LEVEL_INFO, '[CR:Info] Clearing the cache. Path: crlist');
	}

	/**
	 * @param array $data
	 * @return array
	 */
	public function calcData($data = array()) {
		if(empty($data['nominal'])) $data['nominal'] = 1;
		$data['valuerate'] = round(($data['value'] / $data['nominal']), 4);
		$valuerate = $this->calcValueRate($data['valuerate'], $data['rate']);
		if ((float)$valuerate == (float)$data['valuerate']) {
			$data['rate'] = '';
		}
		$data['valuerate'] = $valuerate;
		return $data;
	}

	/**
	 * @param $value
	 * @param $rate
	 * @return float
	 */
	public function calcValueRate($value, $rate) {
		if (preg_match('/%$/', $rate)) {
			$rate = str_replace('%', '', $rate);
			$rate = $value / 100 * $rate;
		}
		$value += $rate;
		return round($value, 4);
	}

	/**
	 * @param string $message
	 * @param array $data
	 * @param array $placeholders
	 * @return array|string
	 */
	public function error($message = '', $data = array(), $placeholders = array())
	{
		$response = array(
			'success' => false,
			'message' => $this->modx->lexicon($message, $placeholders),
			'data' => $data,
		);
		return $this->config['json_response']
			? $this->modx->toJSON($response)
			: $response;
	}

	/**
	 * @param string $message
	 * @param array $data
	 * @param array $placeholders
	 * @return array|string
	 */
	public function success($message = '', $data = array(), $placeholders = array())
	{
		$response = array(
			'success' => true,
			'message' => $this->modx->lexicon($message, $placeholders),
			'data' => $data,
		);
		return $this->config['json_response']
			? $this->modx->toJSON($response)
			: $response;
	}

	/**
	 * from https://github.com/Mark-H/ClientConfig/blob/master/core/components/clientconfig/model/clientconfig/clientconfig.class.php#L75
	 *
	 * Grab settings (from cache if possible) as key => value pairs.
	 * @return array|mixed
	 */
	public function getList() {
		/* Attempt to get from cache */
		$cacheOptions = array(xPDO::OPT_CACHE_KEY => 'crlist');
		$list = $this->modx->getCacheManager()->get('crlist', $cacheOptions);
		if (empty($list) && $this->modx->getCount('CRlist') > 0) {
			$collection = $this->modx->getCollection('CRlist');
			$list = array();
			/* @var CRlist $setting */
			foreach ($collection as $value) {
				$list[$value->get('charcode')] = $value->get('valuerate');
			}
			/* Write to cache again */
			$this->modx->cacheManager->set('crlist', $list, 0, $cacheOptions);
		}
		return (is_array($list)) ? $list : array();
	}

	/**
	 * Function for formatting price
	 *
	 * @param string $price
	 * @param string $format
	 * @param bool $no_zeros
	 * @return mixed|string
	 */
	public function formatPrice($price = '0', $format = '[2, ".", " "]', $no_zeros = true) {
		$pf = $this->modx->fromJSON($format);
		if (is_array($pf)) {
			$price = number_format($price, $pf[0], $pf[1], $pf[2]);
		}
		if ($no_zeros) {
			if (preg_match('/\..*$/', $price, $matches)) {
				$tmp = rtrim($matches[0], '.0');
				$price = str_replace($matches[0], $tmp, $price);
			}
		}
		return $price;
	}

}