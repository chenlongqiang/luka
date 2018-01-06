<tr>
	<th>会社名</th>

	<th>開始時間</th>

	<th>予約希望期間</th>

	<th>操作機能</th>
</tr>

{foreach from=$params.scheduleListTable item=row}
<tr>

		<td ALIGN="center">{$row->username|escape}</td>

		<td ALIGN="center">{$row->start_time|date_format:"%G年%m月%d日-%k時%M分"|escape}</td>

		<td ALIGN="center">{$row->length|date_format:"%k時間%M分"|escape}</td>

		<td>
			<a href='javascript:void(0)' id='edit_{$row->id|escape}_{$row->start_time|escape}'">編集</a>
			<a href='#' id='delete_{$row->id|escape}_{$row->start_time|escape}'>削除</a>
		</td>

</tr>

{/foreach}

