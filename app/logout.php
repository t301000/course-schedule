<?php
require_once '../bootstrap.php';

unset($_SESSION[USER_SESSION_KEY]);

header('Location: ./index.php');