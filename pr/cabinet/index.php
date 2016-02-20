<?
include($_SERVER['DOCUMENT_ROOT'].'/beacon.php');
$GLOBALS['site_settings']['TAB_TITLE'] = 'Кировцены - Личный кабинет';
$GLOBALS['site_settings']['META']['TITLE'] = 'Кировцены';
$GLOBALS['site_settings']['META']['DESCRIPTION'] = 'Кировцены - аналитика цен на продукты в Кирове';
$GLOBALS['site_settings']['META']['KEYWORDS'] = 'Киров, цены, продукты';
include($GLOBALS['site_settings']['root_path'].'/template/header/index.php');
?><h1>Личный кабинет</h1>
<?if($_FILES['user_img']){
	$white_list = array('png', 'bmp', 'gif', 'jpg', 'jpeg');
	if(!is_array(LoadFile('user_img', $white_list, 1048576, $_SERVER['DOCUMENT_ROOT'].'/'.$GLOBALS['site_settings']['site_folder'].$GLOBALS['site_settings']['img_path']))){
		$query = "INSERT INTO ".$GLOBALS['site_settings']['db']['tables']['images']." (path,alt,title,creator) VALUES ({?},{?},{?},{?})";
		$image_id = $db->query($query, array($GLOBALS['site_settings']['site_folder'].$GLOBALS['site_settings']['img_path'].$_FILES['user_img']['name'],'','',$_SESSION['user']['id']));
		if($image_id){
			$query = "SELECT `id` FROM `".$GLOBALS['site_settings']['db']['tables']['user_images']."` WHERE `user` = {?} AND `main` = {?}";
			$old_image = $db->selectRow($query, array($_SESSION['user']['id'], 1));
			if($old_image['id']){
				$query = "DELETE FROM ".$GLOBALS['site_settings']['db']['tables']['user_images']." WHERE `id` = {?}";
				$delete_old = $db->query($query, array($old_image['id']));
			}
			$query = "INSERT INTO ".$GLOBALS['site_settings']['db']['tables']['user_images']." (user,image,alt,title,main,creator) VALUES ({?},{?},{?},{?},{?},{?})";
			$image_rel_id = $db->query($query, array($_SESSION['user']['id'],$image_id,'','',1,$_SESSION['user']['id']));
			//rp(array($query, array($user_id,$image_id,'','',1,$_SESSION['user']['id'])));
		}
	}
}?>
<script>
	function trim( str, charlist ) {
		charlist = !charlist ? ' \s\xA0' : charlist.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '\$1');
		var re = new RegExp('^[' + charlist + ']+|[' + charlist + ']+$', 'g');
		return str.replace(re, '');
	}

	function ImgSend(name) {
		$('#'+name).submit();
		/*//formData=$('#'+name).serialize();
		form = $('#'+name);
		//formData = new FormData($(form)[0]);
		var formData = new FormData($('#img_form')[0]); 
		$.post(
			'edit_ajax.php',
			formData,
			function(data){

			}
		);*/
	}
	function cancel(name){
		old_value = $('input[name='+name+']').attr('old_value');
		$('input[name='+name+']').replaceWith('<span id="edit_'+name+'">'+htmlspecialchars(old_value, null, null, false)+'</span>');
		$('button#cancel_'+name+'_button').siblings('button#edit_'+name+'_button').removeClass('save').text('Редактировать');
		$('button#cancel_'+name+'_button').remove();
	}
	function edit_value(name){
		if(!$('button#edit_'+name+'_button').hasClass('save')){ 
			old_value = $('span#edit_'+name+':eq(0)').text();
			$('span#edit_'+name).replaceWith('<input type="text" name="'+name+'" value="'+old_value+'" old_value="'+old_value+'">');
			$('button#edit_'+name+'_button').addClass('save').text('Сохранить').after('<button id="cancel_'+name+'_button" onclick="cancel(\''+name+'\'); return false;">Отмена</button>');
		}else{
			input = $('#edit_'+name+'_button').siblings('input');
			name_ajax = $(input).attr('name');
			value_ajax = $(input).val();
			if(name == 'email'){
				var re = /^[\w-\.]+@[\w-]+\.[a-z]{2,4}$/i;
				var myMail = value_ajax;
				var valid = re.test(myMail);
				if(!valid){
					alert('Некорректный email');
					return false;
				}
			}
			$.post(
				'edit_ajax.php',
				{
					name: name_ajax,
					value: value_ajax
				},
				function(data){
					value = $('input[name='+name+']').val();
					old_value = $('input[name='+name+']').attr('old_value');
					if(value != old_value){
						//$(this).closest('div').innerHTML = data; 
						if(Number(data) != '0'){
							alert(data);
						}else{
							$('input[name='+name+']').replaceWith('<span id="edit_'+name+'">'+htmlspecialchars(value, null, null, false)+'</span>');
							$('button#cancel_'+name+'_button').siblings('button#edit_'+name+'_button').removeClass('save').text('Редактировать');
							$('button#cancel_'+name+'_button').remove();
						}
						//else alert('no');
					}else{
						cancel(name);
					}
				}
			);
		}
	}
	
	function textareaCancel(name){
		old_value = $('textarea[name='+name+']').attr('old_value');
		$('textarea[name='+name+']').replaceWith('<div id="edit_'+name+'">'+htmlspecialchars(old_value, null, null, false)+'</div>');
		if(trim(value).length > 0) button_text = 'Редактировать';
		else button_text = 'Добавить';
		$('button#cancel_'+name+'_button').siblings('button#edit_'+name+'_button').removeClass('save').text(button_text);
		$('button#cancel_'+name+'_button').remove();
	}
	function textareaEdit_value(name){
		if(!$('button#edit_'+name+'_button').hasClass('save')){ 
			old_value = $('div#edit_'+name+':eq(0)').text();
			$('div#edit_'+name).replaceWith('<textarea name="'+name+'" old_value="'+old_value+'">'+old_value+'</textarea>');
			$('button#edit_'+name+'_button').addClass('save').text('Сохранить').after('<button id="cancel_'+name+'_button" onclick="textareaCancel(\''+name+'\'); return false;">Отмена</button>');
		}else{
			input = $('#edit_'+name+'_button').siblings('div').find('textarea:eq(0)');
			name_ajax = $(input).attr('name');
			value_ajax = $(input).val();
			$.post(
				'edit_ajax.php',
				{
					name: name_ajax,
					value: value_ajax
				},
				function(data){
					value = $('textarea[name='+name+']').val();
					old_value = $('textarea[name='+name+']').attr('old_value');
					if(value != old_value){
						//$(this).closest('div').innerHTML = data; 
						if(Number(data) != '0'){
							alert(data);
						}else{
							$('textarea[name='+name+']').replaceWith('<div id="edit_'+name+'">'+htmlspecialchars(value, null, null, false)+'</div>');
							if(trim(value).length > 0) button_text = 'Редактировать';
							else button_text = 'Добавить';
							$('button#cancel_'+name+'_button').siblings('button#edit_'+name+'_button').removeClass('save').text(button_text);
							$('button#cancel_'+name+'_button').remove();
						}
						//else alert('no');
					}else{
						textareaCancel(name);
					}
				}
			);
		}
	}
