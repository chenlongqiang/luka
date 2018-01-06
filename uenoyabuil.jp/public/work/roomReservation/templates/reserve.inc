<section class='floatLeft'>
	<form id='reserveForm'>
		<table class="formTable">

			<tr>
				<th>ビル名</th>
				<td>
					<div id="err_build_id" class="error hidden"></div>
					{if $params.flg == "edit"}
						{foreach from=$params.buildTable item=row}
							{$row->name|escape}
						{/foreach}
					{else}
						<select name="searchBuildName" id="searchBuildName">
							<option value="-1">ビル選択</option>
							{foreach from=$params.buildTable item=row}
								<option value="{$row->id|escape}">{$row->name|escape}</option>
							{/foreach}
						</select>
					{/if}
				</td>
			</tr>

			<tr>
				<th>部屋名</th>
				<td id="dataTableContainer">
					<div id="err_id" class="error hidden"></div>
					{if $params.flg == "edit"}
						<select name="searchRoomName" id="searchRoomName">
							{foreach from=$params.roomTable item=row}
								<option value="{$row->id|escape}" {if isset($params.row->id)}{if $row->id == $params.row->id }selected{/if}{/if}>{$row->name|escape}</option>
							{/foreach}
						</select>
					{else}
						<select name="searchRoomName" id="searchRoomName">
							<option value="-1">部屋選択</option>
						</select>
					{/if}
				</td>
			</tr>
			<tr>
				<th>日付</th>
				<td>
					<select name="year" id="year">
						{foreach from=$params.year item=row}
							<option value="{$row['year']|escape}" {if $params.flg == "edit"}{if $row['year'] == ($params.row->start_time|date_format:"%G") }selected{/if}{else}{if $row['year'] == ($smarty.now|date_format:"%G")}selected{/if}{/if}>{$row['year']|escape}年</option>
						{/foreach}
					</select>
					<select name="month" id="month">
						{foreach from=$params.month item=row}
							<option value="{$row['month']|escape}" {if $params.flg == "edit"}{if $row['month'] == ($params.row->start_time|date_format:"%m") }selected{/if}{else}{if $row['month'] == ($smarty.now|date_format:"%m")}selected{/if}{/if}>{$row['month']|escape}月</option>
						{/foreach}
					</select>
					<select name="day" id="day">
						{foreach from=$params.day item=row}
							<option value="{$row['day']|escape}" {if $params.flg == "edit"}{if $row['day'] == ($params.row->start_time|date_format:"%d") }selected{/if}{else}{if $row['day'] == ($smarty.now|date_format:"%d")}selected{/if}{/if}>{$row['day']|escape}日</option>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<th>会社名</th>
				<td>
					<div id="err_username" class="error hidden"></div>
					<input type='text' id='username' name='username' {if $params.flg == "edit"}value="{$params.row->username|escape}"{/if}/>
				</td>
			</tr>
			<tr>
				<th>希望開始時間</th>
				<td>
					<select name="startTime" id="startTime">
						{foreach from=$params.startTime item=row}
							<option value="{$row|escape}" {if isset($params.row->start_time)}{if $row == ($params.row->start_time|date_format:"%k:%M") }selected{/if}{/if}>{$row|escape}</option>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<th>希望使用期間</th>
				<td>
					<div id="err_length" class="error hidden"></div>
					<select name="timeLength" id="timeLength">
						{foreach from=$params.timeLength key=rowKey item=row }
							<option value="{$rowKey|escape}" {if isset($params.row->length)}{if $rowKey == $params.row->length }selected{/if}{/if}>{$row|escape}</option>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr>

				<td class="right" colspan="2" {if $params.flg == "edit"}id="oldData_{$smarty.post.roomId|escape}_{$smarty.post.startTime|escape}"{/if}>
					<button id="regist" type="button">{if $params.flg == "newRegist"}新規登録{else}編集{/if}</button>
				</td>
			</tr>
		</table>
	</form>
</section>
