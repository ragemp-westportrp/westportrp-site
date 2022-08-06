<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
global $user;
global $tmp;

$localQb = $qb;

$serverName = 'Python';
/*if (!$user->isAdmin()) {
	echo '<h4 class="center" style="margin: 250px;">Тех. работы</h4>';
	return;
}*/

/*@error_reporting ( E_ALL ^ E_DEPRECATED ^ E_NOTICE );
@ini_set ( 'error_reporting', E_ALL ^ E_DEPRECATED ^ E_NOTICE );
@ini_set ( 'display_errors', true );
@ini_set ( 'html_errors', false );
error_reporting(E_ALL);
ini_set("display_errors", 1);*/

$qb
    ->createQueryBuilder('')
    ->otherSql("set session sql_mode=''", false)
    ->executeQuery()
    ->getResult()
;

$online = $qb->createQueryBuilder('stats_online')->selectSql()->where('timestamp > FROM_UNIXTIME(' . (time() - (24 * 3600 * 3)) . ')')->executeQuery()->getResult();
$dateTimeSrv = $qb->createQueryBuilder('daynight')->selectSql()->executeQuery()->getSingleResult();

$userList = $localQb
    ->createQueryBuilder('users')
    ->selectSql()
    ->orderBy('name ASC')
    ->where('is_online = 1')
    ->executeQuery()
    ->getResult()
;

$topReg = $localQb
    ->createQueryBuilder('users')
    ->selectSql()
    ->orderBy('id DESC')
    ->limit(100)
    ->executeQuery()
    ->getResult()
;

$banList = $localQb
    ->createQueryBuilder('ban_list')
    ->selectSql()
    ->orderBy('id DESC')
    ->limit(500)
    ->executeQuery()
    ->getResult()
;

$userCountList = $localQb
    ->createQueryBuilder('users')
    ->selectSql('count(*)')
    ->executeQuery()
    ->getSingleResult()
;
$userCountList = reset($userCountList);

$monitoringList = [];

$monitoringList['statistic'] = $localQb
    ->createQueryBuilder('users')
    ->selectSql('sum(money) as money, sum(money_bank) as moneyBank, count(*) as allPlayers')
    ->where('login_date > ' . (time() - (60 * 60 * 24 * 30)))
    ->executeQuery()
    ->getSingleResult()
;

$monitoringList['statistic_car'] = $localQb
    ->createQueryBuilder('cars')
    ->selectSql('sum(price) as money')
    ->where('user_id <> 0')
    ->executeQuery()
    ->getSingleResult()
;

$monitoringList['statistic_aprt'] = 0;

$monitoringList['statistic_bizz'] = $localQb
    ->createQueryBuilder('business')
    ->selectSql('sum(price) as money, sum(bank) as money1')
    ->where('user_id <> 0')
    ->executeQuery()
    ->getSingleResult()
;

$monitoringList['statistic_house'] = $localQb
    ->createQueryBuilder('houses')
    ->selectSql('sum(price) as money')
    ->where('user_id <> 0')
    ->executeQuery()
    ->getSingleResult()
;

$monitoringList['statistic']['money'] = $monitoringList['statistic']['money'] + $monitoringList['statistic']['moneyBank'] + $monitoringList['statistic_house']['money'] + $monitoringList['statistic_bizz']['money']+ $monitoringList['statistic_bizz']['money1'] + $monitoringList['statistic_aprt']['money'] + $monitoringList['statistic_car']['money'];

$monitoringList['statistic_car'] = $localQb
    ->createQueryBuilder('cars')
    ->selectSql('sum(price) as money')
    ->executeQuery()
    ->getSingleResult()
;

$monitoringList['statistic_aprt'] = $localQb
    ->createQueryBuilder('stocks')
    ->selectSql('sum(price) as money')
    ->executeQuery()
    ->getSingleResult()
;

$monitoringList['statistic_bizz'] = $localQb
    ->createQueryBuilder('business')
    ->selectSql('sum(price) as money, sum(bank) as money1')
    ->executeQuery()
    ->getSingleResult()
;

$monitoringList['statistic_house'] = $localQb
    ->createQueryBuilder('houses')
    ->selectSql('sum(price) as money')
    ->executeQuery()
    ->getSingleResult()
;

$monitoringList['statistic']['moneyFull'] = $monitoringList['statistic_house']['money'] + $monitoringList['statistic_bizz']['money']+ $monitoringList['statistic_bizz']['money1'] + $monitoringList['statistic_aprt']['money'] + $monitoringList['statistic_car']['money'];

