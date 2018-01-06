/**
 * @fileOverview JavaScript設定定義クラス用JSファイル
 * @auther 安芸情報システム株式会社 齋藤 毅
 * @version 1.0
 */

/**
 * @class 設定定義クラス
 */
var Env = {
	// 汎用配列デリミタ
	arrayDelimiter	: '|',
	// ページのURL定義
	pageUrl		: {
		top		: './default.aspx',	// ログイン後に遷移する画面
		login	: './login.aspx'
	},
	// AJAXページのURL定義
	ajaxUrl		: {
		html	: './ajax/html.aspx',
		json	: './ajax/json.aspx',
		login	: './ajax/login.aspx',
		logout	: './ajax/logout.aspx',
		regist	: './ajax/regist.aspx'
	},
	// AJAXで取得するデータのタイプ（XMLは未使用）
	ajaxType	: {
		HTML	: 'HTML',
		JSON	: 'JSON',
		XML		: 'XML'
	},
	// ajaxType = HTML で使用するテンプレートの種類（格納ディレクトリが templates/ajax かどうか）
	ajaxTemplateType	: {
		ajax	: 0,	// templates/ajax
		page	: 1		// templates
	},
	// jQuery-UI.datepicker のロケール設定
	datepickerRegional : {
		ja : {
			closeText			: '閉じる',
			prevText			: 'Prev',
			nextText			: 'Next',
			currentText			: '今日',
			monthNames			: [ '1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月' ],
			monthNamesShort		: [ '1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月' ],
			weekHeader			: '週',
			dayNames			: [ '日曜日', '月曜日', '火曜日', '水曜日', '木曜日', '金曜日', '土曜日' ],
			dayNamesShort		: [ '日', '月', '火', '水', '木', '金', '土' ],
			dayNamesMin			: [ '日', '月', '火', '水', '木', '金', '土' ],
			dateFormat			: 'yy/mm/dd', 
			firstDay			: 0,    
			isRTL				: false,
			showMonthAfterYear	: true,
			yearSuffix			: '年'
		}
	},
	// 各システムに属するページリスト ※システム名取得に使用
	systemPages : {
		common					: [ 'login', 'default' ],
		temporaryAdvanceManager	: [ 'temporaryAdvanceManager' ],
		remainingCashManager	: [ 'remainingCashManager' ],
		advertisingCostManager	: [ 'advertisingCostManager' ]
	},
	// 経費材料費仮払金管理
	temporaryAdvanceManager	: {
		// ページタイトル ※ナビゲーションリンク（パンくず）で使用
		pageTitle	: {
			menu					: 'メニュー',
			bankTransferAtEmployed	: '新人採用時振込入力',
			bankTransferAtApplied	: '申請振込入力',
			bankTransferAtRetired	: '退職時振込入力',
			changeAmount			: '定額変更入力',
			remitMoneyAtTermEnd		: '期末入金入力',
			adjustment				: '給与振込調整入力',
			reportEmployeeDetail	: '社員別仮払金明細照会',
			reportMonthlyBalance	: '月次仮払金振込・残高リスト',
			reportPeriodBalance		: '期末残高リスト',
			exportPCAData			: 'PCA給与取込データ作成',
			exportTermReceive		: '期末入金取り込み'
		},
		// ヘッダメニュー動作（サブメニューを表示するもの）
		subMenuIds	: [ 
			'#bankTransfer',
			'#report',
			'#export'
		],
		// レポートデータ取得コマンド
		reportDataCommand	: 'setReportDataParams',
		// コンテンツ取得用パラメータ
		loadContentsParams	: {
			// 申請フォーム ( 共通 )
			applyFormParams	: {
				cmd				: 'setFormParams',
				templateName	: 'applyForm'
			},
			// 照会・リスト ( 共通 )
			reportParams	: {
				cmd : 'setReportParams'
			},
			bankTransferAtApplied	: function(){ return this.applyFormParams; },	// 申請時振込入力
			bankTransferAtEmployed	: function(){ return this.applyFormParams; },	// 新人採用時振込入力
			bankTransferAtRetired	: function(){ return this.applyFormParams; },	// 退職時振込入力
			remitMoneyAtTermEnd		: function(){ return this.applyFormParams; },	// 期末入金入力
			changeAmount			: function(){ return this.applyFormParams; },	// 定額変更入力
			adjustment				: function(){ return this.applyFormParams; },	// 給与振込調整入力
			reportEmployeeDetail	: function(){ return this.reportParams; },		// 社員別仮払金明細照会
			reportPeriodBalance		: function(){ return this.reportParams; },		// 期末残高リスト
			reportMonthlyBalance	: function(){ return this.reportParams; }		// 月次仮払金振込・残高リスト
		}
	},
	// 社員現金残高管理
	remainingCashManager	: {
		// ページタイトル ※ナビゲーションリンク（パンくず）で使用
		pageTitle	: {
			menu	: 'メニュー',
			receive	: '現金受入入力'
		},
		// ヘッダメニュー動作（サブメニューを表示するもの）
		subMenuIds	: [],
		loadContentsParams : {
			// 現金受入フォーム
			cashFormParams	: {
				cmd				: 'setFormParams',
				templateName	: 'cashForm'
			},
			receive	: function(){ return this.cashFormParams; }
		}
	},
	// 広告費管理
	advertisingCostManager	: {
		// ページタイトル ※ナビゲーションリンク（パンくず）で使用
		pageTitle	: {
			menu						: 'メニュー',
			budgetLeaflet				: '予算入力 ( ちらし )',
			budgetTownpage				: '予算入力 ( タウンページ )',
			budgetWebPc					: '予算入力 ( Web-PC )',
			budgetWebMobile				: '予算入力 ( Web-Mobile )',
			budgetMagnet				: '予算入力 ( マグネット )',
			resultLeaflet				: '実績入力 ( ちらし )',
			resultTownpage				: '実績入力 ( タウンページ )',
			resultWebPc					: '実績入力 ( Web-PC )',
			resultWebMobile				: '実績入力 ( Web-Mobile )',
			resultMagnet				: '実績入力 ( マグネット )',
			checkLeaflet				: '実績入力確認 ( ちらし )',
			checkTownpage				: '実績入力確認 ( タウンページ )',
			checkWebPc					: '実績入力確認 ( Web-PC )',
			checkWebMobile				: '実績入力確認 ( Web-Mobile )',
			checkMagnet					: '実績入力確認 ( マグネット )',
			townpageMasterMaintenance	: 'タウンページマスタメンテナンス'
		},
		// ヘッダメニュー動作（サブメニューを表示するもの）
		subMenuIds	: [ 
			'#budget', '#result', '#check', '#product'
		],
		loadContentsParams : {
			// 広告費予算入力フォーム ( 共通 )
			advertisingCostFormBudgetParams	: {
				cmd				: 'setBudgetFormParams',
				templateName	: 'advertisingCostBudgetForm'
			},
			// 広告費実績入力フォーム ( 共通 )
			advertisingCostFormResultParams	: {
				cmd				: 'setResultFormParams',
				templateName	: 'advertisingCostResultForm'
			},
			// 広告費実績確認表 ( 共通 )
			advertisingCostReportResultCheckParams	: {
				cmd				: 'setResultCheckReportParams',
				templateName	: 'reportResultCheck'
			},
			// 広告費製作入力フォーム ( 共通 )
			advertisingCostFormProductParams : {
				cmd				: 'setProductFormParams',
				templateName	: 'advertisingCostProductForm'
			},
			budgetLeaflet				: function(){ return this.advertisingCostFormBudgetParams; },				// 予算入力 ちらし
			budgetTownpage				: function(){ return this.advertisingCostFormBudgetParams; },				// 予算入力 タウンページ
			budgetWebPc					: function(){ return this.advertisingCostFormBudgetParams; },				// 予算入力 Web ( PC )
			budgetWebMobile				: function(){ return this.advertisingCostFormBudgetParams; },				// 予算入力 Web ( 携帯 )
			budgetMagnet				: function(){ return this.advertisingCostFormBudgetParams; },				// 予算入力 マグネット
			resultLeaflet				: function(){ return this.advertisingCostFormResultParams; },				// 実績入力 ちらし
			resultTownpage				: function(){ return this.advertisingCostFormResultParams; },				// 実績入力 タウンページ
			resultWebPc					: function(){ return this.advertisingCostFormResultParams; },				// 実績入力 Web ( PC )
			resultWebMobile				: function(){ return this.advertisingCostFormResultParams; },				// 実績入力 Web ( 携帯 )
			resultMagnet				: function(){ return this.advertisingCostFormResultParams; },				// 実績入力 マグネット
			checkLeaflet				: function(){ return this.advertisingCostReportResultCheckParams; },		// 実績入力確認 ちらし
			checkTownpage				: function(){ return this.advertisingCostReportResultCheckParams; },		// 実績入力確認 タウンページ
			checkWebPc					: function(){ return this.advertisingCostReportResultCheckParams; },		// 実績入力確認 Web ( PC )
			checkWebMobile				: function(){ return this.advertisingCostReportResultCheckParams; },		// 実績入力確認 Web ( 携帯 )
			checkMagnet					: function(){ return this.advertisingCostReportResultCheckParams; },		// 実績入力確認 マグネット
			productLeaflet				: function(){ return this.advertisingCostFormProductParams; },				// 製作入力 ちらし
			productMagnet				: function(){ return this.advertisingCostFormProductParams; },				// 製作入力 マグネット
			townpageMasterMaintenance	: function(){ return { cmd : 'setTownpageMasterMaintenanceFormParams' } }	// タウンページマスタメンテナンス
		}
	}
}