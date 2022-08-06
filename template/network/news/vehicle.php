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

<style>
    td, th {
        padding: 2px 5px;
    }
</style>

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
    </div>
</div>
