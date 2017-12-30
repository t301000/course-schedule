<?php
require_once '../bootstrap.php';

$openid = new LightOpenID($_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);

switch ($openid->mode) {
    // 使用者同意
    case 'id_res':
        // 取得 user OpenID 資料
        $userData = getUserData();
        // 檢查是否可以登入
        $canLogin = checkCanLogin($userData);
        // 可以登入則寫入 session
        if ($canLogin) {
            $user = [
                'username' => $userData['openid_username'],
                'name' => $userData['real_name']
            ];
            $_SESSION[USER_SESSION_KEY] = $user;
        }

        header('Location: ./index.php');
        exit();
        break;
    // 使用者取消認證
    case 'cancel':
        header('Location: ./index.php');
        exit();
        break;
    // 啟動 OpenID 認證流程
    default:
        unset($_SESSION[USER_SESSION_KEY]);
        startOpenidAuth($openid);
}

/**
 * 取得 OpenID 資料
 *
 * 取得之原始資料範例：
 *
 * $openid->identity
 * string(36) "https://openid.ntpc.edu.tw/user/xxxxx"
 *
 * $openid->getAttributes()
 * array(9) {
 *     ["namePerson/friendly"]=>
 *     string(9) "王小明"
 *     ["contact/email"]=>
 *     string(21) "xxxx@apps.ntpc.edu.tw"
 *     ["namePerson"]=>
 *     string(9) "王小明"
 *     ["birthDate"]=>
 *     string(10) "1983-06-25"
 *     ["person/gender"]=>
 *     string(1) "M"
 *     ["contact/postalCode/home"]=>
 *     string(64) "5EE2EFCE20722348C2E27AA5E21F60FE69F811651068288F6F7F264BAF4620FB"
 *     ["contact/country/home"]=>
 *     string(12) "xx國中"
 *     ["pref/language"]=>
 *     string(6) "000000"
 *     ["pref/timezone"]=>
 *     string(116) "[{"id":"014579","name":"新北市立xx國民中學","role":"教師","title":"專任教師","groups":["導師"]}]"
 * }
 *
 *
 * 回傳之資料範例：
 * $user_data = [
 *   'openid_username' => 'openiduser',
 *   'id_code' => '5EE2EFCE20722348C2E27AA5E21F60FE69FA11651069288F6F6F264BAF4620FB',
 *   'real_name' => '王小明',
 *   'nick_name' => '王小明',
 *   'gender' => '男',
 *   'birthday' => '1973-08-14',
 *   'email' => 'xxxxxx@apps.ntpc.edu.tw',
 *   'schoolNameShort' => '中正國中',
 *   'grade' => '00',
 *   'class' => '00',
 *   'num' => '00',
 *   'auth_info' => [
 *      [
 *          'id' => '014569',
 *          'name' => '新北市立中正國民中學',
 *          'role' => '教師',
 *          'title' => '專任教師',
 *          'groups' => ['導師']
 *      ]
 *   ]
 * ];
 *
 *
 * @return null|array
 */
function getUserData()
{
    global $openid;
    $user_data = null;
    if ($openid->validate()) {
        // Notice: Only variables should be passed by reference
        // http://stackoverflow.com/questions/4636166/only-variables-should-be-passed-by-reference
        // $user_data['openid_username'] = end(array_values(explode('/', $openid->identity)));
        $identity_array = array_values(explode('/', $openid->identity));
        $user_data['openid_username'] = end($identity_array);
        $attr = $openid->getAttributes();
        $user_data['id_code'] = $attr['contact/postalCode/home'];
        $user_data['real_name'] = $attr['namePerson'];
        // $user_data['nick_name'] = $attr['namePerson/friendly'];
        // $user_data['gender'] = ($attr['person/gender'] == 'M') ? '男' : '女';
        // $user_data['birthday'] = $attr['birthDate'];
        // $user_data['email'] = $attr['contact/email'];
        // $user_data['schoolNameShort'] = $attr['contact/country/home'];
        // $user_data['grade'] = substr($attr['pref/language'], 0, 2);
        // $user_data['class'] = substr($attr['pref/language'], 2, 2);
        // $user_data['num'] = substr($attr['pref/language'], 4, 2);
        // foreach (json_decode($attr['pref/timezone']) as $item) {
        //     $user_data['auth_info'][$item->id] = [
        //         'schoolName' => $item->name,
        //         'role' => $item->role,
        //         'title' => $item->title,
        //         'groups' => $item->groups
        //     ];
        // }
        $user_data['auth_info'] = json_decode($attr['pref/timezone'], true);
        // var_dump($user_data);
    }
    return $user_data;
}
/**
 * 啟動 OpenID 認證流程
 */
function startOpenidAuth()
{
    global $openid, $required;

    $openid->identity = 'http://openid.ntpc.edu.tw/';
    $openid->required = $required;
    header('Location: ' . $openid->authUrl());
    exit();
}

/**
 * 檢查是否可以登入
 *
 * @param user_data
 *
 * @return bool
 */
function checkCanLogin($user_data) {
    global $login_rule;

    $auth_info = $user_data['auth_info'];

    $result = false;
    foreach ($auth_info as $info) {
        if ($info['id'] == $login_rule['school_code'] && $info['role'] == $login_rule['role']) {
            $result = true;
            break;
        }
    }

    return $result;
}