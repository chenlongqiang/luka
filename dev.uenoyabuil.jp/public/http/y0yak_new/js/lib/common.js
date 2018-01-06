/**
 * @fileOverview JavaScript共通関数クラス定義用JSファイル
 * @auther 安芸情報システム株式会社 齋藤 毅
 * @version 1.0
 */

/**
 * @class 共通関数クラス
 */
var common = {
	/**
	 * URLからシステム名を取得する
	 */
	getSystemName : function(){
		var rootPath = location.pathname.substring( 0, location.pathname.lastIndexOf( '/' ) + 1 );
		var basename = location.pathname.replace( rootPath, '' ).replace( '.aspx', '' );
		
		for( sysName in Env.systemPages ){
			if( Env.systemPages[ sysName ].join( Env.arrayDelimiter ).match( basename ) != null ){
				return sysName;
			}
		}
		alert( "Env.systemPages の設定を確認してください" );
	},
	/**
	 * Ajaxで指定した要素にHTMLをロードする
	 * @param	{String}	elementId			HTMLを流し込む対象となる要素のID
	 * @param	{String}	templateFileName	テンプレートファイル名
	 * @param	{Object}	params				テンプレートに渡すパラメータ（ JSONオブジェクト ）params.element で指定したIDの要素のみ取得
	 * @param	{Function}	callback			コールバック関数
	 */
	loadHtml : function( elementId, templateFileName, params, callback ){
		
		// params が null などの場合、既定のオブジェクトを代入
		if( !params ) params = { templateType : 0 };
		
		// callback が null などの場合、空の関数オブジェクトを代入
		if( !callback ) callback = function( param ){};
		
		// elementId をチェック
		if( !elementId ){
			alert( '対象要素のIDを指定して下さい' );
			return;
		}
		
		// templateName をチェック → 指定されていれば params に追加
		if( !templateFileName ){
			alert( 'テンプレートファイル名を指定して下さい' );
			return;
		} else {
			params.templateFileName = templateFileName;
		}
		
		// 対象の要素に HTML をロード
		$( '#' + elementId ).load( Env.ajaxUrl.html + ( params.element ? ' #' + params.element : '' ), params, function( html ){
			try{
				var json = eval( "(" + html + ")" );
				if( !common.errorHandle( json ) ) callback( html );
			} catch( e ) {
				callback( html );
			}
		} );
	},
	/**
	 * ナビゲーションリンクHTMLを生成する
	 * @param	{String}	route	ルート階層を表す文字列（例： 'TOP|[page1]|[page2]' → TOP ＞ page1 ＞ page2 ）
	 * @returns	{String}	ナビゲーションリンクHTML
	 */
	generateNavigation : function( route ){

		var titles = Env[ common.getSystemName() ].pageTitle;
		var arrayRoute = route.split( Env.arrayDelimiter );
		var arrayRouteWithAnchor = new Array();
		
		for( idx in arrayRoute ){
		
			var pageId = arrayRoute[ idx ];

			if( titles[ pageId ] && idx < arrayRoute.length - 1 ){
				arrayRouteWithAnchor.push( "<a href='#load' id='navi_" + pageId + "'>" + titles[ pageId ] + "</a>" );
			} else {
				arrayRouteWithAnchor.push( titles[ pageId ] );
			}
		}
		
		return "<a href='" + Env.pageUrl.top + "' id='top'>TOP</a> ＞ " + arrayRouteWithAnchor.join( " ＞ " );
	},
	/**
	 * バリデーションチェック結果をページに反映する
	 * @param	{Object}	json	バリデーションチェック結果 ( JSONオブジェクト )
	 */
	setValidationResult : function( json ){
		// バリデーションチェック結果をクリア
		$( "#error, div[ id ^= 'err_' ]" ).html( '' )
										  .addClass( 'hidden' );
		
		var listItems = '';
		for( idx in json.errorMessageList ){
			listItems += '<li>' + json.errorMessageList[ idx ] + '</li>';
		}
		
		// エラーメッセージ（全体）
		$( "#error" ).removeClass( 'hidden' )
					 .html( '<ul>' + listItems + '</ul>' );
		
		// 個別のエラー
		for( idx in json.error ){
			$( "[ id ^= 'err_" + idx + "' ]" ).removeClass( 'hidden' )
											  .html( json.error_msg[ idx ] );
		}
	},
	/**
	 * バリデーションチェック結果をクリア
	 */
	clearValidationResult : function(){
		// バリデーションチェック結果をクリア
		$( "#error, div[ id ^= 'err_' ]" ).html( '' )
										  .addClass( 'hidden' );
	},
	/**
	 * ログアウト処理
	 */
	logout : function(){
		if( confirm( 'ログアウトします。よろしいですか？' ) ){
			$.post( Env.ajaxUrl.logout, function( message ){
				alert( message );
				location.href = Env.pageUrl.login;
			} );
		}
	},
	/**
	 * 登録ボタンクリック後の処理
	 * @param	{Object}	json	結果情報 ( JSONオブジェクト )
	 */
	afterRegist : function( json ){
		if( json.result == 'SUCCESS' ){
			alert( json.message );
		} else if( json.result == 'ERROR' ){
			alert( json.exceptionType + "\n" + json.exceptionMessage );
		}
	},
	/**
	 * デバッグ用の hidden の DIV に文字列を流し込む
	 * @param	{String}	str	デバッグ文字列
	 */
	debug : function( str ){
		$( "#debug" ).html( str );
	},
	/**
	 * JSONオブジェクトの値を文字列ダンプ
	 * @param	{Object}	json JSONオブジェクト
	 * @returns	{String}	ダンプ文字列
	 */
	dumpResult : new Array(),
	dump : function( json, depth ){
		if( !depth ) depth = 0;
		common.dumpResult[ depth ] = new Array();
		for( key in json ){
			if( typeof json[ key ] == 'object' ){
				common.dumpResult[ depth ].push( common.repeat( "　", depth ) + key + " :\n" + common.dump( json[ key ], depth + 1 ) );
			} else {
				common.dumpResult[ depth ].push( common.repeat( "　", depth ) + key + " : " + json[ key ] );
			}
		}
		return common.dumpResult[ depth ].join( "\n" );
	},
	/**
	 * 文字列を指定した回数繰り返した文字列を返す
	 * @param	{String}	str 文字列
	 * @param	{Integer}	num 繰り返す回数
	 * @returns	{String}	指定した文字列を指定回繰り返した文字列
	 */
	repeat : function( str, num ){
		var ret = '';
		for( i = 0 ; i < num ; i++ ){
			ret = ret + str;
		}
		return ret;
	},
	/**
	 * 値を指定した桁数の文字列に変換する
	 * @param	{Object}	value	変換する値
	 * @param	{Integer}	digit	桁数。小数点以下は切り捨てされる。
	 * @param	{String}	[pad]	パディング文字。先頭の文字を使用する。省略した場合は 0。
	 * @param	{String}	[which]	左埋め、右埋めを選択。( left | right ) 省略した場合は left。
	 * @returns	{String}	文字列
	 */
	pad : function( value, digit, pad, which ){
		// 既定値セット
		if( !pad	) pad	= '0';
		if( !which	) which	= 'left';
		// 引数チェック
		if( !value )									{ alert( 'common.pad : 値を指定してください'						); return null; }
		if( !digit || typeof digit != 'number' )		{ alert( 'common.pad : 桁を整数で指定してください'					); return null; }
		if( !( which == 'left' || which == 'right' ) )	{ alert( 'common.pad : 「left」 又は 「right」 で指定してください'	); return null; }
		
		var tmp = typeof value == 'number' ? parseInt( value ).toString() : $.trim( value );
		while( tmp.length < digit ){
			switch( which ){
				case 'left'	: tmp =	 pad.toString().charAt(0) + tmp	; break;
				case 'right': tmp += pad.toString().charAt(0)		; break;
			}
		}
		return tmp;
	},
	/**
	 * Ajax用例外ハンドラ
	 * @param	{Object}	json	JSONオブジェクト
	 * @returns	{Boolean}	true : エラーあり | false : エラーなし
	 */
	errorHandle : function( json ){
		if( json.error == true ) {
			alert( json.type + "\n" + json.message );
			if( json.type.match( 'NoSessionException' ) ) location.reload();	// セッションエラー時は再読み込み
			return true;
		} else {
			return false;
		}
	},
	/**
	 * 数値にカンマ区切り書式を適用する
	 * @param	{Number}	value		数値
	 * @param	{String}	symbol		通貨記号
	 * @param	{Number}	accuracy	精度 ( 小数点以下の桁数 )
	 * @returns	{String}	変換後の数値文字列
	 */
	formatNumber : function( value, symbol, accuracy ){
		if( typeof value != 'number' ) alert( 'common.formatNumber : value パラメータには数値を設定してください' );
		accuracy = typeof accuracy == 'number' ? Math.round( accuracy ) : 0;

		// 整数部分と小数点以下に分ける
		var intPart		= Math.floor( value );
		var decimalPart	= accuracy ? Math.round( ( value - intPart ) * Math.pow( 10, accuracy ) ) : 0;

		// 整数部分は3桁区切りに
		var num = intPart.toString();
		while( num != ( num = num.replace( /^(-?[0-9]+)([0-9]{3})/, "$1,$2" ) ) );
		
		var ret = decimalPart ? num + '.' + ( decimalPart / Math.pow( 10, accuracy ) ).toString().substr( 2 ) : num;
		return symbol ? symbol + ret : ret;
	},
	/**
	 * 数値にカンマ区切り書式を適用する ( 簡易版 )
	 * @param	{Number}	value		数値
	 * @returns	{String}	変換後の数値文字列
	 */
	f : function( value, accuracy ){	// \記号なし
		return common.formatNumber( typeof value == 'number' ? value : Number( value ), null, accuracy );
	},
	fc : function( value, accuracy ){	// \記号あり
		return common.formatNumber( typeof value == 'number' ? value : Number( value ), '\\', accuracy );
	},
	/**
	 * 通貨型式文字列を数値に変換する
	 * @param	{String}	value	通貨形式文字列
	 * @returns	{Number}	数値
	 */
	val : function( value ){
		return Number( value.replace( /[^0-9.-]/g, '' ) );
	}
}

$( document ).ready( function(){

	// jQuery-ui-datepicker のロケール設定
	//$.datepicker.setDefaults( Env.datepickerRegional.ja );
	
	// Ajaxグローバルイベントの登録
	$( "#loading" )
		.bind( 'ajaxSend', function(){
			$( this ).fadeTo( 'fast', 0.5 ); 
		})
		.bind( 'ajaxComplete', function(){
			$( this ).fadeOut( 'fast' );
		}
	);
	
	// ログアウトクリック時
	$( "#logout" ).click( function(){
		common.logout();
		return false;
	});
	
	// Ajax の cache を無効に
	$.ajaxSetup( { cache : false } );
});
