<?php
// For debug
ini_set('display_errors', 1);
ini_set('error_reporting', -1);
ini_set('apc.cache_by_default', 'Off');

if(!isset($_REQUEST['ctx']) OR !in_array($_REQUEST['ctx'], array('web'))){
	$_REQUEST['ctx'] = 'web';
}

// Load MODX config
if (file_exists(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php')) {
	require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
} else {
	require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
}
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$modx->getService('error','error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);

if(!isset($_REQUEST['password']) || ($_REQUEST['password'] !== $modx->getOption('currencyrate_password'))) {
	$modx->log(1, print_r('[CR:Error] password incorrect : '. $_REQUEST['password'], 1));
	return;
}

$currencyrate = $modx->getService('currencyrate', 'currencyrate', $modx->getOption('currencyrate_core_path', null, $modx->getOption('core_path') . 'components/currencyrate/') . 'model/currencyrate/');
if (!($currencyrate instanceof currencyrate)) return '';

if($currencyrate->rateIntoDb())
	$modx->log(1, print_r('[CR:Success] currency exchange rate updated. Time: '. date('Y-m-d H:i:s'), 1));
else
	$modx->log(1, print_r('[CR:Error] NO updated. Time: '. date('Y-m-d H:i:s'), 1));