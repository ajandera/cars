<?php

/**
 * 2017-12-21
 * * ajax.coreDelete renamed
 * 2017-12-14
 * * ajax.obrazkyCropThis renamed
 * 2017-08-11
 * + id_souvisi ve zpracovani, better dir structure
 * 2017-04-09
 * coreDBsel
 *
 * 2016-12-20
 *
 * @param unknown_type $nameIT
 * @param unknown_type $DB_alias
 * @param unknown_type $id_souvisi
 * @param unknown_type $styl
 */


function coreSouboryUploader($nameIT,$DB_alias,$id_souvisi,$styl=1,$crop='1/1',$returnStr=false)
{	global $C,$C_text_adresa;


	if ($returnStr)
	{
		ob_start();
	}

	?>


	<div class="modal fade" id="modal_<?php echo $nameIT?>" role="dialog" aria-labelledby="modalLabel" tabindex="-1">
      <div class="modal-dialog" role="document">
        <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modalLabel">Nástroj na oříznutí</h4>
          </div>

          <div class="modal-body">
            <div class="img-container">
              <img id="image_<?php echo $nameIT?>" src="" alt="Picture" class="img-responsive">
              <?php
              echo createInputHidden($nameIT.'_x',0);
              echo createInputHidden($nameIT.'_y',0);
              echo createInputHidden($nameIT.'_w',0);
              echo createInputHidden($nameIT.'_h',0);
              echo createInputHidden($nameIT.'_idimg','');
              ?>
            </div>
          </div>

          <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Zrušit</button>
				<button type="button" class="btn btn-primary" onclick="obrazkyCropThis_<?php echo $nameIT?>();">Oříznout</button>
          </div>

        </div>
      </div>
    </div>
	<script>
	$(document).ready(function()
	{

  	  window.addEventListener('DOMContentLoaded', function () {
	      var image = document.getElementById('image_<?php echo $nameIT?>');
	      var cropBoxData;
	      var canvasData;
	      var cropper;

	      $('#modal_<?php echo $nameIT?>').on('shown.bs.modal', function (event)
	      {
	      	var button = $(event.relatedTarget);
			var img = button.data('img');
			var x = button.data('x');
			var y = button.data('y');
			var w = button.data('w');
			var h = button.data('h');

			$('#image_<?php echo $nameIT?>').attr('src',img);
			$('#<?php echo $nameIT?>_idimg').val(button.data('id'));

	        cropper = new Cropper(image, {
	          aspectRatio: <?php echo $crop;?>,
	          viewMode: 1,
	          data:  {x:x, y:y, width:w, height:h},
	          crop: function(e)
				{
	          		$('#<?php echo $nameIT.'_x';?>').val(e.detail.x);
	          		$('#<?php echo $nameIT.'_y';?>').val(e.detail.y);
	          		$('#<?php echo $nameIT.'_w';?>').val(e.detail.width);
	          		$('#<?php echo $nameIT.'_h';?>').val(e.detail.height);
				},
	          ready: function ()
	          {
	            // Strict mode: set crop box data first
	            cropper.setCropBoxData(cropBoxData).setCanvasData(canvasData);
	          }
	        });
	      }).on('hidden.bs.modal', function () {
	        cropBoxData = cropper.getCropBoxData();
	        canvasData = cropper.getCanvasData();
	        cropper.destroy();
	      });
	    });
    });
  </script>

	<div class="HAuploader-button marginT0" id="buttonholder_<?php echo $nameIT?>">
        <i class="fa fa-plus-circle fa-fw fa-lg" aria-hidden="true"></i> <?php echo l("Kliknutím sem přidáte soubory.");?>
        <input id="fileupload_<?php echo $nameIT?>" type="file" name="files[]" multiple>
    </div>
    <div id="progress_<?php echo $nameIT?>" class="HAuploader-progress"><div class="progress-bar progress-bar-success"></div></div>
    <div id="files_<?php echo $nameIT?>" class="HAuploader-files"></div>

	<?php
	$addNew = false;
	if (empty($id_souvisi) && !$_GET['poptavka'])
	{
		$addNew = true;
		$pocetPolozek = 0;
	}
	else
	{
		$pocetPolozek = 0;
		$sql_query2 = coreDBSel("SELECT * FROM {$C->db_prefix}prirazene_obrazky WHERE id_souvisi = ? and prirazeni_alias = ? ORDER BY poradi, id",array($id_souvisi,$DB_alias));
		if ($sql_query2)
		{
			$pocetPolozek = $sql_query2->recordCount();
		}
	}

	?>
	<table class="HAuploader-table" id="donefiles_<?php echo $nameIT?>">
	<tbody>
	<?php
	if (!$addNew)
	{

			$_POST["{$nameIT}_id"] = array();
			$_POST["{$nameIT}_nazev"] = array();
			$_POST["{$nameIT}_obrazek"] = array();
			$_POST["{$nameIT}_poradi"] = array();
			$_POST["{$nameIT}_info"] = array();
			$_POST["{$nameIT}_skryt"] = array();

			if ($sql_query2)
			{
				while ($ou = $sql_query2->fetchRow())
				{
					$_POST["{$nameIT}_id"][] = $ou["id"];
					$_POST["{$nameIT}_nazev"][] = htmlspecialchars(stripslashes($ou["nazev"]));
					$_POST["{$nameIT}_obrazek"][] = $ou["obrazek"];
					$_POST["{$nameIT}_poradi"][] = $ou["poradi"];
					$_POST["{$nameIT}_info"][] = $ou["info"];
					$_POST["{$nameIT}_skryt"][] = $ou["stav"];
					$_POST["{$nameIT}_crop_x"][] = $ou["crop_x"];
					$_POST["{$nameIT}_crop_y"][] = $ou["crop_y"];
					$_POST["{$nameIT}_crop_w"][] = $ou["crop_w"];
					$_POST["{$nameIT}_crop_h"][] = $ou["crop_h"];
				}
			}
		  	if (isset($_POST["{$nameIT}_nazev"]))
			{
				foreach ($_POST["{$nameIT}_nazev"] as $k => $v)
			 	{
			 		if (isset($_POST["{$nameIT}_id"][$k]))
			 			$p_zakprodID = $_POST["{$nameIT}_id"][$k];
			 		else
			 			$p_zakprodID = false;

			 		$p_nazev = $_POST["{$nameIT}_nazev"][$k];
				  	$p_obrazek = $_POST["{$nameIT}_obrazek"][$k];
				  	$p_poradi = $_POST["{$nameIT}_poradi"][$k];
				  	$p_info = $_POST["{$nameIT}_info"][$k];
					$p_skryt = $_POST["{$nameIT}_skryt"][$k];
					$p_crop_x = $_POST["{$nameIT}_crop_x"][$k];
					$p_crop_y = $_POST["{$nameIT}_crop_y"][$k];
					$p_crop_w = $_POST["{$nameIT}_crop_w"][$k];
					$p_crop_h = $_POST["{$nameIT}_crop_h"][$k];

				  	echo '
						<tr>
							<td>';
					if (file_exists($p_obrazek))
					{

						$hashids = new Hashids\Hashids(date('Ymd'));
						$id = $hashids->encode($p_zakprodID);
						$secure_download = $C->dir.'object/soubory/'.$id;

				  		$xps = pathinfo($p_obrazek);
				  		if (in_array(strtolower($xps['extension']),array('jpg','jpeg','png','gif')))
				  		{
					  		if ($styl == 2)
					  		{
					  			echo '<a href="'.$secure_download.'" target="_blank" data-lightbox="gallery" class="fancy"><img src="/'.getImageThumb($p_obrazek,200,200,true,$p_crop_x,$p_crop_y,$p_crop_w,$p_crop_h).'" class="img-responsive" id="imgThumb'.$p_zakprodID.'" style="vertical-align: top;" alt="" /></a> ';
					  		}else{
					  			echo '<a href="'.$secure_download.'" target="_blank" data-lightbox="gallery" class="fancy"><img src="/'.getImageThumb($p_obrazek,200,200,true,$p_crop_x,$p_crop_y,$p_crop_w,$p_crop_h).'" class="img-responsive" id="imgThumb'.$p_zakprodID.'" style="vertical-align: top;" alt="" /></a> ';
					  		}
				  		}
						else if (in_array(strtolower($xps['extension']),array('mp4')))
				  		{
					  		echo '
								<video
									id="'. $k .'"
									class="video-js"
									controls
									preload="auto"
									width="200"
									height="200"
									data-setup="{}"
								>
									<source src="'.$p_obrazek.'" type="video/mp4" />
								</video>
							';
				  		}
				  		else
				  		{
				  			echo '<a href="'.$secure_download.'" target="_blank"><i class="fa fa-file-text fa-fw fa-3x" aria-hidden="true"></i> '.$xps['basename'].'</a> ';
				  		}
				  	}
				  	echo '<input type="hidden" class="" name="'.$nameIT.'_obrazek[]" value="'.'" /></td>';
				  	if ($styl == 2)
				  	{
				  		echo '<td><input type="text" class="form-control" name="'.$nameIT.'_nazev[a'.$p_zakprodID.']" value="'.$p_nazev.'" placeholder="Název díla" />';
						echo ' <i class="fa fa-sign-in click fa-fw bold fa-2x" onclick="CKEDITOR.instances[\'text\'].insertHtml(\'<a href=\\\'{hrefToGallery:'.$p_zakprodID.'}\\\' target=\\\'magazinegallery\\\' title=\\\'Zobrazit galerii\\\'><img src=\\\''.$C_text_adresa.$p_obrazek.'\\\' class=\\\'\\\' alt=\\\'\'+$(this).prev().val()+\'\\\' /></a>\');" title="Vložit do textu"></i>';
				  		echo '</td>';
				  	}

				  	echo '<td class="HAuploader-tablecontrols">'.($p_zakprodID?'<i class="fa fa-trash-o click marginT fa-fw bold fa-2x" onclick="DeleteRowID'.$nameIT.'(\''.$p_zakprodID.'\',this);" alt="'.l("smazat").'"></i>':'<i class="fa fa-trash-o deleteRow marginT fa-fw bold fa-2x" alt="'.l("smazat").'"></i>').'<i class="fa fa-arrows moveIT marginT fa-fw bold fa-2x" alt="'.l("přesunout").'"></i>
				  	<a data-target="#modal_'.$nameIT.'" data-toggle="modal" class="click" data-img="'.$p_obrazek.'" data-id="'.$p_zakprodID.'" data-x="'.$p_crop_x.'" data-y="'.$p_crop_y.'" data-w="'.$p_crop_w.'" data-h="'.$p_crop_h.'"><i class="fa fa-crop marginT fa-fw bold fa-2x" alt="'.l("Oříznout").'"></i></a>
				  	<i id="uu'.$p_zakprodID.'" class="fa fa-fw bold fa-2x click '.($p_skryt==1?'fa-eye-slash red':'fa-eye').'" title="Skrýt/Zobrazit z výpisu" onclick="changeLine(\'prirazene_obrazky\', '.$p_zakprodID.')"></i>
				  	'.($p_zakprodID?'<input type="hidden" name="'.$nameIT.'_id[a'.$p_zakprodID.']" value="'.$p_zakprodID.'" />':'').'<input type="hidden" name="'.$nameIT.'_poradi[a'.$p_zakprodID.']" value="'.$p_poradi.'" class="poradiClass" /></td>
					</tr>';
				}
			}
	} ?>
		</tbody>
		</table>

	<script type="text/javascript">
	var fileUploadIndex = 0;
	$(function () {
    'use strict';
    $('#fileupload_<?php echo $nameIT?>').fileupload({
        url: '<?php echo $C->dir;?>_js/fileupload/server/php/',
        dataType: 'json',
        autoUpload: true,
        limitMultiFileUploads: 1,
        sequentialUploads: true,
        dropZone: null,
        pasteZone: null,
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png|heic|pdf|doc|docx|xls|xlsx|ppt|mp4)$/i,
        maxFileSize: 12000000 // 12 MB
    }).on('fileuploadadd', function (e, data) {

        data.context = $('<div/>').appendTo('#files_<?php echo $nameIT?>');
        $.each(data.files, function (index, file)
        {
			$(data.context).append('<strong>'+(file.name)+'</strong> ('+bytesToSize(file.size)+') <span class="HAuploader-loading"><?php echo l("nahrávám soubor")?>...</span>');
        });
        stopForm = 1;

    }).on('fileuploadprocessalways', function (e, data) {

        var index = data.index,
            file = data.files[index],
            node = $(data.context.children()[index]);
			stopForm = 1;
        if (file.error)
        {
        	data.context.find(".HAuploader-loading").hide();
            data.context.prepend('<span class="HAuploader-error">'+file.error+': </span>');
			stopForm = 0;
        }


    }).on('fileuploadprogressall', function (e, data) {

    	var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#progress_<?php echo $nameIT?> .progress-bar').css('width',progress + '%' );

    }).on('fileuploaddone', function (e, data) {
        $.each(data.result.files, function (index, file)
        {
        	fileUploadIndex++;
        	$(data.context[index]).fadeOut();

        	if(file.type == 'image/png' || file.type == 'image/gif' || file.type == 'image/jpg' || file.type == 'image/jpeg')
        	{
        		var nahled = '<img src="'+file.url+'" class="opacity50 img-responsive" style="max-height: 200px; max-width: 200px" alt="" />';
        	}
        	else
        	{
        		var nahled = '<i class="fa fa-file-text fa-fw fa-3x" aria-hidden="true"></i> '+file.name;
        	}

			$("#donefiles_<?php echo $nameIT?> tbody").append('<tr>'+
				'<td>'+nahled+'<input type="hidden" value="<?php echo $C->dir; ?>files/_temp/'+file.name+'" name="<?php echo $nameIT?>_obrazek['+fileUploadIndex+']" /></td>'+
				<?php
				if ($styl == 2)
				{
				  	echo '\'<td><input class="form-control" type="text" name="'.$nameIT.'_nazev[\'+fileUploadIndex+\']"  placeholder="Název díla" /></td>\'+';
				} ?>
				'<td class="HAuploader-tablecontrols"><i class="fa fa-trash-o deleteRow marginT fa-fw bold fa-2x" alt="<?php echo l("smazat");?>"></i><i class="fa fa-arrows moveIT marginT fa-fw bold fa-2x" alt="<?php echo l("přesunout");?>"></i><input type="hidden" class="poradiClass" name="<?php echo $nameIT?>_poradi['+fileUploadIndex+']" value="" /></td>'+
			'</tr>');
			$('#donefiles_<?php echo $nameIT?> tbody').trigger( "sortstart" );
			stopForm = 0;
        });
    }).on('fileuploadfail', function (e, data) {
        $.each(data.result.files, function (index, file)
        {
        	data.context.find(".HAuploader-loading").hide();
            data.context.prepend('<span class="HAuploader-error">'+file.error+': </span>');
            stopForm = 0;
        });
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
	});

	function DeleteRowID<?php echo $nameIT?>(idk, tuten)
	{
		conf = confirm('<?php echo l("Opravdu smazat?")?>');
		if(conf)
		{
			$.ajax({type: "POST", 	headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') },url: "<?php echo $C->dir; ?>ajax/core/ajax.coreDelete/",data: {id: idk, typ: "prirazene_obrazky"},		success: function(html)
			{
				if (html=="x"){ alert("<?php echo l("Vyskytla se chyba při úpravě.")?>"); }
				else{	$(tuten).parent().parent().fadeOut().remove();	}
			}});
		}
	}
	function obrazkyCropThis_<?php echo $nameIT?>()
	{
		var x = $('#<?php echo $nameIT?>_x').val();
		var y = $('#<?php echo $nameIT?>_y').val();
		var w = $('#<?php echo $nameIT?>_w').val();
		var h = $('#<?php echo $nameIT?>_h').val();
		var idimg = $('#<?php echo $nameIT?>_idimg').val();

		$.ajax({type: "POST",headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') },url: "<?php echo $C->dir; ?>ajax/core/ajax.obrazkyCropThis/",data: {x: x, y: y, w: w, h: h, id: idimg},
		success: function(html)
		{
			if (html=="x")
			{
				alert("<?php echo l("Vyskytla se chyba při úpravě.")?>");
			}
			else
			{
				$('#modal_<?php echo $nameIT?>').modal('hide');
				$('#imgThumb'+idimg).addClass('opacity30');
			}
		}});
	}

	$(document).ready(function(){

		$('#buttonholder_<?php echo $nameIT?>').bind('mousemove',function(e) {
        var offset = $(this).offset();
        $(this).find('input').css({
                'top': e.pageY - offset.top - ($('#buttonholder_<?php echo $nameIT?> input').innerHeight() / 2), //centers verticaly
                'left': e.pageX - offset.left - ($('#buttonholder_<?php echo $nameIT?> input').innerWidth() * 0.95) // moves the right part under the cursor
        });});

		$('#donefiles_<?php echo $nameIT?> tbody').sortable(
		{
			handle: '.moveIT',
			stop: function(event, ui)
			{
				$('#donefiles_<?php echo $nameIT?> tbody tr').each(function(index) {
					$(this).find(".poradiClass").val(index);
			  	});
			},
			create: function(event, ui)
			{
				$('#donefiles_<?php echo $nameIT?> tbody tr').each(function(index) {
					$(this).find(".poradiClass").val(index);
			  	});
			},
			helper: function(e, tr)
			{
			  var $originals = tr.children();
			  var $helper = tr.clone();
			  $helper.children().each(function(index)
			  {
			    $(this).width($originals.eq(index).width())
			  });
			  return $helper;
			}
		});
		$('#donefiles_<?php echo $nameIT?> tbody').bind( "sortstart", function(event, ui) {
			$('#donefiles_<?php echo $nameIT?> tbody tr').each(function(index) {
					$(this).find(".poradiClass").val(index);
			 });
		});

	});
	</script>
<?php

	if ($returnStr)
	{
		return ob_get_clean();
	}
}


function coreSouboryUploaderZpracuj($namePOST,$DB_alias,$id_souvisi,$resizeWidth=6000,$resizeHeight=6000,$thumbWidth=200,$thumbHeight=200,$cropImages=false)
{
	global $_POST,$_FILES,$INFO,$error_write,$error,$Aerror,$C_dbp;

	if (!function_exists("handleUpload")) coreError("Chybí funkce: handleUpload;");
	if (!function_exists("clear_str")) coreError("Chybí funkce: clear_str;");
	if (!function_exists("handleMove")) coreError("Chybí funkce: handleMove;");

	$dirrr = "files/obrazky/".clear_str($DB_alias).'/'.$id_souvisi;

	$pocetImgs = 0;

	if (isset($_POST["{$namePOST}_poradi"]))
	{
		foreach ($_POST["{$namePOST}_poradi"] as $k => $v)
		{

			if (isset($_POST["{$namePOST}_id"][$k]))
			$p_zakprodID = $_POST["{$namePOST}_id"][$k];
			else
			$p_zakprodID = false;

			$p_nazev = clearUnsafeChars($_POST["{$namePOST}_nazev"][$k]);
			$p_poradi = $_POST["{$namePOST}_poradi"][$k];
			$p_info = clearUnsafeChars($_POST["{$namePOST}_info"][$k]);


				if (isset($id_souvisi) && $p_zakprodID)
				{
					$edit = coreDBEdit('prirazene_obrazky',array('nazev'=>array('value'=>$p_nazev,'type'=>'strict'),'poradi'=>array('value'=>$p_poradi,'type'=>'strict'),'info'=>array('value'=>$p_info,'type'=>'strict'),),'id = "'.$p_zakprodID.'" ');
					$pocetImgs++;
				}
				else
				{
					if (!empty($_FILES["{$namePOST}_aobrazek"]["name"][$k]))
					{
						$edit = coreDBInsert('prirazene_obrazky',array('id','id_souvisi'=>array('value'=>$id_souvisi,'type'=>'strict'),'prirazeni_alias'=>array('value'=>$DB_alias,'type'=>'strict'),'id_autor'=>array('value'=>$INFO["id"]),'nazev'=>array('value'=>$p_nazev,'type'=>'strict'),'poradi'=>array('value'=>$p_poradi,'type'=>'strict'),));
						if ($edit)
						{
							$uudi = $edit['id'];
							$p_obrazek  =	handleUpload("{$namePOST}_aobrazek",$dirrr,false,true,$resizeWidth,$resizeHeight,$k,$thumbWidth,$thumbHeight,$cropImages,false,array('pdf','doc','docx','xls','xlsx','ppt','jpg','jpeg','heic','png','gif','mp4'),12,array(6000,6000),75,$uudi);
							if ($p_obrazek)
							{
								$edit = coreDBEdit('prirazene_obrazky',array('obrazek'=>array('value'=>$p_obrazek,'type'=>'strict')),'id = "'.$uudi.'" ');
								$pocetImgs++;
							}
						}
					}
					elseif (!empty($_POST["{$namePOST}_obrazek"][$k]))
					{
						$madd3 = coreDBInsert('prirazene_obrazky',array('id','id_souvisi'=>array('value'=>$id_souvisi,'type'=>'strict'),'prirazeni_alias'=>array('value'=>$DB_alias,'type'=>'strict'),'id_autor'=>array('value'=>$INFO["id"]),'info'=>array('value'=>$p_info,'type'=>'strict'),'nazev'=>array('value'=>$p_nazev,'type'=>'strict'),'poradi'=>array('value'=>$p_poradi,'type'=>'strict'),));
						if ($madd3)
						{
							$uudi = $madd3['id'];

							if (substr($_POST["{$namePOST}_obrazek"][$k],0,5) == "COPY:")
							{
								$_POST["{$namePOST}_obrazek"][$k] = substr($_POST["{$namePOST}_obrazek"][$k],5);

								$path_info = pathinfo($_POST["{$namePOST}_obrazek"][$k]);
								$Fname = $path_info["basename"];
								echo $path_info["dirname"].'/'.$uudi.'-'.$Fname;
								if(copy('.'.$_POST["{$namePOST}_obrazek"][$k],'.'.$path_info["dirname"].'/'.$uudi.'-'.$Fname))
								{
									$_POST["{$namePOST}_obrazek"][$k] = $path_info["dirname"].'/'.$uudi.'-'.$Fname;
								}
								else
								{
									coreErrorMessage('Chyba v kopírování soubor');
									continue;
								}
							}

							$p_obrazek  =	handleMove($_POST["{$namePOST}_obrazek"][$k],$dirrr,false,true,$resizeWidth,$resizeHeight,$thumbWidth,$thumbHeight,$cropImages,false,array('pdf','doc','docx','xls','xlsx','ppt','jpg','jpeg','png','gif','mp4'),12,array(6000,6000),75,$uudi);
							if ($p_obrazek)
							{
								$edit = coreDBEdit('prirazene_obrazky',array('obrazek'=>array('value'=>$p_obrazek,'type'=>'strict')),'id = "'.$uudi.'" ');
								$pocetImgs++;
							}
						}
					}
				}
		}
	}

	return $pocetImgs;
}


function getCoreSoubory($DB_alias,$id_souvisi,$limit=500)
{	global $C;

	$limit = (int)$limit;

	if ($id_souvisi == "ALL")
	{
		$sql = coreDBSel("SELECT * FROM {$C->db_prefix}prirazene_obrazky WHERE stav = 0 AND prirazeni_alias = ?  ORDER BY id_souvisi DESC, poradi DESC LIMIT ? ",array($DB_alias,$limit));
	}
	elseif (!is_numeric($id_souvisi))
	{
		return false;
	}
	else
	{
		$sql = coreDBSel("SELECT * FROM {$C->db_prefix}prirazene_obrazky WHERE stav = 0 AND prirazeni_alias = ? AND id_souvisi = ? ORDER BY poradi, id LIMIT ? ",array($DB_alias,$id_souvisi,$limit));
	}

	$arr = array();

	if ($sql)
	{
		while ($x = $sql->fetchRow())
		{
			if (!is_file($x["obrazek"])) continue;
			$xps = pathinfo($x["obrazek"]);
			$x["thumbnail"] = $xps["dirname"].'/thumb_'.$xps["basename"];
			$x["extension"] = strtolower($xps["extension"]);
			$x["filename"] = $xps["basename"];

			$hashids = new Hashids\Hashids(date('Ymd'));
			$id = $hashids->encode($x["id"]);

			$x["secure_download"] = $C->dir.'object/soubory/'.$id;
			$arr[$x["id"]] = $x;
		}
	}

	if (empty($arr))
	{
		return false;
	}
	else
	{
		return $arr;
	}

}
function deleteCoreSoubory($DB_alias,$id_souvisi)
{
	global $C;

	$DB_alias = addslashes($DB_alias);
	$sql = coreDBSel("DELETE FROM {$C->db_prefix }prirazene_obrazky WHERE stav >= 0 AND prirazeni_alias = ? AND id_souvisi = ? ",array($DB_alias,$id_souvisi));

	if (!$sql)
	{
		return false;
	}
	else
	{
		return true;
	}
}

