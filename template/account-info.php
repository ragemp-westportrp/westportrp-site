<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
global $user;
global $userInfo;
global $server;
global $serverName;
global $page;
global $typeLogUser;
global $imgVehList;

$onlineStatus = $this->accInfo['is_online'] ? 'green' : 'red';

$cars = $qb->createQueryBuilder('cars')->selectSql()->where('user_id = \'' . $this->accInfo['id'] . '\'')->executeQuery()->getResult();

if($page['p'] == 'admin/users') {
    //print_r($this->userInfo);
}

$fullTax = 0;
foreach ($cars as $item)
    $fullTax = $fullTax + $item['tax_money'];

$biz = null;
$house = null;
$aprt = null;
$stock = null;
$condo = null;

if ($this->accInfo['business_id'] > 0) {
    $biz = $qb->createQueryBuilder('business')->selectSql()->where('id = \'' . $this->accInfo['business_id'] . '\'')->executeQuery()->getSingleResult();
}
if ($this->accInfo['house_id'] > 0) {
    $house = $qb->createQueryBuilder('houses')->selectSql()->where('id = \'' . $this->accInfo['house_id'] . '\'')->executeQuery()->getSingleResult();
    if ($house['user_id'] != $this->accInfo['id'])
        $house = null;
}
if ($this->accInfo['apartment_id'] > 0) {
    $aprt = $qb->createQueryBuilder('apartment')->selectSql()->where('id = \'' . $this->accInfo['apartment_id'] . '\'')->executeQuery()->getSingleResult();
}
if ($this->accInfo['stock_id'] > 0) {
    $stock = $qb->createQueryBuilder('stocks')->selectSql()->where('id = \'' . $this->accInfo['stock_id'] . '\'')->executeQuery()->getSingleResult();
}
if ($this->accInfo['condo_id'] > 0) {
    $condo = $qb->createQueryBuilder('condos')->selectSql()->where('id = \'' . $this->accInfo['condo_id'] . '\'')->executeQuery()->getSingleResult();
}

$fullTax = $fullTax + $biz['tax_money'] + $house['tax_money'] + $condo['tax_money'] + $aprt['tax_money'] + $stock['tax_money'];

$img = $this->accInfo['phone_bg'] === '0' ? 'https://i.imgur.com/TqN7uJa.png' : $this->accInfo['phone_bg'];
//$img = 'https://i.imgur.com/TqN7uJa.png';

$btnDonate = '';

if ($this->accInfo['vip_type'] > 0 && $this->accInfo['vip_time'] > time()) {
    $btnDonate = '
        <form method="post">
            <input type="hidden" name="uid" value="' . $this->accInfo['id'] . '">
            <input type="hidden" name="price" value="' . ($fullTax * -1) . '">
            <input type="hidden" name="hex" value="' . hash('sha256', $this->accInfo['name']) . '">
            <button name="pay-tax-vip" class="btn waves-effect blue accent-4" style="width: 100%">Оплатить налоги $' . number_format($fullTax * -1) . '</button>
        </form>
    ';
}

$warns = '<span class="green-text">0</span>';
if ($this->accInfo['warns'] == 1)
    $warns = '<span class="orange-text">1</span>';
if ($this->accInfo['warns'] == 2)
    $warns = '<span class="red-text">2</span>';

$vip = '<span class="red-text">NONE</span>';

if ($this->accInfo['vip_time']> time())
{
    //<?php echo date('d/m/y', $this->accInfo['login_date'])
    if ($this->accInfo['vip_type'] == 1)
        $vip = '<span class="green-text tooltipped" data-position="top" data-tooltip="Дата окончания: ' . date('d/m/y', $this->accInfo['vip_time']) . '">LIGHT</span>';
    if ($this->accInfo['vip_type'] == 2)
        $vip = '<span class="blue-text tooltipped" data-position="top" data-tooltip="Дата окончания: ' . date('d/m/y', $this->accInfo['vip_time']) . '">HARD</span>';
}

