<div id="ec_reliquat" class="panel">
    <div class="panel-heading">
        <i class="icon-truck"></i>
        {l s='Manage product to ship' mod='ec_reliquat'}
    </div>
    <form action="{$url_form}" method="post"  enctype="multipart/form-data">
        <table class="table">
            <thead>
                <tr class="nodrag nodrop">
                    <th></th>
                    <th class="left"><span class="title_box">{l s='Product name' mod='ec_reliquat'}</span></th>
                    <th class="text-center">{l s='Available quantity' mod='ec_reliquat'}</th>
                    <th class="text-center"><span class="title_box">{l s='Quantity' mod='ec_reliquat'}</span></th>
                    <th class="text-center">{l s='Quantity shipped' mod='ec_reliquat'}</th>
                    <th class="text-center">{l s='Quantity shipped today' mod='ec_reliquat'}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                {foreach $products as $product}
                <tr class="product-line-row">
                    <td width="5%"><img src="../img/tmp/product_mini_{$product.product_id}_{$product.product_attribute_id}.jpg?time=1554370182" alt="" class="imgm img-thumbnail"></td>
                    <td style="color:#00aff0;" width="25%">
                        <span class="productName">{$product.product_name}</span><br>
                        {l s='Reference number:' mod='ec_reliquat'} {$product.product_reference}<br>
                    </td>
                    <td class="productQuantity text-center" width="10%">
                    <b>{$product.quantity_available}</b>
                    </td>
                    <td class="productQuantity text-center" width="10%">
                        <span class="product_quantity_show badge">{$product.product_quantity}</span>
                    </td>
                    <td class="productQuantity text-center" width="10%">
                        <span class="badge badge-{$product.class_badge}">{$product.qty_ship}</span>
                    </td>
                    {if $product.product_quantity != $product.qty_ship}
                        <td class="text-center" width="15%">
                            <input type="number" id="quantity_shipped_{$product.id_order_detail}" name="products[{$product.id_order_detail}]" value="1" max="{$product.product_quantity-$product.qty_ship}" min="0">
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
                        <div class="form-group">
                            <label class="control-label col-lg-3">{l s='Tracking number' mod='ec_reliquat'}</label>
                            <div class="col-lg-9">
                                <input type="text" name="trackingNumber" value="" placeholder="XXXXXXXXX">
                                <p class="help-block">{l s='This field is not required. You can shipped product without tracking number.' mod='ec_reliquat'}</p>
                            </div>
                            <label class="control-label col-lg-3 required">{l s='Carriers' mod='ec_reliquat'}</label>
                            <div class="col-lg-9">
                                <select name="id_carrier" >
                                {foreach $carriers as $carrier}
                                    <option value="{$carrier.id_carrier}">{$carrier.name}</option>
                                {/foreach}
                                </select>
                                <p class="help-block">{l s='Select the carrier for this shipping.' mod='ec_reliquat'}</p>
                            </div>
                            <label class="control-label col-lg-3 required">{l s='Order state' mod='ec_reliquat'}</label>
                            <div class="col-lg-9">
                                <select name="id_order_state" >
                                {foreach $order_states as $order_state}
                                    <option value="{$order_state.id_order_state}">{$order_state.name}</option>
                                {/foreach}
                                </select>
                                <p class="help-block">{l s='Select order state for this shipping.' mod='ec_reliquat'}</p>
                            </div>
                            <label class="control-label col-lg-3">{l s='Email' mod='ec_reliquat'}</label>
                            <div class="col-lg-9">
                                <span class="switch prestashop-switch fixed-width-lg">
                                    <input name="reliquatEmail" id="reliquatEmail_on" value="1" checked="checked" type="radio">
                                    <label for="reliquatEmail_on">
                                        {l s='Yes' mod='ec_reliquat'}
                                    </label>
                                    <input name="reliquatEmail" id="reliquatEmail_off" value="0" type="radio">
                                    <label for="reliquatEmail_off">
                                        {l s='No' mod='ec_reliquat'}
                                    </label>
                                    <a class="slide-button btn"></a>
                                </span>
                                <p class="help-block">{l s='Send an email to inform the client.' mod='ec_reliquat'}</p>
                            </div>
                            <label class="control-label col-lg-3">{l s='Attachments' mod='ec_reliquat'}</label>
                            <div class="attachment">
                                <div class="col-lg-2">
                                    <input type="text" name="filename_reliquat_attachment1" value="" placeholder="{l s='filename' mod='ec_reliquat'}">
                                </div>
                                <div class="col-lg-7">
                                    <div class="col-lg-5">
                                        <input data-id="1" id="reliquat_attachment1" type="file" name="reliquat_attachment1" class="hide reliquat_attachment">
                                		<div class="dummyfile input-group">
                                			<span class="input-group-addon"><i class="icon-file"></i></span>
                                			<input id="reliquat_attachment-name1" data-id="1" class="reliquat_attachment-name" type="text" name="reliquat_attachment1" readonly="">
                                			<span class="input-group-btn">
                                				<button data-id="1" type="button" name="submitAddAttachments" class="btn btn-default reliquat_attachment-selectbutton">
                                					<i class="icon-folder-open"></i> Ajouter un fichier
                                                </button>
                 							</span>
                                		</div>
                                    </div>
                                    <button id="addattachment" class="btn btn-default"><i class="icon-plus"></i></button>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <input type="hidden" name="id_order" value="{$ec_id_order}">
                                <button type="submit" name="submitEcReliquatShip" class="btn btn-primary pull-left">{l s='Send' mod='ec_reliquat'}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
