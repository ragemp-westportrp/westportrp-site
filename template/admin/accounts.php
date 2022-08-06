<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
global $userInfo;
global $server;
global $accountAllowColumns;

if (!$userInfo['allow_acc']) {

    echo '
        <div class="container">
            <div class="section">
                <h4 class="grey-text">Нет доступа</h4>
            </div>
        </div>
    ';


    return;
}

if (!isset($_GET['q'])) {

    echo '
        <div class="container">
            <div class="section">
                <h4 class="grey-text">Для начала сделайте запрос через глобальный поиск</h4>
                <form>
                    <input type="search" class="input-search" placeholder="Глобальный поиск" name="q">
                </form>
            </div>
        </div>
    ';

    return;
}

$where = $server->charsString($_GET['q']);

$result = $qb
    ->createQueryBuilder('accounts')
    ->selectSql()
    ->where('login = \'' . $where . '\'')
    ->orWhere('social = \'' . $where . '\'')
    ->orWhere('serial = \'' . $where . '\'')
    ->orWhere('email = \'' . $where . '\'')
    ->orWhere('reg_ip = \'' . $where . '\'')
    ->orWhere('id = \'' . $where . '\'')
    ->executeQuery()
    ->getSingleResult()
;


$adminList = [];
if ($userInfo['admin_level'] < 5) {
    $result = $qb->createQueryBuilder('users')->selectSql('social')->where('admin_level > 4')->executeQuery()->getResult();
    foreach ($result as $item) {
        array_push($adminList, $item['social']);
    }

    if (in_array($result['social'], $adminList))
        $result = null;
}

?>

<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12">
                <h4 class="grey-text"><?php echo $result['login'] ?> (<?php echo $result['id'] ?>)</h4>
                <form>
                    <input type="search" class="input-search" value="<?php echo isset($_GET['q']) ? $_GET['q'] : '' ?>" placeholder="Глобальный поиск" name="q">
                </form>
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
                        if (in_array($key, $accountAllowColumns)) {

                            echo '<tr>';
                            echo '<form method="post">';
                            echo '<input type="hidden" name="key" value="' . $key . '">';
                            echo '<input type="hidden" name="oldv" value="' . $value . '">';
                            echo '<input type="hidden" name="id" value="' . $result['id'] . '">';
                            echo '<td>' . $key . '</td>';
                            echo '<td><input type="text" name="val" value="' . $value . '"></td>';
                            echo '<td><button name="edit-acc-column" class="btn btn-floating z-depth-0 waves-effect blue accent-4 right"><i class="material-icons">edit</i></button></td>';
                            echo '</form>';
                            echo '</tr>';
                        }
                    }
                }
                else {
                    foreach ($result as $key => $value) {
                        if (in_array($key, $accountAllowColumns)) {

                            echo '<tr>';
                            echo '<td>' . $key . '</td>';
                            echo '<td>' . $value . '</td>';
                            echo '<td></td>';
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
</div>