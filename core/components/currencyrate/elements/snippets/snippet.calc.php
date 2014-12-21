<?php
/** @var array $scriptProperties */
/** @var currencyrate $currencyrate */
if (!$currencyrate = $modx->getService('currencyrate', 'currencyrate', $modx->getOption('currencyrate_core_path', null, $modx->getOption('core_path') . 'components/currencyrate/') . 'model/currencyrate/', $scriptProperties)) {
	return 'Could not load currencyrate class!';
}
if(empty($input)) {return '';}
if(!empty($multiplier)) {
	$output = $currencyrate->formatPrice(($input * $multiplier), $format, $noZeros);
}
if(!empty($divider)) {
	$output = $currencyrate->formatPrice(($input / $divider), $format, $noZeros);
}

if (!empty($toPlaceholder)) {
	$modx->setPlaceholder($toPlaceholder, $output);
}
else {
	return $output;
}