</script>
	<?
	if($_SESSION['user']['id']){
		//echo '<pre>'; print_r($_SESSION['user']); echo '</pre>';
		
		//rp(Img_Select(array('user_obj' => $_SESSION['user']['id'], 'main' => 1), array('path')));
		$my_img = Img_Select(array('user' => $_SESSION['user']['id'], 'main' => 1, array('path')));
		$user_props = User_Select($_SESSION['user']['id'], array('name', 'login', 'text', 'email'));?>
		<div style=" height: 140px; width: 140px; background-color: #EDEDED; border: 2px solid #AAAAAA; position: relative; display: inline-block; margin: 20px">
			<?if($my_img){?>
				<a class="fancybox" href="<?=$my_img['path']?>">
					<img style=" max-width: 140px; max-height: 100%; margin:auto; position: absolute; top: 0; left: 0; bottom: 0; right: 0;" src="<?=$my_img['path']?>">
				</a>
			<?}else{?>
				<button style="margin:auto; position: absolute; top: 40%; left: 0; bottom: 40%; right: 0;" onclick="$('#imgSel').click(); return false" >Добавить фото</button>
			<?}?>
			<div style="position: absolute; margin-left: 160px; width: 600px;">
				<div>
					<h3 style="display: inline-block; margin-top: 0px;">
						<b>Ф.И.О.: </b>
					</h3>
					<span id="edit_name"><?=$user_props['name']?></span>
					<button id="edit_name_button" onclick="edit_value('name'); return false;">Редактировать</button>
				</div>
				<div>
					<h3 style="display: inline-block">
						<b>Логин: </b>
					</h3>
					<span id="edit_login"><?=$user_props['login']?></span>
					<button id="edit_login_button" onclick="edit_value('login'); return false;">Редактировать</button>
				</div>
				<div>
					<h3 style="display: inline-block">
						<b>Email: </b>
					</h3> 
					<span id="edit_email"><?=$user_props['email']?></span>
					<button id="edit_email_button" onclick="edit_value('email'); return false;">Редактировать</button>
				</div>
			</div>
		</div>
		<div>
			<?if($my_img ){/*?>
				<div style="margin-left: 20px; margin-top: -20px;">
					<form id="img_form" method="post" enctype="multipart/form-data">
						<input type="file" name="user_img" onchange="ImgSend('img_form'); return false;" id="imgSel" style="display: none">
						<button style="margin:auto;" onclick="$('#imgSel').click(); return false">Сменить фото</button>
						<button style="margin:auto;">Х</button>
					</form>
				</div>
			<?*/}?>
			<form style="display: none" id="img_form" method="post" enctype="multipart/form-data">
				<input style="display: none" type="file" id="imgSel" name="user_img" onchange="ImgSend('img_form'); return false";><br>
			</form>
			<?if($my_img){?>
				<input type="submit" onclick="$('#imgSel').click(); return false" name="Сменить фото" value="Сменить фото">
			<?}?>
		</div><div>
			<h3 style="display: inline-block; margin-bottom: 0px;">
				<b>Информация о себе: </b>
			</h3>
			<div>
			<div id="edit_text"><?=$user_props['text']?></div>
			</div>
			<?if($user_props['text']){?>
				<button id="edit_text_button" onclick="textareaEdit_value('text'); return false;">Редактировать</button>
			<?}else{?>
				<button id="edit_text_button" onclick="textareaEdit_value('text'); return false;">Добавить</button>
			<?}?>
		</div><br>
		<form action="<?=$GLOBALS['site_settings']['current_address']?>" method="get">
			<input type="hidden" name="user" value="exit">
			<button type="submit">Выйти из аккаунта</button>
		</form>
	<?}else{
		echo "<script>document.location.href = 'http://".$GLOBALS['site_settings']['server'].$GLOBALS['site_settings']['site_folder']."/';</script>";
	}	
	
	?>
<?include($GLOBALS['site_settings']['root_path'].'/template/footer/index.php');?>
