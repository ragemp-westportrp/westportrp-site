<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}
global $user;
global $qb;
global $fractionId;

$fractionId = 7;

$bName = $this->titleHtml;
$logo = 'https://i.imgur.com/1L4a9LU.png';
$bDesc = 'Добро пожаловать на официальный сайт службы новостей!';
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

    <link rel="shortcut icon" href="<?php echo $logo ?>">

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
<nav class="red" role="navigation">
    <div class="nav-wrapper container">
        <a id="logo-container" href="/network/news" class="brand-logo hide-on-med-and-down" style="">
            <img src="<?php echo $logo ?>" class="logo" style="margin: 7px; height: 50px; float: left; filter: none; width: auto;">
        </a>

        <ul class="right hide-on-med-and-down">
            <li><a class="white-text" href="/network/news">Главная</a></li>
            <li><a class="white-text" href="/network/news/map">Карта</a></li>
            <li><a class="white-text" href="/network/news/vehicles">Список транспорта</a></li>
            <li><a class="white-text" href="/network/news/ads">Объявления</a></li>
            <li><a class="white-text" href="/network/news/bio">Биографии</a></li>
            <?php
            if($user->isLogin()) {
                if (($user->isLeader() || $user->isSubLeader()) && $user->isNews() || $user->isAdmin())
                    echo '<li><a class="white-text" href="/network/news/log">Лог действий</a></li>';

                echo '<li><a class="white-text" href="/network/news/users">Сотрудники</a></li>';
            }
            else
                echo '<li><a class="white-text" href="/login">Войти</a></li>';
            ?>
        </ul>

        <ul id="nav-mobile" class="sidenav">
            <li>
                <div class="userView" style="height: 150px;">
                    <div class="background">
                        <img src="http://i.imgur.com/CXOUFxl.jpg" style="width: 100%;">
                    </div>
                    <a href="#!user"><img class="circle" src="<?php echo $logo ?>"></a>
                </div>
            </li>
            <li><a href="/network/news">Главная</a></li>
            <?php
            if($user->isLogin()) {
                if ($user->isLeader() && $user->isNews() || $user->isAdmin())
                    echo '<li><a href="/network/news/log">Лог действий</a></li>';
            }
            else
                echo '<li><a href="/login">Войти</a></li>';
            ?>
        </ul>
        <a href="#" data-activates="nav-mobile" class="button-collapse sidenav-trigger"><i class="material-icons black-text">menu</i></a>
    </div>
</nav>