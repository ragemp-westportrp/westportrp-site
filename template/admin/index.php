<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $logList;
global $userInfo;
global $qb;


$rules = $qb->createQueryBuilder('rules')->selectSql()->executeQuery()->getResult();
?>

<div class="container">
    <div class="section">
        <?php

        echo '<div class="row">';
        echo '<div class="col s12">';
        if ($userInfo['allow_stats'] > 0) {
            echo '<a href="/admin/stats" class="btn waves-effect blue accent-4">Статистика</a>';
        }
        if ($userInfo['allow_acc'] > 0) {
            echo '<a href="/admin/users" class="btn waves-effect blue accent-4">Персонажи</a>';
            echo '<a href="/admin/accounts" class="btn waves-effect blue accent-4">Аккаунты</a>';
        }
        echo '</div>';
        if ($userInfo['admin_level'] >= 4) {
            echo '
                <div class="col s12 m4">
                    <h5 class="grey-text wd-font">Добавить промокод</h5>
                    <form method="post" class="card-panel">
                        <div class="input-field" style="margin: 0">
                            <input placeholder="Специальный промокод" type="text" name="promo" required="">
                        </div>
                        <div class="input-field" style="margin: 0">
                            <button class="btn z-depth-0 blue accent-4" name="add-promo-spec">Добавить</button>
                        </div>
                    </form>
                </div>
            ';
        }
        echo '
                <div class="col s12 m4">
                    <h5 class="grey-text wd-font">Поиск аккаунта</h5>
                    <form method="get" action="/account-info-0" class="card-panel">
                        <div class="input-field" style="margin: 0">
                            <input placeholder="ID" type="number" name="id" required="">
                        </div>
                        <div class="input-field" style="margin: 0">
                            <button class="btn z-depth-0 blue accent-4" value="true" name="check-id">Применить</button>
                        </div>
                    </form>
                </div>
            </div>
            ';
        ?>
        <div class="row">
            <div class="col s12">
                <h5 class="grey-text wd-font">Список доступных таблиц</h5>
                <table class="bw-text highlight responsive-table">
                    <tbody>
                    <?php

                    $idx = 0;

                    foreach ($logList as $item) {

                        if ($userInfo['admin_level'] < $item[2])
                            continue;

                        $idx++;

                        echo '
                            <tr>
                                <td>' . $idx . '.</td>
                                <td>' . $item[0] . '</td>
                                <td><a class="btn btn-small waves-effect blue accent-4" style="width: 100%" href="/admin/log-' . $item[1] . '">Открыть</a></td>
                            </tr>
                          
                        ';
                    }

                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col s12">
                <h5 class="grey-text wd-font">Админы</h5>
                <table class="bw-text highlight responsive-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ник</th>
                            <th>Уровень</th>
                            <th>В игре</th>
                            <th>На сайте</th>
                            <th>За сегодня</th>
                            <th>Последний вход</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php

                    $idx = 0;
                    $list = $qb->createQueryBuilder('users')->selectSql()->where('admin_level > 0')->andWhere('status_rp = 0')->orderBy('is_online DESC, admin_level DESC, count_aask DESC')->executeQuery()->getResult();
                    foreach ($list as $item) {
                        $idx++;
                        $countAll = $qb->createQueryBuilder('report_user_answer')->selectSql('COUNT(*) as count')->where('social_from = \'' . $item['social'] . '\'')->executeQuery()->getSingleResult();
                        echo '
                            <tr>
                                <td>' . $idx . '.</td>
                                <td>' . $item['name'] . ' ' . ($item['is_online'] ? '<span class="green-text">*</span>' : '<span class="red-text">*</span>') . '</td>
                                <td>' . $item['admin_level'] . '</td>
                                <td>' . $item['count_aask'] . '</td>
                                <td>' . reset($countAll) . '</td>
                                <td>' . round($item['online_cont'] * 8.5 / 60, 2) . 'ч.</td>
                                <td><span class="grey-text">' . gmdate('H:i d-m-Y', $item['login_date'] + 3600 * 3) . '</span></td>
                            </tr>
                        ';
                    }

                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col s12">
                <h5 class="grey-text wd-font">Хелперы</h5>
                <table class="bw-text highlight responsive-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ник</th>
                            <th>Уровень</th>
                            <th>Ответов</th>
                            <th>За сегодня</th>
                            <th>Последний вход</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php

                    $idx = 0;
                    $list = $qb->createQueryBuilder('users')->selectSql()->where('helper_level > 0')->andWhere('admin_level = 0')->orderBy('is_online DESC, helper_level DESC, count_hask DESC')->executeQuery()->getResult();
                    foreach ($list as $item) {
                        $idx++;
                        echo '
                            <tr>
                                <td>' . $idx . '.</td>
                                <td>' . $item['name'] . ' ' . ($item['is_online'] ? '<span class="green-text">*</span>' : '<span class="red-text">*</span>') . '</td>
                                <td>' . $item['helper_level'] . '</td>
                                <td>' . $item['count_hask'] . '</td>
                                <td>' . round($item['online_cont'] * 8.5 / 60, 2) . 'ч.</td>
                                <td><span class="grey-text">' . gmdate('H:i d-m-Y', $item['login_date'] + 3600 * 3) . '</span></td>
                            </tr>
                        ';
                    }

                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
        if ($userInfo['allow_acc']) {
            ?>
            <div class="row">
                <div class="col s12">
                    <?php

                    foreach ($rules as $item)
                    {
                        echo '
                            <div class="card-panel">
                                <div class="row">
                                    <form method="post" class="input-field col s12">
                                        <textarea id="textarea1" name="text" class="materialize-textarea">' . htmlspecialchars_decode($item['text']) . '</textarea>
                                        <label for="textarea1">' . $item['name'] . '</label>
                                        <input type="hidden" name="id" value="' . $item['id'] . '">
                                        <button name="save-rules" class="btn btn-large wd-font waves-effect blue accent-4">Сохранить</button>
                                    </form>
                                </div>
                            </div>
                        ';
                    }

                    ?>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>
