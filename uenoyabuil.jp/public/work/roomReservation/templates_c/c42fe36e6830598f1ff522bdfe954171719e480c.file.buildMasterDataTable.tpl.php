<?php /* Smarty version Smarty-3.0.8, created on 2015-11-28 23:46:01
         compiled from "/var/www/vhost/uenoyabuil.jp/public/work/roomReservation/./templates/dataTable/buildMasterDataTable.tpl" */ ?>
<?php /*%%SmartyHeaderCode:5897813135659be29e28933-92191126%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c42fe36e6830598f1ff522bdfe954171719e480c' => 
    array (
      0 => '/var/www/vhost/uenoyabuil.jp/public/work/roomReservation/./templates/dataTable/buildMasterDataTable.tpl',
      1 => 1317002340,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5897813135659be29e28933-92191126',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_escape')) include '/var/www/vhost/uenoyabuil.jp/public/work/lib/Smarty/libs/plugins/modifier.escape.php';
?><table id='dataTable'>
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
