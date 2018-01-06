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
	
	<header style='margin:0; border-bottom:none;'>
		<h1 style="background-color: #003A66; color: #FFFFFF; font-size: 11pt; font-weight: bold; height: 25px; margin: 0; padding-top: 10px; padding-right: 10px; text-align: right; width: auto;">会議室予約情報</h1>
	</header>

	<div class='floatLeft' style='height: 730px; background-color: #e5e5e5;'>
		<nav class='gadget' style='margin:0; border:none;'>
			<a href="http://www.uenoyabuil.co.jp/"><img src="img/side_logo.gif" style="border:0;"></a>
			<div style='padding: 20px;'><h3>会議室予約連絡先</h3><br><b>電　話</b><br>０８２－２２７－５７３５<br>
				<b>ＦＡＸ</b><br>０８２－２２３－８５５４<br>
				<b>E-mail</b><br><a href="mailto:uenoyabuil@oregano.ocn.ne.jp">uenoyabuil@oregano.ocn.ne.jp</a><br><br><a href="http://uenoyabuil.jp/y0yak_new/kiyaku.html">コンファレンススクエア<br>
				（会議スペース）利用規則</a>
                <br>
                <br>
                <a href="http://www.uenoyabuil.co.jp/">トップページへ</a></div>
<!--
			<div class='title padding3 margin1'>menu</div>

			{foreach from=$config.master.menu item=menu key=key}
				{if is_array( $menu )}
				<div class='menu'>{$key}
					{foreach from=$menu item=menuItem key=k}
					<div class='menu pointer' title='{$k|escape}'>{$menuItem|escape}</div>
					{/foreach}
				</div>
				{else}
				<div class='menu pointer' title='{$key|escape}'>{$menu|escape}</div>
				{/if}
			{/foreach}
-->
		</nav>
	</div>

	<div class='floatLeft'>

		<!-- [start] Page Contents -->
		<div class='contentsContainer margin5' style='border:0;'>
<!--
			<div class='title padding3 margin1'>{$fn|escape}</div>
-->
			{include file="schedule.inc"}
		
		</div>
		<!-- [ end ] Page Contents -->
	
	</div>

	<footer class='clearBoth'>{$config.master.footer|escape}</footer>

	<div id="loading" class="hidden"><img src="./img/loading.gif" alt="loading" /></div>

	<div class="hidden" id="debug" style="white-space:pre; font: x-small monospace;"></div>

</body>
</html>
