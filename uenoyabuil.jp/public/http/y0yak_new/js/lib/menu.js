/**
 * @fileOverview メニュー用JSファイル
 * @auther 安芸情報システム株式会社 齋藤 毅
 * @version 1.0
 */
$( document ).ready( function(){

	$( document ).click( function( e ){
		
		// 全SubMenuを隠す
		$( "table[ id $= 'SubMenu' ]" ).fadeOut( 'fast' );
		
		// 選択中のメニューのバッファをクリア
		document.selectedMenuId = null;
	});
	
	// メニュークリック時（サブメニューがある場合）
	$( Env[ common.getSystemName() ].subMenuIds.join( ', ' ) ).click( function( e ){
			
		if( document.selectedMenuId != this.id ) {
		
			// 全SubMenuを隠す → SubMenuを表示・位置設定
			$( "table[ id $= 'SubMenu' ]" ).fadeOut( 'fast' );
			$( "#" + this.id + "SubMenu" )
				.fadeIn( 'normal' )
				.css	( 'top'	, $( this ).offset().top + $( this ).height() + 12 )
				.css	( 'left', $( this ).offset().left + 1 )
				.css	( 'z-index', '1' )
				.width	( $( this ).outerWidth() );
			
			// 選択中のメニューをバッファ
			document.selectedMenuId = this.id;
		}
		
		// イベントのバブリング防止
		e.stopPropagation();
	});

});

/**
 * 対象の要素に HTML をロード ( メニュー項目クリック時 )
 */
function loadContents( id, func ){

	var flgDatePicker	= false;	// jQueryUI.datepicker のセット
	var flgMonthSelect	= false;	// jQueryUI.dialog のセット

	// 各システムのメニュー
	if( id == 'menu' ){
	
		var postData = {
			templateType	: Env.ajaxTemplateType.page,
			cmd				: 'dummy',
			element			: 'topMenu'
		};

		var templateName	= common.getSystemName();
		var navigationRoute	= 'menu';
	
	} else {
		
		if( Env[ common.getSystemName() ].loadContentsParams[ id ] ){
			var params = Env[ common.getSystemName() ].loadContentsParams[ id ]();
		} else {
			var params = { cmd : 'dummy' }
		}
		
		var postData = {
			pageId			: id,
			templateType	: Env.ajaxTemplateType.ajax,
			cmd				: params.cmd
		}
		
		var navigationRoute	= 'menu|' + id;
		var templateName	= common.getSystemName() + '\\' + ( params.templateName ? params.templateName : id );

	}
	
	common.loadHtml( 'contents', templateName, postData, function( html ){
		$( "#navi" ).html( common.generateNavigation( navigationRoute ) );
		func();	// コールバック
	});
}