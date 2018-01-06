$( document ).ready( function(){

	var keyNumber 	= window.location.pathname.lastIndexOf( "/" );
	var fileName	= window.location.pathname.substr( keyNumber + 1 );

	

	switch( fileName ) {
		case 'buildMaster.php' :
			buildMasterForm.ready();
			break;
		case 'roomMaster.php' :
			roomMasterForm.ready();
			break;
	}
});

var commonForm = {
	mode	: null,
	cmdMaster	: null,

	afterRegist : function( json ){

		if( json ){
			this.updateDataTable();
			this.resetForm();
			
		} else {
			alert( json.error );
		}
	},

	//更新するHTML文のみロード
	updateDataTable : function(){

		switch( this.cmdMaster ) {
			case 'build' :
				$( '#dataTableContainer' ).load( 'ajax/html.php', { cmd : 'updateBuildMasterDataTable' } );
			break;
			case 'room' :
				$( '#dataTableContainer' ).load( 'ajax/html.php', { cmd : 'updateRoomMasterDataTable', id : $( '#searchBuildName' ).val() } );
			break;
		}
	},

	/**
	 * フォームをリセット
	 */
	resetForm : function(){
		$( "input[type='text'], input[type='number']" ).each(function(){

			$(this).val("");
		});



		var thisObject = this;
		$( "#dataTable tr"	).removeClass( 'selected' );						// ハイライトをクリア
		thisObject.selectedKey = null;
	},



	/**
	 * 更新モード変更
	 * @param {String}	mode	モード
	 * [制限事項]
	 *	呼び出し元オブジェクトに、
	 *		resetForm			[メソッド]
	 *	が必要
	 */
	changeMode : function( mode ){

		// バリデーション結果をクリア
//		common.clearValidationResult();

		switch( mode ){
			case 'update' :
				// ボタンキャプション
				$( "#regist" ).html( '更新' );
				// 新規登録モードへのリンク表示
				$( "#insertMode" ).parent().removeClass( 'hidden' );
				break;
			case 'insert' :
				// ボタンキャプション
				$( "#regist" ).html( '新規登録' );
				// 新規登録モードへのリンク非表示
				$( "#insertMode" ).parent().addClass( 'hidden' );
				// フォームをリセット
				this.resetForm();
				break;
		}
		
		this.mode = mode;
	}
};

var buildMasterForm = {
	

	// 新規登録・更新 ボタンクリック
	ready : function(){

		this.cmdMaster = 'build';

		//var thisObject = this;
		var thisObject = $.extend( {}, commonForm, buildMasterForm );

		// メンバ初期化
		thisObject.mode = 'insert';



		$( '#regist' ).click( function(){

			var msg = { insert : '新規登録', update : '更新' };

			if( confirm( 'ビル情報を' + msg[ thisObject.mode ] + 'します。よろしいですか？' ) ){

				var postData = {
					cmd		: thisObject.mode + 'BuildMaster',
					id		: $( '#id' ).val(),
					name	: $( '#name'	).val(),
					old_id	: thisObject.selectedKey
				};
				$.post( 'ajax/regist.php', postData, function( json ){
				
					if( !json.code ) {
						common.setValidationResult(json);
					} else {
						// バリデーション結果をクリア
						common.clearValidationResult();
						thisObject.afterRegist( json ); 
						alert("ビル情報を" + msg[ thisObject.mode ] + "できました。"); 
					}
				}, 'json' );
			}

			return false;
		});


		$( "a[ id ^= 'edit_' ]" ).live( 'click', function(){

			var key		= this.id.replace( 'edit_', '' );// ビルID



			thisObject.selectedKey	= key;
			var name	= $( "#name_" + key ).val();				// ビル名

			$( "#id"	).val( key	);	// ビルID
			$( "#name"	).val( name );	// ビル名
			

									
			$( "#dataTable tr" ).removeClass( 'selected' );	// ハイライトをいったんクリア
			$( this ).parent().parent().addClass( 'selected' );		// 選択レコードのハイライト
			
			// 更新モードに変更
			thisObject.changeMode( 'update' );
			
			return false;
		});

		// 削除リンククリック
		$( "a[ id ^= 'delete_' ]" ).live( 'click', function(){
			
			// バリデーション結果をクリア
			common.clearValidationResult();
		
			$( this ).parent().parent().addClass( 'selected' );
			
			if( confirm( 'データを削除します。よろしいですか？' ) ){
				var key = this.id.replace( 'delete_', '' );
				var postData = {
					cmd	: 'deleteBuildMaster',
					id	: key
				}
				$.post( 'ajax/regist.php', postData, function( json ){
					thisObject.afterRegist( json );
					alert( "ビル情報を削除できました。");
					thisObject.afterRegist( json ); }
				, 'json' );

			}
			$( "#dataTable tr" 		).removeClass( 'selected' );
			return false;



		});
		
		// 新規登録...リンククリック
		$( "#insertMode" ).click( function(){
			thisObject.changeMode( 'insert' );
			return false;
		});


		
	}


};



