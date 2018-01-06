<?php /* Smarty version Smarty-3.0.8, created on 2011-08-04 16:43:12
         compiled from "/var/www/vhosts/uenoya.hiroshimacity.jp/work/roomReservation/./templates/dataTable/buildMasterDataTable.inc" */ ?>
<?php /*%%SmartyHeaderCode:12490006974e3a4d90489d16-48323407%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1d702fd833d82379b6c3edfd2ee3deb5446410b8' => 
    array (
      0 => '/var/www/vhosts/uenoya.hiroshimacity.jp/work/roomReservation/./templates/dataTable/buildMasterDataTable.inc',
      1 => 1312443740,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12490006974e3a4d90489d16-48323407',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
﻿<table id='dataTable'>
	<tr>
		<th></th>
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
			<a href='#' id='edit_<?php echo $_smarty_tpl->getVariable('row')->value->id;?>
'>編集</a>
			<a href='#' id='delete_<?php echo $_smarty_tpl->getVariable('row')->value->id;?>
'>削除</a>
			<input type='hidden' id='name_<?php echo $_smarty_tpl->getVariable('row')->value->id;?>
' value='<?php echo $_smarty_tpl->getVariable('row')->value->name;?>
' />
		</td>
		<td><?php echo $_smarty_tpl->getVariable('row')->value->id;?>
</td>
		<td><?php echo $_smarty_tpl->getVariable('row')->value->name;?>
</td>
	</tr>
	<?php }} ?>
</table>
