<center>
	<div>
		<div class="floatLeft padding10">
			<table class="formTable">
				<tr>
					<th>ビル名</th>
					<td>
							<select name="searchBuildName" id="searchBuildName">
								<option>ビル選択</option>
								{foreach from=$params.buildTable item=row}
									<option value="{$row->id|escape}">{$row->name|escape}</option>
								{/foreach}
							</select>
					</td>
					<th>会議室</th>
					<td id="dataTableContainer">
							<select name="searchRoomName" id="searchRoomName">
								<option>会議室選択</option>
							</select>
					</td>
				</tr>
			</table>
		</div>
		<div class="floatRight padding5a">
		<table>
			<tr>
				<th>例</th>
				<td style="text-align: center;">
					× = 午前予約済<br>
					-------------------<br>
					× = 午後予約済
				</td>
				<td>
					× = 全日予約済
				</td>
			</tr>
		</table>
		</div>
		<center>
			<div id="jMonthCalendar" class="clearBoth margin5"></div>
		</center>
	</div>
</center>
