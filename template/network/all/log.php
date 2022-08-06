<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
global $server;
global $fractionId;

$text = '';
if(isset($_GET['q'])) {
    $text = $server->charsString($_GET['q']);
    $logRows = $qb
        ->createQueryBuilder('log_fraction')
        ->selectSql()
        ->where('fraction_id = ' . $fractionId)
        ->andWhere('(name LIKE \'%' . $text . '%\' OR text LIKE \'%' . $text . '%\' OR text2 LIKE \'%' . $text . '%\')')
        ->orderBy('id DESC')
        ->executeQuery()
        ->getResult()
    ;
}
else
    $logRows = $qb->createQueryBuilder('log_fraction')->selectSql()->where('fraction_id = ' . $fractionId)->limit(2500)->orderBy('id DESC')->executeQuery()->getResult();

?>

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
                <th>Имя</th>
                <th>Действие</th>
                <th>Описание</th>
                <th>Время</th>
            </tr>
            </thead>
            <tbody>';
                $count = 0;
                foreach ($logRows as $item) {
                    echo '
                    <tr>
                        <td>' . (++$count) . '</td>
                        <td>' . $item['name'] . '</td>
                        <td>' . $item['text'] . '</td>
                        <td>' . $item['text2'] . '</td>
                        <td>' . gmdate("d-m-Y, H:i", $item['timestamp'] + (3600 * 3)) . '</td>
                    </tr>';
                }
                echo '
            </tbody>
        </table>
    </div>
';

?>