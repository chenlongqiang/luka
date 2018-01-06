<?php /* Smarty version Smarty-3.0.8, created on 2011-08-18 17:59:02
         compiled from "/var/www/vhosts/uenoya.hiroshimacity.jp/work/roomReservation/./templates/dataTable/buildMasterDataTable.tpl" */ ?>
<?php /*%%SmartyHeaderCode:19704255094e4cd45662bc04-87552207%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4ffb14980434bcddaf735192070ee7439819692b' => 
    array (
      0 => '/var/www/vhosts/uenoya.hiroshimacity.jp/work/roomReservation/./templates/dataTable/buildMasterDataTable.tpl',
      1 => 1313657092,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '19704255094e4cd45662bc04-87552207',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
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
