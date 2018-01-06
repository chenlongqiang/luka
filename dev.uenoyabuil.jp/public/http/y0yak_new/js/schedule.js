var options = {
	height: 450,
	width: 785,
	navLinks: {
		enableToday: true,
		enableNextYear: true,
		enablePrevYear: true,
		p:'&lsaquo; 先月', 
		n:'来月 &rsaquo;', 
		t:'本日'
	},
	locale: {
		days: ["日曜日", "月曜日", "火曜日", "水曜日", "木曜日", "金曜日", "土曜日", "日曜日"],
		months: ["１月", "２月", "３月", "４月", "５月", "６月", "７月", "８月", "９月", "１０月", "１１月", "１２月"]
	}
};

$( document ).ready( function(){
/*
		var events = [
			{	EventID	: 1,
				Date	: new Date( 2011, 7, 10 ),
				Title	: "×",
				URL		: "http://www.yahoo.co.jp",
				CssClass: 'am'
			},

			{	EventID	: 2,
				Date	: new Date( 2011, 7, 10 ),
				Title	: "×",
				URL		: "http://www.yahoo.co.jp"
			}

		]
*/		
	$.jMonthCalendar.Initialize( options );


	$( "#searchBuildName" ).change( function(){
	
		$( '#dataTableContainer' ).load( 'ajax/html.php', { cmd : 'selectScheduleDataTable', id : $(this).val() }, function(){ registScheduleEvent(); } );
	});


});

function registScheduleEvent(){

	$( "#searchRoomName" ).change( function(){

		getSchedule();
	});
}

function getSchedule(dateIn) {

		var dateObj = null;
		var year = "";
		var month = "";
		var prevMonth = "";
		var nextMonth = "";

		if (dateIn == undefined) {
			dateObj = new Date();
		} else {
			dateObj = new Date(dateIn);
		}
/*
		year = dateObj.getFullYear();
		month = dateObj.getMonth() + 1;
		if( month == 1 ) {	//検索する月が１月の場合
			prevMonth = (year - 1) + "-12";
			nextMonth = year + "-02";
			month = year + "-01";
		} else if (month == 12) {　//検索する月が１２月の場合
			prevMonth = year + "-11";
			nextMonth = (year + 1) + "-01";
			month = year + "-" + month;
		} else {
			prevMonth = month - 1;
			prevMonth = year + "-" + (prevMonth < 10 ? "0" + prevMonth : prevMonth );
			nextMonth = month + 1;
			nextMonth = year + "-" + (nextMonth < 10 ? "0" + nextMonth : nextMonth );
			month = year + "-" + (month < 10 ? "0" + month : month );
		}
*/
		//選択した部屋のすべての予約情報を取得
		var postData = {
			cmd : 'selectScheduleData',
			id 	: $( "#searchRoomName" ).val()
//			prev_start_time : prevMonth,
//			start_time : month,
//			next_start_time : nextMonth
		};

		$.post( 'ajax/json.php', postData, function( json ){
//alert(json);
			events = [];
//			$.jMonthCalendar.ReplaceEventCollection( events );
			var index = 0;

			for(var i in json){
			events[index] = [];
			events[index+1] = [];
				switch(json[i].a){
					case "am":
					case "all":
						events[index].EventID = json[i].reserveId;
						events[index].CssClass = json[i].a;
						events[index].Title	= "×";
						events[index].URL = "#";
						events[index].Date = json[i].date;
						break;
					case "pm":
						//午前、午後両方ある場合
						if (typeof events[index-1] != "object") events[index-1] = [];
						if( events[index-1].Date == json[i].date ) {
//						events[index].CssClass = "pmAll";
							events[index].EventID = json[i].reserveId;
							events[index].Title	= "×";
							events[index].URL = "#";
							events[index].Date = json[i].date;
							break;
						} else {
							events[index].CssClass = "am";
							events[index].Title	= "　";
							events[index].URL = "#";
							events[index].Date = json[i].date;
							events[index+1].EventID = json[i].reserveId;
							events[index+1].Title	= "×";
							events[index+1].URL = "#";
							events[index+1].Date = json[i].date;
							index++;
							break;
						}
				}
				index++;
			}
			//$("#jMonthCalendar").empty();
			$.jMonthCalendar.ReplaceEventCollection( events );
			$.jMonthCalendar.DrawCalendar();

//			$.jMonthCalendar.Initialize( options, events );
			//DrawEventsOnCalendar();
		}, 'json' );
}
