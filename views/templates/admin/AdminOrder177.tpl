<div id="ec_reliquat" class="card">
    <div class="card-header">
        <h3 class="card-header-title">
            {l s='Manage product to ship' mod='ec_reliquat'}
        </h3>
    </div>
    <div class="card-body">
        <form action="{$url_form}" method="post"  enctype="multipart/form-data">
            <table class="table">
                <thead>
                    <tr class="nodrag nodrop">
                        <th></th>
                        <th class="left"><span class="title_box">{l s='Product name' mod='ec_reliquat'}</span></th>
                        {* <th class="text-center">{l s='Available quantity' mod='ec_reliquat'}</th> *}
                        <th class="text-center"><span class="title_box">{l s='Quantity' mod='ec_reliquat'}</span></th>
                        <th class="text-center">{l s='Quantity shipped' mod='ec_reliquat'}</th>                        
                        <th class="text-center">{l s='Quantity shipped today' mod='ec_reliquat'}</th>
                        <th><strong>{l s='Warehouse' mod='wkwarehouses'}</strong></th>

                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    {foreach $products as $product}
                    <tr class="product-line-row">
                        <td width="5%"><img src="{$ec_base_uri}/img/tmp/product_mini_{$product.product_id}_{$product.product_attribute_id}.jpg?time=1554370182" alt="" class="imgm img-thumbnail"></td>
                        <td style="color:#00aff0;" width="25%">
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
                        <td class="productQuantity text-center" width="10%">
                            <span class="badge badge-{$product.class_badge}">{$product.qty_ship}</span>
                        </td>
                        {if $product.product_quantity != $product.qty_ship}
                        <td class="text-center" width="15%">
                            <input type="number" id="quantity_shipped_{$product.id_order_detail}" name="products[{$product.id_order_detail}-qty]" value="{$product.product_quantity-$product.qty_ship}" max="{$product.product_quantity-$product.qty_ship}" min="0">
                        </td>
                        <td>
                         {if !empty($product.warehouses)}

                         <select id="" name="products[{$product.id_order_detail}-wh]" class="warehouse-select">
                            <option value="0" selected="selected">{l s='Select Warehouse' mod='wkwarehouses'}</option>
                            {foreach from=$product.warehouses item='warehouse'}
                            <option value="{$warehouse['warehouse_id']|intval}" >{$warehouse['title']|escape:'html':'UTF-8'} ({$warehouse['qty']})</option>
                            {/foreach}
                        </select>
                        {else}
                        <input type="hidden" name="products[{$product.id_order_detail}-wh]" value="0">
                        {/if}
                    </td>
                    <td width="10%">
                        <div class="col-lg-2">
                            <button class="btn btn-default" onclick="javascript:$('input#quantity_shipped_{$product.id_order_detail}').val(0); return false;">
                                {l s='Don\'t ship this product' mod='ec_reliquat'}
                            </button>
                        </div>
                    </td>
                    {/if}
                </tr>
                {/foreach}
            </tbody>
        </table>
        <br>
        <div class="row-margin-top">
            <h3>
                {l s='Shipping information' mod='ec_reliquat'}
            </h3>
            <div class="row">
                <div class="col-xs-12">
                    <div class="form-horizontal">

                        <div class="col-lg-9">
                            <div class="form-group">
                                <label class="form-control-label" for="ec_trackingNumber">{l s='Tracking number' mod='ec_reliquat'}</label>
                                <input type="text" id="ec_trackingNumber" name="trackingNumber" class="form-control" placeholder="XXXXXXXXX">
                                <small class="form-text">{l s='This field is not required. You can shipped product without tracking number.' mod='ec_reliquat'}</small>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <div class="form-group row type-choice">

                                <label for="ec_carrier" class="form-control-label label-on-top col-12">
                                    {l s='Carriers' mod='ec_reliquat'}
                                </label>
                                <div class="col-12">
                                    <select id="ec_carrier" name="id_carrier" class="custom-select">
                                        {foreach $carriers as $carrier}
                                        <option value="{$carrier.id_carrier}">{$carrier.name}</option>
                                        {/foreach}
                                    </select>
                                    <small class="form-text">{l s='Select the carrier for this shipping.' mod='ec_reliquat'}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <div class="form-group row type-choice">
                                <label for="ec_order_state" class="form-control-label label-on-top col-12">
                                    {l s='Order state' mod='ec_reliquat'}
                                </label>
                                <div class="col-12">
                                    <select id="ec_order_state" name="id_order_state" class="custom-select">
                                        {foreach $order_states as $order_state}
                                        <option value="{$order_state.id_order_state}">{$order_state.name}</option>
                                        {/foreach}
                                    </select>
                                    <small class="form-text">{l s='Select order state for this shipping.' mod='ec_reliquat'}</small>
                                </div>
                            </div>

                        </div>
                        <div class="col-lg-9">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="ps-switch">
                                        <input id="reliquatEmail_0" class="ps-switch" name="reliquatEmail" value="0"  type="radio">
                                        <label for="reliquatEmail_0">{l s='No' mod='ec_reliquat'}</label>
                                        <input id="reliquatEmail_1" class="ps-switch" name="reliquatEmail" value="1" checked="checked" type="radio">
                                        <label for="reliquatEmail_1">{l s='Yes' mod='ec_reliquat'}</label>
                                        <span class="slide-button"></span>
                                    </span>
                                </div>
                                <small class="form-text">{l s='Send an email to inform the client.' mod='ec_reliquat'}</small>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <label class="control-label">{l s='Attachments' mod='ec_reliquat'}</label>
                            <div class="ec_reliquat_file">
                                <input type="text" class="form-control" name="filename_reliquat_attachment1" value="" placeholder="Nom du fichier" style="margin-bottom: 10px;">
                                <div class="custom-file">


                                    <input type="file" id="reliquat_attachment1" name="reliquat_attachment1" class="custom-file-input" data-locale="fr">


                                    <label class="custom-file-label" for="category_cover_image">
                                        {l s='Choose a file' mod='ec_reliquat'}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="hidden" name="id_order" value="{$ec_id_order}">
                            <button type="submit" name="submitEcReliquatShip" class="btn btn-primary pull-left" style="margin-top: 10px;">Envoyer</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
</div>
</div>
