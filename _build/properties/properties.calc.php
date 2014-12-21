<?php

$properties = array();

$tmp = array(
	'input' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'multiplier' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'divider' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'format' => array(
		'type' => 'textfield',
		'value' => '[2, ".", " "]',
	),
	'noZeros' => array(
		'type' => 'combo-boolean',
		'value' => true,
	),
	'toPlaceholder' => array(
		'type' => 'textfield'
		,'value' => ''
	),

);

foreach ($tmp as $k => $v) {
	$properties[] = array_merge(
		array(
			'name' => $k,
			'desc' => PKG_NAME_LOWER . '_prop_' . $k,
			'lexicon' => PKG_NAME_LOWER . ':properties',
		), $v
	);
}

return $properties;