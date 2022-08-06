<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
global $serverName;
global $user;
global $userInfo;
global $nationals;
global $donateCars;

if (!$user->isLogin()) {
    echo '
        <div>
            <div class="container">
                <div class="section">
                    <div class="row">
                        <div class="col s12 bw-text">
                            <h4 class="wd-font" style="margin: 150px 0px">
                                Авторизуйся через личный кабинет и пополни STATECOIN
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    ';
    return;
}


$datings = $qb->createQueryBuilder('user_dating')->selectSql()->where('user_owner = \'' . $this->accInfo['id'] . '\'')->orWhere('user_id = \'' . $this->accInfo['id'] . '\'')->limit(500)->executeQuery()->getResult();
$donateVeh = $qb->createQueryBuilder('veh_info')->selectSql()->where('price_dc > \'0\'')->orderBy('price_dc ASC')->executeQuery()->getResult();


$onlineStatus = $this->accInfo['is_online'] ? 'green' : 'red';

$img = 'https://i.imgur.com/AGC3ILF.png';

$carImg = [
    'https://i.imgur.com/eyEdtlK.png',
    'https://i.imgur.com/bHQ9RL1.png',
    'https://i.imgur.com/dRphsLE.png',
    'https://i.imgur.com/PkXG17y.png',
    'https://i.imgur.com/tznuO6m.png',
];
?>

<style>
    .roulette {
        width: 50000px;
        height: 128px !important;
    }

    .roulette-modal {
        max-height: 80% !important;
    }

    .casebox {
       cursor: pointer;
    }

    .caseimg {
        float: left;
        width: 120px;
        height: 128px;
        object-fit: cover;
        margin: 0 2px;
    }

    .roulette-list {
        display: flex;
        flex-wrap: wrap;
    }

    .roulette-list .caseimg {
        margin: 2px auto !important;
    }

    .rare0 {
        border: 4px #b6b6b6 solid; /*Обычная*/
    }
    .rare1 {
        border: 4px #b0c3d9 solid; /*Ширпотреб*/
    }
    .rare2 {
        border: 4px #5e98d9 solid; /*Необычная*/
    }
    .rare3 {
        border: 4px #4b69ff solid; /*Редкая*/
    }
    .rare4 {
        border: 4px #8847ff solid; /*Очень редкая*/
    }
    .rare5 {
        border: 4px #d32ce6 solid; /*Невероятно редкая*/
    }
    .rare6 {
        border: 4px #b28a33 solid; /*Элитная*/
    }
    .rare7 {
        border: 4px #ade55c solid; /*Мистическая*/
    }
    .rare8 {
        border: 4px #eb4b4b solid; /*Засекреченная*/
    }
    .rare9 {
        border: 4px #b71c1c solid; /*Легендарная*/
    }

    .casebox-bg {
        width: 100%;
        height: 160px;
        object-fit: cover;
    }

    .casebox-img {
        width: 50%;
        margin-top: -5%;
        width: 160px;
        height: 160px;
        object-fit: contain;
        filter: grayscale(50%);
    }

    .casebox-title {
        position: relative;
        top: -70%;
        line-height: 30px;
    }

    .casebox-content {
        height: 160px;
        width: 100%;
        margin-top: -160px;
    }

    .casebox1 {
        filter: grayscale(100%);
        z-index: -1;
    }
    .casebox2 {
        filter: invert(32%) sepia(70%) saturate(1624%) hue-rotate(215deg) brightness(105%) contrast(104%);
        z-index: -1;
    }
    .casebox3 {
        filter: invert(60%) sepia(83%) saturate(7291%) hue-rotate(247deg) brightness(99%) contrast(105%);
        z-index: -1;
    }
    .casebox6 {
        filter: invert(32%) sepia(55%) saturate(1421%) hue-rotate(328deg) brightness(119%) contrast(85%);
        z-index: -1;
    }
    .casebox5 {
        filter: invert(52%) sepia(74%) saturate(431%) hue-rotate(3deg) brightness(93%) contrast(79%);
        z-index: -1;
    }
    .casebox4 {
        filter: invert(100%) sepia(13%) saturate(5498%) hue-rotate(18deg) brightness(92%) contrast(92%);
        z-index: -1;
    }
</style>

<div style="width: 100%; overflow: hidden; position: absolute; height: 100%; z-index: -1;">
    <img src="<?php echo $img ?>" style="filter: blur(10px); z-index: -1; position: absolute; top: 0; left: -20px; width: 110%; max-height: 450px; object-fit: cover;">
