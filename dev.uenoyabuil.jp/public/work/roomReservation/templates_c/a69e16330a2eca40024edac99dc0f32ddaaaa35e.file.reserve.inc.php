<?php /* Smarty version Smarty-3.0.8, created on 2011-09-26 10:56:08
         compiled from "/var/www/vhosts/uenoya.hiroshimacity.jp/work/roomReservation/./templates/reserve.inc" */ ?>
<?php /*%%SmartyHeaderCode:6435053484e7fdbb8cee6e6-89864923%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a69e16330a2eca40024edac99dc0f32ddaaaa35e' => 
    array (
      0 => '/var/www/vhosts/uenoya.hiroshimacity.jp/work/roomReservation/./templates/reserve.inc',
      1 => 1317002150,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6435053484e7fdbb8cee6e6-89864923',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_escape')) include '/var/www/vhosts/uenoya.hiroshimacity.jp/work/lib/Smarty/libs/plugins/modifier.escape.php';
if (!is_callable('smarty_modifier_date_format')) include '/var/www/vhosts/uenoya.hiroshimacity.jp/work/lib/Smarty/libs/plugins/modifier.date_format.php';
?><section class='floatLeft'>
	<form id='reserveForm'>
		<table class="formTable">

			<tr>
				<th>ビル名</th>
				<td>
					<div id="err_build_id" class="error hidden"></div>
					<?php if ($_smarty_tpl->getVariable('params')->value['flg']=="edit"){?>
						<?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('params')->value['buildTable']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
?>
							<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('row')->value->name);?>

						<?php }} ?>
					<?php }else{ ?>
						<select name="searchBuildName" id="searchBuildName">
							<option value="-1">ビル選択</option>
							<?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('params')->value['buildTable']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
?>
								<option value="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('row')->value->id);?>
"><?php echo smarty_modifier_escape($_smarty_tpl->getVariable('row')->value->name);?>
</option>
							<?php }} ?>
						</select>
					<?php }?>
				</td>
			</tr>

			<tr>
				<th>部屋名</th>
				<td id="dataTableContainer">
					<div id="err_id" class="error hidden"></div>
					<?php if ($_smarty_tpl->getVariable('params')->value['flg']=="edit"){?>
						<select name="searchRoomName" id="searchRoomName">
							<?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('params')->value['roomTable']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
?>
								<option value="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('row')->value->id);?>
" <?php if (isset($_smarty_tpl->getVariable('params',null,true,false)->value['row']->id)){?><?php if ($_smarty_tpl->getVariable('row')->value->id==$_smarty_tpl->getVariable('params')->value['row']->id){?>selected<?php }?><?php }?>><?php echo smarty_modifier_escape($_smarty_tpl->getVariable('row')->value->name);?>
</option>
							<?php }} ?>
						</select>
					<?php }else{ ?>
						<select name="searchRoomName" id="searchRoomName">
							<option value="-1">部屋選択</option>
						</select>
					<?php }?>
				</td>
			</tr>
			<tr>
				<th>日付</th>
				<td>
					<select name="year" id="year">
						<?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('params')->value['year']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
?>
							<option value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['row']->value['year']);?>
" <?php if ($_smarty_tpl->getVariable('params')->value['flg']=="edit"){?><?php if ($_smarty_tpl->tpl_vars['row']->value['year']==(smarty_modifier_date_format($_smarty_tpl->getVariable('params')->value['row']->start_time,"%G"))){?>selected<?php }?><?php }else{ ?><?php if ($_smarty_tpl->tpl_vars['row']->value['year']==(smarty_modifier_date_format(time(),"%G"))){?>selected<?php }?><?php }?>><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['row']->value['year']);?>
年</option>
						<?php }} ?>
					</select>
					<select name="month" id="month">
						<?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('params')->value['month']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
?>
							<option value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['row']->value['month']);?>
" <?php if ($_smarty_tpl->getVariable('params')->value['flg']=="edit"){?><?php if ($_smarty_tpl->tpl_vars['row']->value['month']==(smarty_modifier_date_format($_smarty_tpl->getVariable('params')->value['row']->start_time,"%m"))){?>selected<?php }?><?php }else{ ?><?php if ($_smarty_tpl->tpl_vars['row']->value['month']==(smarty_modifier_date_format(time(),"%m"))){?>selected<?php }?><?php }?>><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['row']->value['month']);?>
月</option>
						<?php }} ?>
					</select>
					<select name="day" id="day">
						<?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('params')->value['day']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
?>
							<option value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['row']->value['day']);?>
" <?php if ($_smarty_tpl->getVariable('params')->value['flg']=="edit"){?><?php if ($_smarty_tpl->tpl_vars['row']->value['day']==(smarty_modifier_date_format($_smarty_tpl->getVariable('params')->value['row']->start_time,"%d"))){?>selected<?php }?><?php }else{ ?><?php if ($_smarty_tpl->tpl_vars['row']->value['day']==(smarty_modifier_date_format(time(),"%d"))){?>selected<?php }?><?php }?>><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['row']->value['day']);?>
日</option>
						<?php }} ?>
					</select>
				</td>
			</tr>
			<tr>
				<th>会社名</th>
				<td>
					<div id="err_username" class="error hidden"></div>
					<input type='text' id='username' name='username' <?php if ($_smarty_tpl->getVariable('params')->value['flg']=="edit"){?>value="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('params')->value['row']->username);?>
"<?php }?>/>
				</td>
			</tr>
			<tr>
				<th>希望開始時間</th>
				<td>
					<select name="startTime" id="startTime">
						<?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('params')->value['startTime']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
?>
							<option value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['row']->value);?>
" <?php if (isset($_smarty_tpl->getVariable('params',null,true,false)->value['row']->start_time)){?><?php if ($_smarty_tpl->tpl_vars['row']->value==(smarty_modifier_date_format($_smarty_tpl->getVariable('params')->value['row']->start_time,"%k:%M"))){?>selected<?php }?><?php }?>><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['row']->value);?>
</option>
						<?php }} ?>
					</select>
				</td>
			</tr>
			<tr>
				<th>希望使用期間</th>
				<td>
					<div id="err_length" class="error hidden"></div>
					<select name="timeLength" id="timeLength">
						<?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['rowKey'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('params')->value['timeLength']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
 $_smarty_tpl->tpl_vars['rowKey']->value = $_smarty_tpl->tpl_vars['row']->key;
?>
							<option value="<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['rowKey']->value);?>
" <?php if (isset($_smarty_tpl->getVariable('params',null,true,false)->value['row']->length)){?><?php if ($_smarty_tpl->tpl_vars['rowKey']->value==$_smarty_tpl->getVariable('params')->value['row']->length){?>selected<?php }?><?php }?>><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['row']->value);?>
</option>
						<?php }} ?>
					</select>
				</td>
			</tr>
			<tr>

				<td class="right" colspan="2" <?php if ($_smarty_tpl->getVariable('params')->value['flg']=="edit"){?>id="oldData_<?php echo smarty_modifier_escape($_POST['roomId']);?>
_<?php echo smarty_modifier_escape($_POST['startTime']);?>
"<?php }?>>
					<button id="regist" type="button"><?php if ($_smarty_tpl->getVariable('params')->value['flg']=="newRegist"){?>新規登録<?php }else{ ?>編集<?php }?></button>
				</td>
			</tr>
		</table>
	</form>
</section>
