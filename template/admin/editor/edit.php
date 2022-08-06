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
    if ($item[1] == $page['admin/editor/edit-'] && $userInfo['admin_level'] >= $item[2]) {
        $logName = $item[0];
        if ($item[3] != 0 && $userInfo['admin_level'] >= $item[3])
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

if (!isset($_GET['id'])) {

    echo '
        <div class="container">
            <div class="section">
                <h4 class="grey-text">Ошибочка, не передана строка</h4>
            </div>
        </div>
    ';

    return;
}

$table = $server->charsString($page['admin/editor/edit-']);

$result = $qb
    ->createQueryBuilder($table)
    ->selectSql()
    ->where('id = \'' . intval($_GET['id']) . '\'')
    ->executeQuery()
    ->getSingleResult()
;
?>

<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12">

                <h4 class="grey-text"><?php echo $table ?> (<?php echo $_GET['id'] ?>)</h4>
                <table class="striped">
                    <thead>
                    <tr>
                        <th>Key</th>
                        <th>Value</th>
                        <th></th>
                    </tr>
                    </thead>

                    <tbody>

                    <?php

                    if ($userInfo['allow_acc_edit']) {
                        foreach ($result as $key => $value) {

                            if ($logName == 'accounts' && $key == 'money_donate') continue;

                            if ($key != 'id') {
                                echo '<tr>';
                                echo '<form method="post">';
                                echo '<input type="hidden" name="key" value="' . $key . '">';
                                echo '<input type="hidden" name="oldv" value="' . $value . '">';
                                echo '<input type="hidden" name="id" value="' . $result['id'] . '">';
                                echo '<input type="hidden" name="table" value="' . $table . '">';
                                echo '<td>' . $key . '</td>';
                                echo '<td><input type="text" name="val" value="' . $value . '"></td>';
                                echo '<td><button name="edit-table-column" class="btn btn-floating z-depth-0 waves-effect blue accent-4 right"><i class="material-icons">edit</i></button></td>';
                                echo '</form>';
                                echo '</tr>';
                            }
                        }
                    }
                    else {
                        foreach ($result as $key => $value) {
                            echo '<tr>';
                            echo '<td>' . $key . '</td>';
                            echo '<td>' . $value . '</td>';
                            echo '<td></td>';
                            echo '</tr>';
                        }
                    }

                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>