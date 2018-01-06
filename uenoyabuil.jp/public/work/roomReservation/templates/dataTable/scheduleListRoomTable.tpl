<select name="searchRoomName" id="searchRoomName">
	<option>会議室選択</option>
	{foreach from=$params.roomTable item=row}
		<option value="{$row->id|escape}">{$row->name|escape}</option>
	{/foreach}
</select>