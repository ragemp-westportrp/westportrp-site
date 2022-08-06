<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}


global $qb;
global $user;
global $userInfo;
global $serverName;
global $server;
global $fractionId;

$newsLast = $qb->createQueryBuilder('rp_news')->selectSql()->where('fraction = ' . $fractionId)->limit(1000)->orderBy('id DESC')->executeQuery()->getResult();
$result = $qb->createQueryBuilder('users')->selectSql()->where('fraction_id = ' . $fractionId)->andWhere('is_leader = 1')->andWhere('admin_level = 0')->limit(1)->executeQuery()->getSingleResult();
$leaderName = empty($result['name']) ? 'Отсутствует' : $result['name'];

$coffer = $qb->createQueryBuilder('official_bank')->selectSql()->where('id = 6')->limit(1)->executeQuery()->getSingleResult();


$businessList = $qb->createQueryBuilder('business')->selectSql()->orderBy('type ASC')->executeQuery()->getResult();
?>

<img src="https://i.imgur.com/3Qxdoyf.png" style="position: absolute; z-index: -1; width: 100%; height: 400px; object-fit: cover;">

<div class="container">
    <div class="section">
        <div class="row" style="margin-top: 20px">
            <div class="col s12 m6 l7 white-text">
                <img style="width: 120px;" src="https://gtalogo.com/img/6765.png">
                <h3>Arcadius Business Center</h3>
            </div>
            <div class="col s12 m6 l5 hide">
                <div class="center" style="margin-top: 80px">
                    <i style="font-size: 4rem" class="material-icons white-text">account_balance</i>
                    <div class="center" style="color: rgba(255,2552,255,0.7)">Бюджет организации</div>
                    <h3 class="center white-text">$<?php echo number_format($coffer['money']); ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="container" style="padding: 50px 0; margin-top: 40px">
    <div class="section">
        <div class="row">
            <?php

            $idPrev = -1;
            foreach ($businessList as $item) {

                if ($item['price'] == 0)
                    continue;

                if ($idPrev != $item['type'])
                    echo '<div class="col s12"><h4 class="grey-text">' . $user->getBusinessName($item['type']) . '</h4></div>';
                $idPrev = $item['type'];

                if ($item['type'] == 10)
                    echo '
                        <div class="col s12 m6">
                            <ul class="collection" style="border-radius: 8px;">
                                <li style="min-height: 64px; padding-left: 20px;" class="collection-item avatar">
                                  <span class="title"><b>' . $item['name'] . '</b></span>
                                  <p>Цена: $' . number_format($item['price']) . '</p>
                                  <p>Владелец: ' . ($item['user_id'] > 0 ? $item['user_name'] : 'Нет') . '</p>
                                  <a href="/network/business-info' . $item['id'] . '" class="secondary-content blue-text">Подробнее</a>
                                </li>
                            </ul>
                        </div>
                    ';
                else
                    echo '
                        <div class="col s12 m6">
                            <ul class="collection" style="border-radius: 8px;">
                                <li style="min-height: 64px; padding-left: 20px;" class="collection-item avatar">
                                  <span class="title"><b>' . $item['name'] . '</b></span>
                                  <p>Цена: $' . number_format($item['price']) . '</p>
                                  <p>Владелец: ' . ($item['user_id'] > 0 ? $item['user_name'] : 'Нет') . '</p>
                                  <p>Цена на товары: ' . $user->getBusinessPriceName($item['price_product']) . '</p>
                                  <a href="/network/business-info' . $item['id'] . '" class="secondary-content blue-text">Подробнее</a>
                                </li>
                            </ul>
                        </div>
                    ';
            }
            ?>
        </div>
    </div>
</div>
