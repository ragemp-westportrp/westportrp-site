<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
global $userInfo;

if (!$userInfo['allow_stats']) {
    echo '
        <ma class="container">
            <div class="section">
                <h4 class="grey-text">Нет доступа</h4>
            </div>
        </ma>
    ';
    return;
}

$qb
    ->createQueryBuilder('')
    ->otherSql("set session sql_mode=''", false)
    ->executeQuery()
    ->getResult()
;

//SET `sql_mode`='';
//SELECT `id`, AVG(`online`), `timestamp` FROM `stats_online` GROUP BY DAY(`timestamp`);

$onlineDay = $qb->createQueryBuilder('stats_online')->selectSql('id, AVG(online) as online, timestamp')->where('timestamp > FROM_UNIXTIME(' . (time() - (24 * 3600 * 30)) . ')')->andWhere('HOUR(timestamp) > 11')->groupBy('MONTH(timestamp), DAY(timestamp)')->executeQuery()->getResult();
$onlineDayFull = $qb->createQueryBuilder('stats_online')->selectSql('id, AVG(online) as online, timestamp')->where('timestamp > FROM_UNIXTIME(' . (time() - (24 * 3600 * 30)) . ')')->groupBy('MONTH(timestamp), DAY(timestamp)')->executeQuery()->getResult();
$onlineDayMax = $qb->createQueryBuilder('stats_online')->selectSql('id, MAX(online) as online, timestamp')->where('timestamp > FROM_UNIXTIME(' . (time() - (24 * 3600 * 30)) . ')')->groupBy('MONTH(timestamp), DAY(timestamp)')->executeQuery()->getResult();
$online = $qb->createQueryBuilder('stats_online')->selectSql()->where('timestamp > FROM_UNIXTIME(' . (time() - (24 * 3600 * 3)) . ')')->executeQuery()->getResult();
$usersLogin = $qb->createQueryBuilder('log_connect')->selectSql('COUNT(*) as count, timestamp')->where('timestamp > FROM_UNIXTIME(' . (time() - (24 * 3600 * 30)) . ')')->andWhere('type = \'LOGIN\'')->groupBy('DATE(timestamp)')->executeQuery()->getResult();
//$usersLogin2 = $qb->createQueryBuilder('log_connect')->selectSql('COUNT(*) as count')->where('timestamp > FROM_UNIXTIME(' . (time() - (24 * 3600 * 30)) . ')')->andWhere('type = \'LOGIN\'')->groupBy('DATE(timestamp), social')->executeQuery()->getResult();
$usersReg = $qb->createQueryBuilder('users')->selectSql('COUNT(*) as count, reg_timestamp')->where('reg_timestamp > ' . (time() - (24 * 3600 * 30)))->groupBy('DATE(FROM_UNIXTIME(reg_timestamp))')->executeQuery()->getResult();
$accountReg = $qb->createQueryBuilder('accounts')->selectSql('COUNT(*) as count, reg_timestamp')->where('reg_timestamp > ' . (time() - (24 * 3600 * 30)))->groupBy('DATE(FROM_UNIXTIME(reg_timestamp))')->executeQuery()->getResult();
?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    $(document).ready(function() {
        google.charts.load('current', {'packages':['line']});
        google.charts.setOnLoadCallback(drawChartOnline);
        google.charts.setOnLoadCallback(drawChartOnlineDay);
        google.charts.setOnLoadCallback(drawChartUsers);
        google.charts.setOnLoadCallback(drawChartReg);

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
                    subtitle: 'Средний онлайн за месяц.'
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

        function drawChartOnlineDay() {

            var data = new google.visualization.DataTable();
            data.addColumn('string', 'День');
            data.addColumn('number', 'Онлайн днем');
            data.addColumn('number', 'Онлайн сутки');
            data.addColumn('number', 'Онлайн пиковый');

            data.addRows([
                <?php
                    $idx = 0;
                    foreach ($onlineDay as $item) {
                        echo '[\'' . date('Hч., d/m', strtotime($item['timestamp'])) . '\', ' . $item['online'] . ', ' . $onlineDayFull[$idx]['online'] . ', ' . $onlineDayMax[$idx]['online'] . '],';
                        $idx++;
                    }
                ?>
            ]);

            var options = {
                chart: {
                    title: 'Статистика онлайна',
                    subtitle: 'Статистика онлайна.'
                },
                height: 300,
                axes: {
                    x: {
                        0: {side: 'top'}
                    }
                }
            };

            var chart = new google.charts.Line(document.getElementById('onlineDay'));
            chart.draw(data, google.charts.Line.convertOptions(options));
        }

        function drawChartUsers() {

            var data = new google.visualization.DataTable();
            data.addColumn('string', 'День');
            data.addColumn('number', 'Регистраций Персонажей');
            data.addColumn('number', 'Регистраций Аккаунтов');

            data.addRows([

                <?php
                $i = 0;
                foreach ($usersLogin as $item) {
                    $countReg = 0;
                    foreach ($usersReg as $itemReg) {
                        if (gmdate('d/m/y', strtotime($item['timestamp'])) == gmdate('d/m/y', $itemReg['reg_timestamp'] + 84000))
                            $countReg = $itemReg['count'];
                    }
                    $countAccReg = 0;
                    foreach ($accountReg as $itemReg) {
                        if (gmdate('d/m/y', strtotime($item['timestamp'])) == gmdate('d/m/y', $itemReg['reg_timestamp'] + 84000))
                            $countAccReg = $itemReg['count'];
                    }
                    echo '[\'' . date('d/m/y', strtotime($item['timestamp'])) . '\', ' . $countReg . ', ' . $countAccReg . '],';
                }
                ?>
            ]);

            var options = {
                chart: {
                    title: 'Статистика',
                    subtitle: 'Статистика авторизаций за 30 дней.'
                },
                height: 300,
                axes: {
                    x: {
                        0: {side: 'top'}
                    }
                }
            };

            var chart = new google.charts.Line(document.getElementById('users'));
            chart.draw(data, google.charts.Line.convertOptions(options));
        }
        function drawChartReg() {

            var data = new google.visualization.DataTable();
            data.addColumn('string', 'День');
            data.addColumn('number', 'Регистраций');
            //data.addColumn('number', 'Авторизаций');

            data.addRows([

                <?php
                foreach ($usersReg as $item) {
                    echo '[\'' . date('d/m/y', $item['reg_timestamp']) . '\', ' . $item['count'] . '],';
                }
                ?>
            ]);

            var options = {
                chart: {
                    title: 'Статистика',
                    subtitle: 'Статистика регистраций за 30 дней.'
                },
                height: 300,
                axes: {
                    x: {
                        0: {side: 'top'}
                    }
                }
            };

            var chart = new google.charts.Line(document.getElementById('reg'));
            chart.draw(data, google.charts.Line.convertOptions(options));
        }
    });
