<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $userInfo;
global $logList;
global $page;
global $qb;
global $server;

$isSuccess = false;
$isEdit = false;
$isDel = false;
$isInsert = false;
$logName = '';

$adminList = [];
if ($userInfo['admin_level'] < 5) {
    $result = $qb->createQueryBuilder('users')->selectSql('social')->where('admin_level > 4')->executeQuery()->getResult();
    foreach ($result as $item) {
        array_push($adminList, $item['social']);
    }
}

foreach ($logList as $item) {
    if ($item[1] == $page['admin/log-'] && $userInfo['admin_level'] >= $item[2]) {
        $logName = $item[0];
        $isSuccess = true;

        if ($item[3] != 0 && $userInfo['admin_level'] >= $item[3])
            $isEdit = true;
        if ($item[4] != 0 && $userInfo['admin_level'] >= $item[4])
            $isInsert = true;
        if ($item[4] != 0 && $userInfo['admin_level'] >= $item[4])
            $isDel = true;
    }
}

if (!$isSuccess) {

    echo '
        <div class="container">
            <div class="section">
                <h4 class="grey-text">Нет доступа</h4>
            </div>
        </div>
    ';

    return;
}
$countItems = $qb->createQueryBuilder($page['admin/log-'])->selectSql('COUNT(*) as count')->executeQuery()->getSingleResult();

$columnsResult = $qb->createQueryBuilder($page['admin/log-'])->selectSql()->executeQuery()->getSingleResult();
$columns = [];

foreach ($columnsResult as $key => $value) {
    array_push($columns, $key);
}

$sort = 'DESC';
$limit = 100;

if (isset($_GET['sort']) && $_GET['sort'] == 2)
    $sort = 'ASC';
if (isset($_GET['limit']))
    $limit = intval($_GET['limit']);

$result = [];
if (isset($_GET['q']))
{
    $q = $server->charsString($_GET['q']);
    $query = $qb
        ->createQueryBuilder($page['admin/log-'])
        ->selectSql()
        ->limit($limit)
        ->orderBy('id ' . $sort)
    ;

    $isFirst = true;
    $qbExplodeOr = explode(' OR ', $q);
    $qbExplodeAnd = explode(' AND ', $q);

    if (count($qbExplodeAnd) > 1) {
        foreach ($qbExplodeAnd as $explode) {

            $qbExplode = explode('==', $explode);
            $qbExplode2 = explode('===', $explode);
            $qbExplode3 = explode('=+', $explode);
            $qbExplode4 = explode('=-', $explode);

            if (count($qbExplode2) > 1) {
                if ($isFirst)
                    $query->where($qbExplode2[0] . ' = \'' . $qbExplode2[1] . '\'');
                else
                    $query->andWhere($qbExplode2[0] . ' = \'' . $qbExplode2[1] . '\'');
                $isFirst = false;
            }
            else if (count($qbExplode3) > 1) {
                if ($isFirst)
                    $query->where($qbExplode3[0] . ' > \'' . $qbExplode3[1] . '\'');
                else
                    $query->andWhere($qbExplode3[0] . ' > \'' . $qbExplode3[1] . '\'');
                $isFirst = false;
            }
            else if (count($qbExplode4) > 1) {
                if ($isFirst)
                    $query->where($qbExplode4[0] . ' < \'' . $qbExplode4[1] . '\'');
                else
                    $query->andWhere($qbExplode4[0] . ' < \'' . $qbExplode4[1] . '\'');
                $isFirst = false;
            }
            else if (count($qbExplode) > 1) {
                if ($isFirst)
                    $query->where($qbExplode[0] . ' LIKE \'%' . $qbExplode[1] . '%\'');
                else
                    $query->andWhere($qbExplode[0] . ' LIKE \'%' . $qbExplode[1] . '%\'');
                $isFirst = false;
            }
        }
    }
    else if (count($qbExplodeOr) > 1) {
        foreach ($qbExplodeOr as $explode) {

            $qbExplode = explode('==', $explode);
            $qbExplode2 = explode('===', $explode);
            $qbExplode3 = explode('=+', $explode);
            $qbExplode4 = explode('=-', $explode);

            if (count($qbExplode2) > 1) {
                if ($isFirst)
                    $query->where($qbExplode2[0] . ' = \'' . $qbExplode2[1] . '\'');
                else
                    $query->orWhere($qbExplode2[0] . ' = \'' . $qbExplode2[1] . '\'');
                $isFirst = false;
            }
            else if (count($qbExplode3) > 1) {
                if ($isFirst)
                    $query->where($qbExplode3[0] . ' > \'' . $qbExplode3[1] . '\'');
                else
                    $query->orWhere($qbExplode3[0] . ' > \'' . $qbExplode3[1] . '\'');
                $isFirst = false;
            }
            else if (count($qbExplode4) > 1) {
                if ($isFirst)
                    $query->where($qbExplode4[0] . ' < \'' . $qbExplode4[1] . '\'');
                else
                    $query->orWhere($qbExplode4[0] . ' < \'' . $qbExplode4[1] . '\'');
                $isFirst = false;
            }
            else if (count($qbExplode) > 1) {
                if ($isFirst)
                    $query->where($qbExplode[0] . ' LIKE \'%' . $qbExplode[1] . '%\'');
                else
                    $query->orWhere($qbExplode[0] . ' LIKE \'%' . $qbExplode[1] . '%\'');
                $isFirst = false;
            }
        }
    }
    else {
        $qbExplode = explode('==', $q);
        $qbExplode2 = explode('===', $q);
        $qbExplode3 = explode('=+', $q);
        $qbExplode4 = explode('=-', $q);

        if (count($qbExplode2) > 1) {
            $query->where($qbExplode2[0] . ' = \'' . $qbExplode2[1] . '\'');
        }
        else if (count($qbExplode3) > 1) {
            $query->where($qbExplode3[0] . ' > \'' . $qbExplode3[1] . '\'');
        }
        else if (count($qbExplode4) > 1) {
            $query->where($qbExplode4[0] . ' < \'' . $qbExplode4[1] . '\'');
        }
        else if (count($qbExplode) > 1) {
            $query->where($qbExplode[0] . ' LIKE \'%' . $qbExplode[1] . '%\'');
        }
        else {
            foreach ($columns as $item) {
                if ($item === 'datetime' || $item === 'timestamp')
                    continue;
                if ($isFirst)
                    $query->where($item . ' LIKE \'%' . $q . '%\'');
                else
                    $query->orWhere($item . ' LIKE \'%' . $q . '%\'');
                $isFirst = false;
            }
        }
    }


    $result = $query->executeQuery()->getResult();
}
else
    $result = $qb->createQueryBuilder($page['admin/log-'])->selectSql()->limit($limit)->orderBy('id ' . $sort)->executeQuery()->getResult();