</div>
<div class="container bw-text" style="padding-top: 30px;">
    <div class="section">
        <div class="row">
            <div class="col s12 m8">
                <img class="hide-on-med-and-down" style="border-radius: 50%; border: 5px #000 solid; float: left; margin-right: 30px" src="https://a.rsg.sc//n/<?php echo strtolower($userInfo['social']) ?>">
                <h3 style="margin-bottom: 0px;" class="wd-font white-text"><?php echo $this->accInfo['name'] ?> <span class="<?php echo $onlineStatus ?>-text">•</span></h3>
                <div class="white-text" id="dcbalance" style="z-index: 999">Баланс: <?php echo $userInfo['money_donate'] ?> SC</div>
                <div class="white-text" style="z-index: 999">Баланс: <?php echo $this->accInfo['money_donate'] ?> BP</div>
            </div>
            <div class="col s12 m4">
                <a href="/donate" class="btn waves-effect blue accent-4" style="float: right; margin: 32px 0">Пополнить счет</a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12">
                <ul class="tabs wb z-depth-1">
                    <li class="tab col s2"><a style="font-size: 16px" class="bw-text wd-font" href="#transfer">Обмен</a></li>
                    <li class="tab col s3"><a style="font-size: 16px" class="bw-text wd-font" href="#packs">Наборы</a></li>
                    <li class="tab col s2"><a style="font-size: 16px" class="bw-text wd-font" href="#vip">VIP</a></li>
                    <li class="tab col s3"><a style="font-size: 16px" class="bw-text wd-font" href="#veh">Транспорт</a></li>
                    <li class="tab col s2"><a style="font-size: 16px" class="bw-text wd-font" href="#other">Услуги</a></li>
                    <li class="tab col s2 hide"><a style="font-size: 16px" class="bw-text wd-font" href="#rulette">Рулетка</a></li>
                </ul>
            </div>
        </div>
        <div class="row" id="other">
            <div class="col s12">
                <div class="row">
                    <div class="col s12 l6 bw-text">
                        <div class="card card-h">
                            <div class="card-h-left">
                                <img src="https://i.imgur.com/U3JTAWQ.png">
                            </div>
                            <div class="card-h-right">
                                <h5 class="wd-font bw-text text-accent-4">Прокачать все скиллы</h5>
                                Прокачка работает на все навыки персонажа, кроме удачи и хакерства.
                                <br>
                                <br>
                                <form method="post">
                                    <input name="uid" value="<?php echo $this->accInfo['id'] ?>" type="hidden" class="validate">
                                    <button name="buy-skill" class="btn blue accent-4 waves-effect z-depth-0">Купить за 99 sc</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col s12 l6 bw-text">
                        <div class="card card-h">
                            <div class="card-h-left">
                                <img src="https://i.imgur.com/HvCA2cT.png">
                            </div>
                            <div class="card-h-right">
                                <h5 class="wd-font bw-text text-accent-4">Снять предупреждение</h5>
                                Снимается только 1 предупреждение за 1 платеж. На данный момент у вас <?php echo $this->accInfo['warns'] ?> предуп.
                                <br>
                                <br>
                                <form method="post">
                                    <input name="uid" value="<?php echo $this->accInfo['id'] ?>" type="hidden" class="validate">
                                    <button name="buy-warn" class="btn blue accent-4 waves-effect z-depth-0">Купить за 499 sc</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col s12 l6 bw-text">
                        <div class="card card-h">
                            <div class="card-h-left">
                                <img src="https://i.imgur.com/HvCA2cT.png">
                            </div>
                            <div class="card-h-right">
                                <h5 class="wd-font bw-text text-accent-4">Купить гражданство</h5>
                                Покупка гражданства для вашего персонажа
                                <br>
                                <br>
                                <form method="post">
                                    <input name="uid" value="<?php echo $this->accInfo['id'] ?>" type="hidden" class="validate">
                                    <button name="buy-status" class="btn blue accent-4 waves-effect z-depth-0">Купить за 199 sc</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col s12 l6 bw-text">
                        <div class="card card-h">
                            <div class="card-h-left">
                                <img src="https://i.imgur.com/u12Wumo.png">
                            </div>
                            <div class="card-h-right">
                                <h5 class="wd-font bw-text text-accent-4">Сменить внешность</h5>
                                Внимание, при смене внешности, все ваши навыки будут выставлены на стандартные
                                <br>
                                <br>
                                <form method="post">
                                    <input name="uid" value="<?php echo $this->accInfo['id'] ?>" type="hidden" class="validate">
                                    <button name="buy-custom" class="btn blue accent-4 waves-effect z-depth-0">Купить за 99 sc</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col s12 l6 bw-text">
                        <div class="card card-h">
                            <div class="card-h-left">
                                <img src="https://i.imgur.com/hqS9uNP.png">
                            </div>
                            <div class="card-h-right">
                                <h5 class="wd-font bw-text text-accent-4">Сменить национальность</h5>
                                <form method="post" class="row">
                                    <div class="input-field col s12">
                                        <input name="uid" value="<?php echo $this->accInfo['id'] ?>" type="hidden" class="validate">

                                        <select required name="national">
                                            <?php
                                            $idx = 0;
                                            foreach ($nationals as $national) {
                                                echo '<option value="' . ($idx++) . '">' . $national . '</option>';
                                            }
                                            ?>
                                        </select>
                                        <label>Выбрать национальность</label>

                                        <button name="buy-nat" class="btn blue accent-4 waves-effect z-depth-0">Купить за 49 sc</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col s12 l6 bw-text">
                        <div class="card card-h">
                            <div class="card-h-left">
                                <img src="https://i.imgur.com/qOUBvAw.png">
                            </div>
                            <div class="card-h-right">
                                <h5 class="wd-font bw-text text-accent-4">Сменить ник</h5>
                                <form method="post" class="row">
                                    <div class="input-field col s12">
                                        <input name="uid" value="<?php echo $this->accInfo['id'] ?>" type="hidden" class="validate">
                                        <input id="donateSumNcTrade" placeholder="Имя Фамилия" required name="name" type="text" class="validate">
                                        <label id="moneyNcLabel" for="moneyTransfer">Введите новый ник</label>
                                        <button name="buy-nick" class="btn blue accent-4 waves-effect z-depth-0">Купить за 199 sc</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col s12" style="margin-top: 150px">
                <div class="row">
                    <div class="col s12 l6 bw-text">
                        <div class="card card-h">
                            <div class="card-h-left">
                                <img src="https://i.imgur.com/hqS9uNP.png">
                            </div>
                            <div class="card-h-right">
                                <h5 class="wd-font bw-text text-accent-4">Сменить номер карты</h5>
                                <form method="post" class="row">
                                    <div class="input-field col s12">
                                        <input name="uid" value="<?php echo $this->accInfo['id'] ?>" type="hidden" class="validate">
                                        <input id="donateSumNcTrade" placeholder="Введите 12 цифр" required name="new-card" type="number" class="validate">
                                        <label id="moneyNcLabel" for="moneyTransfer">Введите новый номер</label>
                                        <button name="buy-card" class="btn blue accent-4 waves-effect z-depth-0">Купить за 199 sc</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col s12 l6 bw-text">
                        <div class="card card-h">
                            <div class="card-h-left">
                                <img src="https://i.imgur.com/qOUBvAw.png">
                            </div>
                            <div class="card-h-right">
                                <h5 class="wd-font bw-text text-accent-4">Сменить номер телефона</h5>
                                <form method="post" class="row">
                                    <div class="input-field col s12">
                                        <input name="uid" value="<?php echo $this->accInfo['id'] ?>" type="hidden" class="validate">
                                        <input id="donateSumNcTrade" placeholder="Введите 10 цифр" required name="new-phone" type="number" class="validate">
                                        <label id="moneyNcLabel" for="moneyTransfer">Введите новый номер</label>
                                        <button name="buy-phone" class="btn blue accent-4 waves-effect z-depth-0">Купить за 499 sc</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col s12" style="margin-top: 150px">
                <div class="row">
                    <div class="col s12 m6 bw-text">
                        <div class="card card-h">
                            <div class="card-h-left">
                                <img style="object-position: 55%;" src="https://i.imgur.com/HvCA2cT.png">
                            </div>
                            <div class="card-h-right">
                                <h5 class="wd-font bw-text text-accent-4">Управление знакомствами</h5>
                                <form method="post" class="row">
                                    <input name="uid" value="<?php echo $this->accInfo['id'] ?>" type="hidden" class="validate">
                                    <div class="input-field col s12">
                                        <select required name="removes">
                                            <?php
                                            $skip = false;
                                            $idx = 0;
                                            foreach ($datings as $item) {
                                                $idx++;
                                                if ($skip) {
                                                    $skip = false;
                                                }
                                                else {
                                                    $skip = true;
                                                    echo ' <option value="' . $item['id'] . '|' . $datings[$idx]['id'] . '">' . $item['user_name'] . ' / ' . $datings[$idx]['user_name'] . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                        <label>Выберите пункт который хотите удалить</label>
                                    </div>
                                    <div class="col s12">
                                        <button name="remove-dating" class="btn blue accent-4 waves-effect z-depth-0">Купить за 3 sc</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col s12 l6 bw-text">
                        <div class="card card-h">
                            <div class="card-h-left">
                                <img src="https://i.imgur.com/gK3wQo2.jpg">
                            </div>
                            <div class="card-h-right">
                                <h5 class="wd-font bw-text text-accent-4">Управление знакомствами</h5>
                                Все знакомства будут сброшены, те люди с которыми вы знакомились, они также забудут вас.
                                <br>
                                <br>
                                <form method="post">
                                    <input name="uid" value="<?php echo $this->accInfo['id'] ?>" type="hidden" class="validate">
                                    <button name="buy-dating" class="btn blue accent-4 waves-effect z-depth-0">Купить за 99 sc</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" id="vip">
            <div class="col s12">
                <div class="row">
                    <div class="col s12 bw-text">
                        <div class="card card-h">
                            <div class="card-h-left">
                                <img style="object-position: 55%;" src="https://i.imgur.com/U3JTAWQ.png">
                            </div>
                            <div class="card-h-right">
                                <h5 class="wd-font bw-text text-accent-4">VIP</h5>
                                <span class="grey-text">•</span> На 10% больше денег с зарплат<br>
                                <span class="grey-text">•</span> Скидка 50% на эвакуацию транспорта<br>
                                <span class="grey-text">•</span> Оплата налогов через сайт в 1 клик<br>
                                <span class="grey-text">•</span> На 5 килограмм больше вмещает инвентарь<br>
                                <span class="grey-text">•</span> Ускоренная прокачка скиллов<br>
                                <span class="grey-text">•</span> Возможность стоять в АФК 30 минут<br><br>
                                <div class="row">
                                    <div class="col s12 m6">
                                        <form method="post" class="row">
                                            <input name="uid" value="<?php echo $this->accInfo['id'] ?>" type="hidden" class="validate">
                                            <div class="input-field col s12">
                                                <select required name="days">
                                                    <option value="7">На 7 дней (160sc)</option>
                                                    <option value="14">На 14 дней (300sc)</option>
                                                    <option value="30">На 30 дней (600sc)</option>
                                                    <option value="60">На 60 дней (1100sc)</option>
                                                    <option value="90">На 90 дней (1600sc)</option>
                                                </select>
                                                <label>Купить VIP</label>
                                            </div>
                                            <div class="col s12">
                                                <button name="buy-vip-hard" class="btn blue accent-4 waves-effect z-depth-0">Купить за StateCoins</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col s12 m6">
                                        <form method="post" class="row">
                                            <input name="uid" value="<?php echo $this->accInfo['id'] ?>" type="hidden" class="validate">
                                            <div class="input-field col s12">
                                                <select required name="days">
                                                    <option value="7">На 7 дней (500bp)</option>
                                                    <option value="14">На 14 дней (1000bp)</option>
                                                    <option value="30">На 30 дней (2000bp)</option>
                                                    <option value="60">На 60 дней (3000bp)</option>
                                                    <option value="90">На 90 дней (4000bp)</option>
                                                </select>
                                                <label>Купить VIP</label>
                                            </div>
                                            <div class="col s12">
                                                <button name="buy-vip-hard-2" class="btn blue accent-4 waves-effect z-depth-0">Купить за BonusPoint</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" id="veh">
            <div class="col s12">
                <div class="row">
                    <?php

                    $free = 0;
                    $all = 0;
                    for ($i = 6; $i < 11; $i++) {
                        if (!$this->accInfo['car_id' . $i . '_free'])
                            $free = $i;
                        else
                            $all++;
                    }
                    ?>

                    <div class="col s12 bw-text">
                        <div class="card card-h">
                            <div class="card-h-left" style="width: 25%;">
                                <img src="<?php echo $carImg[0] ?>">
                            </div>
                            <div class="card-h-right" style="width: 75%;">
                                <div class="row">
                                    <div class="col s12 m6">
                                        <h5 class="wd-font bw-text text-accent-4">Слот под транспорт</h5>
                                        Разблокируйте дополнительный слот, под любое транспортное средство навсегда, всего доступно для разблокировки 5 слотов
                                    </div>
                                    <div class="col s12 m6">
                                        <div class="grey-text hide"><span>Куплено <?php echo $all; ?>/5</span></div>
                                        <div class="row center" style="margin-bottom: 0; margin-top: 30px">
                                            <?php
                                            for ($i = 10; $i > 5; $i--) {
                                                $s = 's2';
                                                if ($i == 7 || $i == 9)
                                                    $s = 's3';
                                                if (!$this->accInfo['car_id' . $i . '_free'])
                                                    echo '<div class="col ' . $s . '"><i class="material-icons grey-text">directions_car</i></div>';
                                                else
                                                    echo '<div class="col ' . $s . '"><i class="material-icons green-text">directions_car</i></div>';
                                            }
                                            ?>
                                        </div>
                                        <div class="progress blue accent-2">
                                            <div class="determinate blue accent-4" style="width: <?php echo $all * 20; ?>%"></div>
                                        </div>
                                        <div style="display:flex;">
                                            <form method="post" style="margin: auto">
                                                <input name="uid" value="<?php echo $this->accInfo['id'] ?>" type="hidden" class="validate">
                                                <input name="slot" value="<?php echo $free ?>" type="hidden" class="validate">
                                                <button name="buy-slot" <?php echo $free == 0 ? 'disabled' : '' ?> class="btn blue accent-4 waves-effect z-depth-0"><?php echo $free == 0 ? 'Куплено' : 'Купить за 499 sc' ?></button>
                                            </form>
                                            <form method="post" style="margin: auto">
                                                <input name="uid" value="<?php echo $this->accInfo['id'] ?>" type="hidden" class="validate">
                                                <input name="slot" value="<?php echo $free ?>" type="hidden" class="validate">
                                                <button name="buy-slot-d" <?php echo $free == 0 ? 'disabled' : '' ?> class="btn blue accent-4 waves-effect z-depth-0"><?php echo $free == 0 ? 'Куплено' : 'Купить за $3 000 000' ?></button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col s12" style="margin-top: 70px">
                <div class="row">
                    <div class="col s12 center"><h3 class="wd-font bw-text">Уникальный транспорт</h3></div>
                    <?php
                    foreach ($donateVeh as $item) {
                        $stock = 'Отсутсвтует';
                        if ($item['stock'] > 0)
                            $stock = number_format($item['stock']) . 'см³';
                        echo '
                            <div class="col s12 l6 bw-text">
                                <div class="card card-h">
                                    <div class="card-h-left" style="width: 70%">
                                        <a target="_blank" href="/car-info-' . $item['display_name'] . '"><img src="/client/images/carsv/640/' . strtolower($item['display_name']) . '.jpg"></a>
                                    </div>
                                    <div class="card-h-right">
                                        <h5 class="wd-font bw-text text-accent-4">' . $item['m_name'] . ' ' . $item['n_name'] . '</h5>
                                        <div class="row">
                                            <div class="input-field col s12">
                                                <div><span class="grey-text">Скорость:</span> ~' . $item['sm'] . 'km/h</div>
                                                <div><span class="grey-text">Гос. стоимость:</span> $' . number_format($item['price']) . '</div>
                                                <div><span class="grey-text">Багажник:</span> ' . $stock . '</div>
                                                <div><span class="grey-text">Налог:</span> <span class="green-text">Отсуствует</span></div>
                                                <div class="hide"><span class="grey-text">Скидка:</span> <span class="red-text">50% до 11.01.21</span></div>
                                            </div>
                                            
                                            <div class="col s12">
                                                <div class="grey-text center hide">Купить</div>
                                                <div style="display: flex; width: 100%">
                                                    <form method="post"  style="width: 50%;">
                                                        <input name="uid" value="' . $this->accInfo['id'] . '" type="hidden" class="validate">
                                                        <input name="vid" value="' . $item['id'] . '" type="hidden" class="validate">
                                                       
                                                        <button style="width: 100%; border-radius: 8px 0 0 8px" name="buy-car" class="btn border-amber waves-effect z-depth-0">' . number_format($item['price_dc']) . ' sc</button>
                                                    </form>
                                                   
                                                    <form method="post"  style="width: 50%;">
                                                        <input name="uid" value="' . $this->accInfo['id'] . '" type="hidden" class="validate">
                                                        <input name="vid" value="' . $item['id'] . '" type="hidden" class="validate">
                                                       
                                                        <button style="width: 100%; border-radius: 0 8px 8px 0px; border-left: none !important;" name="buy-car-2" class="btn border-grey waves-effect z-depth-0">' . number_format($item['price_dc'] * 3) . ' BP</button>
                                                    </form> 
                                                </div>
                                               
                                            </div>
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ';
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="row" id="transfer">
            <div class="col s12 l6 bw-text">
                <div class="card card-h">
                    <div class="card-h-left">
                        <img src="https://i.imgur.com/TBUardH.jpg">
                    </div>
                    <div class="card-h-right">
                        <h5 class="wd-font bw-text text-accent-4">Обменять STATECOIN на валюту</h5>
                        <form method="post" class="row">
                            <div class="input-field col s12">
                                <input name="uid" value="<?php echo $this->accInfo['id'] ?>" type="hidden" class="validate">
                                <input id="donateSumTrade" placeholder="1sc = $250" required onkeyup="$.updateDonateLabel()" name="sum" type="number" class="validate">
                                <label id="moneyDcLabel" for="moneyTransfer">Введите сумму (1sc = $250)</label>
                                <button name="buy-dc" class="btn blue accent-4 waves-effect z-depth-0">Обменять</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col s12 l6 bw-text">
                <div class="card card-h">
                    <div class="card-h-left">
                        <img src="https://i.imgur.com/vFXjARI.png">
                    </div>
                    <div class="card-h-right">
                        <h5 class="wd-font bw-text text-accent-4">Обменять BONUS POINT на валюту</h5>
                        <form method="post" class="row">
                            <div class="input-field col s12">
                                <input name="uid" value="<?php echo $this->accInfo['id'] ?>" type="hidden" class="validate">
                                <input id="donateSumNcTrade" placeholder="1bp = $100" required onkeyup="$.updateDonateNcLabel()" name="sum" type="number" class="validate">
                                <label id="moneyNcLabel" for="moneyTransfer">Введите сумму (1bp = $100)</label>
                                <button name="buy-nc" class="btn blue accent-4 waves-effect z-depth-0">Обменять</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" id="packs">
            <div class="col s12 bw-text <?php echo $this->accInfo['donate_pack1'] > 0 ? 'hide' : '' ?>">
                <div class="card card-h">
                    <div style="width: 55%" class="card-h-left">
                        <img id="changeLite" src="/client/images/carsv/640/premier.jpg">
                    </div>
                    <div class="card-h-right" style="margin-left: 24px">
                        <h4 class="wd-font bw-text text-accent-4">Стартовый набор</h4>
                        <form method="post" class="row">
                            <div class="col s12 m7">
                                <span class="grey-text">•</span> Автомобиль на выбор
                                <br><span class="grey-text">•</span> $75.000
                                <br><span class="grey-text">•</span> 7 дней VIP
                                <br><span class="grey-text">•</span> 1 доп. уровень рабочего стажа
                                <br><span class="grey-text">•</span> A, B и C лицензии на 24 месяца
                                <br><span class="grey-text">•</span> Случайная редкая маска
                                <br>
                                <br>
                            </div>
                            <div class="col s12 m5">
                                <div class="row" style="margin-bottom: 0">
                                    <div class="col s12">
                                        <p style="margin-top: 0">
                                            <label>
                                                <input data-icon="/client/images/carsv/640/premier.jpg" checked data-type="lite" name="group1" value="1" type="radio" id="lite1"  />
                                                <span for="lite1">Declasse Premier</span>
                                            </label>
                                        </p>
                                        <p>
                                            <label>
                                                <input data-icon="/client/images/carsv/640/enduro.jpg" data-type="lite" name="group1" value="2" type="radio" id="lite2" />
                                                <span for="lite2">Dinka Enduro</span>
                                            </label>
                                        </p>
                                        <p>
                                            <label>
                                                <input data-icon="/client/images/carsv/640/virgo3.jpg" data-type="lite" name="group1" value="3" type="radio" id="lite3" />
                                                <span for="lite3">Dundreary Virgo Classic</span>
                                            </label>
                                        </p>
                                        <p>
                                            <label>
                                                <input data-icon="/client/images/carsv/640/issi3.jpg" data-type="lite" name="group1" value="4" type="radio" id="lite4" />
                                                <span for="lite4">Weeny Issi Classic</span>
                                            </label>
                                        </p>
                                    </div>
                                    <input name="uid" value="<?php echo $this->accInfo['id'] ?>" type="hidden" class="validate">
                                </div>
                            </div>
                            <div class="col s12">
                                <button name="dc-pack-oracle" <?php echo $this->accInfo['donate_pack1'] > 0 ? 'disabled' : '' ?> class="btn blue accent-4 waves-effect z-depth-0 tooltipped" data-position="bottom" data-delay="50" data-tooltip="Набор можно купить только 1 раз"><?php echo $this->accInfo['donate_pack1'] > 0 ? 'Куплено' : 'Купить за 299 sc' ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col s12 bw-text <?php echo $this->accInfo['donate_pack2'] > 0 ? 'hide' : '' ?>">
                <div class="card card-h">
                    <div style="width: 55%" class="card-h-left">
                        <img id="changeMedium" src="/client/images/carsv/640/cheburek.jpg">
                    </div>
                    <div class="card-h-right" style="margin-left: 24px">
                        <h4 class="wd-font bw-text text-accent-4">Полный набор</h4>
                        <form method="post" class="row">
                            <div class="col s12 m7">
                                <span class="grey-text">•</span> Автомобиль на выбор
                                <br><span class="grey-text">•</span> $250.000
                                <br><span class="grey-text">•</span> 14 дней VIP
                                <br><span class="grey-text">•</span> 2 доп. уровня рабочего стажа
                                <br><span class="grey-text">•</span> Прокачать все характеристики персонажа
                                <br><span class="grey-text">•</span> Случайная невероятно редкая маска
                                <br>
                                <br>
                            </div>
                            <div class="col s12 m5">
                                <div class="row" style="margin-bottom: 0">
                                    <div class="col s12">
                                        <p style="margin-top: 0">
                                            <label>
                                                <input data-icon="/client/images/carsv/640/cheburek.jpg" checked data-type="medium" name="group1" value="1" type="radio" id="medium4" />
                                                <span for="medium4">RUNE Cheburek</span>
                                            </label>
                                        </p>
                                        <p>
                                            <label>
                                                <input data-icon="/client/images/carsv/640/knjo.jpg" data-type="medium" name="group1" value="2" type="radio" id="medium1"  />
                                                <span for="medium1">Dinka Blista Kanjo</span>
                                            </label>
                                        </p>
                                        <p>
                                            <label>
                                                <input data-icon="/client/images/carsv/640/granger.jpg" data-type="medium" name="group1" value="3" type="radio" id="medium2" />
                                                <span for="medium2">Declasse Granger</span>
                                            </label>
                                        </p>
                                        <p>
                                            <label>
                                                <input data-icon="/client/images/carsv/640/sultan2.jpg" data-type="medium" name="group1" value="4" type="radio" id="medium3" />
                                                <span for="medium3">Karin Sultan Classic</span>
                                            </label>
                                        </p>
                                    </div>
                                    <input name="uid" value="<?php echo $this->accInfo['id'] ?>" type="hidden" class="validate">
                                </div>
                            </div>
                            <div class="col s12">
                                <button name="dc-pack-sultan" <?php echo $this->accInfo['donate_pack2'] > 0 ? 'disabled' : '' ?> class="btn blue accent-4 waves-effect z-depth-0 tooltipped" data-position="bottom" data-delay="50" data-tooltip="Набор можно купить только 1 раз"><?php echo $this->accInfo['donate_pack2'] > 0 ? 'Куплено' : 'Купить за 999 sc' ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col s12 bw-text <?php echo $this->accInfo['donate_pack3'] > 0 ? 'hide' : '' ?>">
                <div class="card card-h">
                    <div style="width: 55%" class="card-h-left">
                        <img id="changeHard" src="/client/images/carsv/640/dubsta.jpg">
                    </div>
                    <div class="card-h-right" style="margin-left: 24px">
                        <h4 class="wd-font bw-text text-accent-4">Коллекционный набор</h4>
                        <form method="post" class="row">
                            <div class="col s12 m7">
                                <span class="grey-text">•</span> Автомобиль на выбор
                                <br><span class="grey-text">•</span> $1.000.000
                                <br><span class="grey-text">•</span> 30 дней VIP
                                <br><span class="grey-text">•</span> 3 доп. уровня рабочего стажа
                                <br><span class="grey-text">•</span> 5 дополнительных слотов под транспорт
                                <br><span class="grey-text">•</span> Случайная мистическая маска
                                <br>
                                <br>
                            </div>
                            <div class="col s12 m5">
                                <div class="row" style="margin-bottom: 0">
                                    <div class="col s12">
                                        <p style="margin-top: 0">
                                            <label>
                                                <input data-icon="/client/images/carsv/640/dubsta.jpg" checked data-type="hard" name="group1" value="1" type="radio" id="hard3" />
                                                <span for="hard3">Benefactor Dubsta</span>
                                            </label>
                                        </p>
                                        <p>
                                            <label>
                                                <input data-icon="/client/images/carsv/640/drafter.jpg" data-type="hard" name="group1" value="2" type="radio" id="hard1"  />
                                                <span for="hard1">Obey 8F Drafter</span>
                                            </label>
                                        </p>
                                        <p>
                                            <label>
                                                <input data-icon="/client/images/carsv/640/superd.jpg" data-type="hard" name="group1" value="3" type="radio" id="hard2" />
                                                <span for="hard2">Enus Super Diamond</span>
                                            </label>
                                        </p>
                                        <p>
                                            <label>
                                                <input data-icon="/client/images/carsv/640/akuma.jpg" data-type="hard" name="group1" value="4" type="radio" id="hard4" />
                                                <span for="hard4">Dinka Akuma</span>
                                            </label>
                                        </p>
                                    </div>
                                    <input name="uid" value="<?php echo $this->accInfo['id'] ?>" type="hidden" class="validate">
                                </div>
                            </div>
                            <div class="col s12">
                                <button name="dc-pack-seven" <?php echo $this->accInfo['donate_pack3'] > 0 ? 'disabled' : '' ?> class="btn blue accent-4 waves-effect z-depth-0 tooltipped" data-position="bottom" data-delay="50" data-tooltip="Набор можно купить только 1 раз"><?php echo $this->accInfo['donate_pack3'] > 0 ? 'Куплено' : 'Купить за 4999 sc' ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col s12" style="margin-top: 70px">
                <div class="row">
                    <div class="col s12"><h3 class="wd-font bw-text center">Малые наборы</h3></div>
                    <div class="col s12 l4 bw-text">
                        <div class="card card-h">
                            <div class="card-h-left">
                                <img src="https://i.imgur.com/WCSEoWP.jpg">
                            </div>
                            <div class="card-h-right">
                                <h5 class="wd-font bw-text text-accent-4">Набор I</h5>
                                <span class="grey-text">•</span> $25.000
                                <br><span class="grey-text">•</span> 1 день VIP
                                <br><span class="grey-text">•</span> Случайная маска
                                <br>
                                <br>
                                <form method="post">
                                    <input name="uid" value="<?php echo $this->accInfo['id'] ?>" type="hidden" class="validate">
                                    <button name="dc-pack-1" class="btn blue accent-4 waves-effect z-depth-0">Купить за 99 sc</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col s12 l4 bw-text">
                        <div class="card card-h">
                            <div class="card-h-left">
                                <img src="https://i.imgur.com/zgjaFSH.jpg">
                            </div>
                            <div class="card-h-right">
                                <h5 class="wd-font bw-text text-accent-4">Набор II</h5>
                                <span class="grey-text">•</span> $50.000
                                <br><span class="grey-text">•</span> 3 дня VIP
                                <br><span class="grey-text">•</span> Случайная маска
                                <br>
                                <br>
                                <form method="post">
                                    <input name="uid" value="<?php echo $this->accInfo['id'] ?>" type="hidden" class="validate">
                                    <button name="dc-pack-2" class="btn blue accent-4 waves-effect z-depth-0">Купить за 199 sc</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col s12 l4 bw-text">
                        <div class="card card-h">
                            <div class="card-h-left">
                                <img src="https://i.imgur.com/mDjBMj8.jpg">
                            </div>
                            <div class="card-h-right">
                                <h5 class="wd-font bw-text text-accent-4">Набор III</h5>
                                <span class="grey-text">•</span> $125.000
                                <br><span class="grey-text">•</span> 7 дней VIP
                                <br><span class="grey-text">•</span> Случайная маска
                                <br>
                                <br>
                                <form method="post">
                                    <input name="uid" value="<?php echo $this->accInfo['id'] ?>" type="hidden" class="validate">
                                    <button name="dc-pack-3" class="btn blue accent-4 waves-effect z-depth-0">Купить за 499 sc</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col s12" style="margin-top: 70px">
                <div class="row">
                    <div class="col s12"><h3 class="wd-font bw-text center">Большие наборы</h3></div>
                    <div class="col s12 l4 bw-text">
                        <div class="card card-h">
                            <div class="card-h-left">
                                <img src="https://i.imgur.com/ay3U2VX.jpg">
                            </div>
                            <div class="card-h-right">
                                <h5 class="wd-font bw-text text-accent-4">Набор IV</h5>
                                <span class="grey-text">•</span> $250.000
                                <br><span class="grey-text">•</span> 14 дней VIP
                                <br><span class="grey-text">•</span> Случайная редкая маска
                                <br>
                                <br>
                                <form method="post">
                                    <input name="uid" value="<?php echo $this->accInfo['id'] ?>" type="hidden" class="validate">
                                    <button name="dc-pack-4" class="btn blue accent-4 waves-effect z-depth-0">Купить за 999 sc</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col s12 l4 bw-text">
                        <div class="card card-h">
                            <div class="card-h-left">
                                <img src="https://i.imgur.com/MrpANoO.jpg">
                            </div>
                            <div class="card-h-right">
                                <h5 class="wd-font bw-text text-accent-4">Набор V</h5>
                                <span class="grey-text">•</span> $500.000
                                <br><span class="grey-text">•</span> 30 дней VIP
                                <br><span class="grey-text">•</span> Случайная редкая маска
                                <br>
                                <br>
                                <form method="post">
                                    <input name="uid" value="<?php echo $this->accInfo['id'] ?>" type="hidden" class="validate">
                                    <button name="dc-pack-5" class="btn blue accent-4 waves-effect z-depth-0">Купить за 1999 sc</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col s12 l4 bw-text">
                        <div class="card card-h">
                            <div class="card-h-left">
                                <img src="https://i.imgur.com/Sh8ezxn.jpg">
                            </div>
                            <div class="card-h-right">
                                <h5 class="wd-font bw-text text-accent-4">Набор VI</h5>
                                <span class="grey-text">•</span> $1.500.000
                                <br><span class="grey-text">•</span> 60 дней VIP
                                <br><span class="grey-text">•</span> Случайная редкая маска
                                <br>
                                <br>
                                <form method="post">
                                    <input name="uid" value="<?php echo $this->accInfo['id'] ?>" type="hidden" class="validate">
                                    <button name="dc-pack-6" class="btn blue accent-4 waves-effect z-depth-0">Купить за 4999 sc</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col s12" style="margin-top: 70px">
                <div class="row">
                    <div class="col s12"><h3 class="wd-font bw-text center">Элитные наборы</h3></div>
                    <div class="col s12 l4 bw-text">
                        <div class="card card-h">
                            <div class="card-h-left">
                                <img src="https://i.imgur.com/ZZnedQI.jpg">
                            </div>
                            <div class="card-h-right">
                                <h5 class="wd-font bw-text text-accent-4">Набор VII</h5>
                                <span class="grey-text">•</span> $3.000.000
                                <br><span class="grey-text">•</span> 90 дней VIP
                                <br><span class="grey-text">•</span> Случайная элитная маска
                                <br><span class="grey-text">•</span> Nissan Skyline
                                <br>
                                <br>
                                <form method="post">
                                    <input name="uid" value="<?php echo $this->accInfo['id'] ?>" type="hidden" class="validate">
                                    <button name="dc-pack-7" class="btn blue accent-4 waves-effect z-depth-0">Купить за 9999 sc</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col s12 l4 bw-text">
                        <div class="card card-h">
                            <div class="card-h-left">
                                <img src="https://i.imgur.com/jeOCZVb.jpg">
                            </div>
                            <div class="card-h-right">
                                <h5 class="wd-font bw-text text-accent-4">Набор VIII</h5>
                                <span class="grey-text">•</span> $5.000.000
                                <br><span class="grey-text">•</span> 120 дней VIP
                                <br><span class="grey-text">•</span> Случайная элитная маска
                                <br><span class="grey-text">•</span> BMW M5
                                <br>
                                <br>
                                <form method="post">
                                    <input name="uid" value="<?php echo $this->accInfo['id'] ?>" type="hidden" class="validate">
                                    <button name="dc-pack-8" class="btn blue accent-4 waves-effect z-depth-0">Купить за 14999 sc</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col s12 l4 bw-text">
                        <div class="card card-h">
                            <div class="card-h-left">
                                <img src="https://i.imgur.com/posVhst.jpg">
                            </div>
                            <div class="card-h-right">
                                <h5 class="wd-font bw-text text-accent-4">Набор IX</h5>
                                <span class="grey-text">•</span> $11.000.000
                                <br><span class="grey-text">•</span> 150 дней VIP
                                <br><span class="grey-text">•</span> Случайная элитная маска
                                <br><span class="grey-text">•</span> Bentley Bentayga
                                <br>
                                <br>
                                <form method="post">
                                    <input name="uid" value="<?php echo $this->accInfo['id'] ?>" type="hidden" class="validate">
                                    <button name="dc-pack-9" class="btn blue accent-4 waves-effect z-depth-0">Купить за 29999 sc</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row hide" id="rulette">

            <div class="col s12" id="caseList">
                <div class="row">
                    <div class="col s12 center"><a href="#modalInventory" class="btn blue accent-4 inventory modal-trigger">Открыть инвентарь</a></div>
                    <div class="col s12 center" style="margin: 35px 0;"><h3 class="wd-font white-text">Доступные кейсы</h3></div>
                    <?php
                        //
                        $caseList = $qb->createQueryBuilder('donate_case_list')->selectSql()->executeQuery()->getResult();
                        foreach ($caseList as $item) {
                            continue;
                            $price = $item['price'];
                            if (isset($userInfo['case' . $item['id'] . '_count']) && $userInfo['case' . $item['id'] . '_count'] > 0)
                                $price = 0;
                            echo '
                                <div class="col s12 l4 bw-text casebox modal-trigger" data-id="' . $item['id'] . '" data-price="' . $price . '" href="#modal1">
                                    <img class="casebox' . $item['color'] . ' casebox-bg" src="https://i.imgur.com/CM4Boj4.png">
                                    <div class="center casebox-content">
                                        <img class="casebox-img" src="' . $item['img'] . '">
                                        <h4 class="casebox-title wd-font white-text">' . $item['name'] . '<br><span style="font-size: 22px">' . number_format($price) . ' sc</span></h4>
                                    </div>
                                </div>
                            ';
                        }
                    ?>
                </div>
                <div class="row hide">
                    <div class="col s12 center" style="margin: 35px 0;"><h3 class="wd-font white-text">Доступные кейсы</h3></div>
                    <div class="col s12 l4"></div>
                    <div class="col s12 l4 bw-text casebox modal-trigger" data-id="0" data-price="999" href="#modal1">
                        <img class="casebox-bg" src="https://i.imgur.com/CM4Boj4.png">
                        <div class="center casebox-content">
                            <img class="casebox-img" style="margin-top: 5%" src="https://cdn.discordapp.com/attachments/304629496039079957/723059869367664660/90.png">
                            <h4 class="casebox-title wd-font white-text">Транспорт<br><span style="font-size: 22px">999 sc</span></h4>
                        </div>
                    </div>
                    <div class="col s12 l4"></div>
                </div>

                <div class="row hide">
                    <div class="col s12 l4 bw-text casebox modal-trigger" data-id="1" data-price="49" href="#modal1">
                        <img class="casebox1 casebox-bg" src="https://i.imgur.com/CM4Boj4.png">
                        <div class="center casebox-content">
                            <img class="casebox-img" src="https://i.imgur.com/h4iLGuT.png">
                            <h4 class="casebox-title wd-font white-text">Все маски<br><span style="font-size: 22px">49 sc</span></h4>
                        </div>
                    </div>
                    <div class="col s12 l4 bw-text casebox modal-trigger" data-id="2" data-price="99" href="#modal1">
                        <img class="casebox2 casebox-bg" src="https://i.imgur.com/CM4Boj4.png">
                        <div class="center casebox-content">
                            <img class="casebox-img" src="https://i.imgur.com/h4iLGuT.png">
                            <h4 class="casebox-title wd-font white-text">Редкие маски<br><span style="font-size: 22px">99 sc</span></h4>
                        </div>
                    </div>
                    <div class="col s12 l4 bw-text casebox modal-trigger" data-id="3" data-price="249" href="#modal1">
                        <img class="casebox3 casebox-bg" src="https://i.imgur.com/CM4Boj4.png">
                        <div class="center casebox-content">
                            <img class="casebox-img" src="https://i.imgur.com/h4iLGuT.png">
                            <h4 class="casebox-title wd-font white-text">Элитные маски<br><span style="font-size: 22px">249 sc</span></h4>
                        </div>
                    </div>
                </div>

                <div class="row hide">
                    <div class="col s12 l4 bw-text casebox modal-trigger" data-id="4" data-price="99" href="#modal1">
                        <img class="casebox4 casebox-bg" src="https://i.imgur.com/CM4Boj4.png">
                        <div class="center casebox-content">
                            <img class="casebox-img" src="https://i.imgur.com/h4iLGuT.png">
                            <h4 class="casebox-title wd-font white-text">Невероятный<br><span style="font-size: 22px">99 sc</span></h4>
                        </div>
                    </div>
                    <div class="col s12 l4 bw-text casebox modal-trigger" data-id="5" data-price="249" href="#modal1">
                        <img class="casebox5 casebox-bg" src="https://i.imgur.com/CM4Boj4.png">
                        <div class="center casebox-content">
                            <img class="casebox-img" src="https://i.imgur.com/h4iLGuT.png">
                            <h4 class="casebox-title wd-font white-text">Секретный<br><span style="font-size: 22px">249 sc</span></h4>
                        </div>
                    </div>
                    <div class="col s12 l4 bw-text casebox modal-trigger" data-id="6" data-price="499" href="#modal1">
                        <img class="casebox6 casebox-bg" src="https://i.imgur.com/CM4Boj4.png">
                        <div class="center casebox-content">
                            <img class="casebox-img" src="https://i.imgur.com/h4iLGuT.png">
                            <h4 class="casebox-title wd-font white-text">Легендарный<br><span style="font-size: 22px">499 sc</span></h4>
                        </div>
                    </div>
                </div>

                <script>
                    $(document).ready(function() {
                        $("#emperor").on('change', function() {
                            $('#changeLite').attr('src', $(this).context[$(this).context.selectedIndex].getAttribute('data-icon'));
                        });

                        $('input[type="radio"]').on('change', function() {
                            if ($(this).context.getAttribute('data-type') === 'lite')
                                $('#changeLite').attr('src', $(this).context.getAttribute('data-icon'));
                            if ($(this).context.getAttribute('data-type') === 'medium')
                                $('#changeMedium').attr('src', $(this).context.getAttribute('data-icon'));
                            if ($(this).context.getAttribute('data-type') === 'hard')
                                $('#changeHard').attr('src', $(this).context.getAttribute('data-icon'));
                        });

                        $('.tabs .tab a').on('click', function() {
                            window.location.href = $(this).context.getAttribute('href');
                        });

                        let whatIsIt = 0;
                        let rouletter = $('.roulette');

                        $.getRandomInt = function (min, max) {
                            return Math.floor(Math.random() * (max - min)) + min;
                        };

                        $('.start').click(function(){

                            $.ajax({
                                type: 'POST',
                                url: '/index.php',
                                data: 'ajax=true&action=roulette&type=' + whatIsIt + '&uid=' + <?php echo $this->accInfo['id'] ?>,
                                success: function(json) {
                                    try {

                                        let data = JSON.parse(json);

                                        //console.log(data);

                                        if (data.id) {
                                            let option = {
                                                speed : 25,
                                                duration : $.getRandomInt(0, 3),
                                                stopImageNumber : parseInt(data.id),
                                                startCallback : function() {
                                                    $('#startRulette').attr('disabled', 'true');
                                                    $('#sellItem').addClass('hide');
                                                    $('#sellItemBtn').attr('item-id', '');
                                                    console.log('start');
                                                },
                                                slowDownCallback : function() {
                                                    console.log('slowDown');
                                                },
                                                stopCallback : function($stopElm) {
                                                    console.log('stop');
                                                    $.updateBalance();
                                                    M.toast({html: data.message, classes: 'rounded'});
                                                    $('#startRulette').removeAttr('disabled');
                                                    $('#sellItem').removeClass('hide');
                                                    $('#sellItemBtn').attr('item-id', '' + data.idx);
                                                    $('#sellItemBtn').text('Продать за ' + data.dc + 'dc');
                                                }
                                            };
                                            rouletter.roulette('option', option);
                                            rouletter.roulette('start');
                                        }
                                        else {
                                            M.toast({html: data.message, classes: 'rounded'});
                                        }
                                    }
                                    catch (e) {
                                        console.log(e, json);
                                        M.toast({html: 'Произошла неизвестная ошибка #1', classes: 'rounded'});
                                    }
                                },
                                error: function () {
                                    M.toast({html: 'Произошла неизвестная ошибка #2', classes: 'rounded'});
                                }
                            });
                        });

                        $('.casebox').click(function(){

                            whatIsIt = $(this).attr('data-id');

                            $('.start').text(`Крутить за ${$(this).attr('data-price')} sc`);

                            $.ajax({
                                type: 'POST',
                                url: '/index.php',
                                data: 'ajax=true&action=rouletteLoad&type=' + whatIsIt,
                                success: function(data) {
                                    try {

                                        $('.roulette').html(data);
                                        $('#rouletteList').html(data);

                                        $('.tooltipped').tooltip();

                                        $('.roulette').roulette({
                                            startCallback : function() {
                                                console.log('start');
                                            },
                                            slowDownCallback : function() {
                                                console.log('slowDown');
                                            },
                                            stopCallback : function($stopElm) {
                                                console.log('stop');
                                            }
                                        });
                                    }
                                    catch (e) {
                                        M.toast({html: 'Произошла неизвестная ошибка #1', classes: 'rounded'});
                                    }
                                },
                                error: function () {
                                    M.toast({html: 'Произошла неизвестная ошибка #2', classes: 'rounded'});
                                }
                            });
                        });

                        $('.inventory').click(function(){

                            $.ajax({
                                type: 'POST',
                                url: '/index.php',
                                data: 'ajax=true&action=inventoryLoad',
                                success: function(data) {
                                    try {
                                        $('#inventoryList').html(data);
                                    }
                                    catch (e) {
                                        M.toast({html: 'Произошла неизвестная ошибка #1', classes: 'rounded'});
                                    }
                                },
                                error: function () {
                                    M.toast({html: 'Произошла неизвестная ошибка #2', classes: 'rounded'});
                                }
                            });
                        });

                        $('#sellItemBtn').click(function(){
                            var id = $('#sellItemBtn').attr('item-id');
                            $.ajax({
                                type: 'POST',
                                url: '/index.php',
                                data: 'ajax=true&action=sellItem&id=' + id,
                                success: function(data) {
                                    M.toast({html: data, classes: 'rounded'});
                                    $.updateBalance();
                                },
                                error: function () {
                                    M.toast({html: 'Произошла неизвестная ошибка, продайте предмет через инвентарь', classes: 'rounded'});
                                }
                            });
                            $('#sellItem').addClass('hide');
                            $('#sellItemBtn').attr('item-id', '');
                        });


                        $.updateBalance = function() {
                            $.ajax({
                                type: 'POST',
                                url: '/index.php',
                                data: 'ajax=true&action=updateBalance',
                                success: function(data) {
                                    $('#dcbalance').text('Баланс: ' + data + ' sc');
                                },
                                error: function () {

                                }
                            });
                        };
                    });
                </script>
            </div>
            <div class="col s12" id="caseOpen">
            </div>
        </div>
    </div>
</div>

<!-- Modal Structure -->
<div id="modal1" class="modal bottom-sheet roulette-modal">
    <div class="modal-content">
        <div class="center"><h4 class="wd-font bw-text">Крутить рулетку</h4></div>
        <div class="row">
            <div class="col s12">
                <div class="row">
                    <div class="col s12 m4"></div>
                    <div class="col s12 m4 center">
                        <div style="width: 370px;margin: auto;"><i class="material-icons" style="font-size: 2.5rem">keyboard_arrow_down</i></div>
                        <div class="roulette_container" style="width: 370px; height: 128px; overflow: hidden; margin: auto" >
                            <div class="roulette" style="display:none;">
                                <img style="float: left" src="https://cdn0.iconfinder.com/data/icons/fruits/128/Strawberry.png" alt="">
                                <img style="float: left" src="https://cdn0.iconfinder.com/data/icons/fruits/128/Cherry.png" alt="">
                                <img style="float: left" src="https://cdn0.iconfinder.com/data/icons/fruits/128/Apple.png" alt="">
                                <img style="float: left" src="https://cdn0.iconfinder.com/data/icons/fruits/128/Lemon.png" alt="">
                                <img style="float: left" src="https://cdn0.iconfinder.com/data/icons/fruits/128/Kiwi.png" alt="">
                                <img style="float: left" src="https://cdn0.iconfinder.com/data/icons/fruits/128/Pear.png" alt="">
                            </div>
                        </div>
                        <div style="width: 370px;margin: auto;"><i class="material-icons" style="font-size: 2.5rem">keyboard_arrow_up</i></div>
                        <br>
                        <button id="startRulette" class="btn blue accent-4 btn-large btn-primary start">Крутить за 99 sc</button>

                        <div class="hide" id="sellItem">
                            <br>
                            <button id="sellItemBtn" item-id="" class="btn amber accent-4 btn-large btn-primary">Продать за 99 sc</button>
                        </div>
                    </div>
                    <div class="col s12 m4"></div>
                </div>
            </div>
        </div>
        <div class="center"><br><br><h4 class="wd-font bw-text">Содержимое рулетки</h4></div>
        <div id="rouletteList" class="roulette-list">
            <img style="float: left" src="https://cdn0.iconfinder.com/data/icons/fruits/128/Strawberry.png" alt="">
            <img style="float: left" src="https://cdn0.iconfinder.com/data/icons/fruits/128/Cherry.png" alt="">
            <img style="float: left" src="https://cdn0.iconfinder.com/data/icons/fruits/128/Apple.png" alt="">
            <img style="float: left" src="https://cdn0.iconfinder.com/data/icons/fruits/128/Lemon.png" alt="">
            <img style="float: left" src="https://cdn0.iconfinder.com/data/icons/fruits/128/Kiwi.png" alt="">
            <img style="float: left" src="https://cdn0.iconfinder.com/data/icons/fruits/128/Pear.png" alt="">
        </div>
        <br><br>
        <div>
            <button class="modal-action z-depth-0 modal-close waves-effect btn btn-floating grey" style="position: absolute; top: 10px; right: 30px;"><i class="material-icons">close</i></button>
        </div>
    </div>
</div>

<!-- Modal Structure -->
<div id="modalInventory" class="modal bottom-sheet roulette-modal">
    <div class="modal-content">
        <div class="center"><h4 class="wd-font bw-text">Инвентарь рулетки</h4></div>
        <div class="row">
            <div class="col s12">
                <table>
                    <tbody id="inventoryList">
                        <tr>
                            <td><img class="caseimg rare9" src="https://cdn0.iconfinder.com/data/icons/fruits/128/Pear.png" alt=""></td>
                            <td>Название предмета</td>
                            <td><a class="btn green accent-4">Активировать</a></td>
                            <td><a class="btn blue accent-4">Продать за 100 sc</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <br><br>
        <div>
            <button class="modal-action z-depth-0 modal-close waves-effect btn btn-floating grey" style="position: absolute; top: 10px; right: 30px;"><i class="material-icons">close</i></button>
        </div>
    </div>
</div>