</script>


<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12">
                <h5 class="grey-text wd-font">Добавить промокод</h5>
                <div class="row">
                    <div class="col s12 m4">
                        <form method="post" class="card-panel">
                            <div class="input-field" style="margin: 0">
                                <input placeholder="Специальный промокод" type="text" name="promo" required="">
                            </div>
                            <div class="input-field" style="margin: 0">
                                <button class="btn z-depth-0 blue accent-4" name="add-promo-spec">Добавить</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col s12">
                <div class="card-panel">
                    <div id="online" style="height: 300px"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col s12">
                <div class="card-panel">
                    <div id="onlineDay" style="height: 300px"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col s12">
                <div class="card-panel">
                    <div id="users" style="height: 300px"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col s12">
                <div class="card-panel">
                    <div id="reg" style="height: 300px"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php



global $qb;
global $serverName;
global $userInfo;

if ($userInfo['admin_level'] >= 0) {
    echo '
                <div class="container">
                    <div class="section">
                        <br>
                        <div class="row">';
    if (true) {
        echo '
		                            <div class="col s12">
		                            <div class="card-panel">
		                            <table class="highlight responsive-table">
								        <thead>
								          <tr>
								              <th>#</th>
								              <th>Промокод</th>
                                              <th>Кол-во</th>
                                              <th>Всё время</th>
								          </tr>
								        </thead>

								        <tbody>';

        $resultPromo = $qb
            ->createQueryBuilder('users')
            ->selectSql('COUNT(*) as c, promocode, social')
            ->groupBy('promocode')
            ->orderBy('c DESC')
            ->executeQuery()
            ->getResult()
        ;

        $count = 1;
        foreach ($resultPromo as $item) {
            if ($item['promocode'] == '') continue;
            if ($item['c'] <= 1) continue;

            //$parthUserList = $qb->createQueryBuilder('users')->selectSql('COUNT(*)')->where('promocode = \'' . $item['promocode'] . '\'')->orderBy('id DESC')->executeQuery()->getSingleResult();
            $parthUserList2 = $qb->createQueryBuilder('users')->selectSql('COUNT(*)')->where('promocode = \'' . $item['promocode'] . '\' AND online_time > 203')->orderBy('id DESC')->executeQuery()->getSingleResult();
            $acc = $qb->createQueryBuilder('accounts')->selectSql('id')->where('social = \'' . $item['social'] . '\'')->executeQuery()->getSingleResult();

            /*$parthUserAcLogSum = $qb
                ->createQueryBuilder('log_donate_payment')
                ->selectSql('SUM(log_donate_payment.money_p) as summ')
                ->where('users.promocode = \'' . $item['promocode'] . '\'')
                ->leftJoin('users ON log_donate_payment.user_id = users.id')
                ->groupBy('log_donate_payment.id')
                ->executeQuery()
                ->getResult()
            ;*/

            $parthUserAcLogSum = $qb
                ->createQueryBuilder('log_donate_trade')
                ->selectSql('SUM(log_donate_trade.money_remove) as summ')
                ->where('users.promocode = \'' . $item['promocode'] . '\'')
                ->leftJoin('users ON log_donate_trade.user_id = users.id')
                ->groupBy('log_donate_trade.id')
                ->executeQuery()
                ->getResult()
            ;

            $allSum = 0;
            foreach ($parthUserAcLogSum as $sum)
                $allSum += reset($sum);

            if ($userInfo['admin_level'] < 5)
                $allSum = 0;

            echo '
                <tr>
                    <td>' . ($count++) . '</td>
                    <td>' . $item['c'] . ' (' . reset($parthUserList2) . ')</td>
                    <td>' . $item['promocode'] . '</td>
                    <td>' . number_format($allSum) . 'p.</td>
              </tr>
            ';
        }

        echo '</tbody>
							      </table>
							      </div>
							      </div>
	                            ';
    }

    echo '</div>
                    </div>
                </div>
            ';
}