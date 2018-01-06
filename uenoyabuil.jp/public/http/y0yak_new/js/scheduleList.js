$( document ).ready( function(){

	$( "#searchBuildName" ).change( function(){
	
		$( '#dataTableContainer' ).load( 'ajax/html.php', { cmd : 'selectScheduleListRoomTable', id : $(this).val() }, function(){ registScheduleList(); } );
	});
  	var pickerOpts = { 
  		dateFormat: "yy-mm-dd",
  		yearRange: '2011:2015',
  		clearText:'クリア',
  		closeText:'閉じる',
  		prevText:'先月',
       	nextText:'来月',
       	monthNames:['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月'],
       	dayNames:['日', '月', '火', '水', '木', '金', '土'],
       	dayNamesMin:['日', '月', '火', '水', '木', '金', '土']
  	};
  	$( "#dateStart" ).datepicker(pickerOpts);
	$( "#dateStop" ).datepicker(pickerOpts);


	$( "a[ id ^= 'delete_' ]" ).live( 'click', function(){
		
		if( confirm( '選択した予約情報を削除します。よろしいですか？' ) ){
			var key = this.id.replace( 'delete_', '' ).split( '_' );
			var postData = {
				cmd	: 'deleteReserveInfo',
				id	: key[0],
				start_time : key[1]
			}
			$.post( 'ajax/regist.php', postData, function( json ){ getScheduleList(); alert( "予約情報を削除できました。"); }, 'json' );

		}
		return false;
	});

	$( "a[ id ^= 'edit_' ]" ).live( 'click', function(){
		var key = this.id.replace( 'edit_', '' ).split( '_' );

//alert($( "#searchBuildName" ).val());

		$( "#sendValue" ).html( "<input name=\"buildId\" type=\"hidden\" value=\"" + $( "#searchBuildName" ).val() + "\"><input name=\"roomId\" type=\"hidden\" value=\"" + $( "#searchRoomName" ).val() + "\"><input name=\"startTime\" type=\"hidden\" value=\"" + key[1] + "\">" );

		document.scheduleListForm.submit();

	});

});

function registScheduleList(){

	$( "#searchRoomName" ).change( function(){

		getScheduleList();
	});
}

function getScheduleList() {
	
	var dateFormat = new DateFormat("yyyy-MM-dd HH:mm:ss");
	var todayDate = dateFormat.format(new Date());
	var dateStart;
	if( $( "#dateStart" ).attr("value") != "" ) {
		dateStart = $( "#dateStart" ).attr("value") + ' 00:00:00';
	}
	var dateStop;
	if( $( "#dateStop" ).attr("value") != "" ) {
		dateStop = $( "#dateStop" ).attr("value") + ' 23:59:59';
	}
	//選択した部屋のすべての予約情報を取得
	var postData = {
		cmd : 'selectScheduleListDataTable',
		id 	: $( "#searchRoomName" ).val(),
		start_time : todayDate,
		dateStart : dateStart,
		dateStop : dateStop
	};
	$( '#dataTableContainerScheduleList' ).load( 'ajax/html.php', postData, function(){} );
}
