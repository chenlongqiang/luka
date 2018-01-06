/**
 * @auther 安芸情報システム株式会社 齋藤 毅
 *
 * 階層マルチセレクトプラグイン
 *	options :
 *		iconUrl		: アイコン画像ファイルのディレクトリパス ※必須
 *		iconType	: アイコン画像のタイプ 'triangle' | 'sign'								既定値 'triangle'
 *		speed		: jQuery.Effect のスピード 'fast' | 'normal' | 'slow' | milliseconds	既定値 'fast'
 *  methods :
 *		getSelectedItems	: 選択された項目の jQueryオブジェクトを返す
 *		reset				: 選択項目をクリアする
 *  event :
 *		select.multipleSelect.TS	: 選択操作終了時イベント。コールバック関数の引数に選択項目jQueryオブジェクトを受け取る
 */
( function( $ ){

	$.widget( "TS.multipleSelect", {
		// default options
		options : {
			iconUrl		: null,
			iconType	: 'triangle',
			speed		: 'normal'
		},
		// 初期化処理
		_init : function(){
			var element	= this.element;
			var options	= this.options;
			
			switch( options.iconType ){
				case 'sign' :
					options.iconOpen	= 'ui-icon-minus.png';
					options.iconClose	= 'ui-icon-plus.png';
					break;
				case 'triangle' :
				default			:
					options.iconOpen	= 'ui-icon-triangle-1-s.png';
					options.iconClose	= 'ui-icon-triangle-1-w.png';
					break;
			}
			
			// 初期化
			element.addClass( 'ui-TS-multipleSelect ui-widget ui-widget-content' );
			$( "ul > li > ul", element ).addClass( 'ui-helper-hidden' )			// 孫要素以下を隠す
										.selectable( {							// jQueryUi.selectable設定
											filter	: 'li:not( :has( ul ) )',	// 末端要素のみ選択可
											stop	: function(){
												element.trigger( 'select.multipleSelect.TS', [ $( '.ui-selected', this ) ] );	// カスタムイベント
											}
										});
			
			$( "ul > li"	 , element ).addClass( 'ui-corner-all' )		// ul を内包する要素にアイコンを付ける
										.each( function(){
											if( $( 'ul', this ).size() > 0 ){
												$( this ).prepend( "<img src='" + options.iconUrl + '/' + options.iconClose + "' />" );
											}
										});
			
			// リスト項目イベント
			$( "li", element.children( 'ul' ) ).click( function( e ){
				
				// 自身の配下、兄弟要素の配下の全アイコンをリセット
				$( 'img', $( this ).parent() ).attr( 'src', options.iconUrl + '/' + options.iconClose );
				
				// 自身のアイコンをセット
				$( this ).children( 'img' ).attr( 'src', function(){
					if( $( this ).siblings( 'ul' ).css( 'display' ) == 'none' ){
						return options.iconUrl + '/' + options.iconOpen;
					} else {
						return options.iconUrl + '/' + options.iconClose;
					}
				});
				
				// 兄弟要素を閉じる
				$( this ).siblings().children( 'ul' ).slideUp( options.speed, function(){
					// その配下のリストを閉じる
					$( 'ul', this ).css( 'display', 'none' );
				});
				
				// 内包するリストを開閉
				$( this ).children( 'ul' ).slideToggle( options.speed, function(){
					// 閉じた場合はその配下のリストを閉じる
					if( $( this ).css( 'display' ) == 'none' ) $( 'ul', this ).css( 'display', 'none' );
				});
				
				return false;
			});
		},
		getSelectedItems : function(){
			return $( '.ui-selected', this.element );
		},
		reset : function(){
			$( '.ui-selected', this.element ).removeClass( 'ui-selected' );
			return false;
		}
	});
	
})( jQuery );
