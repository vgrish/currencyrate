<?php

if ($object->xpdo) {
    /** @var modX $modx */
    $modx =& $object->xpdo;

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $modelPath = $modx->getOption('currencyrate_core_path', null,
                    $modx->getOption('core_path') . 'components/currencyrate/') . 'model/';
            $modx->addPackage('currencyrate', $modelPath);

            $manager = $modx->getManager();
            $objects = array(
                'CRlist',
            );
            foreach ($objects as $tmp) {
                $manager->createObjectContainer($tmp);
            }

            $level = $modx->getLogLevel();

            $modx->setLogLevel(xPDO::LOG_LEVEL_FATAL);
            $manager->addField('CRlist', 'active', array('after' => 'valuerate'));
            $manager->addField('CRlist', 'rank', array('after' => 'active'));

            $modx->setLogLevel($level);

            break;

        case xPDOTransport::ACTION_UNINSTALL:
            break;
    }
}
return true;
