<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
global $page;
global $serverName;
global $user;
global $userInfo;

//echo '<div class="container"><div class="section" style="padding: 150px 0"><h1 class="grey-text">Временно не рабоатет</h1></div></div>';
//return;

/*if ($serverName == 'M83') {
    echo '<div class="container"><div class="section" style="padding: 150px 0"><h1 class="grey-text">Скоро будет доступно</h1></div></div>';
    return;
}*/

/*if($userInfo['age'] < 19) {
    echo '<div class="container"><div class="section" style="padding: 150px 0"><h1 class="grey-text">Вашему персонажу должно быть 19 лет<br></h1></div></div>';
    return;
}*/

if (! function_exists("array_key_last")) {
    function array_key_last($array) {
        if (!is_array($array) || empty($array)) {
            return NULL;
        }

        return array_keys($array)[count($array)-1];
    }
}

/*if ($user->isAdmin()) {
	print_r($_POST);
	$result = array_key_last($_POST);
	print_r($result);
}*/

$qb
    ->createQueryBuilder('')
    ->otherSql("set session sql_mode=''", false)
    ->executeQuery()
    ->getResult()
;

$tradeDay1 = $qb
    ->createQueryBuilder('log_coin_trade')
    ->selectSql('COUNT(*) as count')
    ->groupBy('date')
    ->where('date = \'' . gmdate("Y-m-d") . '\'')
    ->executeQuery()
    ->getSingleResult()
;

$tradeAll = $qb
    ->createQueryBuilder('log_coin_trade')
    ->selectSql('COUNT(*) as count')
    ->executeQuery()
    ->getSingleResult()
;

$logCoinOutSum = $qb
    ->createQueryBuilder('log_coin_out')
    ->selectSql('SUM(sum) as sum_all')
    ->where('user_id = \'' . $userInfo['id'] . '\'')
    ->executeQuery()
    ->getSingleResult()
;

$curse = 100;
if ($serverName == 'Andromeda' || $serverName == 'SunFlower')
    $curse = 300;

$acBuy = $qb
    ->createQueryBuilder('trade_coin')
    ->selectSql()
    ->orderBy('id DESC')
    ->where('type = 1')
    ->executeQuery()
    ->getResult()
;

$moneyBuy = $qb
    ->createQueryBuilder('trade_coin')
    ->selectSql()
    ->orderBy('id DESC')
    ->where('type = 0')
    ->executeQuery()
    ->getResult()
;

$token1 = hash('sha256', time());
$token2 = hash('sha256', time());
?>

<div id="<?php echo $token1 ?>"></div>

