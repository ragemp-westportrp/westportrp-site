<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

/*@error_reporting ( E_ALL ^ E_DEPRECATED ^ E_NOTICE );
@ini_set ( 'error_reporting', E_ALL ^ E_DEPRECATED ^ E_NOTICE );
@ini_set ( 'display_errors', true );
@ini_set ( 'html_errors', false );
error_reporting(E_ALL);
ini_set("display_errors", 1);*/



global $qb;
global $user;
global $serverName;
global $server;

$settings = $qb->createQueryBuilder('page_settings')->selectSql()->executeQuery()->getSingleResult();
if ($settings['car_list']) {
    echo '
        <div class="container">
            <div class="section center">
                <h3 class="wd-font bw-text">Тех. работы</h3>
            </div>
        </div>
    ';
    return;
}

//error_log( $server->getClientIp() . "\n", 3, 'logs/carlist.log');

$qb
    ->createQueryBuilder('')
    ->otherSql("set session sql_mode=''", false)
    ->executeQuery()
    ->getResult()
;
?>


<div class="container">
    <div class="section">
        <div class="section">
            <div class="row">
                <?php

                $carList = $qb
                    ->createQueryBuilder('cars c')
                    ->selectSql('*, count(c.name) as n')
                    ->orderBy("price ASC, c.name ASC")
                    ->groupBy("c.name HAVING n <> 0")
                    ->where("price > 0 AND with_delete = 0")
                    ->executeQuery()
                    ->getResult()
                ;
                $carList2 = $qb
                    ->createQueryBuilder('cars c')
                    ->selectSql('name, count(c.name) as n')
                    ->orderBy("price ASC, c.name ASC")
                    ->groupBy("c.name HAVING n <> 0")
                    ->where("user_id = 0 AND with_delete = 0")
                    ->executeQuery()
                    ->getResult()
                ;
                $vInfo = $qb
                    ->createQueryBuilder('veh_info')
                    ->selectSql('display_name, price, class_name, class_name_ru, m_name, n_name')
                    ->where('type <> 0 OR price_dc > 0')
                    ->orderBy('class_name, price ASC')
                    ->executeQuery()
                    ->getResult()
                ;
                $classPrew = '';

                $idx = 0;
                $className = '';
                foreach ($vInfo as $item) {


                    $countVeh = 0;
                    $countAll = 0;
                    foreach ($carList2 as $item2) {
                        if ($item2['name'] == $item['display_name'])
                            $countVeh = $item2['n'];
                    }
                    foreach ($carList as $item2) {
                        if ($item2['name'] == $item['display_name'])
                            $countAll = $item2['n'];
                    }

                    //if ($countAll < 1)
                    //    continue;

                    if ($className != $item['class_name'])
                    {
                        echo '<div class="col s12"><h4 class="grey-text">' . $item['class_name'] . '</h4></div>';
                    }
                    $className = $item['class_name'];

                    echo '
                        <div class="col s12 l3">
                            <div class="card">
                                <div class="card-image">
                                    <a target="_blank" href="/car-info-' . $item['display_name'] . '"><img alt="' . $item['display_name'] . '" loading="lazy" src="/client/images/carsv/640/' . strtolower($item['display_name']) . '.jpg" style="height: 200px; object-fit: cover;"></a>
                                    <span class="card-title hide" style="font-size: 1.2rem">' . $item['m_name'] . ' ' . $item['n_name'] . '</span>
                                    <span class="card-title" style="font-size: 1.2rem">' . $item['display_name'] . '</span>
                                </div>
                                <div class="card-action">
                                    <a target="_blank" href="/car-info-' . $item['display_name'] . '" class="bw-text btn z-depth-0">$' . number_format($item['price']) . '</a>
                                    <a target="_blank" href="/car-info-' . $item['display_name'] . '" class="bw-text btn z-depth-0 right">' . $countVeh . '/' . $countAll . ' шт.</a>
                                </div>
                            </div>
                        </div>
                        ';

                    $idx++;
                }
                ?>
            </div>
        </div>
    </div>
</div>

