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
