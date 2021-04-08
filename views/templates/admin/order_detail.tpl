

<div id="ec_reliquat_orderdetail" class="box">
    <H3>{l s='Shipments' mod='ec_reliquat'} </H3>
    <table class="table">
        <thead class="thead-default">
            <tr class="nodrag nodrop">
                <th class="left"><span class="title_box">{l s='Product name' mod='ec_reliquat'}</span></th>
                {* <th class="text-center">{l s='Available quantity' mod='ec_reliquat'}</th> *}
                <th class="text-center"><span class="title_box">{l s='Ordenados' mod='ec_reliquat'}</span></th>
                <th class="text-center">{l s='Cancelados' mod='ec_reliquat'}</th>                        
                <th class="text-center">{l s='Enviados' mod='ec_reliquat'}</th>  
                <th>{l s='Info' mod='ec_reliquat'}</th>

                <th></th>
            </tr>
        </thead>
        <tbody>
            {foreach $products as $product}
            <tr class="product-line-row">

                <td style="color:#00aff0;" width="70%">
                    <span class="productName">{$product.product_name}</span><br>
                    {l s='Vendor Part Number:' mod='ec_reliquat'} {$product.product_supplier_reference}<br>
                    {l s='Manufacturer Part Number:' mod='ec_reliquat'} {$product.product_mpn}<br>

                    {l s='Reference number:' mod='ec_reliquat'} {$product.product_reference}<br>

                </td>
                     {*    <td class="productQuantity text-center" width="10%">
                            <b>{$product.quantity_available}</b>
                        </td> *}
                        <td class="productQuantity text-center" width="10%">
                            <span class="product_quantity_show badge">{$product.product_quantity}</span>
                        </td>
                        <td class="productCancel text-center" width="10%">
                            <span class="badge badge-warning">{$product.qty_cancel}</span>
                        </td>
                        <td class="productQuantity text-center" width="10%">
                            <span class="badge badge-success">{$product.qty_ship}</span>
                        </td>
                        <td><a href="#" data-id="{$product.id_order_detail}" class="showProdsReliquat"><i class="material-icons">add</i></a></td>

                    </tr>
                    <tr style="width: 100%; display: none; " class="table reliquat_products" id="products{$product.id_order_detail}">
                        <td colspan="6">
                            <table style="width: 80%;" >
                                <thead class="thead-default">
                                    <tr>
                                        <th>{l s='Invoice' mod='ec_reliquat'}</th>
                                        <th>{l s='Date' mod='ec_reliquat'}</th>
                                        <th>{l s='Quantity' mod='ec_reliquat'}</span></th>
                                        <th>{l s='Tracking number' mod='ec_reliquat'}</th>
                                        <th>{l s='Documents' mod='ec_reliquat'}</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    {foreach $product['reliquats'] as $reliquat}
                                    <tr>
                                       <tr>
                                        <td>{$reliquat.id_reliquat}</td>
                                        <td>{$reliquat.date_add}</td>
                                        <td>{$reliquat.quantity}</td>
                                        <td>{if isset($reliquat.tracking_url)}<a target="_blank" href="{$reliquat.tracking_url}">{$reliquat.tracking_number}</a>{else}{$reliquat.tracking_number}{/if}</td>
                                        <td>Packing list <a href="{$link_delivery_slip}&id_order={$reliquat.id_order}&id_reliquat={$reliquat.id_reliquat}"><i class="material-icons">cloud_download</i></a>{if isset($reliquat.tracking_number)}<br>
                                            Invoice <a href="https://www.rgdist.net/modules/quickbooks_online/generateInvoice.php?invoice={$reliquat.id_reliquat}"><i class="material-icons">cloud_download</i></a>{/if}
                                        </td>                     
                                    </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                        </td> 
                    </tr>
                    {/foreach}
                </tbody>
            </table>
            <table id="order-products" class="table table-bordered">
                <thead class="thead-default">
                    <tr>
                        <th>{l s='Invoice' mod='ec_reliquat'}</th>
                        <th>{l s='Date' mod='ec_reliquat'}</th>
                        <th>{l s='Tracking number' mod='ec_reliquat'}</th>
                        <th>{l s='Carrier' mod='ec_reliquat'}</th>
                        <th>{l s='Order State' mod='ec_reliquat'}</th>
                        <th>{l s='Documents' mod='ec_reliquat'}</th>
                        <th>{l s='Products' mod='ec_reliquat'}</th>
                        <th>{l s='Attachments' mod='ec_reliquat'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach $reliquats as $reliquat}
                    <tr>
                        <td>{$reliquat.id_reliquat}</td>
                        <td>{$reliquat.date_add}</td>
                        <td>{if isset($reliquat.tracking_url)}<a target="_blank" href="{$reliquat.tracking_url}">{$reliquat.tracking_number}</a>{else}{$reliquat.tracking_number}{/if}</td>
                        <td>{$reliquat.carrier}</td>
                        <td>{$reliquat.order_state}</td>
                        <td>Packing list <a href="{$link_delivery_slip}&id_order={$reliquat.id_order}&id_reliquat={$reliquat.id_reliquat}"><i class="material-icons">cloud_download</i></a>{if isset($reliquat.tracking_number)}<br>
                            Invoice <a href="https://www.rgdist.net/modules/quickbooks_online/generateInvoice.php?invoice={$reliquat.id_reliquat}"><i class="material-icons">cloud_download</i></a>{/if}
                        </td>
                        <td><a href="#" data-id="{$reliquat.id_reliquat}" class="showProdsReliquat"><i class="material-icons">add</i></a></td>
                        <td>{if count($reliquat['attachments']) > 0}<a href="#" data-id="{$reliquat.id_reliquat}" class="showAttsReliquat"><i class="material-icons">add</i></a>{/if}</td>
                    </tr>
                    <tr style="width: 100%; display: none;" class="table reliquat_products" id="products{$reliquat.id_reliquat}">
                        <td colspan="6">
                            <table style="width: 80%;" >
                                <thead class="thead-default">
                                    <tr>
                                        <th style="width:15%;">&nbsp;</th>
                                        <th style="width:50%;"><span class="title_box ">{l s='Product' mod='ec_reliquat'}</span></th>
                                        <th style="width:35%;"><span class="title_box ">{l s='Quantity' mod='ec_reliquat'}</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach $reliquat['products'] as $product}
                                    <tr>
                                        <td>
                                            <img src="../img/tmp/product_mini_{$product['product_id']}_{$product['product_attribute_id']}.jpg?time=1554738725" alt="" class="imgm img-thumbnail">
                                        </td>
                                        <td>
                                            <strong>{$product['product_name']}</strong><br>
                                            {$product['product_reference']}<br>                                                            
                                        </td>
                                        <td>
                                            <span>{$product['quantity']}</span>
                                        </td>                        
                                    </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                        </td> 
                    </tr>
                    <tr  style="width: 100%; display: none;" class="table reliquat_attachments" id="attachments{$reliquat.id_reliquat}">
                        <td colspan="6">
                            <table style="width: 80%;" >
                                <thead class="thead-default">
                                    <tr>

                                        <th style="width:50%;">{l s='File name' mod='ec_reliquat'}</th>
                                        <th style="width:50%;">{l s='Download' mod='ec_reliquat'}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach $reliquat['attachments'] as $attachment}
                                    <tr>
                                        <td>
                                            {$attachment['name']}.{$attachment['extension']}                                                            
                                        </td>
                                        <td>
                                            <a href="{$dl_script}&k={$attachment['cle']}&front=1"><i class="material-icons">cloud_download</i></a>
                                        </td>                        
                                    </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                        </td> 
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>