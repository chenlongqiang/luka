<?php /* Smarty version Smarty-3.0.8, created on 2011-09-26 13:58:00
         compiled from "/var/www/vhosts/uenoya.hiroshimacity.jp/work/roomReservation/./templates/dataTable/reserveDataTable.tpl" */ ?>
<?php /*%%SmartyHeaderCode:10242142484e8006580771c5-22417600%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3149ff2be47526cef6567967fbc187b8347634f5' => 
    array (
      0 => '/var/www/vhosts/uenoya.hiroshimacity.jp/work/roomReservation/./templates/dataTable/reserveDataTable.tpl',
      1 => 1317002321,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '10242142484e8006580771c5-22417600',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_escape')) include '/var/www/vhosts/uenoya.hiroshimacity.jp/work/lib/Smarty/libs/plugins/modifier.escape.php';
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