$banList = $qb
    ->createQueryBuilder('ban_list')
    ->selectSql()
    ->where('ban_to = \'' . $this->accInfo['name'] . '\'')
    ->orderBy('id DESC')
    ->limit(500)
    ->executeQuery()
    ->getResult()
;

$days = $server->getDaysFromTime($this->accInfo['reg_timestamp']);
?>
<style>
    .modal-big {
        height: 90%;
        width: 90%;
    }
    .card-panel {
        background: none !important;
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 18px;
    }
</style>
<div style="width: 100%; overflow: hidden; position: absolute; height: 100%; z-index: -1;">
    <img src="<?php echo $img ?>" style="opacity: 0.2; filter: blur(10px); z-index: -1; position: absolute; top: 0; left: -20px; width: 110%; height: 90%; object-fit: cover;">
</div>
<div class="container bw-text" style="padding-top: 30px;">
    <div class="section">
        <div class="row">
            <div class="col s12 m8" style="display: flex">
                <img class="hide-on-med-and-down" style="border-radius: 50%; border: 5px #000 solid; margin-right: 30px; height: 138px;" src="https://a.rsg.sc//n/<?php echo strtolower($this->accInfo['social']) ?>">
                <div class="card-panel transparent center" style="margin: 0; width: 100%; max-height: 144px;">
                    <h3 style="margin: 0;" class="wd-font white-text"><?php echo $this->accInfo['name'] ?> <span class="<?php echo $onlineStatus ?>-text">•</span></h3>
                    <div class="amber-text">Средний онлайн</div>
                    <div class="amber-text"><?php echo round(($this->accInfo['online_time'] * 8.5 / 60) /  $days, 2) ?>ч</div>
                </div>
            </div>
            <div class="col s12 m4 hide">
                <div class="row">
                    <div class="col s12 center">
                        <a style="width: 100%; margin-bottom: 6px" href="#modalHistory-<?php echo $this->accInfo['id'] ?>" class="btn blue accent-4 modal-trigger">История персонажа</a>
                        <a style="width: 100%; margin-bottom: 6px" href="#modalAd-<?php echo $this->accInfo['id'] ?>" class="btn blue accent-4 modal-trigger">Лог активности</a>
                        <?php echo $btnDonate ?>
                    </div>
                </div>
            </div>
            <div class="col s12 m4">
                <div class="card-panel transparent center" style="margin: 0; width: 100%; max-height: 144px;">
                    <?php echo '$' . number_format(round($this->accInfo['money'] + $this->accInfo['money_bank'], 2)) ?>
                    <hr style="margin: 6px 0;">
                    <?php echo $this->accInfo['bank_card'] > 0 ? $server->bankFormat($this->accInfo['bank_card']) : 'Нет банковской карты' ?>
                    <hr style="margin: 6px 0;">
                    <?php echo $this->accInfo['phone'] > 0 ? $server->phoneFormat($this->accInfo['phone']) : 'Нет телефона' ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col s6 m4 l2 center">
                <div class="card-panel">
                    <h5 class="wd-font green-text" style="margin: 0"><?php echo round($this->accInfo['online_time'] * 8.5 / 60, 2) . 'ч' ?></h5>
                    <span class="grey-text">Отыграно</span>
                </div>
            </div>
            <div class="col s6 m4 l3 center">
                <div class="card-panel">
                    <h5 class="wd-font" style="margin: 0"><?php echo date('d/m/y', $this->accInfo['reg_timestamp']) ?></h5>
                    <span class="grey-text">Регистрация</span>
                </div>
            </div>
            <div class="col s12 m4 l2 center">
                <div class="card-panel">
                    <h5 class="wd-font" style="margin: 0"><?php echo $vip ?></h5>
                    <span class="grey-text">VIP</span>
                </div>
            </div>
            <div class="col s6 m4 l3 center">
                <div class="card-panel">
                    <h5 class="wd-font" style="margin: 0"><?php echo date('d/m/y', $this->accInfo['login_date']) ?></h5>
                    <span class="grey-text">Вход</span>
                </div>
            </div>
            <div class="col s6 m4 l2 center">
                <div class="card-panel">
                    <h5 class="wd-font" style="margin: 0"><?php echo $warns ?></h5>
                    <span class="grey-text">Варнов</span>
                </div>
            </div>

            <div class="col s6 m4 l2 center">
                <div class="card-panel">
                    <h5 class="wd-font" style="margin: 0"><?php echo ($this->accInfo['stats_strength'] + 1) ?>%</h5>
                    <span class="grey-text">Сила</span>
                </div>
            </div>
            <div class="col s6 m4 l2 center">
                <div class="card-panel">
                    <h5 class="wd-font" style="margin: 0"><?php echo ($this->accInfo['stats_endurance'] + 1) ?>%</h5>
                    <span class="grey-text">Выносливость</span>
                </div>
            </div>
            <div class="col s6 m4 l2 center">
                <div class="card-panel">
                    <h5 class="wd-font" style="margin: 0"><?php echo ($this->accInfo['stats_lung_capacity'] + 1) ?>%</h5>
                    <span class="grey-text">Объем легких</span>
                </div>
            </div>
            <div class="col s6 m4 l2 center">
                <div class="card-panel">
                    <h5 class="wd-font" style="margin: 0"><?php echo ($this->accInfo['stats_shooting'] + 1) ?>%</h5>
                    <span class="grey-text">Стрельба</span>
                </div>
            </div>
            <div class="col s6 m4 l2 center">
                <div class="card-panel">
                    <h5 class="wd-font" style="margin: 0"><?php echo ($this->accInfo['stats_driving'] + 1) ?>%</h5>
                    <span class="grey-text">Вождение</span>
                </div>
            </div>
            <div class="col s6 m4 l2 center">
                <div class="card-panel">
                    <h5 class="wd-font" style="margin: 0"><?php echo ($this->accInfo['stats_flying'] + 1) ?>%</h5>
                    <span class="grey-text">Пилотирование</span>
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 50px">
            <?php
            $cIdx = 0;
            foreach ($cars as $item) {
                $l2 = 'l3';
                if ($cIdx > 3)
                    $l2 = 'l2';
                $cIdx++;
                echo '
                        <div class="col s6 m4 ' . $l2 . ' center">
                            <div class="card">
                                <div class="card-image">
                                    <a target="_blank" href="/car-info-' . $item['name'] . '"><img style="max-height: 150px; object-fit: cover" src="/client/images/carsv/640/' . strtolower($item['name']) . '.jpg"></a>
                                </div>
                                <div class="card-content wd-font">
                                    <a class="bw-text" target="_blank" href="/car-info-' . $item['name'] . '">' . $item['name'] . '</a>
                                </div>
                            </div>
                        </div>
                    ';
            }
            ?>
        </div>

        <div class="row" style="margin-top: 50px">
            <div class="col s6 m4 l3 center">
                <div class="card-panel">
                    <div>
                        <i class="material-icons <?php echo $house ? 'green-text' : '' ?>" style="font-size: 60px">
                            home
                        </i>
                    </div>
                    <div>
                        <?php
                        if ($house) {
                            echo '<div>' . $house['address'] . ' #' . $house['number'] . '</div><hr>';
                            echo '<div>' . $house['street'] . '</div><hr>';
                            echo '<div>$' . number_format($house['price']) . '</div>';
                        }
                        else {
                            echo '<div>У Вас нет дома</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col s6 m4 l3 center">
                <div class="card-panel">
                    <div>
                        <i class="material-icons <?php echo $condo ? 'green-text' : '' ?>" style="font-size: 60px">
                            home
                        </i>
                    </div>
                    <div>
                        <?php
                        if ($condo) {
                            echo '<div>' . $condo['address'] . ' #' . $condo['number'] . '</div><hr>';
                            echo '<div>' . $condo['street'] . '</div><hr>';
                            echo '<div>$' . number_format($condo['price']) . '</div>';
                        }
                        else {
                            echo '<div>У Вас нет квартиры</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col s6 m4 l3 center">
                <div class="card-panel">
                    <div>
                        <i class="material-icons <?php echo $biz ? 'green-text' : '' ?>" style="font-size: 60px">
                            store
                        </i>
                    </div>
                    <div>
                        <?php
                        if ($biz) {
                            echo '<div>' . $biz['name'] . '</div><hr>';
                            echo '<div>$' . number_format($biz['bank']) . '</div><hr>';
                            echo '<div>$' . number_format($biz['price']) . '</div>';
                        }
                        else {
                            echo '<div>У Вас нет бизнеса</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col s6 m4 l3 center">
                <div class="card-panel">
                    <div>
                        <i class="material-icons <?php echo $stock ? 'green-text' : '' ?>" style="font-size: 60px">
                            archive
                        </i>
                    </div>
                    <div>
                        <?php
                        if ($stock) {
                            echo '<div>' . $stock['address'] . ' #' . $stock['number'] . '</div><hr>';
                            echo '<div>' . $stock['street'] . '</div><hr>';
                            echo '<div>$' . number_format($stock['price']) . '</div>';
                        }
                        else {
                            echo '<div>У Вас нет склада</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
            if ($this->accInfo['parthner_promocode'] != '') {

                $parthUserList = $qb->createQueryBuilder('users')->selectSql('COUNT(*)')->where('promocode = \'' . $this->accInfo['parthner_promocode'] . '\'')->orderBy('id DESC')->executeQuery()->getSingleResult();
                $parthUserList2 = $qb->createQueryBuilder('users')->selectSql('COUNT(*)')->where('promocode = \'' . $this->accInfo['parthner_promocode'] . '\' AND online_time > 203')->orderBy('id DESC')->executeQuery()->getSingleResult();

                echo '
                    <div class="row" style="margin-top: 50px">
                        <div class="col s12 center">
                            <div class="card-panel">
                                Приглашено игроков ' . reset($parthUserList) . '<br>
                                Отыгравшие 24ч ' . reset($parthUserList2) . '<br>
                            </div>
                        </div>
                    </div>
                
                ';
            }
        ?>
        <div class="row" style="margin-top: 50px">
            <div class="col s6 m4 l2 center">
                <div class="card-panel">
                    <div>
                        <i class="material-icons <?php echo $this->accInfo['a_lic'] ? 'green-text' : '' ?>" style="font-size: 60px">
                            motorcycle
                        </i>
                    </div>
                    Категория A
                </div>
            </div>
            <div class="col s6 m4 l2 center">
                <div class="card-panel">
                    <div>
                        <i class="material-icons <?php echo $this->accInfo['b_lic'] ? 'green-text' : '' ?>" style="font-size: 60px">
                            directions_car
                        </i>
                    </div>
                    Категория B
                </div>
            </div>
            <div class="col s6 m4 l2 center">
                <div class="card-panel">
                    <div>
                        <i class="material-icons <?php echo $this->accInfo['c_lic'] ? 'green-text' : '' ?>" style="font-size: 60px">
                            local_shipping
                        </i>
                    </div>
                    Категория C
                </div>
            </div>
            <div class="col s6 m4 l2 center">
                <div class="card-panel">
                    <div>
                        <i class="material-icons <?php echo $this->accInfo['air_lic'] ? 'green-text' : '' ?>" style="font-size: 60px">
                            airplanemode_active
                        </i>
                    </div>
                    Авиа
                </div>
            </div>
            <div class="col s6 m4 l2 center">
                <div class="card-panel">
                    <div>
                        <i class="material-icons <?php echo $this->accInfo['ship_lic'] ? 'green-text' : '' ?>" style="font-size: 60px">
                            directions_boat
                        </i>
                    </div>
                    Водная
                </div>
            </div>
            <div class="col s6 m4 l2 center">
                <div class="card-panel">
                    <div>
                        <i class="material-icons <?php echo $this->accInfo['gun_lic'] ? 'green-text' : '' ?>" style="font-size: 60px">
                            verified_user
                        </i>
                    </div>
                    Оружие
                </div>
            </div>
            <div class="col s6 m4 l2 center">
                <div class="card-panel">
                    <div>
                        <i class="material-icons <?php echo $this->accInfo['fish_lic'] ? 'green-text' : '' ?>" style="font-size: 60px">
                            public
                        </i>
                    </div>
                    Ловля рыбы
                </div>
            </div>
            <div class="col s6 m4 l2 center">
                <div class="card-panel">
                    <div>
                        <i class="material-icons <?php echo $this->accInfo['biz_lic'] ? 'green-text' : '' ?>" style="font-size: 60px">
                            store_mall_directory
                        </i>
                    </div>
                    Бизнес
                </div>
            </div>
            <div class="col s6 m4 l2 center">
                <div class="card-panel">
                    <div>
                        <i class="material-icons <?php echo $this->accInfo['law_lic'] ? 'green-text' : '' ?>" style="font-size: 60px">
                            local_library
                        </i>
                    </div>
                    Юрист
                </div>
            </div>
            <div class="col s6 m4 l2 center">
                <div class="card-panel">
                    <div>
                        <i class="material-icons <?php echo $this->accInfo['taxi_lic'] ? 'green-text' : '' ?>" style="font-size: 60px">
                            local_taxi
                        </i>
                    </div>
                    Перевозка
                </div>
            </div>
            <div class="col s6 m4 l2 center">
                <div class="card-panel">
                    <div>
                        <i class="material-icons <?php echo $this->accInfo['med_lic'] ? 'green-text' : '' ?>" style="font-size: 60px">
                            local_hospital
                        </i>
                    </div>
                    Страховка
                </div>
            </div>
            <div class="col s6 m4 l2 center">
                <div class="card-panel">
                    <div>
                        <i class="material-icons <?php echo $this->accInfo['work_lic'] != '' ? 'green-text' : '' ?>" style="font-size: 60px">
                            work
                        </i>
                    </div>
                    Work ID
                </div>
            </div>
        </div>
        <div class="row" style="margin-top: 50px" >
            <div class="col s12">
                <ul class="tabs z-depth-0 transparent">
                    <li class="tab col s3"><a style="font-size: 16px" class="bw-text wd-font" href="#history">История персонажа</a></li>
                    <li class="tab col s3"><a style="font-size: 16px" class="bw-text wd-font" href="#banlist">Ваши наказания</a></li>
                    <li class="tab col s3"><a style="font-size: 16px" class="bw-text wd-font" href="#log">Лог авторизаций</a></li>
                    <li class="tab col s3"><a style="font-size: 16px" class="bw-text wd-font" href="#money">Денежные операции</a></li>
                </ul>
            </div>
        </div>
        <div class="row" id="money">
            <div class="col s12">
                <table class="highlight responsive-table" style="background: none !important;">
                    <thead>
                    <tr>
                        <th data-field="id">#</th>
                        <th data-field="do">Сумма</th>
                        <th data-field="gid">Описание</th>
                        <th data-field="timestamp">Дата</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $idx = 0;

                    $adList = $qb->createQueryBuilder('log_bank_user')->selectSql()->where('user_id = ' . $this->accInfo['id'])->orderBy('id DESC')->limit(50)->executeQuery()->getResult();
                    $adList2 = $qb->createQueryBuilder('log_cash_user')->selectSql()->where('user_id = ' . $this->accInfo['id'])->andWhere('text NOT LIKE \'%Ответ на вопрос%\'')->orderBy('id DESC')->limit(50)->executeQuery()->getResult();

                    $adList = array_merge($adList, $adList2);

                    usort($adList, function ($a, $b) {
                        if ($a['timestamp'] == $b['timestamp']) {
                            return 0;
                        }
                        return ($a['timestamp'] < $b['timestamp']) ? 1 : -1;

                    });

                    foreach ($adList as $item) {
                        echo '
                            <tr>
                                <td class="grey-text">' . (++$idx) . '.</td>
                                <td>' . ($item['price'] >= 0 ? '<span class="green-text">$' . number_format($item['price']) . '</span>' : '<span class="red-text">$' . number_format($item['price']) . '</span>') . '</td>
                                <td>' . $item['text'] . '</td>
                                <td>' . $item['datetime'] . '</td>
                            </tr>
                        ';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row" id="banlist">
            <div class="col s12 <?php echo count($banList) < 1 ? 'hide' : ''; ?>">
                <table class="highlight responsive-table" style="background: none !important;">
                    <thead>
                    <tr>
                        <th data-field="date">Дата</th>
                        <th data-field="name_from">Администратор</th>
                        <th data-field="name_to">Нарушитель</th>
                        <th data-field="count">Кол-во</th>
                        <th data-field="reason">Причина</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($banList as $item) {
                        echo '
                            <tr>
                                <td>' . gmdate("d/m, H:i", $item['datetime'] + 3600 * 3) . '</td>
                                <td>' . $item['ban_from'] . '</td>
                                <td>' . $item['ban_to'] . '</td>
                                <td>' . $item['count'] . '</td>
                                <td>' . $item['reason'] . '</td>
                            </tr>
                            ';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <div class="col s12 <?php echo count($banList) < 1 ? '' : 'hide'; ?>">
                <h4 class="grey-text center">Наказания отсутствуют</h4>
            </div>
        </div>
        <div class="row" id="log">
            <div class="col s12">
                <table class="highlight responsive-table" style="background: none !important;">
                    <thead>
                    <tr>
                        <th data-field="id">#</th>
                        <th data-field="do">Действие</th>
                        <th data-field="gid">Game ID</th>
                        <th data-field="editor">IP</th>
                        <th data-field="timestamp">Дата</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $adList = $qb->createQueryBuilder('log_connect')->selectSql()->where('account_id = ' . $this->accInfo['id'])->orderBy('id DESC')->limit(50)->executeQuery()->getResult();
                    $idx = 1;
                    foreach ($adList as $item) {
                        echo '
                    <tr>
                        <td>' . ($idx++) . '.</td>
                        <td>' . $item['type'] . '</td>
                        <td>' . $item['game_id'] . '</td>
                        <td>' . $item['address'] . '</td>
                        <td>' . $item['timestamp'] . '</td>
                    </tr>
                    ';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row" id="history">
            <div class="col s12">
                <table class="highlight responsive-table" style="background: none !important;">
                    <thead>
                    <tr>
                        <th data-field="id">#</th>
                        <th data-field="do">Нарушитель</th>
                        <th data-field="rp_date">Игровая Дата</th>
                        <th data-field="timestamp">Дата</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $history = $qb->createQueryBuilder('log_player')->selectSql()->where('user_id = ' . $this->accInfo['id'])->orderBy('id DESC')->limit(500)->executeQuery()->getResult();
                    foreach ($history as $item) {
                        echo '
                    <tr>
                        <td>' . $item['id'] . '.</td>
                        <td>' . $item['do'] . '</td>
                        <td>' . $item['rp_datetime'] . '</td>
                        <td>' . gmdate("m-d-Y, H:i", $item['timestamp'] + 3600 * 3) . '</td>
                    </tr>
                    ';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>