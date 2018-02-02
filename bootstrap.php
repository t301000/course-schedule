<?php
require_once 'vendor/autoload.php';

date_default_timezone_set('Asia/Taipei');
session_start();

/* 設定開始 */
// 網站標題
define('SITE_TITLE', '育林國中教師課表下載');
// user data 的 session key
define('USER_SESSION_KEY', 'openid_user');
// 放置檔案之目錄路徑，自行建立該目錄，結尾不加 /
define('FILES_DIR_PATH', '/var/www/course_schedule_files');
// 清單列表檔名
// 姓名|openid|檔名含副檔名
define('LIST_FILENAME', 'user-list');
// 允許登入條件（全符合）
$login_rule = [
    'school_code' => '014569',
    'role' => '教師'
];
// 欲取得之 OpenID 欄位
$required = array(
    // 'namePerson/friendly', // 暱稱
    // 'contact/email', // email
    'namePerson', // 姓名
    // 'birthDate', // 生日，1985-06-12
    // 'person/gender', // 性別，M 男
    'contact/postalCode/home', // 識別碼
    // 'contact/country/home', // 單位簡稱，xx國中
    // 'pref/language', // 年級班級座號，6 碼
    'pref/timezone' // 授權資訊，含單位代碼、單位全銜、職務別、職稱別、身份別等資料，可能有多筆
);
/* 設定結束 */

/* 計算學年度、學期 */
$now = getdate();
$year = $now['year'] - 1911;
$mon = $now['mon'];
// 學年度
$year = $mon >=8 ? $year : --$year;
// 學期
$section = ($mon >= 8 || $mon <=1) ? '上' : '下';

/* 處理錯誤訊息 */
$error = isset($_SESSION['error']) ? $_SESSION['error']: null;
unset($_SESSION['error']);
