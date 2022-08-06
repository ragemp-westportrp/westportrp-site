<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
global $userInfo;
global $server;
global $page;
global $logList;

$isEdit = false;
$logName = '';

foreach ($logList as $item) {
    if ($item[1] == $page['admin/editor/insert-'] && $userInfo['admin_level'] >= $item[2]) {
        $logName = $item[0];
        if ($item[4] != 0 && $userInfo['admin_level'] >= $item[4])
            $isEdit = true;
    }
}

if (!$userInfo['allow_acc'] || !$isEdit) {

    echo '
        <div class="container">
            <div class="section">
                <h4 class="grey-text">Нет доступа</h4>
            </div>
        </div>
    ';


    return;
}

$table = $server->charsString($page['admin/editor/insert-']);

$result = $qb
    ->createQueryBuilder($table)
    ->selectSql()
    ->limit(1)
    ->executeQuery()
    ->getSingleResult()
;
?>

<form method="post" class="container">
    <div class="section">
        <div class="row">
            <div class="col s12">
                <h4 class="grey-text"><?php echo $table ?> <button name="insert-table-row" class="btn right green accent-4 waves-effect">Добавить</button></h4>

                <input type="hidden" name="table" value="<?php echo $table ?>">
                <table class="striped">
                    <thead>
                    <tr>
                        <th>Key</th>
                        <th>Value</th>
                    </tr>
                    </thead>

                    <tbody>

                    <?php

                    if ($userInfo['allow_acc_edit']) {
                        foreach ($result as $key => $value) {
                            if ($key != 'id') {
                                echo '<tr>';
                                echo '<td>' . $key . '</td>';
                                echo '<td><input type="text" name="val-' . $key . '"></td>';
                                echo '</tr>';
                            }
                        }
                    }

                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>