<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $user;
global $qb;
global $userInfo;

$winner = [
    '$1.000.000 + VIP Hard на 90 дней + Любая именная маска + Fathom Faterix или Benefactor Bose или Ubermacht Mist',
    '$900.000 + VIP Hard на 60 дней + Любая именная маска + Ubermacht Mist',
    '$800.000 + VIP Hard на 60 дней + Любая именная маска + Benefactor Bose',
    '$700.000 + VIP Hard на 60 дней + Любая именная маска + Fathom Faterix',
    '$600.000 + VIP Hard на 60 дней + Любая именная маска + Ubermacht Legion',
    '$500.000 + VIP Hard на 30 дней + Любая именная маска + Pegassi Torero Custom',
    '$400.000 + VIP Hard на 30 дней + Любая именная маска + Benefactor Dubsta LSE',
    '$300.000 + VIP Hard на 30 дней + Любая именная маска + Obey 9R',
    '$200.000 + VIP Hard на 30 дней + Любая именная маска + Benefactor Streiter S',
    '$100.000 + VIP Hard на 30 дней + Любая именная маска + Obey Argento',
    '$100.000 + VIP Hard на 30 дней + AK-102 (Gold)',
    '$100.000 + VIP Hard на 30 дней + AK-102 (Gold)',
    '$100.000 + VIP Hard на 30 дней + Revolver MK2 (Patriotic)',
    '$100.000 + VIP Hard на 30 дней + Revolver MK2 (Sessana Nove)',
    '$100.000 + VIP Hard на 30 дней + Revolver MK2 (Woodland Camo)',
    '$100.000 + VIP Hard на 30 дней + HK-416 (Gold)',
    '$100.000 + VIP Hard на 30 дней + HK-416 (Gold)',
    '$100.000 + VIP Hard на 30 дней + HK-416A5 (Patriotic)',
    '$100.000 + VIP Hard на 30 дней + HK-416A5 (Sessana Nove)',
    '$100.000 + VIP Hard на 30 дней + Desert Deagle (Platinum)',
    '$100.000 + VIP Hard на 30 дней + Desert Deagle (Platinum)',
    '$100.000 + VIP Hard на 30 дней + Desert Deagle (Platinum)',
    '$100.000 + VIP Hard на 30 дней + G36KV (Patriotic)',
    '$100.000 + VIP Hard на 30 дней + G36KV (Sessana Nove)',
    '$100.000 + VIP Hard на 30 дней + G36KV (Digital Camo)',
    '$100.000 + VIP Hard на 30 дней + Маска на выбор',
    '$100.000 + VIP Hard на 30 дней + Маска на выбор',
    '$100.000 + VIP Hard на 30 дней + Маска на выбор',
    '$100.000 + VIP Hard на 30 дней + Маска на выбор',
    '$100.000 + VIP Hard на 30 дней + Маска на выбор',
    '$100.000 + VIP Hard на 30 дней + Маска на выбор',
    '$100.000 + VIP Hard на 30 дней + Маска на выбор',
    '$100.000 + VIP Hard на 30 дней + Маска на выбор',
    '$100.000 + VIP Hard на 30 дней + Маска на выбор',
    '$100.000 + VIP Hard на 30 дней + Маска на выбор',
    '$100.000 + VIP Hard на 30 дней',
    '$100.000 + VIP Hard на 30 дней',
    '$100.000 + VIP Hard на 30 дней',
    '$100.000 + VIP Hard на 30 дней',
    '$100.000 + VIP Hard на 30 дней',
    '$75.000 + VIP Hard на 15 дней',
    '$75.000 + VIP Hard на 15 дней',
    '$75.000 + VIP Hard на 15 дней',
    '$75.000 + VIP Hard на 15 дней',
    '$75.000 + VIP Hard на 15 дней',
    '$50.000 + VIP Hard на 10 дней',
    '$50.000 + VIP Hard на 10 дней',
    '$50.000 + VIP Hard на 10 дней',
    '$50.000 + VIP Hard на 10 дней',
    '$50.000 + VIP Hard на 10 дней',
];

for ($i = 0; $i < 100; $i++)
    array_push($winner, 'Отсуствует');
?>

<div class="container" style="margin-top: 0; margin-bottom: 5%">
    <div class="section">
        <div class="row">
            <div class="col s12 m12">
                <h3 class="wd-font bw-text">Суть конкурса</h3>
                <div class="bw-text" style="margin-bottom: 50px">
                    Уважаемые игроки, на сервере действует конкурс, достаточно зайти на сервер и играть, 5 Июля, согласно таблицы ниже, будут выданы призы участникам, которые наиграли наибольшое количество часов за этот месяц. Удачи!<br>
                    <label>Конкурс заканчивается 5 Июля</label>
                </div>
                <h3 class="wd-font bw-text">Список участников</h3>
                <table class="highlight responsive-table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Ник</th>
                        <th>Отыграно</th>
                        <th>Приз</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php

                    $users = $qb
                        ->createQueryBuilder('users')
                        ->selectSql()
                        ->where('admin_level = 0')
                        ->orderBy('online_contall DESC, name ASC')
                        ->limit(100)
                        ->executeQuery()
                        ->getResult()
                    ;
                    $count = 0;
                    foreach ($users as $item) {

                        //<td>' . round($item['online_contall'] * 8.5 / 60, 2) . 'ч</td>

                        echo '
                        <tr>
                            <td>' . ($count + 1) . '.</td>
                            <td>' .  $item['name'] . '</td>
                            <td>' . round($item['online_contall'] * 8.5 / 60, 2) . 'ч</td>
                            <td class="grey-text">' . $winner[$count] . '</td>
                        </tr>';

                        $count++;
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>