$monitoringList['statistic_car'] = $localQb
    ->createQueryBuilder('cars')
    ->selectSql('sum(price) as money')
    ->where('user_id = 0')
    ->executeQuery()
    ->getSingleResult()
;

$monitoringList['statistic_aprt'] = $localQb
    ->createQueryBuilder('stocks')
    ->selectSql('sum(price) as money')
    ->where('user_id = 0')
    ->executeQuery()
    ->getSingleResult()
;

$monitoringList['statistic_condo'] = $localQb
    ->createQueryBuilder('condos')
    ->selectSql('sum(price) as money')
    ->where('user_id = 0')
    ->executeQuery()
    ->getSingleResult()
;

$monitoringList['statistic_bizz'] = $localQb
    ->createQueryBuilder('business')
    ->selectSql('sum(price) as money, sum(bank) as money1')
    ->where('user_id = 0')
    ->executeQuery()
    ->getSingleResult()
;

$monitoringList['statistic_house'] = $localQb
    ->createQueryBuilder('houses')
    ->selectSql('sum(price) as money')
    ->where('user_id = 0')
    ->executeQuery()
    ->getSingleResult()
;

$monitoringList['statistic']['moneyFree'] = $monitoringList['statistic_house']['money'] + $monitoringList['statistic_condo']['money']+ $monitoringList['statistic_bizz']['money1'] + $monitoringList['statistic_aprt']['money'] + $monitoringList['statistic_car']['money'];


if (mb_strlen($dateTimeSrv['hour'],'UTF-8') == 1)
    $dateTimeSrv['hour'] = '0' . $dateTimeSrv['hour'];
if (mb_strlen($dateTimeSrv['minute'],'UTF-8') == 1)
    $dateTimeSrv['minute'] = '0' . $dateTimeSrv['minute'];
if (mb_strlen($dateTimeSrv['day'],'UTF-8') == 1)
    $dateTimeSrv['day'] = '0' . $dateTimeSrv['day'];
if (mb_strlen($dateTimeSrv['month'],'UTF-8') == 1)
    $dateTimeSrv['month'] = '0' . $dateTimeSrv['month'];
?>


<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    $(document).ready(function() {
        google.charts.load('current', {'packages':['line']});
        google.charts.setOnLoadCallback(drawChartOnline);


        function drawChartOnline() {

            var data = new google.visualization.DataTable();
            data.addColumn('string', 'День');
            data.addColumn('number', 'Онлайн');

            data.addRows([
                <?php
                foreach ($online as $item) {
                    echo '[\'' . date('Hч., d/m', strtotime($item['timestamp'])) . '\', ' . $item['online'] . '],';
                }
                ?>
            ]);

            var options = {
                chart: {
                    title: 'Статистика онлайна',
                    subtitle: 'Всего регистраций: <?php echo number_format($userCountList) ?>'
                },
                height: 300,
                axes: {
                    x: {
                        0: {side: 'top'}
                    }
                }
            };

            var chart = new google.charts.Line(document.getElementById('online'));
            chart.draw(data, google.charts.Line.convertOptions(options));
        }
    });
</script>


