<?php /* Smarty version Smarty-3.0.8, created on 2011-09-26 10:57:51
         compiled from "/var/www/vhosts/uenoya.hiroshimacity.jp/work/roomReservation/./templates/dataTable/scheduleListDataTable.tpl" */ ?>
<?php /*%%SmartyHeaderCode:19287727944e7fdc1f2bd180-74258845%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '10a6605067c2f3bb882176d672a6ac0a2b7556e6' => 
    array (
      0 => '/var/www/vhosts/uenoya.hiroshimacity.jp/work/roomReservation/./templates/dataTable/scheduleListDataTable.tpl',
      1 => 1317002256,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '19287727944e7fdc1f2bd180-74258845',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_escape')) include '/var/www/vhosts/uenoya.hiroshimacity.jp/work/lib/Smarty/libs/plugins/modifier.escape.php';
if (!is_callable('smarty_modifier_date_format')) include '/var/www/vhosts/uenoya.hiroshimacity.jp/work/lib/Smarty/libs/plugins/modifier.date_format.php';
?><tr>
	<th>会社名</th>

	<th>開始時間</th>

	<th>予約希望期間</th>

	<th>操作機能</th>
</tr>

<?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('params')->value['scheduleListTable']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
?>
<tr>

		<td ALIGN="center"><?php echo smarty_modifier_escape($_smarty_tpl->getVariable('row')->value->username);?>
</td>

		<td ALIGN="center"><?php echo smarty_modifier_escape(smarty_modifier_date_format($_smarty_tpl->getVariable('row')->value->start_time,"%G年%m月%d日-%k時%M分"));?>
</td>

		<td ALIGN="center"><?php echo smarty_modifier_escape(smarty_modifier_date_format($_smarty_tpl->getVariable('row')->value->length,"%k時間%M分"));?>
</td>

		<td>
			<a href='javascript:void(0)' id='edit_<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('row')->value->id);?>
_<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('row')->value->start_time);?>
'">編集</a>
			<a href='#' id='delete_<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('row')->value->id);?>
_<?php echo smarty_modifier_escape($_smarty_tpl->getVariable('row')->value->start_time);?>
'>削除</a>
		</td>

</tr>

<?php }} ?>

