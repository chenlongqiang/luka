<?php /* Smarty version Smarty-3.0.8, created on 2011-09-26 10:47:21
         compiled from "/var/www/vhosts/uenoya.hiroshimacity.jp/work/roomReservation/./templates/buildMaster.inc" */ ?>
<?php /*%%SmartyHeaderCode:8554468294e7fd9a9a2d3d5-49621015%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '60031135df0238a192ec88e587ffd0fc8c9eaea6' => 
    array (
      0 => '/var/www/vhosts/uenoya.hiroshimacity.jp/work/roomReservation/./templates/buildMaster.inc',
      1 => 1317001618,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8554468294e7fd9a9a2d3d5-49621015',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_escape')) include '/var/www/vhosts/uenoya.hiroshimacity.jp/work/lib/Smarty/libs/plugins/modifier.escape.php';
?><section class='floatLeft'>
	<form id='buildMasterForm'>
		<table class="formTable">

			<tr>
				<th>ビルID</th>
				<td>
					<div id="err_id" class="error hidden"></div>
					<input type='number' id='id' name='id' />
				</td>
			</tr>

			<tr>
				<th>ビル名</th>
				<td>
					<div id="err_name" class="error hidden"></div>
					<input type='text' id='name' name='name' />
				</td>
			</tr>
			<tr>
				<td colspan="2" class="right"><button type='button' id='regist'>新規登録</button></td>
			</tr>
		</table>
	</form>
	<div id="error" class="error hidden margin5v"></div>
	<div class="hidden right margin10"><a href="insertMode" id="insertMode">新規登録...</a></div>
</section>
<section class="floatLeft">
	<div id="dataTableContainer">
	<table id='dataTable'>
		<tr>
			<th>操作機能</th>

			<th>ビルID</th>

			<th>ビル名</th>
		</tr>
		<?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('params')->value['table']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
?>
		<tr>
			<td>
				<a href='#' id='edit_<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('row')->value->id);?>
'>編集</a>
				<a href='#' id='delete_<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('row')->value->id);?>
'>削除</a>
				<input type='hidden' id='name_<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('row')->value->id);?>
' value='<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('row')->value->name);?>
' />
			</td>

			<td><?php echo smarty_modifier_escape($_smarty_tpl->getVariable('row')->value->id);?>
</td>

			<td><?php echo smarty_modifier_escape($_smarty_tpl->getVariable('row')->value->name);?>
</td>
		</tr>
		<?php }} ?>
	</table>

	</div>
</section>
