<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $blocks;
global $userInfo;
global $page;
global $user;
global $qb;


?>

<!DOCTYPE html>
<html lang="<?php echo $this->langType ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>
    <title><?php echo $this->titleHtml; ?></title>

    <meta property="og:title" content="<?php echo $this->titleHtml; ?>" />
    <meta property="og:description" content="State99 - Американская мечта прямо на твоих экранах!" />
    <meta property="og:site_name" content="<?php echo $this->title; ?>">
    <meta property="og:type" content="top">
    <meta property="og:url" content="<?php echo $this->siteName ?>">
    <meta property="og:image" content="/client/images/logo/logo-b.png">

    <meta name="description" content="Американская мечта прямо на твоих экранах!">
    <meta name="keywords" content="Key" />
    <meta name="generator" content="State99 <?php echo $this->version ?>">
    <meta name="theme-color" content="#000">
    <meta name="verification" content="5049ff827bf431ddeab1b74d0d8bf3" />

    <link rel="shortcut icon" href="/client/images/logo/logo-w.png" type="image/x-icon" />

    <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>

    <!-- CSS  -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i" rel="stylesheet">

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="/client/css/material-charts.css" rel="stylesheet" media="screen,projection">
    <link href="/client/css/extended.css?v=9" type="text/css" rel="stylesheet" media="screen,projection"/>
    <link href="/client/css/<?php echo isset($_COOKIE['theme']) ? 'light' : 'black' ?>.css" type="text/css" rel="stylesheet" media="screen,projection"/>
    <link href="/client/css/animate.css" type="text/css" rel="stylesheet" media="screen,projection"/>
    <link href="/client/css/colors.css?v=3" type="text/css" rel="stylesheet" media="screen,projection"/>

    <link href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" type="text/css" rel="stylesheet" media="screen,projection"/>
    <script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>

    <style>
        td, th {
            padding: 5px 15px;
        }
    </style>

    <?php
        if($this->isMap) {
            echo '
            <link rel="stylesheet" href="/client/map/leaflet.css" />
            <link media="all" type="text/css" rel="stylesheet" href="/client/map/map.css">
            <script src="/client/map/jquery-1.11.2.min.js"></script>
            <script src="/client/map/leaflet.js"></script>
            ';
        }
    ?>
</head>
<body <?php echo ($this->overflowHidden ? 'style="overflow-x: hidden"' : '') ?>>
<nav class="bw-text transparent z-depth-0 wd-font" role="navigation">
    <div class="nav-wrapper container">
        <a id="logo-container" href="/" style="left: 0px;" class="brand-logo hide-on-med-and-down">
            <img src="/client/images/logo/logo-w.png" class="logo" style="margin: 7px; width: 50px;  height: 50px;">
        </a>
        <ul class="right hide-on-med-and-down">
            <li><a class="bw-text" id="hover" href="/"><?=LANG_HEADER_MAIN?></a></li>
            <li><a class="bw-text" id="hover" href="https://forum.state-99.com/">Форум</a></li>
            <li><a class="bw-text" id="hover" href="/rules">Правила</a></li>
            <li><a class="bw-text" id="hover" href="/donate"><?=LANG_HEADER_DONATE?></a></li>
            <?php
            if ($user->isLogin()) {
                echo '<li><a class="bw-text" id="hover" href="/report">Жалобы</a></li>';
                echo '<li><a class="bw-text" id="hover" href="/profile">' . LANG_HEADER_PROFILE . '</a></li>';
                echo '<li><a class="bw-text" id="hover" href="/logout">' . LANG_HEADER_EXIT . '</a></li>';
            }
            else
                echo '<li><a class="btn border-amber border-accent-4 white-text" href="/login">' . LANG_HEADER_ENTER . '</a></li>';
            ?>
        </ul>
        <ul id="slide-out" class="sidenav wb">
            <li>
                <div class="user-view" style="background: rgba(0,0,0,0.2);
}">
                    <div class="background">
                        <img src="https://i.imgur.com/EahoqpI.png" style="width: 100%;">
                    </div>
                    <a href="/"><img class="circle" src="/client/images/logo/logo-w.png"></a>
                    <a href="/"><span class="white-text name">STATE 99 RolePlay</span></a>
                    <a href="/"><span class="white-text email"><?=LANG_HEADER_TITLE?></span></a>
                </div>
            </li>
            <li><a class="bw-text" href="/"><?=LANG_HEADER_MAIN?></a></li>
            <li><a class="bw-text" href="https://forum.state-99.com/">Форум</a></li>
            <li><a class="bw-text" href="/donate"><?=LANG_HEADER_DONATE?></a></li>
            <li><a class="bw-text" href="/rules">Правила</a></li>
            <li><a class="bw-text" href="https://discord.gg/84VerfZBGT">Discord</a></li>
            <li><a class="bw-text" target="_blank" href="https://playo.ru/goods/gta5/?s=m4d9l06f">Купить GTA-V</a></li>

            <?php
            if ($user->isLogin()) {
                echo '<li><a class="bw-text" href="/report">Жалобы</a></li>';
                echo '<li><a class="bw-text" href="/profile">' . LANG_HEADER_PROFILE . '</a></li>';
                echo '<li><a class="bw-text" href="/logout">' . LANG_HEADER_EXIT . '</a></li>';
            }
            else
                echo '<li><a class="bw-text" href="/login">' . LANG_HEADER_ENTER . '</a></li>';
            ?>
        </ul>
        <a href="#" data-target="slide-out" class="sidenav-trigger bw-text"><i class="material-icons">menu</i></a>
    </div>
</nav>
<script>

    /*$('.dropdown-button-new').dropdown({
            inDuration: 300,
            outDuration: 225,
            constrainWidth: false,
            hover: false,
            gutter: 0,
            belowOrigin: false,
            alignment: 'left',
            stopPropagation: false
        }
    );*/
</script>
<main>