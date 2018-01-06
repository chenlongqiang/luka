<?php /* Smarty version Smarty-3.0.8, created on 2011-09-26 10:56:23
         compiled from "/var/www/vhosts/uenoya.hiroshimacity.jp/work/roomReservation/./templates/scheduleList.inc" */ ?>
<?php /*%%SmartyHeaderCode:4571502004e7fdbc70faf79-87569366%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8da2456be3001d23c8693422b13421687decd819' => 
    array (
      0 => '/var/www/vhosts/uenoya.hiroshimacity.jp/work/roomReservation/./templates/scheduleList.inc',
      1 => 1317001922,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4571502004e7fdbc70faf79-87569366',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_escape')) include '/var/www/vhosts/uenoya.hiroshimacity.jp/work/lib/Smarty/libs/plugins/modifier.escape.php';
?><section class='floatLeft'>

	<form id='scheduleListForm' name='scheduleListForm' action='reserve.php' method="POST">

		<table class="formTable">
			<tr>
				<th>検索範囲</th>
				<td>
					<div class="demo">
						<input id="dateStart" type="text" value="" readonly="readonly"></input>
					</div>
				</td>
				<th>～</th>
				<td>
					<div class="demo">
						<input id="dateStop" type="text" value="" readonly="readonly"></input>
					</div>
				</td>
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
		<br>
		<div><b>※検索範囲を指定しない場合には、本日からの検索結果になります。<br>
		&nbsp;&nbsp;&nbsp;尚、いずれの日付を指定することにより、検索ができます。</b></div>
		<br>
		<table class="formTable" id="dataTableContainerScheduleList">


		</table>
	<div id="sendValue"></div>


	</form>

</section>