<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12">
                <h4 class="grey-text wd-font">Статистика сервера <?php echo $serverName; ?></h4>
                <label>Игровое время: <?php echo $dateTimeSrv['hour'] . ':' . $dateTimeSrv['minute'] . ' ' . $dateTimeSrv['day'] . '/' . $dateTimeSrv['month'] . '/' . $dateTimeSrv['year'] ?></label>
            </div>
            <div class="col s12">
                <div class="card-panel">
                    <div id="online" style="height: 300px"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container" style="margin-top: -80px">
    <div class="section">
        <div class="section">
            <div class="row">
                <div class="row hide">
                    <div class="col s12">
                        <h4 class="grey-text center">Статистика сервера <?php echo $serverName; ?>
                            <br><label>Зарегестрированно игроков: <?php echo $userCountList ?></label></h4>
                    </div>
                </div>
                <div class="row hide">
                    <div class="col s12 m4">
                        <div class="icon-block">
                            <h2 class="center blue-text"><i class="material-icons">group</i></h2>
                            <h6 class="center bw-text">Имущество активных игроков</h6>
                            <h4 class="center bw-text">$<?php echo number_format($monitoringList['statistic']['money']) ?></h4>
                        </div>
                    </div>
                    <div class="col s12 m4">
                        <div class="icon-block">
                            <h2 class="center blue-text"><i class="material-icons">assignment</i></h2>
                            <h6 class="center bw-text">Общая ценность имущества</h6>
                            <h4 class="center bw-text">$<?php echo number_format($monitoringList['statistic']['moneyFull']) ?></h4>
                        </div>
                    </div>

                    <div class="col s12 m4">
                        <div class="icon-block">
                            <h2 class="center blue-text"><i class="material-icons">assignment_turned_in</i></h2>
                            <h6 class="center bw-text">Свободного имущества</h6>
                            <h4 class="center bw-text">$<?php echo number_format($monitoringList['statistic']['moneyFree']) ?></h4>
                        </div>
                    </div>
                </div>

                <div class="col s12 hide">
                    <br>
                    <hr>
                    <br>
                </div>

                <div class="col s12">
                    <ul class="tabs wb z-depth-1">
                        <li class="tab col s3"><a class="bw-text" href="#monitoring">Игроки</a></li>
                        <li class="tab col s3"><a class="bw-text" href="#banlist">Банлист</a></li>
                        <li class="tab col s3"><a class="bw-text" href="#top">Топ 100 игроков</a></li>
                        <li class="tab col s3"><a class="bw-text" href="#top-reg">100 последних регистраций</a></li>
                    </ul>
                </div>
                <div class="col s12" id="monitoring">
                    <div class="card-panel">
                        <table class="highlight">
                            <thead>
                            <tr>
                                <th data-field="id">#</th>
                                <th data-field="name">Имя</th>
                                <th data-field="name">Национальность</th>
                                <th data-field="name">Всего в игре</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $i = 0;
                            foreach ($userList as $item) {

                                $name = $item['name'];
                                if ($item['admin_level'] > 4 && $user->isLogin() && $user->isAdmin())
                                    $name = '<b class="red-text">' . $name . '</b>';
                                else if ($item['admin_level'] > 2 && $user->isLogin() && $user->isAdmin())
                                    $name = '<span class="blue-text">' . $name . '</span>';
                                else if ($item['admin_level'] > 0 && $user->isLogin() && $user->isAdmin())
                                    $name = '<span class="indigo-text">' . $name . '</span>';
                                else if ($item['helper_level'] > 0)
                                    $name = '<span class="amber-text">' . $name . '</span>';
                                else if ($item['vip_type'] == 2)
                                    $name = '<span class="green-text">' . $name . '</span>';
                                else if ($item['vip_type'] == 1)
                                    $name = '<span class="teal-text">' . $name . '</span>';

                                echo '
		                                <tr>
		                                    <td>' . (++$i) .  '</td>
		                                    <td>' . $name . '</td>
		                                    <td>' . $item['national'] . '</td>
		                                    <td>' . round($item['online_time'] * 8.5 / 60, 2) . 'ч</td>
		                                </tr>
	                                ';
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col s12" id="banlist">
                    <div class="card-panel">
                        <table class="highlight responsive-table">
                            <thead>
                            <tr>
                                <th data-field="name_from">Администратор</th>
                                <th data-field="name_to">Нарушитель</th>
                                <th data-field="date">Дата</th>
                                <th data-field="count">Кол-во</th>
                                <th data-field="reason">Причина</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($banList as $item) {
                                echo '
		                            <tr>
		                                <td>' . $item['ban_from'] . '</td>
		                                <td>' . $item['ban_to'] . '</td>
		                                <td>' . gmdate("m-d, H:i", $item['datetime'] + 3600 * 3) . '</td>
		                                <td>' . $item['count'] . '</td>
		                                <td>' . $item['reason'] . '</td>
		                            </tr>
		                            ';
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col s12" id="top">
                    <?php $tmp->showBlockPage('top'); ?>
                </div>
                <div class="col s12" id="top-reg">
                    <div class="card-panel">
                        <table class="highlight">
                            <thead>
                            <tr>
                                <th data-field="id">#</th>
                                <th data-field="name">Имя</th>
                                <th data-field="name">Национальность</th>
                                <th data-field="name">В игре</th>
                                <th data-field="reg">Дата регистрации</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $i = 0;
                            foreach ($topReg as $item) {
                                echo '
                                    <tr>
                                        <td>' . (++$i) .  '</td>
                                        <td>' . $item['name'] . '</td>
                                        <td>' . $item['national'] . '</td>
                                        <td>' . round($item['online_time'] * 8.5 / 60, 2) . 'ч</td>
                                        <td>' . gmdate("d-m, H:i", $item['reg_timestamp'] + (3600 * 3)) . '</td>
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
    </div>
</div>

