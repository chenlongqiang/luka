$( document ).ready( function(){

	$( "#searchBuildName" ).change( function(){

		$( '#dataTableContainer' ).load( 'ajax/html.php', { cmd : 'selectReserveDataTable' , id : $(this).val() } );

	});



	$( "#searchRoomName" ).live('change', function(){

		// バリデーション結果をクリア
		common.clearValidationResult();
	});

	$( "#regist" ).live( 'click', function(){
		var key = [];

		if( $( '#regist' ).text() == '新規登録' ) {
			mode = 'insert';
		} else {
			mode = 'update';
			key = $('#regist').parent().attr('id').replace( 'oldData_', '' ).split( '_' );
		}


		if( confirm( '予約情報を' + $( '#regist' ).text() + 'します。よろしいですか？' ) ){

			var postData = {
				cmd				: mode + 'ReserveInfo',
				id				: $( '#searchRoomName' ).val(),
				username		: $( '#username' ).val(),
				start_time		: $( '#year' ).val() + '-' + $( '#month' ).val() + '-' + $( '#day' ).val() + ' ' + $( '#startTime' ).val(),
				length			: $( '#timeLength' ).val(),
				old_id			: key[0],
				old_start_time	: key[1]
			};



			$.post( 'ajax/regist.php', postData, function( json ){
			
				if( !json.code ) {
					common.setValidationResult(json);
				} else {
					alert("予約情報を" + $( '#regist' ).text() + "できました。");
					document.location = "reserve.php"; 
				}
			}, 'json' );
		}
		
		return false;
	});






});