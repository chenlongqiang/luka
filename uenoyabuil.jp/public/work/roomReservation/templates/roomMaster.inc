<section class='floatLeft'>
	<form id='buildMasterForm'>
		<table class="formTable">

			<tr>
				<th>ビル名</th>
				<td>
						<div id="err_build_id" class="error hidden"></div>
						<select name="searchBuildName" id="searchBuildName">
							<option value="-1">ビル選択</option>
							{foreach from=$params.table item=row}
								<option value="{$row->id|escape}">{$row->name|escape}</option>
							{/foreach}
						</select>
				</td>
			</tr>
			<tr>
				<th>部屋名</th>
				<td>
					<div id="err_name" class="error hidden"></div>
					<input type='text' id='name' name='name' />
				</td>
			</tr>
			<tr>
				<td colspan="2" class="right"><button type="button" id='regist'>新規登録</button></td>
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
			<th>ビル名</th>
			<th>部屋名</th>
		</tr>
	</table>

	</div>
</section>