<div class="container" id="<?php echo $token2 ?>">
    <div class="section">
        <div class="section">
            <div class="row">
                <div class="col s12 white-text">
                    <h4 class="grey-text">Торговая площадка</h4>
                    <label>Баланс StateCoin: <?php echo number_format($userInfo['money_donate']) ?>sc</label><br>
                    <label>Баланс игровой валюты: $<?php echo number_format($userInfo['money']) ?></label><br>
                    <label>Выведено средств: <?php echo number_format(reset($logCoinOutSum)) ?>sc</label>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <ul class="tabs card">
                        <li class="tab col s3"><a class="white-text" href="#tab1">Покупка игровой валюты</a></li>
                        <li class="tab col s3"><a class="white-text" href="#tab2">Покупка StateCoin</a></li>
                        <li class="tab col s2"><a class="white-text" href="#tab3">Вывод</a></li>
                        <li class="tab col s2"><a class="white-text" href="#tab4">Обмен</a></li>
                        <li class="tab col s2"><a class="white-text" href="#tab5">ЛОГ</a></li>
                    </ul>
                </div>

                <div class="col s12" id="tab3">
                    <div class="row">
                        <div class="col s12">
                            <br>
                            <h5 class="grey-text">Информация!</h5>
                            <div class="white-text">1. Ваши StateCoin можно вывести с аккаунта в рубли.</div>
                            <div class="white-text">2. Минимальная сумма вывода 200sc.</div>
                            <div class="white-text">3. Вывод средств происходит с учетом комисси 50%.</div>
                            <div class="white-text">4. Вывод средств возможен только если ваш аккаунт зарегестрирован в нашей системе более 7 суток.</div>
                            <div class="white-text">5. Вывод происходит мнговенно, достаточно указать реквизиты.</div>
                            <div class="white-text">6. Учтите, что за любые махинации с тоговой площадкой вы можете быть заблокированы платежной системой unitpay.ru и администрацией сайта state-99.com.</div>
                            <br>
                        </div>
                        <div class="col s12 m6 l4">
                            <form method="post" class="card-panel">
                                <label>На банковскую карту</label>
                                <input type="hidden" name="type" value="card">
                                <input placeholder="Номер карты" required="" value="<?php echo $userInfo['wallet_card'] ?>" type="text" name="number">
                                <input placeholder="Сумма" required="" min="1" type="number" name="money">
                                <button name="trade-trans-to-card" style="width: 100%" class="waves-effect z-depth-0 waves-light btn btn-large border-amber border-accent-4">Вывести</button>
                            </form>
                        </div>
                        <div class="col s12 m6 l4">
                            <form method="post" class="card-panel">
                                <label>На QIWI кошелёк</label>
                                <input type="hidden" name="type" value="qiwi">
                                <input placeholder="Номер телефона" required="" value="<?php echo $userInfo['wallet_qiwi'] ?>" type="text" name="number">
                                <input placeholder="Сумма" required="" min="1" type="number" name="money">
                                <button name="trade-trans-to-card" style="width: 100%" class="waves-effect z-depth-0 waves-light btn btn-large border-amber border-accent-4">Вывести</button>
                            </form>
                        </div>
                        <div class="col s12 m6 l4">
                            <form method="post" class="card-panel">
                                <label>На PayPal аккаунт</label>
                                <input type="hidden" name="type" value="paypal">
                                <input placeholder="Номер счёта" required="" value="<?php echo $userInfo['wallet_paypal'] ?>" type="text" name="number">
                                <input placeholder="Сумма" required="" min="1" type="number" name="money">
                                <button name="trade-trans-to-card" style="width: 100%" class="waves-effect z-depth-0 waves-light btn btn-large border-amber border-accent-4">Вывести</button>
                            </form>
                        </div>
                        <div class="col s12 m6 l4">
                            <form method="post" class="card-panel">
                                <label>На яндекс деньги</label>
                                <input type="hidden" name="type" value="yandex">
                                <input placeholder="Номер счёта" required="" value="<?php echo $userInfo['wallet_yandex'] ?>" type="text" name="number">
                                <input placeholder="Сумма" required="" min="1" type="number" name="money">
                                <button name="trade-trans-to-card" style="width: 100%" class="waves-effect z-depth-0 waves-light btn btn-large border-amber border-accent-4">Вывести</button>
                            </form>
                        </div>
                        <div class="col s12 m6 l4">
                            <form method="post" class="card-panel">
                                <label>На WebMoney рубли (WMR)</label>
                                <input type="hidden" name="type" value="webmoneyWmr">
                                <input placeholder="Номер счёта" required="" value="<?php echo $userInfo['wallet_webmoneyWmr'] ?>" type="text" name="number">
                                <input placeholder="Сумма" required="" min="1" type="number" name="money">
                                <button name="trade-trans-to-card" style="width: 100%" class="waves-effect z-depth-0 waves-light btn btn-large border-amber border-accent-4">Вывести</button>
                            </form>
                        </div>
                        <div class="col s12 m6 l4">
                            <form method="post" class="card-panel">
                                <label>На WebMoney доллары (WMZ)</label>
                                <input type="hidden" name="type" value="webmoney">
                                <input placeholder="Номер счёта" required="" value="<?php echo $userInfo['wallet_webmoney'] ?>" type="text" name="number">
                                <input placeholder="Сумма" required="" min="1" type="number" name="money">
                                <button name="trade-trans-to-card" style="width: 100%" class="waves-effect z-depth-0 waves-light btn btn-large border-amber border-accent-4">Вывести</button>
                            </form>
                        </div>
                        <div class="col s12">
                            <div class="card-panel">
                                <table class="highlight responsive-table">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Реквизиты</th>
                                        <th>Списано</th>
                                        <th>Статус</th>
                                        <th>Дата</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php

                                    $logCoinOut = $qb
                                        ->createQueryBuilder('log_coin_out')
                                        ->selectSql()
                                        ->where('user_id = \'' . $userInfo['id'] . '\'')
                                        ->orderBy('id DESC')
                                        ->executeQuery()
                                        ->getResult()
                                    ;

                                    if ($userInfo['admin_level'] > 5) {
                                        $logCoinOut = $qb
                                            ->createQueryBuilder('log_coin_out')
                                            ->selectSql()
                                            ->orderBy('id DESC')
                                            ->executeQuery()
                                            ->getResult()
                                        ;
                                    }

                                    foreach ($logCoinOut as $item) {
                                        $status = '<span class="red-text">Ошибка: ' . $item['state'] . '</span>';
                                        if ($item['state'] == 'SUCCESS')
                                            $status = '<span class="green-text">Операция выполнена</span>';
                                        else if ($item['state'] == 'WAIT')
                                            $status = '<span>Операция в обработке</span>';
                                        echo '
                                            <tr>
                                                <td class="grey-text">' . $item['id'] . '.</td>
                                                <td class="grey-text">' . $item['payer'] . '</td>
                                                <td class="green-text">' . $item['sum'] . 'sc</td>
                                                <td>' . $status . '</td>
                                                <td>' . gmdate("d-m-Y, H:i", $item['timestamp'] + (3600 * 3)) . '</td>
                                                <td><form method="post"><input type="hidden" name="id" value="' . $item['payout_id'] . '"><button name="trade-trans-status" class="btn ' . ($item['state'] == 'WAIT' || $userInfo['admin_level'] > 5 ? '' : 'hide') . ' border-amber z-depth-0 waves-effect tooltipped" data-position="top" data-tooltip="Получает актуальный статус выплаты">Cтатус</button></form></td>
                                                
                                                ' . ($userInfo['admin_level'] > 5 ? '<td class="grey-text">ID: ' . $item['user_id'] . '</td>' : '') . '
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

                <div class="col s12" id="tab4">
                    <div class="row">
                        <div class="col s12">
                            <br>
                            <h5 class="grey-text">Информация!</h5>
                            <div class="white-text">1. Для того, чтобы пользоваться торговой площадкой, вам необходимо с вашего аккаунта перевести игровую валюту, на счет аккаунта.</div>
                            <div class="white-text">2. Система видит только ту валюту, которая находится у вас в руках, для перевода с персонажа на аккаунт.</div>
                            <div class="white-text">3. Обмен совершается в обе стороны, как с аккаунта на персонажа, так с персонажа на аккаунт.</div>
                            <div class="white-text">4. Чтобы совершить покупку, вам необходимо иметь на аккаунте вашу игровую валюту, а не на счете персонажа.</div>
                            <div class="white-text">5. После покупки StateCoin вы можете их тратить в донат-шопе или вывести в рубли через вкладку "вывод".</div>
                            <br>
                        </div>
                        <div class="col s12 m6">
                            <form method="post" class="card-panel">
                                <label>Продажа валюты</label>
                                <input placeholder="Сколько валюты продать" required="" min="1" type="number" name="money">
                                <input placeholder="Сколько вы хотите SC" required="" min="1" type="number" name="sc">
                                <button name="trade-money" style="width: 100%" class="waves-effect z-depth-0 waves-light btn btn-large border-amber border-accent-4">Выставить валюту на площадку
                                </button>
                            </form>
                        </div>
                        <div class="col s12 m6">
                            <form method="post" class="card-panel">
                                <label>Продажа StateCoin</label>
                                <input placeholder="Сколько SC продать" required="" min="1" type="number" name="sc">
                                <input placeholder="Сколько вы хотите валюты" required="" min="1" type="number" name="money">
                                <button name="trade-sc" style="width: 100%" class="waves-effect z-depth-0 waves-light btn btn-large  border-amber border-accent-4">Выставить SC на площадку
                                </button>
                            </form>
                        </div>
                        <div class="col s12 m6">
                            <form method="post" class="card-panel">
                                <label>Перевод игровой валюты на аккаунт</label>
                                <select required name="player">
                                    <option value="0">Выберите персонажа</option>
                                    <?php
                                    foreach ($user->getPlayers() as $player)
                                        echo '<option value="' . $player['id'] . '">Зачислить с ' . $player['name'] . ' ($' . number_format($player['money']) . ')</option>';
                                    ?>
                                </select>
                                <input placeholder="Сумма" required="" min="1" type="number" name="money">
                                <button name="trade-player-to-account" style="width: 100%" class="waves-effect z-depth-0 waves-light btn btn-large border-amber border-accent-4">Вывести</button>
                            </form>
                        </div>
                        <div class="col s12 m6">
                            <form method="post" class="card-panel">
                                <label>Перевод игровой валюты на персонажа</label>
                                <select required name="player">
                                    <option value="0">Выберите персонажа</option>
                                    <?php
                                    foreach ($user->getPlayers() as $player)
                                        echo '<option value="' . $player['id'] . '">Зачислить на ' . $player['name'] . '</option>';
                                    ?>
                                </select>
                                <input placeholder="Сумма" required="" min="1" type="number" name="money">
                                <button name="trade-account-to-player" style="width: 100%" class="waves-effect z-depth-0 waves-light btn btn-large border-amber border-accent-4">Перевести</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col s12" id="tab1">
                    <div class="card-panel">
                        <table class="highlight responsive-table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th style="width: 24px;">Игрок</th>
                                <th></th>
                                <th>Сумма</th>
                                <th class="right">Действие</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $count = 1;
                            foreach ($moneyBuy as $item) {
                                echo '
                                <tr>
                                    <td>' . ($count++) . '</td>
                                    <td><img style="width: 24px;border-radius: 50%;margin-top: 5px;" src="https://a.rsg.sc//n/' . strtolower($item['user_social']) . '"></td>
                                    <td>' . $item['user_name'] . ' (' . $item['user_id'] . ')</td>
                                    <td>$' . number_format($item['money']) . '</td>';
                                if ($userInfo['id'] == $item['user_id']) {
                                    echo '
                                        <td class="right">
                                            <form method="POST">
                                                <input name="id" type="hidden" value="' . $item['id'] . '">
                                                <button class="btn waves-effect waves-light border-red border-accent-4 z-depth-0" type="submit" value="true" name="trade-remove">Снять с продажи</button>
                                            </form>
                                        </td>
                                    ';
                                }
                                else {
                                    //echo '<td class="right">Покупка временно не доступна</td>';
                                    echo '
                                        <td class="right">
                                            <form method="POST">
                                                <input name="id" type="hidden" value="' . $item['id'] . '">
                                                <button class="btn waves-effect waves-light border-green border-accent-4 z-depth-0" type="submit" value="true" name="trade-buy-money">Купить за ' . number_format($item['ac']) . ' sc</button>
                                            </form>
                                        </td>
                                    ';
                                }
                                echo '
                                        </tr>
                                    ';
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col s12" id="tab2">
                    <div class="card-panel">
                        <table class="highlight responsive-table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th style="width: 24px;">Игрок</th>
                                <th></th>
                                <th>Сумма</th>
                                <th class="right">Действие</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $count = 1;
                            foreach ($acBuy as $item) {
                                echo '
                                        <tr>
                                            <td>' . ($count++) . '</td>
                                            <td><img style="width: 24px;border-radius: 50%;margin-top: 5px;" src="https://a.rsg.sc//n/' . strtolower($item['user_social']) . '"></td>
                                            <td>' . $item['user_name'] . ' (' . $item['user_id'] . ')</td>
                                            <td>' . number_format($item['ac']) . 'sc</td>
                                        ';

                                if ($userInfo['id'] == $item['user_id']) {
                                    echo '
                                            <td class="right">
                                                <form method="POST">
                                                    <input name="id" type="hidden" value="' . $item['id'] . '">
                                                    <button class="btn waves-effect waves-light border-red border-accent-4 z-depth-0" type="submit" value="true" name="trade-remove">Снять с продажи</button>
                                                </form>
                                            </td>';
                                }
                                else {
                                    //echo '<td class="right">Покупка временно не доступна</td>';
                                    echo '
                                        <td class="right">
                                            <form method="POST">
                                                <input name="id" type="hidden" value="' . $item['id'] . '">
                                                <button class="btn waves-effect waves-light border-green border-accent-4 z-depth-0" type="submit" value="true" name="trade-buy-sc">Купить за $' . number_format($item['money']) . '</button>
                                            </form>
                                        </td>';
                                }
                                echo '
                                    </tr>
                                ';
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col s12" id="tab5">
                    <div class="card-panel">
                        <table class="highlight responsive-table">
                            <thead>
                            <tr>
                                <th>Продавец</th>
                                <th>Покупатель</th>
                                <th>Описание</th>
                                <th>Дата</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php

                            //ini_set('display_errors', '1');

                            $logTrade = $qb
                                ->createQueryBuilder('log_coin_trade')
                                ->selectSql()
                                ->where('user_from_id = \'' . $userInfo['id'] . '\'')
                                ->orWhere('user_to_id = \'' . $userInfo['id'] . '\'')
                                ->orderBy('id DESC')
                                ->limit(50)
                                ->executeQuery()
                                ->getResult()
                            ;

                            if ($userInfo['admin_level'] > 5) {
                                $logTrade = $qb
                                    ->createQueryBuilder('log_coin_trade')
                                    ->selectSql()
                                    ->orderBy('id DESC')
                                    ->limit(50)
                                    ->executeQuery()
                                    ->getResult()
                                ;
                            }

                            foreach ($logTrade as $item) {
                                $idFrom = $userInfo['login'];
                                $idTo = $userInfo['login'];
                                $desc = '';

                                if ($item['user_from_id'] != $userInfo['id']) {
                                    $idFrom = $user->getAccountInfo($item['user_from_id'])['login'];
                                    if ($item['type'] == 0)
                                        $desc = 'Покупка ' . number_format($item['coin']) . 'ac за $' . number_format($item['money']);
                                    else
                                        $desc = 'Покупка $' . number_format($item['money']) . ' за ' . number_format($item['coin']) . 'ac';
                                }
                                if ($item['user_to_id'] != $userInfo['id']) {
                                    $idTo = $user->getAccountInfo($item['user_to_id'])['login'];
                                    if ($item['type'] == 0)
                                        $desc = 'Продажа ' . number_format($item['coin']) . 'ac за $' . number_format($item['money']);
                                    else
                                        $desc = 'Продажа $' . number_format($item['money']) . ' за ' . number_format($item['coin']) . 'ac';
                                }
                                /*else {
                                    $idTo = $user->getAccountInfo($item['user_to_id'])['login'];
                                    if ($item['type'] == 0)
                                        $desc = 'Продажа ' . number_format($item['coin']) . 'ac за $' . number_format($item['money']);
                                    else
                                        $desc = 'Продажа $' . number_format($item['money']) . ' за ' . number_format($item['coin']) . 'ac';
                                }*/


                                echo '
                                    <tr>
                                        <td>' . $idFrom . '</td>
                                        <td>' . $idTo . '</td>
                                        <td>' . $desc . '</td>
                                        <td>' . gmdate("d/m, H:i", $item['timestamp'] + (3600 * 3)) . '</td>
                                    </tr>
                                ';
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <br>
        </div>
    </div>
</div>
