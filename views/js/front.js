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
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/
$(document).ready(function () {
    //$(location).attr('href', 'http://www.google.fr')
    $('body').on('click','#ec_reliquat_orderdetail .showProdsReliquat', function(e) {
        e.preventDefault();
        id_reliquat = $(this).attr('data-id');
        $('.reliquat_attachments').hide();
        $('.showAttsReliquat i ').html('add');
        icon = $(this).children('i').html().trim();
        if (icon == 'add') {
            $(this).children('i').html('remove');
        } else {
            $(this).children('i').html('add');
        }
        if ($('#ec_reliquat_orderdetail #products'+id_reliquat).css('display') == 'none') {
            $('#ec_reliquat_orderdetail #products'+id_reliquat).show();
        } else {
            $('#ec_reliquat_orderdetail #products'+id_reliquat).hide();
        }
    });
    
    $('body').on('click','#ec_reliquat_orderdetail .showAttsReliquat', function(e) {
        e.preventDefault();
        id_reliquat = $(this).attr('data-id');
        $('.reliquat_products').hide();
        $('.showProdsReliquat i ').html('add');
        icon = $(this).children('i').html().trim();
        if (icon == 'add') {
            $(this).children('i').html('remove');
        } else {
            $(this).children('i').html('add');
        }
        if ($('#ec_reliquat_orderdetail #attachments'+id_reliquat).css('display') == 'none') {
            $('#ec_reliquat_orderdetail #attachments'+id_reliquat).show();
        } else {
            $('#ec_reliquat_orderdetail #attachments'+id_reliquat).hide();
        }
    });
    
    $('body').on('click','.deleteattachment', function() {
        cle = $(this).attr('data-cle');
    });
}); 