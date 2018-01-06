<?php /* Smarty version Smarty-3.0.8, created on 2015-11-29 00:54:51
         compiled from "/var/www/vhost/dev.uenoyabuil.jp/public/work/roomReservation/./templates/master.tpl" */ ?>
<?php /*%%SmartyHeaderCode:20470649455659ce4b114a63-61420133%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2dbe93549d98cf00c77b7c4094cb20493660da58' => 
    array (
      0 => '/var/www/vhost/dev.uenoyabuil.jp/public/work/roomReservation/./templates/master.tpl',
      1 => 1317001807,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20470649455659ce4b114a63-61420133',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_escape')) include '/var/www/vhost/dev.uenoyabuil.jp/public/work/lib/Smarty/libs/plugins/modifier.escape.php';
?><?php $_smarty_tpl->tpl_vars['fn'] = new Smarty_variable($_smarty_tpl->getVariable('config')->value['filename'], null, null);?>
<!DOCTYPE html>

<html lang="ja">

<head runat="server">

	<title><?php echo $_smarty_tpl->getVariable('config')->value['master']['title'];?>
 - ウエノヤビル</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
					
<!-- [start] Load JavaScript Files -->
<?php  $_smarty_tpl->tpl_vars['file'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('config')->value[$_smarty_tpl->getVariable('fn')->value]['js_files']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['file']->key => $_smarty_tpl->tpl_vars['file']->value){
?>
<script type='text/javascript' src='<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['file']->value);?>
'></script>
<?php }} ?>
<!-- [ end ] Load JavaScript Files -->

<!-- [start] Load CSS Files -->
<?php  $_smarty_tpl->tpl_vars['file'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('config')->value[$_smarty_tpl->getVariable('fn')->value]['css_files']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['file']->key => $_smarty_tpl->tpl_vars['file']->value){
?>
<link rel='stylesheet' type='text/css' href='<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['file']->value);?>
'>
<?php }} ?>
<!-- [ end ] Load CSS Files -->

</head>

<body>
	
	<header>
		<h1><?php echo smarty_modifier_escape($_smarty_tpl->getVariable('config')->value['master']['title']);?>
</h1>
	</header>

	<div class='floatLeft'>
		<nav class='gadget'>
			<div class='title padding3 margin1'>menu</div>

			<?php  $_smarty_tpl->tpl_vars['menu'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('config')->value['master']['menu']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['menu']->key => $_smarty_tpl->tpl_vars['menu']->value){
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['menu']->key;
?>
				<?php if (is_array($_smarty_tpl->tpl_vars['menu']->value)){?>
				<div class='menu'><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['key']->value);?>

					<?php  $_smarty_tpl->tpl_vars['menuItem'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['menu']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['menuItem']->key => $_smarty_tpl->tpl_vars['menuItem']->value){
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['menuItem']->key;
?>
					<div class='menu pointer' title='<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['k']->value);?>
'><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['menuItem']->value);?>
</div>
					<?php }} ?>
				</div>
				<?php }else{ ?>
				<div class='menu pointer' title='<?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['key']->value);?>
'><?php echo smarty_modifier_escape($_smarty_tpl->tpl_vars['menu']->value);?>
</div>
				<?php }?>
			<?php }} ?>
		</nav>


	</div>
	<div class='floatLeft'>

		<!-- [start] Page Contents -->
		<div class='contentsContainer margin5'>
			<div class='title padding3 margin1'><?php echo smarty_modifier_escape($_smarty_tpl->getVariable('fn')->value);?>
</div>

			<?php $_template = new Smarty_Internal_Template(($_smarty_tpl->getVariable('fn')->value).".inc", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate(); $_template->rendered_content = null;?><?php unset($_template);?>
		
		</div>
		<!-- [ end ] Page Contents -->
	
	</div>

	<footer class='clearBoth'><?php echo smarty_modifier_escape($_smarty_tpl->getVariable('config')->value['master']['footer']);?>
</footer>

	<div id="loading" class="hidden"><img src="./img/loading.gif" alt="loading" /></div>

	<div class="hidden" id="debug" style="white-space:pre; font: x-small monospace;"></div>

</body>
</html>
