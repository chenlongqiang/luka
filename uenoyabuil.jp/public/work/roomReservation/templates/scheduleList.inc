<section class='floatLeft'>

	<form id='scheduleListForm' name='scheduleListForm' action='reserve.php' method="POST">

		<table class="formTable">
			<tr>
				<th>検索範囲</th>
				<td>
					<div class="demo">
						<input id="dateStart" type="text" value="" readonly="readonly"></input>
					</div>
				</td>
				<th>～</th>
				<td>
					<div class="demo">
						<input id="dateStop" type="text" value="" readonly="readonly"></input>
					</div>
				</td>
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
		<br>
		<div><b>※検索範囲を指定しない場合には、本日からの検索結果になります。<br>
		&nbsp;&nbsp;&nbsp;尚、いずれの日付を指定することにより、検索ができます。</b></div>
		<br>
		<table class="formTable" id="dataTableContainerScheduleList">


		</table>
	<div id="sendValue"></div>


	</form>

</section>
