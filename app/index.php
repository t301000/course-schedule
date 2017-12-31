<?php
require_once '../bootstrap.php';

$filename = null;
if (isset($_SESSION[USER_SESSION_KEY])) {
    $handle = fopen(FILES_DIR_PATH . '/' . LIST_FILENAME, 'r');
    if ($handle) {
        while ($line = fgets($handle)) {
            list($name, $openid, $filename) = explode('|', $line);
            if ($openid == $_SESSION[USER_SESSION_KEY]['username']) {
                $_SESSION[USER_SESSION_KEY]['filename'] = trim($filename);
                break;
            }
        }
        // die();
    }
}
?>

<?php include_once './partials/header.php'; ?>


    <div class="container">
        <div class="header text-center py-3">
            <h1>育林國中教師課表下載</h1>
        </div>
        <div class="main w-50 mx-auto text-center">
            <div class="d-flex flex-column">
                <?php if (isset($_SESSION[USER_SESSION_KEY])): ?>
                    <h2 class="my-5"><?=$_SESSION[USER_SESSION_KEY]['name'] ?> 老師</h2>
                    <a class="btn btn-success btn-lg mx-5 mb-5" href="./download.php">
                        下載課表：<?=$year ?>學年度<?=$section ?>學期
                    </a>
                    <a class="btn btn-primary btn-lg mx-5" href="./logout.php">登出</a>
                <?php else: ?>
                    <h2 class="my-5">登入</h2>
                    <div class="d-flex justify-content-center">
                        <a href="openid-login.php">
                            <img src="./images/ntpc-logo.png" class="logo-img" alt="新北市 OpenID 登入">
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($error): ?>
            <div class="alert alert-danger mt-3 mx-auto w-50" role="alert">
                <h5 class="alert-heading">錯誤：</h5>
                <p><?= $error ?></p>
            </div>
        <?php endif; ?>
    </div>

<?php include_once './partials/footer.php'; ?>