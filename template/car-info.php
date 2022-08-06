<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
global $fuelTypes;
global $fuelTypesPostfix;
global $user;
global $modal;
global $server;
global $userInfo;

$carInfo = $qb
    ->createQueryBuilder('veh_info')
    ->selectSql()
    ->where("display_name = '" . $this->carName . "'")
    ->limit(1)
    ->executeQuery()
    ->getSingleResult()
;

if (isset($_POST['addNewVehicle'])) {

    for ($i = 0; $i < intval($_POST['num']); $i++) {
        $number = $server->genVehicleNumber();
        $qb
            ->createQueryBuilder('cars')
            ->insertSql(
                ['name', 'class', 'price', 'fuel', 'number'],
                [$carInfo['display_name'], $carInfo['class_name'], $carInfo['price'], $carInfo['fuel_full'], $number]
            )
            ->executeQuery()
            ->getResult()
        ;
    }

    $modal['show'] = true;
    $modal['text'] = 'ТС был добавлен на авторынок';
}

if (isset($_POST['addNewVehiclePl'])) {
    global $server;
    global $modal;

    $usrInfo = $qb
        ->createQueryBuilder('users')
        ->selectSql()
        ->where('id = \'' . intval($_POST['id']) . '\'')
        ->executeQuery()
        ->getSingleResult()
    ;


    if ($usrInfo['car_id' . $_POST['slot']] == 0) {
        $color = random_int(0, 156);
        $number = $server->genVehicleNumber();

        //'name', 'class', 'price', 'fuel', 'number'
        $qb
            ->createQueryBuilder('cars')
            ->insertSql(
                ['name', 'class', 'price', 'number', 'user_id', 'user_name', 'fuel'],
                [$carInfo['display_name'], $carInfo['class_name'], $carInfo['price'], $number, $usrInfo['id'], $usrInfo['name'], $carInfo['fuel_full']]
            )
            ->executeQuery()
            ->getResult()
        ;

        $lastCar = $qb
            ->createQueryBuilder('cars')
            ->selectSql()
            ->orderBy('id DESC')
            ->limit(1)
            ->where('number = \'' . $number . '\'')
            ->executeQuery()
            ->getSingleResult()
        ;

        $usrInfo = $qb
            ->createQueryBuilder('users')
            ->updateSql(['car_id' . intval($_POST['slot'])], [$lastCar['id']])
            ->where('id = \'' . intval($_POST['id']) . '\'')
            ->executeQuery()
            ->getResult()
        ;

        $modal['show'] = true;
        $modal['title'] = 'Операция успешна';
        $modal['text'] = 'ТС был добавлен на авторынок';
        $modal['success'] = true;
    }
    else {
        $modal['show'] = true;
        $modal['title'] = 'Операция успешна';
        $modal['text'] = 'Данный слот у игрока занят другим трансопртом';
        $modal['success'] = true;
    }
}

$carCount = $qb
    ->createQueryBuilder('cars')
    ->selectSql('COUNT(*)')
    ->where("name = '" . $this->carName . "'")
    ->executeQuery()
    ->getSingleResult()
;
$carCount = reset($carCount);

$settings = $qb->createQueryBuilder('page_settings')->selectSql()->executeQuery()->getSingleResult();
if ($settings['car_list_more']) {
    echo '
        <div class="container">
            <div class="section center">
                <h3 class="wd-font bw-text">Тех. работы</h3>
            </div>
        </div>
    ';
    return;
}

if (empty($carInfo))
{
    echo '
        <div class="container">
            <div class="section center">
                <h3 class="wd-font">Транспорт не найден</h3>
            </div>
        </div>
    ';
    return;
}

$fuelName = $fuelTypes[$carInfo['fuel_type']];
$fuelPostfix = $fuelTypesPostfix[$carInfo['fuel_type']];

?>

