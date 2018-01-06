/**
 * @auther 安芸情報システム株式会社 齋藤 毅
 *
 * ツールチップ表示プラグイン
 *	options :
 *		mode		: [String]	表示イベント ( click [default] | hover )
 *		zIndex		: [Integer]	重ね順 既定値 2000
 *		css			: [Object]	ツールチップ部分のCSS ( jQuery型式 )
 */
( function( $ ){

	$.widget( "TS.tooltip", {
		// default options
		options : {
			mode	: 'click',	// click | hover
			zIndex	: 2000,
			css		: { padding : '5px'	}
		},
		// 初期化処理
		_init : function(){
			var obj		= this;	// this を待避
			var options = obj.options;
			var element = obj.element;
			
			if( element.is( 'a' ) && element.attr( 'title' ) != '' ){
				
				var title = element.attr( 'title' );
				element.attr( 'title', '' );	// 標準のツールチップ表示をキャンセル
				
				switch( options.mode ){
					case 'click' :
						// 関係ないところをクリックしたら閉じる
						$( 'body' ).unbind().click( function( e ) {
							obj._close();
							e.stopPropagation();	// イベントのバブリングを停止
						} );
						
						element.click( function( e ){
							obj._show( title );
							e.stopPropagation();	// イベントのバブリングを停止
							return false;
						});
						break;
					case 'hover' :
						element.hover(
							function( e ){ obj._show( title ); },
							function( e ){ obj._close()		 ; }
						);
						break;
				}
			}
		},
		// 生成・表示
		_show : function( title ){
			
			// tooltip 部分の html
			var container =  $( "<div id='TS_tooltip' class='ui-widget'>" +
								"	<div class='tip ui-icon'></div>" +
								"	<div class='contents ui-state-default ui-corner-all'>" + title + "</div>" +
								"</div>" );
			
			this._close();	// #tooltip があれば削除

			var tipCss = {
				backgroundPosition	: '0px -20px',
				height				: '6px'
			};
			
			container.appendTo( 'body' )
					 .hide()
					 .css( {
						position	: 'absolute',
						top			: this.element.offset().top  + 16,
						left		: this.element.offset().left + 5,
						zIndex		: this.options.zIndex
					 } )
					 .find( 'div.tip'		).css( tipCss ).end()
					 .find( 'div.contents'	).css( this.options.css ).end()
					 .fadeIn();
		},
		// 閉じて削除
		_close : function(){
			if( $( "#TS_tooltip" ).size() ) {
				$( "#TS_tooltip" ).fadeOut( function(){
					$( this ).remove();
				});
			}
		}
	});
	
})( jQuery );
