/**
 * @auther 安芸情報システム株式会社 齋藤 毅
 *
 * tbody 部分スクロール表示プラグイン
 *	options :
 *		height			: [Integer]	tbodyの高さ ( スクロール部分 )
 *		scrollbarWidth	: [Integer]	スクロールバー幅
 *		cellCss			: [Object]	セルのCSS ( jQuery型式 )
 *	methods :
 *		getTableWidth	: テーブル幅を返す
 */
( function( $ ){

	$.widget( "TS.scrollTable", {
		// default options
		options : {
			tableWidth : null,
			cellCss : {
				padding : '5px'
			},
			height			: 250,
			scrollbarWidth	: 20
		},
		// 初期化処理
		_init : function(){
			var obj		= this;	// this を待避
			var options = obj.options;
			var element = obj.element;
			
			var container =  $( "<div class='st_container'>" + 
								"	<div class='st_head'><table /></div>" +
								"	<div class='st_body'></div>" +
								"	<div class='st_foot'><table /></div>" +
								"</div>" );
			
			var head = container.find( '> div.st_head' );
			var foot = container.find( '> div.st_foot' );
			var body = container.find( '> div.st_body' )
								.css ({
									overflowX	: 'hidden',
									overflowY	: 'auto'
								});
			
			// セルに CSS を適用
			element.find( 'td, th' ).css( options.cellCss );

			// 幅の設定
			obj._setWidth( element.find ( '> tbody' ) );
			obj._setWidth( element.find ( '> thead' ) );
			obj._setWidth( element.find ( '> tfoot' ) );
			
			element.after( container );
			element.find ( '> thead' ).appendTo( head.find( '> table' ) );
			element.find ( '> tfoot' ).appendTo( foot.find( '> table' ) );

			// st_body のサイズ設定
			body.css  ( 'max-height', options.height + 'px' )
				.width( element.width() + options.scrollbarWidth );
			
			element.appendTo( body );
		},
		// 幅の設定
		_setWidth : function( obj ){
			var cols = obj.find( '> tr:eq(0) > *' );	// 1行目
			cols.each( function( i ){
				var col = cols.eq( i );
				col.outerWidth( col.outerWidth() + ( navigator.appName == 'Microsoft Internet Explorer' ? 1 : 0 ) );	// IE対策
			});
		},
		getTableWidth : function(){
			return this.element.outerWidth();
		}
	});
	
})( jQuery );
