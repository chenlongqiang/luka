<?php /* Smarty version Smarty-3.0.8, created on 2015-11-28 23:48:02
         compiled from "/var/www/vhost/uenoyabuil.jp/public/work/roomReservation/./templates/dataTable/reserveDataTable.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3736640415659bea25c5ba5-10590712%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f307df790fd448b567e59d52eeb1f8e9a73c22cf' => 
    array (
      0 => '/var/www/vhost/uenoyabuil.jp/public/work/roomReservation/./templates/dataTable/reserveDataTable.tpl',
      1 => 1317002321,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3736640415659bea25c5ba5-10590712',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_escape')) include '/var/www/vhost/uenoyabuil.jp/public/work/lib/Smarty/libs/plugins/modifier.escape.php';
?><div id="err_id" class="error hidden"></div>
<select name="searchRoomName" id="searchRoomName">
	<option value="-1">部屋選択</option>
	<?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('params')->value['roomTable']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
?>
		<option value="<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('row')->value->id);?>
"><?php echo smarty_modifier_escape($_smarty_tpl->getVariable('row')->value->name);?>
</option>
	<?php }} ?>
</select>
