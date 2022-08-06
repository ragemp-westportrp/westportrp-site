<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
global $serverName;
global $user;
global $userInfo;
global $avatars;
global $page;
global $tmp;

if (!$user->isLogin()) {
    echo '
        <div>
            <div class="container">
                <div class="section">
                    <div class="row">
                        <div class="col s12 l4 hide-on-med-and-down">
                           <img style="width: 100%; opacity: 0.4" src="/client/images/logo/logo-w.png">
                        </div>
                        <div class="col s12 l4 white-text">
                            <b>Информация</b>
                            <label class="grey-text text-lighten-2">
                            <br>Курс валют: 1р. = 1 SC = 250$.
                            <br>За StateCoin можно приобрести: уникальный транспорт, недвижимость, бизнес, игровую валюту, и многое другое. После оплаты SC будут начислены на ваш счёт на игровом сервере автоматически.
                            <br><br>После оплаты SC будут автоматически начислены на счёт вашего игрового аккаунта на сервере. Посмотреть баланс можно в личном кабинете на сайте.
                            </label>
                            <br><br><label class="grey-text text-darken-1">Возникили проблемы с пополнением? Напишите в <a class="grey-text text-darken-1" href="https://discord.gg/84VerfZBGT">дискорд</a> нашего сервера или на <a class="grey-text text-darken-1" href="https://forum.state-99.com/index.php?threads/%D0%9F%D1%80%D0%BE%D0%B1%D0%BB%D0%B5%D0%BC%D1%8B-%D1%81-%D0%B4%D0%BE%D0%BD%D0%B0%D1%82%D0%BE%D0%BC.11/">форум</a>.</label>
                            <br><br><a href="/offer"><label class="grey-text text-darken-1" style="display: flex"><i style="font-size: 1.2rem; margin-right: 4px" class="hide material-icons">receipt</i>Договор оферты</label></a>
                        </div>
                        <div class="col s12 l1 white-text"></div>
                        <div class="col s12 l3 white-text">
                            <b>Как пополнить?</b><br>
                            <label class="grey-text text-lighten-2">Для пополнения StateCoin пожалуйста авторизуйтесь под вашими данными аккаунта</label><br><br>
                            <a href="/login" class="btn center waves-effect border-amber border-accent-4">Личный кабинет</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    ';
    return;
}

$accounts = $qb->createQueryBuilder('users')->selectSql()->where('social = \'' . $userInfo['social'] . '\'')->executeQuery()->getResult();

$img = 'https://i.imgur.com/TqN7uJa.png';

?>

<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12 l4 bw-text">
                <h5 class="wd-font bw-text">Пополнение баланса</h5>
                <div class="card card-h">
                    <div class="card-h-left">
                        <img src="https://i.imgur.com/XXpHWrT.png">
                    </div>
                    <div class="card-h-right">
                        <h5 class="wd-font bw-text text-accent-4">Баланс: <?php echo $userInfo['money_donate'] ?> sc</h5>
                        <form action="https://unitpay.ru/pay/357791-f8bce" class="row" style="margin-bottom: 0">
                            <div class="input-field col s12">
                                <input id="donateAcc" type="hidden" name="account" value="1|<?php echo $userInfo['id'] ?>">
                                <input id="donateDesc" type="hidden" name="desc" value="Пополнение SCoin на акканут: <?php echo $userInfo['login'] ?>">
                                <input id="donateSum" placeholder="1p = 1sc" required name="sum" type="number" class="validate">
                                <label for="money">Введите сумму</label>
                                <a href="/offer"><label class="grey-text text-darken-1" style="display: flex"><i style="font-size: 1.2rem; margin-right: 4px" class="hide material-icons">receipt</i>Договор оферты</label></a>
                                <br><button class="btn blue accent-4 waves-effect z-depth-0">Пополнить</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col s12 l8 bw-text">
                <div class="row">
                    <?php

                    for ($i = 0; $i < 3; $i++) {
                        if (!isset($accounts[$i])) {

                            $avatarsList = $avatars[rand(0, 1)];

                            echo '
                                <div class="col s12 l4">
                                    <div>
                                        <h5 class="wd-font grey-text center">Отсуствует</h5>
                                        <img style="width: 100%; height: 196px; object-fit: cover; object-position: top; opacity: 0.6" src="/client/images/logo/logo-w.png">
                                        <a href="#" class="btn disabled blue accent-4 z-depth-0 waves-effect" style="width: 100%">Список услуг</a>
                                    </div>
                                </div>
                            ';
                        } else {
                            $account = $accounts[$i];

                            $sex = json_decode($account['skin'])->SKIN_SEX;

                            $avatarsList = $avatars[$sex];

                            echo '
                                <div class="col s12 l4">
                                    <div>
                                        <h5 class="wd-font bw-text center">' . $account['name'] . '</h5>
                                        <img style="width: 100%; height: 196px; object-fit: cover; object-position: top" src="' . $avatarsList[rand(0, count($avatarsList) - 1)] . '">
                                        <a href="/user-donate-' . $account['id'] . '" class="btn blue accent-4 z-depth-0 waves-effect" style="width: 100%">Список услуг</a>
                                    </div>
                                </div>
                            ';
                        }
                    }

                    /*foreach ($accounts as $account) {
                        echo '
                            <div class="col s12 l4">
                                <div>
                                    <h4 class="wd-font center">' . $account['name'] . '</h4>
                                    <img style="width: 100%;" src="https://i.pinimg.com/originals/03/e4/ab/03e4ab8fdda18bfd973e2bd7438a11ff.png">
                                    <a href="/user-donate-' . $account['id'] . '" class="btn btn-large blue accent-4 z-depth-0 waves-effect" style="width: 100%">Открыть список услуг</a>
                                </div>
                            </div>
                        ';
                    }*/
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
if (!$userInfo['trade_block'] || !$user->isMedia())
    $tmp->showBlockPage('trade');
?>