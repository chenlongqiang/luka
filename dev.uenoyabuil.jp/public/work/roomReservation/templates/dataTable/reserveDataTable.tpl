<div id="err_id" class="error hidden"></div>
<select name="searchRoomName" id="searchRoomName">
	<option value="-1">部屋選択</option>
	{foreach from=$params.roomTable item=row}
		<option value="{$row->id|escape}">{$row->name|escape}</option>
	{/foreach}
</select>
