<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
global $server;
global $bId;
global $business;

$text = '';
if(isset($_GET['q'])) {
    $text = $server->charsString($_GET['q']);
    $logRows = $qb
        ->createQueryBuilder('log_business')
        ->selectSql()
        ->where('business_id = ' . $bId)
        ->andWhere('(product LIKE \'%' . $text . '%\' OR price LIKE \'%' . $text . '%\')')
        ->orderBy('id DESC')
        ->executeQuery()
        ->getResult()
    ;
}
else
    $logRows = $qb->createQueryBuilder('log_business')->selectSql()->where('business_id = ' . $bId)->limit(2500)->orderBy('id DESC')->executeQuery()->getResult();


$countLastMonth = $qb->createQueryBuilder('log_business')->selectSql('SUM(price) as sum')->where('business_id = ' . $bId)->andWhere('price > 0')->andWhere('timestamp > ' . (time() - 86400 * 30))->andWhere('product NOT LIKE \'%Зачиление со счета%\'')->executeQuery()->getSingleResult();
$countLastMonth = reset($countLastMonth);

$countLastWeek = $qb->createQueryBuilder('log_business')->selectSql('SUM(price) as sum')->where('business_id = ' . $bId)->andWhere('price > 0')->andWhere('timestamp > ' . (time() - 86400 * 7))->andWhere('product NOT LIKE \'%Зачиление со счета%\'')->executeQuery()->getSingleResult();
$countLastWeek = reset($countLastWeek);

$countLastDay = $qb->createQueryBuilder('log_business')->selectSql('SUM(price) as sum')->where('business_id = ' . $bId)->andWhere('price > 0')->andWhere('timestamp > ' . (time() - 86400))->andWhere('product NOT LIKE \'%Зачиление со счета%\'')->executeQuery()->getSingleResult();
$countLastDay = reset($countLastDay);

$qb
    ->createQueryBuilder('')
    ->otherSql("set session sql_mode=''", false)
    ->executeQuery()
    ->getResult()
;
$topProd = $qb->createQueryBuilder('log_business')->selectSql('COUNT(price) as count, product')->where('business_id = ' . $bId)->andWhere('price > 0')->andWhere('product NOT LIKE \'%Зачиление со счета%\'')->orderBy('count DESC')->groupBy('product')->limit(4)->executeQuery()->getResult();
?>

<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12">
                <div class="center" style="margin-top: 80px">
                    <h3 class="center grey-text">Статистика</h3>
                </div>
            </div>
            <div class="col s12 l4">
                <div class="center" style="margin-top: 30px">
                    <i style="font-size: 4rem" class="material-icons grey-text">account_balance</i>
                    <div class="center grey-text">За месяц</div>
                    <h3 class="center black-text">$<?php echo number_format($countLastMonth); ?></h3>
                </div>
            </div>
            <div class="col s12 l4">
                <div class="center" style="margin-top: 30px">
                    <i style="font-size: 4rem" class="material-icons grey-text">account_balance</i>
                    <div class="center grey-text">За неделю</div>
                    <h3 class="center black-text">$<?php echo number_format($countLastWeek); ?></h3>
                </div>
            </div>
            <div class="col s12 l4">
                <div class="center" style="margin-top: 30px">
                    <i style="font-size: 4rem" class="material-icons grey-text">account_balance</i>
                    <div class="center grey-text">За сутки</div>
                    <h3 class="center black-text">$<?php echo number_format($countLastDay); ?></h3>
                </div>
            </div>
            <div class="col s12">
                <div class="center" style="margin-top: 100px">
                    <h3 class="center grey-text">Топ популярных товаров</h3>
                </div>
            </div>
            <?php
                foreach ($topProd as $prod) {
                    echo '
                       
                    <div class="col s12 l3">
                        <div class="center" style="margin-top: 30px">
                            <i style="font-size: 2rem" class="material-icons green-text">trending_up</i>
                            <div class="center grey-text">Куплено ' . $prod['count'] . ' раз</div>
                            <h5 class="center black-text">' . $prod['product'] . '</h5>
                        </div>
                    </div>
                    ';
                }
            ?>
        </div>
    </div>
</div>

<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12">

                <?php

                echo '
                <div style="height: 50px; width: 100%;">
                    <h4 class="grey-text text-darken-2">Лог | Всего: ' . count($logRows) . '</h4>
                </div>
                <form>
                    <div class="card-panel input-field" style=" padding: 0;">
                        <input style="padding-left: 14px; border: none;" id="search" class="search_all" type="search" value="' . $text . '" placeholder="Поиск" name="q" required="">
                    </div>
                </form>
            ';

                ?>
            </div>
        </div>
    </div>
</div>

<style>
    td {
        padding: 2px 5px;
    }
</style>
<?php
echo '
    <div class="card-panel" style="margin-bottom: 50px;">
        <table class="highlight">
            <thead>
            <tr>
                <th>#</th>
                <th>Продукт</th>
                <th>Цена</th>
                <th>Время</th>
            </tr>
            </thead>
            <tbody>';
            $count = 0;
            foreach ($logRows as $item) {
                echo '
                    <tr>
                        <td>' . (++$count) . '</td>
                        <td>' . $item['product'] . '</td>
                        <td>' . ($item['price'] > 0 ? '<div class="green-text">$' . number_format($item['price']) . '</div>' : '<div class="red-text">$' . number_format($item['price']) . '</div>') . '</td>
                        <td>' . gmdate("d-m-Y, H:i", $item['timestamp'] + (3600 * 3)) . '</td>
                    </tr>';
                }
                echo '
            </tbody>
        </table>
    </div>
';

?>