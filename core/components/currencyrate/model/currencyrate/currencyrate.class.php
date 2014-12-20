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

	public function getRate($charcode = '')
	{
		$url = $this->modx->getOption('currencyrate_url', '', 'http://www.cbr.ru/scripts/XML_daily.asp');
		$request = urlencode($url);
		if (function_exists('curl_init')) {
			$timeout = $this->modx->getOption('currencyrate_timeout', '', 2);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $request);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			$result = curl_exec($ch);
		} else {
			$result = file_get_contents($request);
		}

		return $result;

	}

}