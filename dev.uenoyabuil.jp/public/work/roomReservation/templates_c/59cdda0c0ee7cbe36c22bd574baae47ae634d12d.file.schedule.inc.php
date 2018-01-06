<?php /* Smarty version Smarty-3.0.8, created on 2011-09-26 10:56:06
         compiled from "/var/www/vhosts/uenoya.hiroshimacity.jp/work/roomReservation/./templates/schedule.inc" */ ?>
<?php /*%%SmartyHeaderCode:8842512914e7fdbb6696724-91169238%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '59cdda0c0ee7cbe36c22bd574baae47ae634d12d' => 
    array (
      0 => '/var/www/vhosts/uenoya.hiroshimacity.jp/work/roomReservation/./templates/schedule.inc',
      1 => 1317001946,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8842512914e7fdbb6696724-91169238',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_escape')) include '/var/www/vhosts/uenoya.hiroshimacity.jp/work/lib/Smarty/libs/plugins/modifier.escape.php';
?><center>
	<div>
		<div class="floatLeft padding10">
			<table class="formTable">
				<tr>
					<th>ビル名</th>
					<td>
							<select name="searchBuildName" id="searchBuildName">
								<option>ビル選択</option>
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
					</td>
					<th>会議室</th>
					<td id="dataTableContainer">
							<select name="searchRoomName" id="searchRoomName">
								<option>会議室選択</option>
							</select>
					</td>
				</tr>
			</table>
		</div>
		<div class="floatRight padding5a">
		<table>
			<tr>
				<th>例</th>
				<td style="text-align: center;">
					× = 午前予約済<br>
					-------------------<br>
					× = 午後予約済
				</td>
				<td>
					× = 全日予約済
				</td>
			</tr>
		</table>
		</div>
		<center>
			<div id="jMonthCalendar" class="clearBoth margin5"></div>
		</center>
	</div>
</center>
