<?php

$settings = array();

$tmp = array(

	'active' => array(
		'xtype' => 'combo-boolean',
		'value' => true,
		'area' => 'currencyrate_main',
	),
	'url' => array(
		'xtype' => 'textfield',
		'value' => 'http://www.cbr.ru/scripts/XML_daily.asp?date_req=',
		'area' => 'currencyrate_main',
	),

	//временные
/*
			'assets_path' => array(
				'xtype' => 'textfield',
				'value' => '{base_path}currencyrate/assets/components/currencyrate/',
				'area' => 'currencyrate_temp',
			),
			'assets_url' => array(
				'xtype' => 'textfield',
				'value' => '/currencyrate/assets/components/currencyrate/',
				'area' => 'currencyrate_temp',
			),
			'core_path' => array(
				'xtype' => 'textfield',
				'value' => '{base_path}currencyrate/core/components/currencyrate/',
				'area' => 'currencyrate_temp',
			),
*/

	//временные

		/*
	'some_setting' => array(
		'xtype' => 'combo-boolean',
		'value' => true,
		'area' => 'currencyrate_main',
	),
	*/
);

foreach ($tmp as $k => $v) {
	/* @var modSystemSetting $setting */
	$setting = $modx->newObject('modSystemSetting');
	$setting->fromArray(array_merge(
		array(
			'key' => 'currencyrate_' . $k,
			'namespace' => PKG_NAME_LOWER,
		), $v
	), '', true, true);

	$settings[] = $setting;
}

unset($tmp);
return $settings;
