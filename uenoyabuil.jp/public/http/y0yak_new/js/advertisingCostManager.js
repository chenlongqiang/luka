/**
 * @fileOverview 用JSファイル
 * @auther 安芸情報システム株式会社 齋藤 毅
 * @version 1.0
 */

/**
 * @class	ページ単位の定数定義クラス
 */
var PageEnv = {}

$( document ).ready( function(){
	return false;
});

/**
 * @class	フォーム画面用スクリプト
 */
var webResultForm = {
	/**
	 *	ドキュメント読み込み完了時
	 *  主にイベント登録処理
	 */
	ready : function(){
	
		// formCommon を継承
		var thisObject = $.extend( {}, formCommon, webResultForm );	

		// jQueryUIを初期化
		thisObject.initUi();
		
		// メンバ変数初期化
		thisObject.mode = 'insert';	// formCommon から継承
		
		// 修正リンククリック
		$( "a[ id ^= 'edit_' ]" ).live( 'click', function(){

			var key		= this.id.replace( 'edit_', '' );		// 対象年月
			thisObject.selectedKey	= key;
			var result	= $( "#resultAmount_" + key ).val();	// 実績額
			var remark	= $( "#remark_" + key		).val();	// 備考

			var yearMonth = key.substr( key.length - 6 );
			$( "#targetYearMonth"	).button( 'option', { label : yearMonth.substr( 0, 4 ) + '/' + yearMonth.substr( 4, 2 ) } );	// 開始年月 ( 対象年月 )
			$( "#resultAmount"		).val( result	);	// 実績額
			$( "#remark"			).val( remark	);	// 備考
									
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
					cmd			: 'delete' + document.pageId.replace( 'result', '' ) + 'Result',
					yearMonth	: key.substr( 0, 4 ) + '/' + key.substr( 4, 2 )
				}
				$.post( Env.ajaxUrl.regist, postData, function( json ){ thisObject.afterRegist( json ); }, 'json' );
			}
			return false;
		});
		
		// 登録ボタンクリック時
		$( "#regist" ).click( function(){
			
			// 共通
			var postData = {
				cmd				: thisObject.mode + document.pageId.replace( 'result', '' ) + 'Result',
				targetYearMonth	: $( "#targetYearMonth"	).text(),
				resultAmount	: common.val( $( "#resultAmount" ).val() ),
				remark			: $( "#remark"			).val()
			};
			
			if( thisObject.mode == 'update' ){
				var yearMonth = thisObject.selectedKey;
				$.extend( postData, {
					orgYearMonth	: yearMonth.substr( 0, 4 ) + '/' + yearMonth.substr( 4, 2 )
				});
			}

			// プロンプトのメッセージ
			var msg = {
				insert	: 'データを新規登録します。よろしいですか？',
				update	: 'データを更新します。よろしいですか？'
			}

			if( confirm( msg[ thisObject.mode ] ) ){
				// データ更新
				$.post( Env.ajaxUrl.regist, postData, function( json ){ thisObject.afterRegist( json ); }, 'json' );
			}
			
			return false;
		});
		
		// 新規登録...リンククリック
		$( "#insertMode" ).click( function(){
			thisObject.changeMode( 'insert' );
			return false;
		});
		
		// 金額変更時
		$( "#resultAmount" ).change( function(){
			var value = common.val( $( this ).val() );
			$( this ).val( common.f( value ) );
		});
		
		// yearMonthDialog を閉じる
		var arr = [ '#targetYearMonth', '#filterStartYearMonth', '#filterEndYearMonth' ];
		$( arr.join( ',' ) ).click( function(){
			var tmpId = this.id;
			$( arr.join( ',' ) ).each( function(){
				if( tmpId != this.id && $( '#' + this.id ).yearMonthDialog( 'isOpen' ) ) $( '#' + this.id ).yearMonthDialog( 'close' );
			});
		});
	},
	/**
	 * フォームをリセット
	 */
	resetForm : function(){
		document.forms[ document.forms.commonForm.mainForm.value ].reset();
		var thisObject = this;
		$( "#targetYearMonth"	).button( 'option', { label : '対象年月' } );		// 対象年月リセット
		$( "#dataTable tr"		).removeClass( 'selected' );						// ハイライトをクリア
		thisObject.selectedKey = null;
	},
	/**
	 * データリスト更新用パラメータ取得
	 */
	getDataListParams : function(){

		var postData = {
			templateType	: Env.ajaxTemplateType.ajax,
			cmd				: 'setResultFormDataListParams',
			pageId			: document.pageId
		}
		
		var filterStart	= $( "#filterStartYearMonth"	).text().match( '^[0-9]{4}\/[0-9]{2}$' );
		var filterEnd	= $( "#filterEndYearMonth"		).text().match( '^[0-9]{4}\/[0-9]{2}$' );

		if( filterStart	) postData.startYearMonth	= filterStart[0];
		if( filterEnd	) postData.endYearMonth		= filterEnd[0];

		var templateName = common.getSystemName() + '\\resultDataList';
		
		return { postData : postData, templateName : templateName };
	},
	/**
	 * jQueryUI初期化
	 */
	initUi : function(){
		formCommon.initUi( this );
	},
	/**
	 * 登録した live イベントの削除
	 */
	die : function(){}
};

/**
 * @class		タウンページ 実績入力画面用スクリプト
 */
var formCommon = {
	mode		: null,
	selectedKey	: null,
	/**
	 * 登録ボタンクリック後の処理
	 * @param {Object}	json	JSONオブジェクト
	 */
	afterRegist : function( json ){
		if( !common.errorHandle( json ) ){
			if( json.result == 'SUCCESS' ){
				// バリデーション結果クリア
				common.clearValidationResult();
				// 結果メッセージ表示
				common.afterRegist( json );
				// 挿入モードに戻す
				this.changeMode( 'insert' );
				// データリスト更新
				this.updateDataList();
			} else if ( json.result == 'ERROR' ) {
				// バリデーション結果クリア
				common.clearValidationResult();
				// 結果メッセージ表示
				common.afterRegist( json );
			} else {
				// バリデーションエラー表示
				common.setValidationResult( json );
			}
		}
	},
	/**
	 * データリスト更新処理
	 * @param {Object}	postData		POSTパラメータ
	 * @param {String}	templateName	テンプレートファイル名
	 * [制限事項]
	 *	呼び出し元オブジェクトに、
	 *		getDataListParams	[メソッド]
	 *	が必要
	 *  getDataListParams の戻り値が false, null, undefined, 0 の場合は何もしない
	 */
	updateDataList : function( func ){
		
		this.flgSetWidth = true;
		
		var params = this.getDataListParams();

		if( params ){
			common.loadHtml( 'dataTableContainer', params.templateName, params.postData, function( html ){
				if( !$( "#dataTable:hidden" ).size() ){
					$( "#dataTable"			).scrollTable( { height : 300 } );
					$( "#filterContainer"	).outerWidth( $( "#dataTable" ).scrollTable( 'getTableWidth' ) );
				}
				if( func ) func();
			} );
		}
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
		common.clearValidationResult();

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
				this.resetForm( this.mode == 'update' );
				break;
		}
		
		this.mode = mode;
	},
	/**
	 * jQueryUI初期化
	 */
	initUi : function( thisObject ){

	},
	/**
	 * 登録した live イベントの削除
	 */
	die : function(){
		$( "a[ id ^= 'edit_' ]"					).die();
		$( "a[ id ^= 'delete_' ]"				).die();
	}
};