<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}
global $serverName;
global $serverNameColor;
global $search;
global $page;
global $user;

$serverNameColor = '<b class="blue-text">G</b><b class="red-text">o</b><b class="amber-text">o</b><b class="blue-text">g</b><b class="green-text">l</b><b class="red-text">e</b>';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>
    <title><?php echo $this->titleHtml; ?></title>

    <link rel="shortcut icon" href="/images/search/logo.png" type="image/png">

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
<body class="white">

<nav class="white z-depth-0" role="navigation" style="z-index: 99999;">
    <div class="nav-wrapper container row">
        <?php
            if(isset($_GET['q']) && !empty($_GET['q'])) {
             echo '
                 <form class="col s12 card-panel" style="padding: 0; margin: 0; border-radius: 2px;">
                     <div class="input-field black-text">
                         <input  style="border-radius: 8px; border: 1px #efefef solid" id="search" type="search" name="q" required value="' . $_GET['q'] . '">
                     </div>
                 </form>
            ';
            }
        ?>
    </div>
</nav>