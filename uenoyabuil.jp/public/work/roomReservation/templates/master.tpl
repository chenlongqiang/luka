{assign var='fn' value=$config.filename}
<!DOCTYPE html>

<html lang="ja">

<head runat="server">

	<title>{$config.master.title} - ウエノヤビル</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
					
<!-- [start] Load JavaScript Files -->
{foreach from=$config.$fn.js_files item=file}
<script type='text/javascript' src='{$file|escape}'></script>
{/foreach}
<!-- [ end ] Load JavaScript Files -->

<!-- [start] Load CSS Files -->
{foreach from=$config.$fn.css_files item=file}
<link rel='stylesheet' type='text/css' href='{$file|escape}'>
{/foreach}
<!-- [ end ] Load CSS Files -->

</head>

<body>
	
	<header>
		<h1>{$config.master.title|escape}</h1>
	</header>

	<div class='floatLeft'>
		<nav class='gadget'>
			<div class='title padding3 margin1'>menu</div>

			{foreach from=$config.master.menu item=menu key=key}
				{if is_array( $menu )}
				<div class='menu'>{$key|escape}
					{foreach from=$menu item=menuItem key=k}
					<div class='menu pointer' title='{$k|escape}'>{$menuItem|escape}</div>
					{/foreach}
				</div>
				{else}
				<div class='menu pointer' title='{$key|escape}'>{$menu|escape}</div>
				{/if}
			{/foreach}
		</nav>


	</div>
	<div class='floatLeft'>

		<!-- [start] Page Contents -->
		<div class='contentsContainer margin5'>
			<div class='title padding3 margin1'>{$fn|escape}</div>

			{include file="$fn.inc"}
		
		</div>
		<!-- [ end ] Page Contents -->
	
	</div>

	<footer class='clearBoth'>{$config.master.footer|escape}</footer>

	<div id="loading" class="hidden"><img src="./img/loading.gif" alt="loading" /></div>

	<div class="hidden" id="debug" style="white-space:pre; font: x-small monospace;"></div>

</body>
</html>
