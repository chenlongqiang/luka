<?php /* Smarty version Smarty-3.0.8, created on 2011-09-26 10:57:50
         compiled from "/var/www/vhosts/uenoya.hiroshimacity.jp/work/roomReservation/./templates/dataTable/scheduleListRoomTable.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1481524294e7fdc1e3fa1a7-49986741%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '88eee5c33bbec2ebaf48c14f29ba29b2f3c50d4e' => 
    array (
      0 => '/var/www/vhosts/uenoya.hiroshimacity.jp/work/roomReservation/./templates/dataTable/scheduleListRoomTable.tpl',
      1 => 1317002222,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1481524294e7fdc1e3fa1a7-49986741',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_escape')) include '/var/www/vhosts/uenoya.hiroshimacity.jp/work/lib/Smarty/libs/plugins/modifier.escape.php';
?><select name="searchRoomName" id="searchRoomName">
	<option>会議室選択</option>
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