var roomMasterForm = {
	ready : function(){
		var thisObject = this;

		this.cmdMaster = 'room';

		var thisObject = $.extend( {}, commonForm, roomMasterForm );


		// メンバ初期化
		thisObject.mode = 'insert';

		// 全ビル選択表示
		$( "#searchBuildName" ).change( function(){

			// バリデーション結果をクリア
			common.clearValidationResult();

			thisObject.changeMode( 'insert' );
			thisObject.updateDataTable();

		
		});
		$( '#regist' ).click( function(){
			var msg = { insert : '新規登録', update : '更新' };
			if( confirm( '部屋情報を' + msg[ thisObject.mode ] + 'します。よろしいですか？' ) ){
				var postData = {
					cmd		: thisObject.mode + 'RoomMaster',
					build_id 	: $( '#searchBuildName' ).val(),
					id			: thisObject.selectedKey,
					name		: $( '#name'	).val()
				};
				$.post( 'ajax/regist.php', postData, function( json ){ 
					if( !json.code ) {
						common.setValidationResult(json);
					} else {
						// バリデーション結果をクリア
						common.clearValidationResult();
						thisObject.afterRegist( json ); 
						alert("部屋情報を" + msg[ thisObject.mode ] + "できました。"); 
					}
				}, 'json' );
			}
			return false;
		});


		$( "a[ id ^= 'edit_' ]" ).live( 'click', function(){

			var key		= this.id.replace( 'edit_', '' );			// 部屋ID
			thisObject.selectedKey	= key;
			var name	= $( "#name_" + key ).val();		// 部屋名


			$( "#id"	).val( key	);		// 部屋ID
			$( "#name"	).val( name	);	// 部屋名

									
			$( "#dataTable tr" 		).removeClass( 'selected' );	// ハイライトをいったんクリア
			$( this ).parent().parent().addClass( 'selected' );		// 選択レコードのハイライト
			
			// 更新モードに変更
			thisObject.changeMode( 'update' );
			
			return false;
		});

		// 削除リンククリック
		$( "a[ id ^= 'delete_' ]" ).live( 'click', function(){
			
			// バリデーション結果をクリア
			common.clearValidationResult();
			$( this ).parent().parent().addClass( 'selected' );

		
			if( confirm( 'データを削除します。よろしいですか？' ) ){
				var key = this.id.replace( 'delete_', '' );
				var postData = {
					cmd	: 'deleteRoomMaster',
					id	: key 
				}
				$.post( 'ajax/regist.php', postData, function( json ){ 
					thisObject.afterRegist( json ); 
					alert( "部屋情報を削除できました。"); 
				}, 'json' );

			}
			$( "#dataTable tr" 		).removeClass( 'selected' );
			return false;

			
		});
		
		// 新規登録...リンククリック
		$( "#insertMode" ).click( function(){
			thisObject.changeMode( 'insert' );
			return false;
		});
		
	},
	
	resetForm : function(){
		$( "input[type='text'], input[type='number']" ).each(function(){

			$(this).val("");
		});


		var thisObject = this;
		$( "#dataTable tr"	).removeClass( 'selected' );						// ハイライトをクリア
		thisObject.selectedKey = null;
	}

};




