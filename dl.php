<?php

require_once dirname(__FILE__) . '/../../config/config.inc.php';
if (Tools::getValue('token') != Configuration::get('EC_RELIQUAT_TOKEN')) {
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    Tools::redirect('Location: ../');
    exit();
}
$key = Tools::getValue('k');
$file = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'ec_reliquat_attachment WHERE cle = "'.pSQL($key).'"');
$upload_dir = dirname(__FILE__).'/files/';

if ($file == false) {
    Tools::redirect('index.php');
}
if (Tools::getValue('front') == 1) {
    Db::getinstance()->update('ec_reliquat_attachment', array('date_download' => pSQL(date('Y-m-d H:i:s'))), 'cle = "'.pSQL($key).'"');
}
if (ob_get_level() && ob_get_length() > 0) {
    ob_end_clean();
}
header('Content-Transfer-Encoding: binary');
header('Content-Type: ' . $file['type']);
header('Content-Length: ' . filesize($upload_dir. $file['cle']));
header('Content-Disposition: attachment; filename="' . utf8_decode($file['name']) .'.'. utf8_decode($file['extension']) . '"');


readfile($upload_dir. $file['cle']);
