<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $user;
global $qb;
global $userInfo;
?>

<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12 m12">
                <h3 class="wd-font bw-text">Голосование за губернатора штата</h3>
                <table class="highlight responsive-table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Кандидат</th>
                        <th>Партия</th>
                        <th>Голосов</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php

                    $qb
                        ->createQueryBuilder('')
                        ->otherSql("set session sql_mode=''", false)
                        ->executeQuery()
                        ->getResult()
                    ;

                    $voteList = $qb
                        ->createQueryBuilder('rp_gov_vote rgv')
                        ->selectSql('*, count(rgv.name) as n')
                        ->groupBy("rgv.name HAVING n <> 0")
                        ->executeQuery()
                        ->getResult()
                    ;

                    $count = 0;
                    $countAll = 0;


                    foreach ($voteList as $item) {
                        $countAll += ($item['n'] - 1);
                    }

                    //(20 * 100) / 300
                    foreach ($voteList as $item) {

                        echo '
                        <tr>
                        <td>' . (++$count) . '</td>
                        <td>' . $item['name'] . '</td>
                        <td>' . $item['company'] . '</td>
                        <td>' . round((($item['n'] - 1) * 100) / $countAll, 2) . '%</td>
                        <td>';
                            if($user->isLogin()) {
                                echo '
                                <form method="post">
                                    <input type="hidden" name="name" value="' . $item['name'] . '">
                                    <button class="btn z-depth-0 waves-effect right blue accent-4" name="send_vote">
                                        Голосовать
                                    </button>
                                </form>';
                            } else {
                                echo '
                                <a href="/login" class="btn z-depth-0 waves-effect right blue accent-4">
                                    Войти
                                </a>';
                            }
                        echo '</td>
                        </tr>';
                    }
                    ?>
                    </tbody>
                </table>
                <h3 class="wd-font bw-text">Список голосовавших</h3>
                <table class="highlight responsive-table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>ID аккаунта</th>
                        <th>Кандидат</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php

                    $voteList2 = $qb
                        ->createQueryBuilder('rp_gov_vote')
                        ->selectSql()
                        ->where("user_id > 0")
                        ->orderBy('id DESC')
                        ->executeQuery()
                        ->getResult()
                    ;

                    $count = count($voteList2);


                    //(20 * 100) / 300
                    foreach ($voteList2 as $item) {

                        echo '
                        <tr>
                        <td>' . ($count--) . '.</td>
                        <td>ID: ' . ($item['user_id'] === $userInfo['id'] ? $item['user_id'] . ' (Это ты)' : $item['user_id']  . '') . '</td>
                        <td>' . $item['name'] . '</td>
                       
                        </tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>