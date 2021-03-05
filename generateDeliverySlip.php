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
$id_order = Tools::getValue('id_order');
$order = new Order((int) $id_order);
$order_invoice_collection = $order->getInvoicesCollection();
//dump($order_invoice_collection);
//echo PDF::TEMPLATE_DELIVERY_SLIP;
$pdf = new PDF($order_invoice_collection, PDF::TEMPLATE_DELIVERY_SLIP, Context::getContext()->smarty);
$pdf->render();
