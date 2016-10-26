<?php

$properties = array();

$tmp = array(
    'tplRow'          => array(
        'type'  => 'textfield',
        'value' => 'tpl.crList.row',
    ),
    'tplOuter'        => array(
        'type'  => 'textfield',
        'value' => 'tpl.crList.outer',
    ),
    'tplEmpty'        => array(
        'type'  => 'textfield',
        'value' => '',
    ),
    'selected'        => array(
        'type'  => 'textfield',
        'value' => '',
    ),
    'showInactive'    => array(
        'type'  => 'combo-boolean',
        'value' => false
    ),
    'outputSeparator' => array(
        'type'  => 'textfield',
        'value' => "\n",
    ),
    'toPlaceholder'   => array(
        'type'  => 'textfield'
    ,
        'value' => ''
    ),

);

foreach ($tmp as $k => $v) {
    $properties[] = array_merge(
        array(
            'name'    => $k,
            'desc'    => PKG_NAME_LOWER . '_prop_' . $k,
            'lexicon' => PKG_NAME_LOWER . ':properties',
        ), $v
    );
}

return $properties;