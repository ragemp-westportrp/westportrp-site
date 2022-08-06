<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
global $user;
global $server;
global $serverName;
global $page;
global $typeLogUser;
global $fractionId;

@error_reporting ( E_ALL ^ E_DEPRECATED ^ E_NOTICE );
@ini_set ( 'error_reporting', E_ALL ^ E_DEPRECATED ^ E_NOTICE );
@ini_set ( 'display_errors', true );
@ini_set ( 'html_errors', false );
error_reporting(E_ALL);
ini_set("display_errors", 1);


if(isset($_GET['q']))
{
    $_GET['q'] = $server->charsString($_GET['q']);
    $searchInfo = $user->getUserInfo($_GET['q']);

    if(!empty($searchInfo) && $searchInfo['admin_level'] === 0) {

        $bankCard = $searchInfo['bank_card'] == 0 ? 'нет' : $searchInfo['bank_card'];
        $phone = $searchInfo['phone'] == 0 ? 'нет' : $searchInfo['phone'];
        $aLic = $searchInfo['a_lic'] == true ? 'есть' : 'нет';
        $bLic = $searchInfo['b_lic'] == true ? 'есть' : 'нет';
        $cLic = $searchInfo['c_lic'] == true ? 'есть' : 'нет';
        $airLic = $searchInfo['air_lic'] == true ? 'есть' : 'нет';
        $shipLic = $searchInfo['ship_lic'] == true ? 'есть' : 'нет';
        $taxiLic = $searchInfo['taxi_lic'] == true ? 'есть' : 'нет';
        $lawLic = $searchInfo['law_lic'] == true ? 'есть' : 'нет';
        $gunLic = $searchInfo['gun_lic'] == true ? 'есть' : 'нет';
        $medLic = $searchInfo['med_lic'] == true ? 'есть' : 'нет';
        $bizzLic = $searchInfo['biz_lic'] == true ? 'есть' : 'нет';
        $fishLic = $searchInfo['fish_lic'] == true ? 'есть' : 'нет';
        $animalLic = $searchInfo['work_lic'] == true ? 'есть' : 'нет';
        $margLic = $searchInfo['marg_lic'] == true ? 'есть' : 'нет';

        $countCars = 0;
        $carId1 = null;
        $carId2 = null;
        $carId3 = null;
        $carId4 = null;
        $carId5 = null;
        $carId6 = null;
        $carId7 = null;
        $carId8 = null;

        $biz = null;
        $house = null;
        $aprt = null;
        $stock = null;
        $condo = null;

        if ($searchInfo['business_id'] > 0) {
            $biz = $qb->createQueryBuilder('business')->selectSql()->where('id = \'' . $searchInfo['business_id'] . '\'')->executeQuery()->getSingleResult();
        }
        if ($searchInfo['house_id'] > 0) {
            $house = $qb->createQueryBuilder('houses')->selectSql()->where('id = \'' . $searchInfo['house_id'] . '\'')->executeQuery()->getSingleResult();
        }
        if ($searchInfo['apartment_id'] > 0) {
            $aprt = $qb->createQueryBuilder('apartment')->selectSql()->where('id = \'' . $searchInfo['apartment_id'] . '\'')->executeQuery()->getSingleResult();
        }
        if ($searchInfo['stock_id'] > 0) {
            $stock = $qb->createQueryBuilder('stocks')->selectSql()->where('id = \'' . $searchInfo['stock_id'] . '\'')->executeQuery()->getSingleResult();
        }
        if ($searchInfo['condo_id'] > 0) {
            $condo = $qb->createQueryBuilder('condos')->selectSql()->where('id = \'' . $searchInfo['condo_id'] . '\'')->executeQuery()->getSingleResult();
        }

        $cars = $qb->createQueryBuilder('cars')->selectSql()->where('user_id = \'' . $searchInfo['id'] . '\'')->executeQuery()->getResult();
        ?>
        <style>
            .modal-big {
                height: 90%;
                width: 90%;
            }
        </style>
        <div class="container">
            <div class="section">
                <div class="row">
                    <div class="col s12">
                        <form>
                            <div class="card-panel input-field" style=" padding: 0;">
                                <input style="padding-left: 14px; border: none;" id="search" class="search_all" type="search" value="<?php echo $_GET['q'] ?>" placeholder="Поиск" name="q" required="">
                            </div>
                        </form>
                    </div>
                    <div class="col s12">
                        <div>
                            <?php
                            $sex = json_decode($searchInfo['skin'])->SKIN_SEX == 1 ? 'Женский' : 'Мужской';
                            echo '
                                    <div style="display: flex; flex-wrap: wrap; margin-top: 50px;">
                                        <img class="circle" style="width: 150px !important; height: 150px !important; object-fit: cover;" src="https://a.rsg.sc//n/' . strtolower($searchInfo['social']) . '">
                                        <div style="margin-left: 32px">
                                         
                                            <h4 style="margin-top: 0">' . $searchInfo['name'] . '</h4>
                                            <label>Пол: ' . $sex . '</label><br>
                                            <label>Возраст: ' . $searchInfo['age'] . '</label><br>
                                            <label>Супруг(а): ' . $searchInfo['partner'] . '</label><br>
                                            <label>Национальность: ' . $searchInfo['national'] . '</label><br>
                                            <label>Вес: ' . $searchInfo['rp_weight'] . 'кг.</label><br>
                                            <label>Рост: ' . $searchInfo['rp_growth'] . 'см.</label><br>
                                        </div>
                                        <a class="btn blue accent-4 modal-trigger" style="margin: auto;" href="#history">История</a>
                                        <a class="btn blue accent-4 modal-trigger" style="margin: auto;" href="#tickets">Штрафы</a>
                                        <a class="btn blue accent-4" style="margin: auto;" href="/network/news/bio?user=' . $searchInfo['name'] . '">Биография</a>
                                    </div>
                                    <br><hr><br>
                                ';
                            ?>

                        </div>

                        <script>
                            $(document).ready(function(){
                                $('.modal').modal();
                            });
                        </script>

                        <div id="history" class="modal modal-fixed-footer modal-big" style="height: 98%;width: 90%;">
                            <div class="modal-content" style="padding: 0;">
                                <table class="striped">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Описание</th>
                                        <th>Дата</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <?php

                                    $logPlayer = $qb
                                        ->createQueryBuilder('log_player')
                                        ->selectSql()
                                        ->where('user_id = ' . $searchInfo['id'])
                                        ->orderBy('id DESC')
                                        ->executeQuery()
                                        ->getResult()
                                    ;

                                    $count = 1;
                                    foreach ($logPlayer as $item) {
                                        echo '
                                        <tr>
                                            <td>' . ($count++) . '</td>
                                            <td>' . $item['do'] . '</td>
                                            <td>' . gmdate("m-d-Y, H:i", $item['timestamp'] + 3600 * 3) . '</td>
                                        </tr>
                                        ';
                                    }

                                    ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <a class="modal-close waves-effect btn-flat red-text">Закрыть</a>
                            </div>
                        </div>

                        <div id="tickets" class="modal modal-fixed-footer modal-big" style="height: 98%;width: 90%;">
                            <div class="modal-content" style="padding: 0;">
                                <table class="striped">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Штраф</th>
                                        <th>Описание</th>
                                        <th>Статус</th>
                                        <th>Сумма</th>
                                        <th>Дата</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <?php

                                    $logPlayer = $qb
                                        ->createQueryBuilder('tickets')
                                        ->selectSql()
                                        ->where('user_id = ' . $searchInfo['id'])
                                        ->orderBy('is_pay ASC, id DESC')
                                        ->executeQuery()
                                        ->getResult()
                                    ;

                                    $count = 1;
                                    foreach ($logPlayer as $item) {
                                        echo '
                                        <tr>
                                            <td>' . ($count++) . '</td>
                                            <td>' . $item['do'] . '</td>
                                            <td>' . $item['do2'] . '</td>
                                            <td>' . ($item['is_pay'] ? 'Оплачен' : 'Не оплачен') . '</td>
                                            <td>$' . number_format($item['price']) . '</td>
                                            <td>' . gmdate("m-d-Y, H:i", $item['timestamp'] + 3600 * 3) . '</td>
                                        </tr>
                                        ';
                                    }

                                    ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <a class="modal-close waves-effect btn-flat red-text">Закрыть</a>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col s12 l4">
                                <table class="highlight transparent">
                                    <tbody>
                                    <tr>
                                        <td>CardID</td>
                                        <td><?php echo $searchInfo['id']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Имя</td>
                                        <td><?php echo $searchInfo['name']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Возраст</td>
                                        <td><?php echo $searchInfo['age']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Банковская карта</td>
                                        <td><?php echo $bankCard; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Телефон</td>
                                        <td><?php echo $phone; ?></td>
                                    </tr>
                                    <?php

                                    if($searchInfo['wanted_level'] > 0) {
                                        echo '
                                            <tr>
                                                <td>Розыск</td>
                                                <td>' . $searchInfo['wanted_level'] . '</td>
                                            </tr>
                                            <tr>
                                                <td>Причина розыска</td>
                                                <td>' . $searchInfo['wanted_reason'] . '</td>
                                            </tr>
                                        ';
                                    }
                                    if($searchInfo['jail_time'] > 0) {
                                        echo '
                                            <tr>
                                                <td>Заключенный</td>
                                                <td>Отбывает срок</td>
                                            </tr>
                                        ';
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col s12 l4">
                                <table class="highlight transparent">
                                    <tbody>
                                    <tr>
                                        <td>Лицензия A</td>
                                        <td><?php echo $aLic; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Лицензия B</td>
                                        <td><?php echo $bLic; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Лицензия C</td>
                                        <td><?php echo $cLic; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Лицензия пилота</td>
                                        <td><?php echo $airLic; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Лицензия на водный ТС</td>
                                        <td><?php echo $shipLic; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Лицензия таксиста</td>
                                        <td><?php echo $taxiLic; ?></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col s12 l4">
                                <table class="highlight transparent">
                                    <tbody>
                                    <tr>
                                        <td>Лицензия юриста</td>
                                        <td><?php echo $lawLic; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Лицензия на оружие</td>
                                        <td><?php echo $gunLic; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Лицензия на бизнес</td>
                                        <td><?php echo $bizzLic; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Лицензия на рыбалку</td>
                                        <td><?php echo $fishLic; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Мед. страховка</td>
                                        <td><?php echo $medLic; ?></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php
                    if (count($cars) > 0) {

                        echo '
                            <div class="col s12 l6">
                            <h5 style="margin-top: 36px;">Транспорт</h5>
                            <div class="row">
                        ';

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
                                        <a target="_blank" href="/car-info-' . $item['name'] . '"><img style="max-height: 150px; object-fit: cover" src="/client/images/carssm/' . $item['name'] . '_1.jpg"></a>
                                    </div>
                                    <div class="card-content wd-font">
                                        <a class="bw-text" target="_blank" href="/car-info-' . $item['name'] . '">' . $item['name'] . '</a>
                                    </div>
                                </div>
                            </div>
                            ';
                        }
                        echo '</div>';
                    }

                    if (!empty($aprt)) {
                        echo '
                            <div class="col s12 l6">
                            <h5 style="margin-top: 36px;">Апартаменты</h5>
                            <div class="card-panel">
                                <table class="highlight">
                                    <tbody>
                                        <tr>
                                            <td>Номер квартиры</td>
                                            <td>' . $aprt['id'] . '</td>
                                        </tr>
                                        <tr>
                                            <td>Этаж</td>
                                            <td>' . $aprt['floor'] . '</td>
                                        </tr>
                                        <tr>
                                            <td>Цена</td>
                                            <td>$' . number_format($aprt['price']) . '</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            </div>
                        ';
                    }
                    if (!empty($house)) {
                        echo '
                            <div class="col s12 l6">
                            <h5 style="margin-top: 36px;">Дом</h5>
                            <div class="card-panel">
                                <table class="highlight">
                                    <tbody>
                                        <tr>
                                            <td>Адрес</td>
                                            <td>' . $house['address'] . ' #' . $house['id'] . '</td>
                                        </tr>
                                        <tr>
                                            <td>Цена</td>
                                            <td>$' . number_format($house['price']) . '</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            </div>
                        ';
                    }
                    if (!empty($condo)) {
                        echo '
                            <div class="col s12 l6">
                            <h5 style="margin-top: 36px;">Квартира</h5>
                            <div class="card-panel">
                                <table class="highlight">
                                    <tbody>
                                        <tr>
                                            <td>Адрес</td>
                                            <td>' . $condo['address'] . ' #' . $condo['id'] . '</td>
                                        </tr>
                                        <tr>
                                            <td>Цена</td>
                                            <td>$' . number_format($condo['price']) . '</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            </div>
                        ';
                    }
                    if (!empty($biz)) {
                        echo '
                            <div class="col s12 l6">
                                <h5 style="margin-top: 36px;">Бизнес</h5>
                                <div class="card-panel">
                                    <table class="highlight">
                                        <tbody>
                                            <tr>
                                                <td>Название</td>
                                                <td>' . $biz['name'] . '</td>
                                            </tr>
                                            <tr>
                                                <td>Цена</td>
                                                <td>$' . number_format($biz['price']) . '</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        ';
                    }
                    if (!empty($stock)) {
                        echo '
                            <div class="col s12 l6">
                            <h5 style="margin-top: 36px;">Склад</h5>
                            <div class="card-panel">
                                <table class="highlight">
                                    <tbody>
                                        <tr>
                                            <td>Адрес</td>
                                            <td>' . $stock['address'] . ' #' . $stock['id'] . '</td>
                                        </tr>
                                        <tr>
                                            <td>Цена</td>
                                            <td>$' . number_format($stock['price']) . '</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            </div>
                        ';
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
    }
    else {
        echo '
        <div class="container">
            <div class="section">
                <div class="row">
                    <div class="col s12">
                        <h4 class="grey-text text-darken-2">Поиск | Пользователь не найден</h4>
                    </div>
                    <form class="col s12">
                        <div class="card-panel input-field" style=" padding: 0;">
                            <input style="padding-left: 14px; border: none;" id="search" class="search_all" type="search" name="q" required="" placeholder="Введите полное имя или Card ID">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    ';
    }
}
else {
    echo '
    <div class="container">
        <div class="section">
            <div class="row">
                <div class="col s12">
                    <h4 class="grey-text text-darken-2">Поиск</h4>
                </div>
                <form class="col s12">
                    <div class="card-panel input-field" style=" padding: 0;">
                        <input style="padding-left: 14px; border: none;" id="search" class="search_all" type="search" name="q" required="" placeholder="Введите полное имя или Card ID">
                    </div>
                </form>
            </div>
        </div>
    </div>
    ';
}