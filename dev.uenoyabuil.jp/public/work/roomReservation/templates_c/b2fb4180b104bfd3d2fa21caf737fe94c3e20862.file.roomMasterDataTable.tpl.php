<?php /* Smarty version Smarty-3.0.8, created on 2015-11-29 00:58:02
         compiled from "/var/www/vhost/dev.uenoyabuil.jp/public/work/roomReservation/./templates/dataTable/roomMasterDataTable.tpl" */ ?>
<?php /*%%SmartyHeaderCode:11690809845659cf0a87f206-94929961%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b2fb4180b104bfd3d2fa21caf737fe94c3e20862' => 
    array (
      0 => '/var/www/vhost/dev.uenoyabuil.jp/public/work/roomReservation/./templates/dataTable/roomMasterDataTable.tpl',
      1 => 1317002307,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '11690809845659cf0a87f206-94929961',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_escape')) include '/var/www/vhost/dev.uenoyabuil.jp/public/work/lib/Smarty/libs/plugins/modifier.escape.php';
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