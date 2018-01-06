<?php /* Smarty version Smarty-3.0.8, created on 2015-11-28 23:51:22
         compiled from "/var/www/vhost/uenoyabuil.jp/public/work/roomReservation/./templates/userSchedule.tpl" */ ?>
<?php /*%%SmartyHeaderCode:6603113775659bf6a399f55-48163058%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f13656bc2a2b3a9d2330c0885fa1c765e2b0849c' => 
    array (
      0 => '/var/www/vhost/uenoyabuil.jp/public/work/roomReservation/./templates/userSchedule.tpl',
      1 => 1386656344,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6603113775659bf6a399f55-48163058',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_escape')) include '/var/www/vhost/uenoyabuil.jp/public/work/lib/Smarty/libs/plugins/modifier.escape.php';
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
	
	<header style='margin:0; border-bottom:none;'>
		<h1 style="background-color: #003A66; color: #FFFFFF; font-size: 11pt; font-weight: bold; height: 25px; margin: 0; padding-top: 10px; padding-right: 10px; text-align: right; width: auto;">会議室予約情報</h1>
	</header>

	<div class='floatLeft' style='height: 730px; background-color: #e5e5e5;'>
		<nav class='gadget' style='margin:0; border:none;'>
			<a href="http://www.uenoyabuil.co.jp/"><img src="img/side_logo.gif" style="border:0;"></a>
			<div style='padding: 20px;'><h3>会議室予約連絡先</h3><br><b>電　話</b><br>０８２－２２７－５７３５<br>
				<b>ＦＡＸ</b><br>０８２－２２３－８５５４<br>
				<b>E-mail</b><br><a href="mailto:uenoyabuil@oregano.ocn.ne.jp">uenoyabuil@oregano.ocn.ne.jp</a><br><br><a href="http://www.uenoyabuil.co.jp/">トップページへ</a></div>
<!--
			<div class='title padding3 margin1'>menu</div>

			<?php  $_smarty_tpl->tpl_vars['menu'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('config')->value['master']['menu']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if ($_smarty_tpl->_count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['menu']->key => $_smarty_tpl->tpl_vars['menu']->value){
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['menu']->key;
?>
				<?php if (is_array($_smarty_tpl->tpl_vars['menu']->value)){?>
				<div class='menu'><?php echo $_smarty_tpl->tpl_vars['key']->value;?>

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
-->
		</nav>
	</div>

	<div class='floatLeft'>

		<!-- [start] Page Contents -->
		<div class='contentsContainer margin5' style='border:0;'>
<!--
			<div class='title padding3 margin1'><?php echo smarty_modifier_escape($_smarty_tpl->getVariable('fn')->value);?>
</div>
-->
			<?php $_template = new Smarty_Internal_Template("schedule.inc", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
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
