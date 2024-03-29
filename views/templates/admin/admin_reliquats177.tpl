<div class="card col-md-12">
    <div class="card-header">
        <h3 class="card-header-title">
            {l s='Products shipped' mod='ec_reliquat'}
        </h3>
    </div>
    <table id="reliquat_table" class="table">
        <thead>
            <tr class="nodrag nodrop">
                <th class="left">
                    <span>{l s='Partial Number' mod='ec_reliquat'}</span>
                </th>
                <th class="text-center">
                    <span>{l s='Tracking Number' mod='ec_reliquat'}</span>
                </th>
                <th class="text-center"><span class="title_box">{l s='Carrier' mod='ec_reliquat'}</span></th>
                <th class="text-center"><span class="title_box">{l s='Current state' mod='ec_reliquat'}</span></th>
                <th class="text-center"><span class="title_box">{l s='Total' mod='ec_reliquat'}</span></th>
                <th class="text-center"><span class="title_box">{l s='Peso' mod='ec_reliquat'}</span></th>
                <th class="text-center"><span class="title_box">{l s='Envio' mod='ec_reliquat'}</span></th>
                <th class="text-center"><span class="title_box">{l s='Ganancia' mod='ec_reliquat'}</span></th>
                <th class="text-center">{l s='Delivery slip' mod='ec_reliquat'}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            {foreach $reliquats as $reliquat}
            <tr>
               <td class="td-reliquat">{$reliquat.id_reliquat}</td>
               <td class="text-center td-reliquat">{if isset($reliquat.tracking_url)}<a target="_blank" href="{$reliquat.tracking_url}">{$reliquat.tracking_number}</a>{else}{$reliquat.tracking_number}{/if}
                   <br />
                   <button name="products" class="btn btn-default" type="button" onclick="ShowProducts({$reliquat.id_reliquat}); return false;">{l s='Products' mod='ec_reliquat'}
                       {if $reliquat['attachments']} <button name="products" class="btn btn-default" type="button" onclick="ShowAttachments({$reliquat.id_reliquat}); return false;">{l s='Attachments' mod='ec_reliquat'}</button>{/if}
                   </td>
                   <td class="text-center td-reliquat">{$reliquat.carrier}</td>
                   <td class="text-center td-reliquat">{$reliquat.order_state}</td>
                   <td class="text-center td-reliquat">{$reliquat.sprice+$reliquat.total_shipping}</td>

                   <td class="text-center td-reliquat">{$reliquat.weight}</td>
                   <td class="text-center td-reliquat">{$reliquat.total_shipping}</td>
                   <td class="text-center">
                    <b>Margen:{$reliquat.margin*100}%</b><br>
                    Ganancia: {$reliquat.profit}<br>
                    Costo Productos: {$reliquat.cost}<br>
                Venta Productos: {$reliquat.sprice}</td>




                <td class="text-center td-reliquat">{$reliquat.date_add} <a href="{$link_delivery_slip}&id_order={$reliquat.id_order}&id_reliquat={$reliquat.id_reliquat}"><i class="material-icons">cloud_download</i></a></td>
                <td><button type="button" data-info_reliquat='{$reliquat|json_encode}' class="editreliquat btn btn-info btn-lg" data-toggle="modal" data-target="#editreliquat">{l s='Edit' mod='ec_reliquat'}</button></td>
            </tr>
            <tr style="width: 100%; display: none;" id="products{$reliquat.id_reliquat}">
                <td colspan="8" style="width:100%">
                    <form action="{$url_form}" method="post" name="qtyeditform" id="shipform" enctype="multipart/form-data">

                        <table style="width: 80%;" class="table" >
                            <thead>
                                <tr>
                                    <th style="width:15%;">&nbsp;</th>
                                    <th style="width:15%;">&nbsp;</th>

                                    <th style="width:50%;"><span class="title_box ">{l s='Product' mod='ec_reliquat'}</span></th>
                                    <th style="width:50%;"><span class="title_box ">{l s='Part Number' mod='ec_reliquat'}</span></th>
                                    <th style="width:10%;"><span class="title_box ">{l s='Quantity' mod='ec_reliquat'}</span></th>
                                    <th style="width:10%;"></th>
                                    <th style="width:10%;"></th>
                                    <th style="width:10%;"><span class="title_box ">{l s='Weight' mod='ec_reliquat'}</span></th>
                                </tr>
                            </thead>
                            <tbody>

                                {foreach $reliquat['products'] as $product}
                                <tr class="product-line-row" height="52">
                                    <td>{l s='Shipped Product' mod='ec_reliquat'}<br> <span> Warehouse: {$product['warehouse']}</span></td>
                                    <td><img src="{$ec_base_uri}/img/tmp/product_mini_{$product['product_id']}_{$product['product_attribute_id']}.jpg?time=1554738725" alt="" class="imgm img-thumbnail"></td>
                                    <td  style="color:#00aff0;">
                                        <span class="productName">{$product['product_name']}</span><br>
                                        {$product['product_reference']}<br>


                                        <span class="productName"></span>                                                          
                                    </td>
                                    <td class="product-line-row">{$product['product_mpn']} <br> {$product['product_supplier_reference']}</td>
                                    <td class="productQuantity">
                                        <span class="product_quantity_show red bold">{$product['quantity']}</span>
                                    </td>  
{*                             edit section

 *}                               

 {if $reliquat.id_order_state == 20}
 <td class="text-center" width="15%">
    <input type="number" id="quantity_shipped_{$product.id_order_detail}" name="products[{$product.id_order_detail}-qty]" value="{$product['quantity']}"  min="0">
