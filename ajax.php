<?php

require_once dirname(__FILE__) . '/../../config/config.inc.php';
require_once dirname(__FILE__) . '/../../init.php';
require_once dirname(__FILE__) . '/ec_reliquat.php';
if (Tools::getValue('token') != Configuration::get('EC_RELIQUAT_TOKEN')) {
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    Tools::redirect('Location: ../');
    exit();
}
$mod = new Ec_reliquat();
switch ((int) Tools::getValue('majsel')) {
    case 1:
        echo $mod->updateReliquat(Tools::getValue('id_reliquat'), Tools::getValue('tracking_number'), Tools::getValue('id_carrier'), Tools::getValue('id_order_state'), Tools::getValue('id_order'));
        echo true;
        break;
    case 2:
        echo $mod->deleteAttachment(Tools::getValue('cle'));
        break;
    default:
        break;
}