?>

<style>
    .dataTables_length {

    }

    .dataTables_filter input[type="search"] {
        height: auto !important;
    }

    .paginate_button {
        border: none !important;
        background: #EEEEEE !important;
    }
    .paginate_button.current {
        color: white !important;
        background: #80CBC4 !important;
    }

    .dataTables_wrapper .select-wrapper input.select-dropdown {
        line-height: 1rem;
        height: 1rem;
        text-align: center;
    }

    .dataTables_wrapper .select-wrapper {
        position: inherit;
        display: table-cell;
        width: 50px;
    }

    .dataTables_filter label {
        display: flex;
    }
</style>

<script>
    $(document).ready( function () {
        $('#myTable').DataTable({
            "lengthMenu": [[50, 100, 500, -1], [50, 100, 500, "All"]],
            "order": [[ 0, "desc" ]]
        });
    } );
</script>

<div class="container">
    <div class="section">
        <h5 class="grey-text"><?php echo $logName ?> (Всего <?php echo number_format(reset($countItems)) ?> строк)</h5>
        <form class="row bw-text">
            <div class="col s12">
                <input type="search" class="input-search" value="<?php echo isset($_GET['q']) ? $_GET['q'] : '' ?>" placeholder="Глобальный поиск" name="q">
            </div>
            <div class="input-field col s6 m6 l3">
                <select class="browser-default" name="sort">
                    <option value="1">С конца</option>
                    <option <?php echo ($sort == 'ASC' ? 'selected' : '') ?> value="2">С начала</option>
                </select>
            </div>
            <div class="input-field col s6 m6 l3">
                <input id="number" required name="limit" value="<?php echo $limit ?>" type="number" class="validate">
                <label for="number">Строк для вывода</label>
            </div>
            <div class="col s12">

                <button class="btn blue accent-4 waves-effect z-depth-0">Применить</button>
            </div>
        </form>
    </div>
</div>
<div style="padding: 24px">
    <table id="myTable" class="striped responsive-table card" style="overflow-x: auto">
        <thead>
        <tr>

            <?php
            foreach ($columns as $item)
                echo '<th>' . $item . '</th>';

            if ($isInsert) {
                echo '<th></th>';
            }
            if ($isEdit) {
                echo '<th></th>';
            }
            if ($isDel) {
                echo '<th></th>';
            }
            ?>
        </tr>
        </thead>

        <tbody>

        <?php

        foreach ($result as $item) {
            $isShow = true;
            $echo = '<tr>';
            foreach ($item as $key => $value) {

                if ($userInfo['admin_level'] < 5 && ($key == 'password' || $key == 'email'))

                if (in_array($value, $adminList))
                    $isShow = false;
                $echo .= '<td>' . $value . '</td>';
            }

            if ($isInsert) {
                $echo .= '<td><a class="btn btn-floating green accent-4 z-depth-0" href="/admin/editor/insert-' . $page['admin/log-'] . '"><i class="material-icons">add</i></a></td>';
            }
            if ($isEdit) {
                $echo .= '<td><a class="btn btn-floating blue accent-4 z-depth-0" href="/admin/editor/edit-' . $page['admin/log-'] . '?id='  . $item['id'] . '"><i class="material-icons">edit</i></a></td>';
            }
            if ($isDel) {
                $echo .= '<td><a class="btn btn-floating red accent-4 z-depth-0" href="/admin/editor/delete-' . $page['admin/log-'] . '?id='  . $item['id'] . '"><i class="material-icons">delete</i></a></td>';
            }
            $echo .= '</tr>';

            if ($isShow)
                echo $echo;

            /*echo '<tr>';
            foreach ($item as $key => $value) {
                echo '<td>' . $value . '</td>';
            }
            if ($isInsert) {
                echo '<td><a class="btn btn-floating green accent-4 z-depth-0" href="/admin/editor/insert-' . $page['admin/log-'] . '"><i class="material-icons">add</i></a></td>';
            }
            if ($isEdit) {
                echo '<td><a class="btn btn-floating blue accent-4 z-depth-0" href="/admin/editor/edit-' . $page['admin/log-'] . '?id='  . $item['id'] . '"><i class="material-icons">edit</i></a></td>';
            }
            if ($isDel) {
                echo '<td><a class="btn btn-floating red accent-4 z-depth-0" href="/admin/editor/delete-' . $page['admin/log-'] . '?id='  . $item['id'] . '"><i class="material-icons">delete</i></a></td>';
            }
            echo '</tr>';*/
        }

        ?>
        </tbody>
    </table>
</div>