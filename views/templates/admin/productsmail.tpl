{foreach from=$products item=product}
    <tr class="product color_line_even" style="text-align: center;"> 
        <td style="border: 1px solid #D6D4D4;" class="product center">{$product.name|escape:'htmlall':'UTF-8'}</td>
        <td style="border: 1px solid #D6D4D4;" class="product center">{$product.quantity|escape:'htmlall':'UTF-8'}</td>
    </tr>
{/foreach}