<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12">
                <h4 class="grey-text <?php echo $userInfo['admin_level'] > 0 ? '' : 'hide' ?>">Всего на авторынке: <?php echo $carCount ?></h4>
                <div class="row">
                    <div class="col s12 l8">
                        <div class="row">
                            <div class="col s12">
                                <div class="card z-depth-0">
                                    <img alt="<?php echo $carInfo['m_name'] . ' ' . $carInfo['n_name']; ?>" src="/client/images/carsv/1080/<?php echo strtolower($this->carName); ?>.jpg" class="materialboxed" style="width: 100%; object-fit: cover;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col s12 l4">
                        <h5 class="light bw-text" style="margin-top: 0">Информация</h5>
                        <?php
                        echo '
                            <table class="bordered highlight">
                                <tbody>
                                    <tr>
                                        <td class="grey-text">Марка</td>
                                        <td>' . $carInfo['m_name'] . '</td>
                                    </tr>                             
                                    <tr>
                                        <td class="grey-text">Модель</td>
                                        <td>' . $carInfo['n_name'] . '</td>
                                    </tr>                           
                                    <tr>
                                        <td class="grey-text">Класс</td>
                                        <td>' . $carInfo['class_name'] . '</td>
                                    </tr>
                                    <tr>
                                        <td class="grey-text">Цена</td>
                                        <td>$' . number_format($carInfo['price']) . '</td>
                                    </tr>
                                </tbody>
                            </table>
                        ';
                        ?>
                        <br>
                        <h5 class="light bw-text">Характеристики</h5>
                        <?php
                        echo '
                            <table class="bordered highlight">
                                <tbody>
                                    <tr>
                                        <td class="grey-text">Максимальная скорость</td>
                                        <td>~' . $carInfo['sm'] . 'км/ч</td>
                                    </tr>
                                    <tr>
                                        <td class="grey-text">Топливо</td>
                                        <td>' . $fuelName . '</td>
                                    </tr>
                                    <tr class="' . ($carInfo['fuel_type'] == 0 ? 'hide' : '') . '">
                                        <td class="grey-text">Вместимость бака</td>
                                        <td>' . $carInfo['fuel_full'] . $fuelPostfix . '</td>
                                    </tr>
                                    <tr class="' . ($carInfo['fuel_type'] == 0 ? 'hide' : '') . '">
                                        <td class="grey-text">Расход топлива</td>
                                        <td>' . $carInfo['fuel_min'] . $fuelPostfix . '</td>
                                    </tr>
                                    <tr>
                                        <td class="grey-text">Вместимость багажника</td>
                                        <td>' . ($carInfo['stock'] > 0 ? $carInfo['stock'] . 'см³' : 'Отсутствует') . '</td>
                                    </tr>
                               
                                </tbody>
                            </table>
                        ';
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        if ($user->isAdmin(5)) {
            echo '
            <form method="post" style="margin-top: 30px;">
                <h4 class="grey-text">Добавить на авторынок</h4>
                <div class="row">
                    <div class="input-field col s6">
                        <input value="true" name="reset-redirect" type="hidden">
                        <input placeholder="Число" value="1" id="num" name="num" type="number" class="validate">
                        <label for="num"></label>
                    </div>
                    <div class="input-field col s6">
                        <button class="btn blue accent-4" name="addNewVehicle">Применить</button>
                    </div>
                </div>
            </form>
            <form method="post" style="margin-top: 30px;">
                <h4 class="grey-text">Добавить игроку</h4>
                <div class="row">
                    <div class="input-field col s6">
                        <input value="true" name="reset-redirect" type="hidden">
                        <input placeholder="ID Игрока" name="id" type="number" class="validate">
                        <label for="id"></label>
                    </div>
                    <div class="input-field col s6">
                        <input placeholder="Слот" name="slot" type="number" class="validate">
                        <label for="slot"></label>
                    </div>
                    <div class="input-field col s6">
                        <button class="btn red" name="addNewVehiclePl">Применить</button>
                    </div>
                </div>
            </form>
            ';
        }
        ?>
    </div>
</div>
