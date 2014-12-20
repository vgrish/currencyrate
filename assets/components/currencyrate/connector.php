<?php

// For debug
ini_set('display_errors', 1);
ini_set('error_reporting', -1);

// Load MODX config
if (file_exists(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php')) {
	require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
} else {
	require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
}

/** @noinspection PhpIncludeInspection */
//require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . 'index.php';
/** @var currencyrate $currencyrate */
$currencyrate = $modx->getService('currencyrate', 'currencyrate', $modx->getOption('currencyrate_core_path', null, $modx->getOption('core_path') . 'components/currencyrate/') . 'model/currencyrate/');
$modx->lexicon->load('currencyrate:default');

// handle request
$corePath = $modx->getOption('currencyrate_core_path', null, $modx->getOption('core_path') . 'components/currencyrate/');
$path = $modx->getOption('processorsPath', $currencyrate->config, $corePath . 'processors/');
$modx->request->handleRequest(array(
	'processors_path' => $path,
	'location' => '',
));