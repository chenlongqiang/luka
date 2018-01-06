/**
 * @auther 安芸情報システム株式会社 齋藤 毅
 *
 * 階層リストセレクトプラグイン
 *	options :
 *		listId		: リスト ( UL ) のID ※必須
 *		iconUrl		: アイコン画像ファイルのディレクトリパス ※必須
 *		iconType	: アイコン画像のタイプ 'triangle' | 'sign'								既定値 'triangle'
 *		speed		: jQuery.Effect のスピード 'fast' | 'normal' | 'slow' | milliseconds	既定値 'fast'
 *		position	: リストの位置 'right' | 'bottom'										既定値 'bottom'
 *		size		: リストのサイズ [ width, height ] ※単位 : pixel						既定値 [ 180, 250 ]
 *		closer		: クリック時にリストを閉じるイベントを登録するセレクタ
 *  methods :
 *		close	: リストを閉じる
 *		isListOpen	: リストが開いているか否かを返す
 *			return  : true | false
 *  event :
 *		select.hierarchySelect.TS	: 選択操作終了時イベント。コールバック関数の第一引数に選択項目文字列、第二引数に選択項目の value属性値を受け取る
 */
( function( $ ){

	$.widget( "TS.hierarchySelect", {
		// default options
		options : {
			listId		: null,
			iconUrl		: null,
			iconType	: 'triangle',
			speed		: 'fast',
			position	: 'bottom',
			size		: [ 180, 240 ],
			closer		: null
		},
		// 初期化処理
		_init : function(){
			var obj		= this;
			var element	= this.element;
			var options	= this.options;
			var list	= $( this.options.listId );
			
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
			var listCss = {
				width		: options.size[ 0 ] + 'px',
				height		: options.size[ 1 ] + 'px',
				position	: 'absolute',
				overflowY	: 'scroll',
				overflowX	: 'hidden',
				display		: 'none',
				zIndex		: 1
			};
			
			list.addClass( 'ui-TS-hierarchySelect ui-widget ui-widget-content ui-corner-all' )
				.css	 ( listCss	);
			
			$( "ul"		, list ).addClass( 'ui-helper-hidden' );
			$( "ul li"	, list ).addClass( 'ui-corner-all' )
								.each( function(){
									if( $( 'ul', this ).size() > 0 ){
										$( this ).prepend( "<img src='" + options.iconUrl + '/' + options.iconClose + "' />" );
									}
								});
			$( "body" + ( options.closer ? ',' + options.closer : '' ) ).click( function(){ obj._close(); });
			
			// 対象要素クリック時
			element.click( function(){
				// 位置設定
				var marginTop  = Number( element.css( 'margin-top'  ).replace( /[^0-9]/g, '' ) );
				var marginLeft = Number( element.css( 'margin-left' ).replace( /[^0-9]/g, '' ) );
				switch( options.position ){
					case 'bottom' :
						list.css( 'top'	, element.position().top  + marginTop + element.outerHeight() )
							.css( 'left', element.position().left + marginLeft );
						break;
					case 'right' :
						list.css( 'top'	, element.position().top )
							.css( 'left', element.position().left + element.outerWidth() );
						break;
				}
				if( list.css( 'display' ) == 'none' ){
					obj._open();
				} else {
					obj._close();
				}
				return false;
			});
			
			// リスト項目イベント
			$( "li", list.children( 'ul' ) ).click( function( e ){
				
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

				// 末端要素クリック時は カスタムイベントをトリガーし全体を閉じる
				if( $( 'ul', this ).size() == 0 ){
					element.trigger( 'select.hierarchySelect.TS', [ $( this ).text(), $( this ).attr( 'value' ) ] );	// カスタムイベント
					obj._close();
				}
				
				return false;
				
			}).hover(
				function(){
					$( this ).addClass( 'ui-state-hover' );
				},
				function(){
					$( this ).removeClass( 'ui-state-hover' );
				}
			);
		},
		_open : function() {
			var value;
			if( this.element.val () ){
				value = this.element.val();		// value 属性を持つ場合
			} else if( this.element.text() ){
				value = this.element.text();	// value 属性を持たない場合、内包するテキスト
			}
			var speed = this.options.speed;
			var list  = $( this.options.listId );
			list.fadeIn( speed, function(){
				list.children( "ul" ).slideDown( speed );
				// value に一致する末尾の li 要素
				var target = $( 'li:contains(' + value + ')', list ).not( ':has( ul )' );
				target.parents ( 'ul' ).slideDown();
				target.addClass( 'ui-state-hover' );
			});
		},
		_close : function() {
			var speed = this.options.speed;
			var list  = $( this.options.listId );
			var iconPath = this.options.iconUrl + '/' + this.options.iconClose;
			list.children( "ul" ).slideUp( speed, function(){
				list.fadeOut( speed );
				$( "ul" , list ).css( 'display', 'none' );
				$( "img", list ).attr( 'src', iconPath );
				$( "li" , list ).removeClass( 'ui-state-hover' );
			});
		},
		close : function() {
			this._close();
		},
		isListOpen : function() {
			return $( this.options.listId ).css( 'display' ) != 'none';
		}
	});
		
})( jQuery );
