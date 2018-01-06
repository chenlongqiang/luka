<?php /* Smarty version Smarty-3.0.8, created on 2012-02-16 16:55:35
         compiled from "/var/www/vhosts/uenoya.hiroshimacity.jp/work/roomReservation/./templates/dataTable/roomMasterDataTable.tpl" */ ?>
<?php /*%%SmartyHeaderCode:9698926354f3cb677ac2b02-63276027%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '05dabab7535a6e609d09923da20348d6b059c7ae' => 
    array (
      0 => '/var/www/vhosts/uenoya.hiroshimacity.jp/work/roomReservation/./templates/dataTable/roomMasterDataTable.tpl',
      1 => 1317002307,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9698926354f3cb677ac2b02-63276027',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_escape')) include '/var/www/vhosts/uenoya.hiroshimacity.jp/work/lib/Smarty/libs/plugins/modifier.escape.php';
?><table id='dataTable'>
		<tr>
			<th>操作機能</th>
			<th>ビル名</th>
			<th>部屋名</th>
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
			<td><?php echo smarty_modifier_escape($_smarty_tpl->getVariable('row')->value->build_name);?>
</td>
			<td><?php echo smarty_modifier_escape($_smarty_tpl->getVariable('row')->value->name);?>
</td>
		</tr>
		<?php }} ?>
</table>