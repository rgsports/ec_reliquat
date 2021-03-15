<div id="ec_reliquat_orderdetail" class="box hidden-sm-down">
    <table id="order-products" class="table table-bordered">
        <thead class="thead-default">
            <tr>
                <th>{l s='Invoice' mod='ec_reliquat'}</th>
                <th>{l s='Tracking number' mod='ec_reliquat'}</th>
                <th>{l s='Carrier' mod='ec_reliquat'}</th>
                <th>{l s='Order State' mod='ec_reliquat'}</th>
                <th>{l s='Delivery Slip' mod='ec_reliquat'}</th>
                <th>{l s='Products' mod='ec_reliquat'}</th>
                <th>{l s='Attachments' mod='ec_reliquat'}</th>
            </tr>
        </thead>
        <tbody>
            {foreach $reliquats as $reliquat}
                <tr>
                <td>{if isset($reliquat.tracking_url)}<a target="_blank" href="{$reliquat.tracking_url}">{$reliquat.tracking_number}</a>{else}{$reliquat.tracking_number}{/if}</td>
                    <td>{$reliquat.carrier}</td>
                    <td>{$reliquat.order_state}</td>
                    <td>{$reliquat.date_add} <a href="{$link_delivery_slip}&id_order={$reliquat.id_order}&id_reliquat={$reliquat.id_reliquat}"><i class="material-icons">cloud_download</i></a></td>
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
