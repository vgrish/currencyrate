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
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$Referral = $modx->getService('referral', 'Referral', $modx->getOption('referral_core_path', null, $modx->getOption('core_path') . 'components/referral/') . 'model/referral/');
$modx->lexicon->load('referral:default');

$currencyrate->rateIntoDb(); 