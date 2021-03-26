<?php
/**
* 2007-2019 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Ec_reliquat extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'ec_reliquat';
        $this->tab = 'administration';
        $this->version = '2.0.0';
        $this->author = 'Ether Creation';
        $this->need_instance = 0;
        $this->upload_path = dirname(__FILE__) .'/files/';
        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Partial delivery');
        $this->description = $this->l('This module allows you to manage partial delivery');
        $this->module_key = 'c365b2711a4abb582e590e7c6926db65';
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Db::getInstance()->execute(
            '
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_reliquat` (
            `id_reliquat` INT AUTO_INCREMENT,
            `id_order` INT,
            `id_order_state` INT,
            `id_carrier` INT,
            `tracking_number` VARCHAR(50),
            `date_add` DATETIME,
            PRIMARY KEY (`id_reliquat`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
    );

        Db::getInstance()->execute(
            '
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_reliquat_product` (
            `id_reliquat_product` INT AUTO_INCREMENT,
            `id_reliquat` INT,
            `id_order_detail` INT,
            `quantity` INT,
            PRIMARY KEY (`id_reliquat_product`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
    );

        Db::getInstance()->execute(
            '
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ec_reliquat_attachment` (
            `id_reliquat_attachment` INT AUTO_INCREMENT,
            `id_reliquat` INT,
            `name` VARCHAR(255),
            `extension` VARCHAR(20),
            `cle` VARCHAR(255),
            `type` VARCHAR(255),
            `date_add` DATETIME,
            `date_download`DATETIME,
            PRIMARY KEY (`id_reliquat_attachment`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
    );
        Configuration::updateValue('EC_RELIQUAT_TOKEN', md5(time() . _COOKIE_KEY_));
        $this->createStatuses();
        $res =parent::install() &&
        $this->registerHook('displayOrderDetail') &&
        $this->registerHook('header') &&
        $this->registerHook('ActionAdminControllerSetMedia');
        if (version_compare(_PS_VERSION_, '1.7.7', '<')) {
            $this->registerHook('AdminOrder');
        } else {
            $this->registerHook('displayAdminOrderMain');
        }
        return $res;
    }

    public function uninstall()
    {
        return parent::uninstall();
    }
    
    public function createStatuses()
    {
        $partial_shipment = new OrderState();
        $partial_shipment->color = '#8A2BE2';
        
        $complete_shipment = new OrderState();
        $complete_shipment->color = '#108510';
        
        $process_in_progress = new OrderState();
        $process_in_progress->color = '#FF8C00';
        
        $shipped = new OrderState();
        $shipped->color = '#8A2BE2';
        
        $delivered = new OrderState();
        $delivered->color = '#108510';
        
        foreach (Language::getLanguages() as $lang) {
            if ($lang['iso_code'] == 'fr') {
                $partial_shipment->name[$lang['id_lang']] = 'Expédition partielle';
                $complete_shipment->name[$lang['id_lang']] = 'Expédition complète';
                $process_in_progress->name[$lang['id_lang']] = 'Reliquat - En cours de préparation';
                $shipped->name[$lang['id_lang']] = 'Reliquat - Expédié';
                $delivered->name[$lang['id_lang']] = 'Reliquat - Livré';
            } else {
                $partial_shipment->name[$lang['id_lang']] = 'Partial shipment';
                $complete_shipment->name[$lang['id_lang']] = 'Complete shipment';
                $process_in_progress->name[$lang['id_lang']] = 'Reliquat - Processing in progress';
                $shipped->name[$lang['id_lang']] = 'Reliquat - Shipped';
                $delivered->name[$lang['id_lang']] = 'Reliquat - Delivered';
            }
        }
        $partial_shipment->save();
        $complete_shipment->save();
        $process_in_progress->save();
        $shipped->save();
        $delivered->save();
        Configuration::updateValue('EC_RELIQUAT_PARTIAL', $partial_shipment->id);
        Configuration::updateValue('EC_RELIQUAT_COMPLETE', $complete_shipment->id);
        Configuration::updateValue('EC_RELIQUAT_PROCESS', $process_in_progress->id);
        Configuration::updateValue('EC_RELIQUAT_SHIPPED', $shipped->id);
        Configuration::updateValue('EC_RELIQUAT_DELIVERED', $delivered->id);
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
    /*     dump($_SERVER);
        $id_order = 5;
        $info_uri = array(
            'route' => 'admin_orders_view',
            'orderId' => $id_order,
        );
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminOrders', true, $info_uri));
        exit(); */
       /*  echo $this->registerHook('displayAdminOrderMain');
       exit(); */
        /**
         * If values have been submitted in the form, process.
         */
        $html = '';
        if (((bool)Tools::isSubmit('submitEc_reliquatModule')) == true) {
            $this->postProcess();
            $html .= $this->displayConfirmation($this->l('Successful update'));
        }

        return $html.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitEc_reliquatModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
        .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        $order_states = OrderState::getOrderStates((int)$this->context->language->id);
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('New status (partial shipment)'),
                        //'desc' => $this->l('Choose a carrier'),
                        'name' => 'EC_RELIQUAT_PARTIAL',
                        'id' => 'test',
                        'required' => true,
                        'options' => array(
                            'query' => $order_states,
                            'id' => 'id_order_state',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('New status (Complete shipment)'),
                        //'desc' => $this->l('Choose a carrier'),
                        'name' => 'EC_RELIQUAT_COMPLETE',
                        'required' => true,
                        'options' => array(
                            'query' => $order_states,
                            'id' => 'id_order_state',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('New status (Reliquat - Processing in progress)'),
                        //'desc' => $this->l('Choose a carrier'),
                        'name' => 'EC_RELIQUAT_PROCESS',
                        'id' => 'test',
                        'required' => true,
                        'options' => array(
                            'query' => $order_states,
                            'id' => 'id_order_state',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('New status (Reliquat - Shipped)'),
                        //'desc' => $this->l('Choose a carrier'),
                        'name' => 'EC_RELIQUAT_SHIPPED',
                        'id' => 'test',
                        'required' => true,
                        'options' => array(
                            'query' => $order_states,
                            'id' => 'id_order_state',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('New status (Reliquat - Delivered)'),
                        //'desc' => $this->l('Choose a carrier'),
                        'name' => 'EC_RELIQUAT_DELIVERED',
                        'id' => 'test',
                        'required' => true,
                        'options' => array(
                            'query' => $order_states,
                            'id' => 'id_order_state',
                            'name' => 'name'
                        )
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'EC_RELIQUAT_PARTIAL' => Configuration::get('EC_RELIQUAT_PARTIAL'),
            'EC_RELIQUAT_COMPLETE' => Configuration::get('EC_RELIQUAT_COMPLETE'),
            'EC_RELIQUAT_PROCESS' => Configuration::get('EC_RELIQUAT_PROCESS'),
            'EC_RELIQUAT_SHIPPED' => Configuration::get('EC_RELIQUAT_SHIPPED'),
            'EC_RELIQUAT_DELIVERED' => Configuration::get('EC_RELIQUAT_DELIVERED'),
        );
    }


    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    public function hookDisplayAdminOrderMain($params)
    {
        $id_lang = (int)$this->context->language->id;
        $id_order = $params['id_order'];
        if (((bool)Tools::isSubmit('submitEcReliquatShip')) == true) {
            $products = Tools::getValue('products');
            $cpt_prod = 0;
            foreach ($products as $qty) {
                $cpt_prod+=$qty;
            }
            if ($cpt_prod > 0) {
                $this->addReliquat($id_order);
                $this->changeOrderState($id_order);
                $info_uri = array(
                    'route' => 'admin_orders_view',
                    'orderId' => $id_order,
                );
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminOrders', true, $info_uri));
                //Tools::redirect($this->context->link->getAdminLink('AdminOrders').'&vieworder&id_order='.$id_order);
            }
        }

        $prods = Db::getInstance()->executeS('SELECT id_order_detail FROM '._DB_PREFIX_.'order_detail WHERE id_order = '.(int)$id_order);
     /*   if (count($prods) == 1) {
            return;
        }*/
        $products = Db::getInstance()->executeS(
            '
            SELECT od.id_order_detail, product_id, product_attribute_id, product_name,product_supplier_reference, product_mpn, product_upc, product_ean13, product_quantity, product_reference,
            (SELECT sum(quantity) FROM '._DB_PREFIX_.'ec_reliquat_product erp WHERE erp.id_order_detail = od.id_order_detail)as qty_ship
            FROM '._DB_PREFIX_.'order_detail od
            WHERE id_order = '.(int)$id_order.'
            
            '
        );
        $id_shop = (int)$this->context->shop->id;
        foreach ($products as &$product) {
            $id_product = $product['product_id'];
            $id_product_attribute = $product['product_attribute_id'];
            $product['quantity_available'] = StockAvailable::getQuantityAvailableByProduct($id_product, $id_product_attribute, $id_shop);
            $product['class_badge'] = $product['product_quantity'] == $product['qty_ship'] ? 'success' : 'warning';
            $mpn =$product['product_mpn'];
            $product['warehouses'] = $this->productAvailableWh($mpn);
        }

        $id_lang = (int)$this->context->language->id;
        $order_states = Db::getInstance()->executeS('SELECT id_order_state, name FROM '._DB_PREFIX_.'order_state_lang WHERE id_lang = '.(int)$id_lang.' AND id_order_state IN ('.Configuration::get('EC_RELIQUAT_PROCESS').','.Configuration::get('EC_RELIQUAT_SHIPPED').','.Configuration::get('EC_RELIQUAT_DELIVERED').') order by id_order_state desc');
        $this->smarty->assign(array(
            'products' => $products,
            'url_form' => $_SERVER['REQUEST_URI'],
            'carriers' => Carrier::getCarriers($id_lang, true, false, false, null, 4),
            'order_states' => $order_states,
            'ec_id_order' => $id_order,
            'ec_base_uri' => Tools::getHttpHost(true).__PS_BASE_URI__,
        ));
        
        $html = $this->display(__FILE__, '/views/templates/admin/AdminOrder177.tpl');
        
        $reliquats = Db::getInstance()->executeS(
            '
            SELECT id_order, id_reliquat, osl.name as order_state, c.name as carrier, tracking_number, date_add, er.id_carrier, er.id_order_state, weight, total_shipping
            FROM '._DB_PREFIX_.'ec_reliquat er
            LEFT JOIN '._DB_PREFIX_.'carrier c ON (er.id_carrier = c.id_carrier)
            LEFT JOIN '._DB_PREFIX_.'order_state_lang osl ON (osl.id_order_state = er.id_order_state AND id_lang = '.(int)$id_lang.')
            WHERE id_order = '.(int)$id_order.'
            '
        );
        
        foreach ($reliquats as &$reliquat) {
            $reliquat['products'] = Db::getInstance()->executeS(
                '
                SELECT id_reliquat_product, quantity, product_id, product_attribute_id, product_name, product_reference
                FROM '._DB_PREFIX_.'ec_reliquat_product erp
                LEFT JOIN '._DB_PREFIX_.'order_detail od ON (od.id_order_detail = erp.id_order_detail)
                WHERE id_reliquat = '.(int)$reliquat['id_reliquat'].'
                '
            );
            $reliquat['attachments'] = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ec_reliquat_attachment WHERE id_reliquat = '.(int)$reliquat['id_reliquat']);
        }
        if ($reliquats) {
            var_dump($reliquats);

            $token = Configuration::get('EC_RELIQUAT_TOKEN');
            $this->smarty->assign(array(
                'reliquats' => $reliquats,
                'dl_script' => Tools::getHttpHost(true).__PS_BASE_URI__.'modules/ec_reliquat/dl.php?token='.$token,
                'link_delivery_slip' => Tools::getHttpHost(true).__PS_BASE_URI__.'modules/ec_reliquat/generateDeliverySlip.php?token='.$token,
                'ec_base_uri' => Tools::getHttpHost(true).__PS_BASE_URI__,
            ));
            $html .= $this->display(__FILE__, '/views/templates/admin/admin_reliquats177.tpl');
        }
        return $html;
    }

    public function productAvailableWh($mfg_pn)
    {
        $query = "SELECT id as warehouse_id, title, qty from geopos_products
        left join vu_items_summary on vu_items_summary.rg_id = geopos_products.item_sid 
        left join geopos_warehouse on  geopos_products.warehouse=  geopos_warehouse.id 
        WHERE  mfg_pn= '".$mfg_pn."' and qty > 0";
        $warehouses=  Db::getInstance()->executeS(
            ''.$query.''
        );
        return $warehouses;
    }

    public function hookAdminOrder($params)
    {
        if (version_compare(_PS_VERSION_, '1.7.7', '>=')) {
            return;
        } 
        $id_lang = (int)$this->context->language->id;
        $id_order = $params['id_order'];
        if (((bool)Tools::isSubmit('submitEcReliquatShip')) == true) {
            $products = Tools::getValue('products');
            $cpt_prod = 0;
            foreach ($products as $qty) {
                $cpt_prod+=$qty;
            }
            if ($cpt_prod > 0) {
                $this->addReliquat($id_order);
                $this->changeOrderState($id_order);
                Tools::redirect($this->context->link->getAdminLink('AdminOrders').'&vieworder&id_order='.$id_order);
            }
        }

        $prods = Db::getInstance()->executeS('SELECT id_order_detail FROM '._DB_PREFIX_.'order_detail WHERE id_order = '.(int)$id_order);
     /*   if (count($prods) == 1) {
            return;
        }*/
        $products = Db::getInstance()->executeS(
            '
            SELECT od.id_order_detail, product_id, product_attribute_id, product_name, product_quantity, product_reference, product_mpn,
            (SELECT sum(quantity) FROM '._DB_PREFIX_.'ec_reliquat_product erp WHERE erp.id_order_detail = od.id_order_detail)as qty_ship
            FROM '._DB_PREFIX_.'order_detail od
            WHERE id_order = '.(int)$id_order.'
            '
        );
        $id_shop = (int)$this->context->shop->id;
        foreach ($products as &$product) {

            $id_product = $product['product_id'];
            $id_product_attribute = $product['product_attribute_id'];
            $product['quantity_available'] = StockAvailable::getQuantityAvailableByProduct($id_product, $id_product_attribute, $id_shop);
            $product['class_badge'] = $product['product_quantity'] == $product['qty_ship'] ? 'success' : 'warning';
            $mpn =$product['product_mpn'];
            $product['warehouses'] = $this->productAvailableWh($mpn);

            //gets warehouses where product is available

        }

        $id_lang = (int)$this->context->language->id;
        $order_states = Db::getInstance()->executeS('SELECT id_order_state, name FROM '._DB_PREFIX_.'order_state_lang WHERE id_lang = '.(int)$id_lang.' AND id_order_state IN ('.Configuration::get('EC_RELIQUAT_PROCESS').','.Configuration::get('EC_RELIQUAT_SHIPPED').','.Configuration::get('EC_RELIQUAT_DELIVERED').')');
        $this->smarty->assign(array(
            'products' => $products,
            'url_form' => $_SERVER['REQUEST_URI'],
            'carriers' => Carrier::getCarriers($id_lang, true, false, false, null, 4),
            'order_states' => $order_states,
            'ec_id_order' => $id_order,
        ));
        
        $html = $this->display(__FILE__, '/views/templates/admin/AdminOrder.tpl');
        
        $reliquats = Db::getInstance()->executeS(
            '
            SELECT id_order, id_reliquat, osl.name as order_state, c.name as carrier, tracking_number, date_add, er.id_carrier, er.id_order_state
            FROM '._DB_PREFIX_.'ec_reliquat er
            LEFT JOIN '._DB_PREFIX_.'carrier c ON (er.id_carrier = c.id_carrier)
            LEFT JOIN '._DB_PREFIX_.'order_state_lang osl ON (osl.id_order_state = er.id_order_state AND id_lang = '.(int)$id_lang.')
            WHERE id_order = '.(int)$id_order.'
            '
        );
        
        foreach ($reliquats as &$reliquat) {
            $reliquat['products'] = Db::getInstance()->executeS(
                '
                SELECT id_reliquat_product, quantity, product_id, product_attribute_id, product_name, product_reference
                FROM '._DB_PREFIX_.'ec_reliquat_product erp
                LEFT JOIN '._DB_PREFIX_.'order_detail od ON (od.id_order_detail = erp.id_order_detail)
                WHERE id_reliquat = '.(int)$reliquat['id_reliquat'].'
                '
            );
            $reliquat['attachments'] = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ec_reliquat_attachment WHERE id_reliquat = '.(int)$reliquat['id_reliquat']);
        }
        if ($reliquats) {
            $token = Configuration::get('EC_RELIQUAT_TOKEN');
            $this->smarty->assign(array(
                'reliquats' => $reliquats,
                'dl_script' => Tools::getHttpHost(true).__PS_BASE_URI__.'modules/ec_reliquat/dl.php?token='.$token,
                'link_delivery_slip' => Tools::getHttpHost(true).__PS_BASE_URI__.'modules/ec_reliquat/generateDeliverySlip.php?token='.$token,
            ));
            $html .= $this->display(__FILE__, '/views/templates/admin/admin_reliquats.tpl');
        }
        return $html;
    }
    
    public function changeOrderState($id_order)
    {
        $products = Db::getInstance()->executeS(
            '
            SELECT od.id_order_detail, product_id, product_attribute_id, product_name, product_quantity, product_reference,
            (SELECT sum(quantity) FROM '._DB_PREFIX_.'ec_reliquat_product erp WHERE erp.id_order_detail = od.id_order_detail)as qty_ship
            FROM '._DB_PREFIX_.'order_detail od
            WHERE id_order = '.(int)$id_order.'
            
            '
        );
        
        $full_ship = 0;
        foreach ($products as &$product) {
            $full_ship += (int)((int)$product['product_quantity']-(int)$product['qty_ship']);
        }
        
        if ($full_ship != 0) {
            $id_order_state = Configuration::get('EC_RELIQUAT_PARTIAL');
        } else {
            $id_order_state = Configuration::get('EC_RELIQUAT_COMPLETE');
        }
        $new_oh = new OrderHistory();
        $new_oh->id_order = $id_order;
        $new_oh->id_order_state = (int) $id_order_state;
        $new_oh->date_add = date('Y-m-d H:i:s');
        $new_oh->add();
    }

    public function addReliquat($id_order)
    {
        $send_email = Tools::getValue('reliquatEmail');
        $id_order_state = Tools::getValue('id_order_state');
        $tracking_number = Tools::getValue('trackingNumber');
        $id_carrier = Tools::getValue('id_carrier');
        //$this->totalizeReliquat($id_reliquat);
        $id_reliquat = self::insertReliquat($id_order, $id_order_state, $id_carrier, $tracking_number);

        self::addReliquatProduct($id_reliquat, $send_email, $id_order_state);
        self::addReliquatAttachment($id_reliquat);
        if ($send_email && $id_order_state != Configuration::get('EC_RELIQUAT_DELIVERED')) {
            $this->sendEmailReliquat($id_order, $id_order_state, $id_carrier);
        }
        //UPDATE '._DB_PREFIX_.'_ec_reliquat LEFT JOIN (select id_reliquat, quantity,sum(quantity) as items, SUM(quantity*product_weight) as weight, SUM(quantity*unit_price_tax_excl) as total_products, SUM(quantity * original_product_price) as total_products_msrp, SUM(quantity*purchase_supplier_price) as total_products_cost from ps_ec_reliquat_product left join ps_order_detail on ps_ec_reliquat_product.id_order_detail = ps_order_detail.id_order_detail group by id_reliquat ) as totals on ps_ec_reliquat.id_reliquat = totals.id_reliquat set ps_ec_reliquat.weight = totals.weight, ps_ec_reliquat.items = totals.items, ps_ec_reliquat.total_products=totals.total_products, ps_ec_reliquat.total_products_msrp = totals.total_products_msrp, ps_ec_reliquat.total_products_cost = totals.total_products_cost
       //totalize
        // Db::getInstance()->executeS(
        //     '
        //     UPDATE '._DB_PREFIX_.'ec_reliquat LEFT JOIN (select id_reliquat, quantity,sum(quantity) as items, SUM(quantity*product_weight) as weight, SUM(quantity*unit_price_tax_excl) as total_products, SUM(quantity * original_product_price) as total_products_msrp, SUM(quantity*purchase_supplier_price) as total_products_cost from ps_ec_reliquat_product left join ps_order_detail on ps_ec_reliquat_product.id_order_detail = ps_order_detail.id_order_detail group by id_reliquat ) as totals on ps_ec_reliquat.id_reliquat = totals.id_reliquat set ps_ec_reliquat.weight = totals.weight, ps_ec_reliquat.items = totals.items, ps_ec_reliquat.total_products=totals.total_products, ps_ec_reliquat.total_products_msrp = totals.total_products_msrp, ps_ec_reliquat.total_products_cost = totals.total_products_cost where '._DB_PREFIX_.'ec_reliquat.id_reliquat ='.(int)$id_reliquat.''
        // );
        $this->totalizeReliquat($id_reliquat);
    }

    public static function insertReliquat($id_order, $id_order_state, $id_carrier, $tracking_number)
    {
        //inserts into order invoice


        Db::getInstance()->insert(
            'ec_reliquat',
            array(
                'id_order' => (int)$id_order,
                'id_order_state' => (int)$id_order_state,
                'id_carrier' => (int)$id_carrier,
                'tracking_number' => pSQL($tracking_number),
                'date_add' => pSQL(date('Y-m-d H:i:s'))
            )
        );
        return Db::getInstance()->insert_ID();

    }
//
    public function totalizeReliquat($id_reliquat)
    {
     Db::getInstance()->executeS(
        '
        UPDATE
        ps_ec_reliquat
        LEFT JOIN (
        select
        id_reliquat,
        quantity,
        sum(quantity) as items,
        SUM(quantity * product_weight) as weight,
        SUM(quantity * unit_price_tax_incl) as total_products,
        SUM(quantity * original_product_price) as total_products_msrp,
        SUM(quantity * purchase_supplier_price) as total_products_cost,
        SUM(
        (quantity * product_weight) *(ps_order_carrier.shipping_cost_tax_incl / IF(ps_order_carrier.weight!=0,ps_order_carrier.weight,0.001))
        ) as total_shipping
        from
        ps_ec_reliquat_product
        left join ps_order_detail on ps_ec_reliquat_product.id_order_detail = ps_order_detail.id_order_detail
        left join ps_order_carrier on ps_order_detail.id_order = ps_order_carrier.id_order
        group by
        id_reliquat
        ) as totals on ps_ec_reliquat.id_reliquat = totals.id_reliquat
        set
        ps_ec_reliquat.weight = totals.weight,
        ps_ec_reliquat.items = totals.items,
        ps_ec_reliquat.total_products = totals.total_products,
        ps_ec_reliquat.total_products_msrp = totals.total_products_msrp,
        ps_ec_reliquat.total_products_cost = totals.total_products_cost,
        ps_ec_reliquat.total_shipping = totals.total_shipping,
        ps_ec_reliquat.date_update = current_timestamp
        where
        ps_ec_reliquat.id_reliquat = '.(int)$id_reliquat.''
    );

     Db::getInstance()->executeS(
        "
        INSERT INTO `ps_order_invoice` (id_order_invoice,`id_order`, `number`, `delivery_number`, `delivery_date`, `total_discount_tax_excl`, `total_discount_tax_incl`, `total_paid_tax_excl`, `total_paid_tax_incl`, `total_products`, `total_products_wt`, `total_shipping_tax_excl`, `total_shipping_tax_incl`, `shipping_tax_computation_method`, `total_wrapping_tax_excl`, `total_wrapping_tax_incl`, `shop_address`, `note`, `date_add`)
        SELECT
        ps_ec_reliquat.id_reliquat,  ps_ec_reliquat.id_order,  ps_ec_reliquat.id_reliquat,  ps_ec_reliquat.id_reliquat,  ps_ec_reliquat.date_add, 0, 0, `total_paid_tax_excl`, `total_paid_tax_incl`,  ps_ec_reliquat.`total_products`, `total_products_wt`, `total_shipping_tax_excl`, 0, 0, `total_wrapping_tax_excl`, `total_wrapping_tax_incl`, 'RG SPORTS', '', ps_ec_reliquat.date_add from ps_ec_reliquat LEFT JOIN ps_orders on ps_orders.id_order = ps_ec_reliquat.id_order WHERE ps_ec_reliquat.id_reliquat=  ".(int)$id_reliquat.' ON DUPLICATE KEY update  ps_order_invoice.total_paid_tax_excl = ps_orders.total_paid_tax_excl,ps_order_invoice.total_paid_tax_incl=ps_orders.total_paid_tax_incl, ps_order_invoice.`total_products` =  ps_ec_reliquat.`total_products`'
    );
 }
 public function updateReliquat($id_reliquat, $tracking_number, $id_carrier, $id_order_state, $id_order)
 {
    Db::getinstance()->update(
        'ec_reliquat',
        array(
            'tracking_number' => pSQL($tracking_number),
            'id_carrier' => pSQL($id_carrier),
            'id_order_state' => pSQL($id_order_state),
            'date_add' => pSQL(date('Y-m-d H:i:s')),
            'date_update' => pSQL(date('Y-m-d H:i:s'))

        ),
        'id_reliquat = '.(int)$id_reliquat
    );
    if ($id_order_state != Configuration::get('EC_RELIQUAT_DELIVERED')) {
        $products = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ec_reliquat_product WHERE id_reliquat = '.(int)$id_reliquat);
        $products_mail = array();
        foreach ($products as $product) {
            $products_mail[$product['id_order_detail']] = $product['quantity'];
        }
        $this->sendEmailReliquat($id_order, $id_order_state, $id_carrier, $products_mail, $tracking_number);
    }
}

public function deleteAttachment($cle)
{
    $upload_dir = dirname(__FILE__).'/files/';
    unlink($upload_dir.$cle);
    return Db::getinstance()->delete('ec_reliquat_attachment', 'cle = "'.pSQL($cle).'"');
}

public static function addReliquatProduct($id_reliquat, $send_email = false, $id_order_state = null, $products = null)
{
    if ($products == null) {
        $products = Tools::getValue('products');
    }
    $products_mail = array();


    foreach ($products as $key=>$value) {
        $productinfo =  explode('-', $key);
        $id_order_detail= $productinfo[0];
        if (strpos($key, 'wh')){
            $id_warehouse = $value;
        }
        if (strpos($key, 'qty')){
            $quantity = $value;
        }
        $sql = 'INSERT INTO `ps_ec_reliquat_product` ( `id_reliquat`, `id_order_detail`, `quantity`, `id_warehouse`)
        VALUES
        ( '.$id_reliquat.', '.$id_order_detail.', '.$quantity.', '.$id_warehouse.')
        ON DUPLICATE KEY UPDATE
        quantity ='.$quantity.',
        id_warehouse = '.$id_warehouse;
        Db::getInstance()->executeS(
            ''.$sql.''
        );    
    }
    //remove items at 0
    Db::getInstance()->executeS(
        'DELETE FROM ps_ec_reliquat_product where id_reliquat = '.$id_reliquat.' and quantity = 0'
    );    
//take out from inventory
    Db::getInstance()->executeS(
        'UPDATE geopos_products left join vu_items_summary on item_sid = vu_items_summary.rg_id LEFT JOIN  ps_order_detail ON ps_order_detail.product_mpn = vu_items_summary.mfg_pn LEFT JOIN ps_ec_reliquat_product  on ps_ec_reliquat_product.id_order_detail = ps_order_detail.id_order_detail and geopos_products.warehouse = ps_ec_reliquat_product.id_warehouse  SET geopos_products.qty = geopos_products.qty-ps_ec_reliquat_product.quantity where ps_ec_reliquat_product.id_warehouse != 0 and id_reliquat = '.$id_reliquat.''
    ); 

    // ob_start();
    // var_dump($products);
    // $result = ob_get_clean();

    // Db::getinstance()->insert(
    //     'ec_reliquat_product',
    //     array(

    //         'text'=> pSQL($result.$sql)
    //     )
    // );

    // foreach ($products as $id_order_detail => $quantity) {
    //     if ($quantity > 0) {
    //         Db::getinstance()->insert(
    //             'ec_reliquat_product',
    //             array(
    //                 'id_reliquat'=> (int)$id_reliquat,
    //                 'id_order_detail'=> (int)$id_order_detail,
    //                 'quantity'=> (int)$quantity
    //             )
    //         );
    //     }
  //   // }
  //   ob_start();
  //   var_dump($products);
  //   $result = ob_get_clean();

  //   if ($products['quantity'] > 0) {
  //     $id_order_detail =$products['id_order_detail'];
  //     $id_warehouse   = $products['id_warehouse'];
  //     $quantity  =$products['quantity'];
  //     Db::getinstance()->insert(
  //       'ec_reliquat_product',
  //       array(
  //           'id_reliquat'=> (int)$id_reliquat,
  //           'id_order_detail'=> (int)$id_order_detail,
  //           'id_warehouse'=> (int)$id_warehouse,
  //           'quantity'=>(int)$quantity,
  //           'text'=> $result
  //       )
  //   );
  // }
  //   ob_start();
  //   var_dump($products);
  //   $result = ob_get_clean();
  //   foreach ($products as $product) {
  //       if ($product['quantity'] > 0) {
  //         $id_order_detail =$product['id_order_detail'];
  //         $id_warehouse   = $product['id_warehouse'];
  //         $quantity  =$product['quantity'];
  //         Db::getinstance()->insert(
  //           'ec_reliquat_product',
  //           array(
  //               'id_reliquat'=> (int)$id_reliquat,
  //               'id_order_detail'=> (int)$id_order_detail,
  //               'id_warehouse'=> (int)$id_warehouse,
  //               'quantity'=>(int)$quantity,
  //               'text'=> $result
  //           )
  //       );
  //     }
  // }

}

public function sendEmailReliquat($id_order, $id_order_state, $id_carrier, $products = null, $tracking_number = null)
{
    $order = new Order($id_order);
    if ($products == null) {
        $products = Tools::getValue('products');
    }



    $customer = new Customer((int)$order->id_customer);
    $templateVars = array(
        '{firstname}' => $customer->firstname,
        '{lastname}' => $customer->lastname,
        '{id_order}' => $order->id,
        '{order_name}' => $order->getUniqReference(),
    );

    $id_lang = $order->id_lang;

    if ($id_order_state == Configuration::get('EC_RELIQUAT_PROCESS')) {
        $subject = $this->l('Processing in progress');
        $template_name = 'reliquat_preparation';
        $products_mail = '<ul>';
        foreach ($products as $id_order_detail => $quantity) {
            $products_mail .= '<li>'.Db::getInstance()->getValue('SELECT product_name FROM '._DB_PREFIX_.'order_detail WHERE id_order_detail = '.(int)$id_order_detail).'</li>';
        }
        $products_mail .= '</ul>';
        $templateVars['{products}'] = $products_mail;
    } else {
        $subject = $this->l('Shipped');
        $template_name = 'reliquat_in_transit';
        if ($tracking_number == null) {
            $tracking_number = Tools::getValue('trackingNumber');
        }
        $followup = '';
        if (Tools::strlen($tracking_number) > 0) {
            $carrier = new Carrier($id_carrier);
            if (Tools::strlen($carrier->url) > 0) {
                $followup = str_replace('@', $tracking_number, $carrier->url);
            }
        }
        $templateVars['{followup}'] = $followup;
        $templateVars['{products}'] = $this->getProductsMail($products);
    }
    Mail::Send(
        $id_lang,
        $template_name,
        $subject,
        $templateVars,
        trim($customer->email),
        $customer->firstname . ' ' . $customer->lastname,
        null,
        null,
        null,
        null,
        dirname(__FILE__) . '/mails/'
    );
}

public function getProductsMail($products)
{
    $prods = array();
    foreach ($products as $id_order_detail => $quantity) {
        if ($quantity > 0) {
            $prods[] = array(
                'name' => Db::getInstance()->getValue('SELECT product_name FROM '._DB_PREFIX_.'order_detail WHERE id_order_detail = '.(int)$id_order_detail),
                'quantity' => $quantity
            );
        }
    }
    $this->smarty->assign(array(
        'products' => $prods,
    ));
    return $this->display(__FILE__, '/views/templates/admin/productsmail.tpl');
}

public function addReliquatAttachment($id_reliquat)
{
    foreach ($_FILES as $key => $val) {
        if (preg_match('/^reliquat_attachment/', $key)) {
            $file = $_FILES[$key];
            if (isset($file['name']) && !empty($file['name'])) {
                $file_name =  Tools::getValue('filename_'.$key);
                $ext = Tools::strtolower(Tools::substr(strrchr($file['name'], '.'), 1));
                $date = date('Y-m-d H:i:s');
                $cle = md5($file_name.$date);
                if (move_uploaded_file($file['tmp_name'], $this->upload_path.$cle)) {
                    Db::getinstance()->insert(
                        'ec_reliquat_attachment',
                        array(
                            'id_reliquat'=> (int)$id_reliquat,
                            'name' => pSQL($file_name),
                            'extension' => pSQL($ext),
                            'cle' => pSQL($cle),
                            'type' => $file['type'],
                            'date_add' => $date,
                        )
                    );
                }
            }
        }
    }
}



public function hookDisplayOrderDetail($params)
{
    $id_order = $params['order']->id;
    $reliquats = Db::getInstance()->executeS(
        '
        SELECT id_order, id_reliquat, osl.name as order_state, c.name as carrier, tracking_number, date_add, er.id_carrier
        FROM '._DB_PREFIX_.'ec_reliquat er
        LEFT JOIN '._DB_PREFIX_.'carrier c ON (er.id_carrier = c.id_carrier)
        LEFT JOIN '._DB_PREFIX_.'order_state_lang osl ON (osl.id_order_state = er.id_order_state AND id_lang = '.$params['order']->id_lang.')
        WHERE id_order = '.(int)$id_order.'
        '
    );
    foreach ($reliquats as &$reliquat) {
        $reliquat['products'] = Db::getInstance()->executeS(
            '
            SELECT id_reliquat_product, quantity, product_id, product_attribute_id, product_name, product_reference
            FROM '._DB_PREFIX_.'ec_reliquat_product erp
            LEFT JOIN '._DB_PREFIX_.'order_detail od ON (od.id_order_detail = erp.id_order_detail)
            WHERE id_reliquat = '.(int)$reliquat['id_reliquat'].'
            '
        );
        if (Tools::strlen($reliquat['tracking_number']) > 0) {
            $carrier = new Carrier($reliquat['id_carrier']);
            if (Tools::strlen($carrier->url) > 0) {
                $reliquat['tracking_url'] = str_replace('@', $reliquat['tracking_number'], $carrier->url);
            }
        }
        $reliquat['attachments'] = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'ec_reliquat_attachment WHERE id_reliquat = '.(int)$reliquat['id_reliquat']);
    }

    if ($reliquats) {
        $token = Configuration::get('EC_RELIQUAT_TOKEN');
        $this->smarty->assign(array(
            'reliquats' => $reliquats,
            'dl_script' => Tools::getHttpHost(true).__PS_BASE_URI__.'modules/ec_reliquat/dl.php?token='.$token,
            'link_delivery_slip' => Tools::getHttpHost(true).__PS_BASE_URI__.'modules/ec_reliquat/generateDeliverySlip.php?token='.$token,
        ));
        return $this->display(__FILE__, '/views/templates/admin/order_detail.tpl');
    }
}



            /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
            public function hookActionAdminControllerSetMedia()
            {
                if (Tools::getValue('controller') == 'AdminOrders' && Tools::getValue('id_order') > 0) {
                    Media::addJsDef(
                        array(
                            'ec_token' => Configuration::get('EC_RELIQUAT_TOKEN'), 
                            'ec_id_order' => Tools::getValue('id_order'),
                            'ec_base_uri' => Tools::getHttpHost(true).__PS_BASE_URI__,
                        )
                    );
                    $this->context->controller->addJS($this->_path.'views/js/back.js');
                    $this->context->controller->addCSS($this->_path.'views/css/back.css');
                }
            }

            /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
            public function hookHeader()
            {
                $this->context->controller->addJS($this->_path.'/views/js/front.js');
                $this->context->controller->addCSS($this->_path.'/views/css/front.css');
            }
        }
