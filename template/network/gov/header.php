<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}
global $user;
global $qb;
global $fractionId;

$fractionId = 1;

$bName = $this->titleHtml;
$logo = 'https://cdn4.iconfinder.com/data/icons/flat-circle-flag/182/circle_flag_us_america_united_states-512.png';
$bDesc = 'Добро пожаловать на официальный сайт правительства штата San Andreas!';
$bTitle = $bName;
$bImg = $logo;

if (isset($_GET['newsId']) && is_numeric($_GET['newsId'])) {
    $item = $qb->createQueryBuilder('rp_news')->selectSql()->where('id = ' . intval($_GET['newsId']))->orderBy('id DESC')->executeQuery()->getSingleResult();

    $bTitle = htmlspecialchars_decode($item['title']);
    $bImg = htmlspecialchars_decode($item['img']);
    $bDesc = 'Автор: ' . htmlspecialchars_decode($item['author_name']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no"/>
    <title><?php echo $bTitle; ?></title>

    <meta property="og:title" content="<?php echo $bTitle ?>" />
    <meta property="og:description" content="<?php echo $bDesc ?>" />
    <meta property="og:site_name" content="<?php echo $bName ?>">
    <meta property="og:image" content="<?php echo $bImg ?>">
    <meta property="og:image:secure_url" content="<?php echo $bImg ?>">
    <meta property="og:image:alt" content="<?php echo $bTitle ?>">
    <meta property="og:locale" content="ru_RU">

    <meta name="description" content="<?php echo $bDesc ?>">

    <link rel="shortcut icon" href="https://i.imgur.com/ZODcxTy.png">

    <!-- CSS  -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="/client/css/extended.css?v=5" type="text/css" rel="stylesheet" media="screen,projection"/>

    <!--  Scripts-->
    <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="/client/js/extended.js"></script>
    <script src="/client/js/main.js?v=31"></script>

</head>
<body>
<nav class="white" role="navigation">
    <div class="nav-wrapper container">
        <a id="logo-container" href="/network/gov" class="brand-logo hide-on-med-and-down" style="">
            <img src="https://i.imgur.com/JiuhOal.png" class="logo" style="margin: 7px; height: 50px; float: left; filter: none; width: auto;">
        </a>

        <ul class="right hide-on-med-and-down">
            <li><a class="black-text" href="/network/gov">Главная</a></li>
            <li><a class="black-text" href="/network/gov/rules">Кодексы и законы</a></li>
            <li><a class="black-text" href="/network/gov/consignment">Список партий</a></li>
            <?php
            if($user->isLogin()) {
                if (($user->isLeader() || $user->isSubLeader()) && $user->isGov() || $user->isAdmin())
                {
                    echo '<li><a class="black-text" href="/network/gov/info">Информация</a></li>';
                    echo '<li><a class="black-text" href="/network/gov/log">Лог действий</a></li>';
                }
                echo '<li><a class="black-text" href="/network/gov/users">Сотрудники</a></li>';
            }
            else
                echo '<li><a class="black-text" href="/login">Войти</a></li>';
            ?>
        </ul>

        <ul id="nav-mobile" class="sidenav">
            <li>
                <div class="userView" style="height: 150px;">
                    <div class="background">
                        <img src="http://i.imgur.com/CXOUFxl.jpg" style="width: 100%;">
                    </div>
                    <a href="#!user"><img class="circle" src="https://images.vexels.com/media/users/3/128978/isolated/preview/bda6ac6e5565b962161be4f66c8868ff-usa-flag-print-map-by-vexels.png"></a>
                </div>
            </li>
            <li><a href="/network/gov">Главная</a></li>
            <li><a href="/network/gov/rules">Кодексы и законы</a></li>
            <li><a href="/network/gov/consignment">Список партий</a></li>
            <?php
            if($user->isLogin()) {
                if ($user->isLeader() && $user->isGov() || $user->isAdmin())
                    echo '<li><a href="/network/gov/log">Лог действий</a></li>';
                    echo '<li><a href="/network/gov/users">Сотрудники</a></li>';
            }
            else
                echo '<li><a href="/login">Войти</a></li>';
            ?>
        </ul>
        <a href="#" data-activates="nav-mobile" class="sidenav-trigger"><i class="material-icons black-text">menu</i></a>
    </div>
</nav>