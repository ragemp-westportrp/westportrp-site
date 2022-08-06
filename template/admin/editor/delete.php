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
    if ($item[1] == $page['admin/editor/delete-'] && $userInfo['admin_level'] >= $item[2]) {
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

$table = $server->charsString($page['admin/editor/delete-']);

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
            <div class="col s12 m6 l3">
            </div>
            <div class="col s12 m6 l6">
                <h4 class="grey-text"><?php echo $table ?> (<?php echo $_GET['id'] ?>)</h4>
                <form method="post" action="/admin/main" class="card-panel">
                    Вы точно хотите удалить строку под номером <?php echo $_GET['id'] ?>?
                    <br>
                    <br>
                    <div>
                        <a href="/admin/main" class="btn waves-effect blue accent-4 z-depth-0">Отмена</a>
                        <input type="hidden" name="table" value="<?php echo $table ?>">
                        <input type="hidden" name="id" value="<?php echo intval($_GET['id']) ?>">
                        <button name="delete-table-row" class="btn waves-effect red accent-4 z-depth-0">Удалить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>