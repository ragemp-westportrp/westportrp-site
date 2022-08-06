<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
global $user;
global $tmp;
global $monthN;

$localQb = $qb;

$banList = $localQb
    ->createQueryBuilder('ban_list')
    ->selectSql()
    ->orderBy('id DESC')
    ->limit(1000)
    ->executeQuery()
    ->getResult()
;
?>

<div class="row">
    <div class="col s12">
        <div class="card-panel">
            <table class="highlight responsive-table">
                <thead>
                <tr>
                    <th data-field="date">Дата</th>
                    <th data-field="name_from">Администратор</th>
                    <th data-field="name_to">Нарушитель</th>
                    <th data-field="count">Кол-во</th>
                    <th data-field="reason">Причина</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($banList as $item) {
                    echo '
                        <tr>
                            <td class="grey-text">' . gmdate("H:s, d ", $item['datetime'] + 3600 * 3) . $monthN[gmdate("m", $item['datetime'] + 3600 * 3)] . '</td>
                            <td>' . $item['ban_from'] . '</td>
                            <td>' . $item['ban_to'] . '</td>
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
</div>

