$( document ).ready( function(){

	$( 'div.menu[ title ]' )
	.click( function(){
		location.href = this.title + '.php';
	})
	.hover(
		function(){ $( this ).addClass		( 'hover' ); },
		function(){ $( this ).removeClass	( 'hover' ); } 
	);

});
