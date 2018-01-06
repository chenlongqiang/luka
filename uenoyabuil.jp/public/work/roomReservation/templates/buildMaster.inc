<section class='floatLeft'>
	<form id='buildMasterForm'>
		<table class="formTable">

			<tr>
				<th>ビルID</th>
				<td>
					<div id="err_id" class="error hidden"></div>
					<input type='number' id='id' name='id' />
				</td>
			</tr>

			<tr>
				<th>ビル名</th>
				<td>
					<div id="err_name" class="error hidden"></div>
					<input type='text' id='name' name='name' />
				</td>
			</tr>
			<tr>
				<td colspan="2" class="right"><button type='button' id='regist'>新規登録</button></td>
			</tr>
		</table>
	</form>
	<div id="error" class="error hidden margin5v"></div>
	<div class="hidden right margin10"><a href="insertMode" id="insertMode">新規登録...</a></div>
</section>
<section class="floatLeft">
	<div id="dataTableContainer">
	<table id='dataTable'>
		<tr>
			<th>操作機能</th>

			<th>ビルID</th>

			<th>ビル名</th>
		</tr>
		{foreach from=$params.table item=row}
		<tr>
			<td>
				<a href='#' id='edit_{$row->id|escape}'>編集</a>
				<a href='#' id='delete_{$row->id|escape}'>削除</a>
				<input type='hidden' id='name_{$row->id|escape}' value='{$row->name|escape}' />
			</td>

			<td>{$row->id|escape}</td>

			<td>{$row->name|escape}</td>
		</tr>
		{/foreach}
	</table>

	</div>
</section>
