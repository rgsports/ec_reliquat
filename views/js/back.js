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
    var cpt = 2;
    $('body').on('click','#addattachment', function(e) {
        e.preventDefault();
        $('.attachment:last').after('<div class="attachment"><label class="control-label col-lg-3"></label><div class="col-lg-2"><input type="text" name="filename_reliquat_attachment'+cpt+'" value="" placeholder="filename"></div><div class="col-lg-7"><div class="col-lg-5"><input data-id="'+cpt+'" id="reliquat_attachment'+cpt+'" type="file" name="reliquat_attachment'+cpt+'" class="hide reliquat_attachment"><div class="dummyfile input-group"><span class="input-group-addon"><i class="icon-file"></i></span><input id="reliquat_attachment-name'+cpt+'" data-id="'+cpt+'" class="reliquat_attachment-name" type="text" name="reliquat_attachment'+cpt+'" readonly=""><span class="input-group-btn"><button data-id="'+cpt+'" type="button" name="submitAddAttachments" class="btn btn-default reliquat_attachment-selectbutton"><i class="icon-folder-open"></i> Ajouter un fichier				</button></span></div></div></div></div>');
        cpt++;
    });
    
     $('body').on('click','.reliquat_attachment-selectbutton', function(e) {
            id = $(this).attr('data-id');
			$('#reliquat_attachment'+id).trigger('click');
		});
        $('body').on('click','.reliquat_attachment-name', function(e) {
			id = $(this).attr('data-id');
			$('#reliquat_attachment'+id).trigger('click');
		});
        $('body').on('dragenter','.reliquat_attachment-name', function(e) {
			e.stopPropagation();
			e.preventDefault();
		});
        $('body').on('dragover','.reliquat_attachment-name', function(e) {
			e.stopPropagation();
			e.preventDefault();
		});
        
        $('body').on('drop','.reliquat_attachment-name', function(e) {
			e.preventDefault();
            id = $(this).attr('data-id');
			var files = e.originalEvent.dataTransfer.files;
			$('#reliquat_attachment'+id)[0].files = files;
			$(this).val(files[0].name);
		});
        $('body').on('change','.reliquat_attachment', function(e) {
            id =$(this).attr('data-id');
			if ($(this)[0].files !== undefined)
			{
				var files = $(this)[0].files;
				var name  = '';
				$.each(files, function(index, value) {
					name += value.name+', ';
				});
				$('#reliquat_attachment-name'+id).val(name.slice(0, -2));
			}
			else // Internet Explorer 9 Compatibility
			{
				var name = $(this).val().split(/[\\/]/);
				$('#reliquat_attachment-name'+id).val(name[name.length-1]);
			}
		});
        
        $('body').on('click','.editreliquat', function() {
            info_reliquat = JSON.parse($(this).attr('data-info_reliquat'));
            $('#editreliquat input.ec_trackingnumber').val(info_reliquat.tracking_number);
            $('#editreliquat select.ec_carrier').val(info_reliquat.id_carrier);
            $('#editreliquat select.ec_order_state').val(info_reliquat.id_order_state);
            $('#editreliquat #btneditreliquat').attr('data-id_reliquat', info_reliquat.id_reliquat);
        });
        
        $('body').on('click','#btneditreliquat', function() {
            id_reliquat = $(this).attr('data-id_reliquat');
            tracking_number = $('#editreliquat input.ec_trackingnumber').val();
            id_carrier = $('#editreliquat select.ec_carrier').val();
            id_order_state = $('#editreliquat select.ec_order_state').val();
            $.ajax({
                url: ec_base_uri+"/modules/ec_reliquat/ajax.php",
                type: "POST",
                data: ({
                    majsel: '1',
                    token: ec_token,
                    id_reliquat: id_reliquat,
                    tracking_number: tracking_number,
                    id_carrier: id_carrier,
                    id_order_state: id_order_state,
                    id_order: ec_id_order,
                }),
                dataType: "json"
            })
            .done(function (data) {
                location.reload();
            });
        });
        
        $('body').on('click','.deleteattachment', function(e) {
            e.preventDefault();
            cle = $('.deleteattachment').attr('data-cle');
            tr = $(this).parent().parent();
            $.ajax({
                url: ec_base_uri+"/modules/ec_reliquat/ajax.php",
                type: "POST",
                data: ({
                    majsel: '2',
                    token: ec_token,
                    cle: cle,
                }),
                dataType: "json"
            })
            .done(function (data) {
                tr.remove();
            });
        });
});

function ShowProducts(id_reliquat)
{
    if ($('#reliquat_table #products'+id_reliquat).css('display') == 'none') {
        $('#reliquat_table #products'+id_reliquat).show();
    } else {
        $('#reliquat_table #products'+id_reliquat).hide();
    }

}
function ShowAttachments(id_reliquat)
{
    if ($('#reliquat_table #attachments'+id_reliquat).css('display') == 'none') {
        $('#reliquat_table #attachments'+id_reliquat).show();
    } else {
        $('#reliquat_table #attachments'+id_reliquat).hide();
    }
}