</td>   
<td>
    <select id="" name="products[{$product.id_order_detail}-wh]" class="warehouse-select">
        <option value="{$product['id_warehouse']}" selected="selected">{$product['warehouse']}</option>
        {foreach from=$product.warehouses item='warehouse'}
        <option value="{$warehouse['warehouse_id']|intval}" >{$warehouse['title']|escape:'html':'UTF-8'} ({$warehouse['qty']})</option>
        {/foreach}
    </select>
</td>
<td class="text-center" width="15%">
    <input type="number" id="product_weight{$product.product_weight}" name="products[{$product.id_order_detail}-weight]" step=".01" value="{$product['product_weight']}"  min="0">
</td>
{/if}
                                {*                         end    edit section
                                 *}                               
                             </tr>
                             {/foreach}
                         </tbody>
                     </table>
                     {if $reliquat.id_order_state == 20}

                     <div class="col-lg-9">
                        <label  for="ec_edit_notes">{l s='Razon de Edicion' mod='ec_reliquat'}</label>
                        <input type="text" id="ec_edit_notes" name="edit_notes" maxlength="255"  required class="form-control" placeholder="">
                        <input type="hidden" name="id_reliquat" value="{$reliquat.id_reliquat}">
                        <input type="hidden" name="submitEcReliquatQtyEdit" value="true">

                        <button type="submit" id="submitEcReliquat"  name="submitEcReliquat" class="btn btn-primary pull-left" onclick="this.disabled=true; document.getElementsByName("qtyeditform")[0].submit();"  style="margin-top: 10px;">Editar Cantidades</button>
                    </div>
                    {/if}
                </form>
            </td>
        </tr>
        
        
        <tr style="display: none;" id="attachments{$reliquat.id_reliquat}">
            <td colspan="8" style="width:100%">
                <table style="width: 80%;" class="table" >
                    <thead>
                        <tr>
                            <th style="width:30%;"><span class="title_box ">{l s='Filename' mod='ec_reliquat'}</span></th>
                            <th style="width:10%;"><span class="title_box ">{l s='Extension' mod='ec_reliquat'}</span></th>
                            <th><span class="title_box ">{l s='Type' mod='ec_reliquat'}</span></th>
                            <th><span class="title_box ">{l s='Add date' mod='ec_reliquat'}</span></th>
                            <th><span class="title_box ">{l s='Download date' mod='ec_reliquat'}</span></th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $reliquat['attachments'] as $attachement}
                        <tr class="product-line-row" height="52">
                            <td>{$attachement['name']}</td>
                            <td  >
                                <span class="productName">{$attachement['extension']}</span>                                                        
                            </td>
                            <td class="productQuantity">
                                <span class="product_quantity_show red bold">{$attachement['type']}</span>
                            </td>
                            <td class="productQuantity product_stock">{$attachement['date_add']}</td>                        
                            <td class="productQuantity product_stock">{$attachement['date_download']}</td>
                            <td class="productQuantity product_stock"><a title="{l s='Donwload' mod='ec_reliquat'}" href="{$dl_script}&k={$attachement['cle']}"><i class="material-icons">cloud_download</i></a><a class="deleteattachment" data-cle="{$attachement['cle']}" title="{l s='Delete' mod='ec_reliquat'}" href="#"><i class="material-icons">delete</i></a></td>  
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



<div id="editreliquat" class="bootstrap modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{l s='Edit Reliquat' mod='ec_reliquat'}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <div class="form-group">
                        <div class="col-lg-1"></div>
                        <label class="col-lg-4"> {l s='Carrier' mod='ec_reliquat'}</label>
                        <div class="col-lg-4 input-group">
                            <select class="ec_carrier form-control" class="selectpicker">
                                {foreach from=$carriers item=carrier}
                                <option value="{$carrier['id_carrier']|escape:'htmlall':'UTF-8'}">{$carrier['name']|escape:'htmlall':'UTF-8'}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-1"></div>
                        <label class="col-lg-4"> {l s='Order states' mod='ec_reliquat'}</label>
                        <div class="col-lg-4 input-group">
                            <select class="ec_order_state form-control" class="selectpicker">
                                {foreach from=$order_states item=order_state}
                                <option value="{$order_state['id_order_state']|escape:'htmlall':'UTF-8'}">{$order_state['name']|escape:'htmlall':'UTF-8'}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-1"></div>
                        <label class="col-lg-4">{l s='Traking number' mod='ec_reliquat'}</label>
                        <div class="col-lg-4 input-group">
                            <input class="ec_trackingnumber form-control" type="text"/>
                        </div>
                    </div>
                </div>
                <button id="closereliquat" type="button" class="btn btn-default" data-dismiss="modal">{l s='Close' mod='ec_reliquat'}</button>
                <button id="btneditreliquat" class="btn btn-primary">{l s='Edit' mod='ec_reliquat'}</button>
            </div>
            {* edit items form *}


        </div>
        {*end edit items*}
        <div class="modal-footer">

        </div>
    </div>
</div>
</div>