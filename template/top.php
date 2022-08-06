<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $user;
global $qb;
global $userInfo;

?>

<div class="row">
    <div class="col s12 l3">
        <ul class="tabs wb z-depth-1" style="height: 914px">
            <li class="tab col s12"><a class="bw-text" href="#money">Самые богатые</a></li>
            <li class="tab col s12"><a class="bw-text" href="#ingame">Самые активные</a></li>
            <li class="tab col s12"><a class="bw-text" href="#work">Работяги</a></li>
            <li class="tab col s12"><a class="bw-text" href="#capt">Выигранных каптов</a></li>
            <li class="tab col s12"><a class="bw-text" href="#captn">Выигранных нарковойн</a></li>
            <li class="tab col s12"><a class="bw-text" href="#kill">Убийств</a></li>
            <li class="tab col s12"><a class="bw-text" href="#death">Смертей</a></li>
            <li class="tab col s12"><a class="bw-text" href="#jail">Посиделок в тюрьме</a></li>
            <li class="tab col s12"><a class="bw-text" href="#crime">Преступлений</a></li>
            <li class="tab col s12"><a class="bw-text" href="#racers">MMR - Гонки</a></li>
            <li class="tab col s12"><a class="bw-text" href="#duels">MMR - Дуэли</a></li>
            <li class="tab col s12"><a class="bw-text" href="#ctatm">Контракты - Банкоматы</a></li>
            <li class="tab col s12"><a class="bw-text" href="#ctatd">Контракты - Закладки</a></li>
            <li class="tab col s12"><a class="bw-text" href="#ctatl">Контракты - Грузы</a></li>
            <li class="tab col s12"><a class="bw-text" href="#ddrive">Расстояние - Транспорт</a></li>
            <li class="tab col s12"><a class="bw-text" href="#dfly">Расстояние - В воздухе</a></li>
            <li class="tab col s12"><a class="bw-text" href="#dswim">Расстояние - В воде</a></li>
            <li class="tab col s12"><a class="bw-text" href="#dwalk">Расстояние - Пешком</a></li>
            <li class="tab col s12"><a class="bw-text" href="#drun">Расстояние - Бегом</a></li>
        </ul>
    </div>
    <div class="col s12 l9" id="duels">
        <table class="highlight responsive-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Ник</th>
                <th>MMR</th>
                <th>Побед</th>
                <th>Всего</th>
                <th>KD</th>
            </tr>
            </thead>

            <tbody>
            <?php

            $users = $qb
                ->createQueryBuilder('users')
                ->selectSql()
                ->orderBy('rating_duel_mmr DESC, rating_duel_win DESC, rating_duel_count DESC')
                ->limit(100)
                ->executeQuery()
                ->getResult()
            ;
            $count = 0;
            foreach ($users as $item) {
                echo '
                <tr>
                    <td>' . ($count + 1) . '.</td>
                    <td>' .  $item['name'] . '</td>
                    <td>' . $item['rating_duel_mmr'] . '</td>
                    <td>' . $item['rating_duel_win'] . '</td>
                    <td>' . $item['rating_duel_count'] . '</td>
                    <td>' . round($item['rating_duel_win'] / $item['rating_duel_count'], 2) . '%</td>
                </tr>';

                $count++;
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="col s12 l9" id="racers">
        <table class="highlight responsive-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Ник</th>
                <th>MMR</th>
                <th>Побед</th>
                <th>Всего</th>
                <th>KD</th>
            </tr>
            </thead>

            <tbody>
            <?php

            $users = $qb
                ->createQueryBuilder('users')
                ->selectSql()
                ->orderBy('rating_racer_mmr DESC, rating_racer_win DESC, rating_racer_count DESC')
                ->limit(100)
                ->executeQuery()
                ->getResult()
            ;
            $count = 0;
            foreach ($users as $item) {
                echo '
                <tr>
                    <td>' . ($count + 1) . '.</td>
                    <td>' .  $item['name'] . '</td>
                    <td>' . $item['rating_racer_mmr'] . '</td>
                    <td>' . $item['rating_racer_win'] . '</td>
                    <td>' . $item['rating_racer_count'] . '</td>
                    <td>' . round($item['rating_racer_win'] / $item['rating_racer_count'], 2) . '%</td>
                </tr>';

                $count++;
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="col s12 l9" id="work">
        <table class="highlight responsive-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Ник</th>
                <th>Lvl</th>
                <th>Exp</th>
            </tr>
            </thead>

            <tbody>
            <?php

            $users = $qb
                ->createQueryBuilder('users')
                ->selectSql()
                ->orderBy('work_lvl DESC, work_exp DESC')
                ->limit(100)
                ->executeQuery()
                ->getResult()
            ;
            $count = 0;
            foreach ($users as $item) {

                //<td>' . round($item['online_contall'] * 8.5 / 60, 2) . 'ч</td>

                echo '
                <tr>
                    <td>' . ($count + 1) . '.</td>
                    <td>' .  $item['name'] . '</td>
                    <td>' . $item['work_lvl'] . '</td>
                    <td>' . $item['work_exp'] . '</td>
                </tr>';

                $count++;
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="col s12 l9" id="ingame">
        <table class="highlight responsive-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Ник</th>
                <th>Часов</th>
            </tr>
            </thead>

            <tbody>
            <?php

            $users = $qb
                ->createQueryBuilder('users')
                ->selectSql('name, online_time')
                ->orderBy('online_time DESC')
                ->limit(100)
                ->executeQuery()
                ->getResult()
            ;
            $count = 0;
            foreach ($users as $item) {

                //<td>' . round($item['online_contall'] * 8.5 / 60, 2) . 'ч</td>

                echo '
                <tr>
                    <td>' . ($count + 1) . '.</td>
                    <td>' .  $item['name'] . '</td>
                    <td>' .  round($item['online_time'] * 8.5 / 60, 2) . 'ч.</td>
                </tr>';

                $count++;
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="col s12 l9" id="money">
        <table class="highlight responsive-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Ник</th>
                <th>Состояние</th>
                <th>Часов</th>
            </tr>
            </thead>

            <tbody>
            <?php

            $users = $qb
                ->createQueryBuilder('users')
                ->selectSql('users.name as uname, money, money_bank, money_payday, houses.price as hprice, condos.price as cprice, stocks.price as sprice, business.price as bprice, yachts.price as yprice, online_time')
                ->orderBy('money + money_bank + money_payday + IFNULL(hprice,0) + IFNULL(cprice,0) + IFNULL(sprice,0) + IFNULL(bprice,0) + IFNULL(yprice,0) DESC')
                ->leftJoin('houses on houses.user_id = users.id LEFT JOIN condos on condos.user_id = users.id LEFT JOIN stocks on stocks.user_id = users.id LEFT JOIN business on business.user_id = users.id LEFT JOIN yachts on yachts.user_id = users.id')
                ->limit(100)
                ->where('login_date > ' . (time() - (60 * 60 * 24 * 30)))
                ->andWhere('admin_level = 0')
                ->executeQuery()
                ->getResult()
            ;
            $count = 0;
            foreach ($users as $item) {

                //<td>' . round($item['online_contall'] * 8.5 / 60, 2) . 'ч</td>

                echo '
                <tr>
                    <td>' . ($count + 1) . '.</td>
                    <td>' .  $item['uname'] . '</td>
                    <td>$' .  number_format($item['money'] + $item['money_bank'] + $item['money_payday'] + $item['hprice'] + $item['cprice'] + $item['sprice'] + $item['bprice'] + $item['yprice']) . '</td>
                    <td>' .  round($item['online_time'] * 8.5 / 60, 2) . 'ч.</td>
                </tr>';

                $count++;
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="col s12 l9" id="dswim">
        <table class="highlight responsive-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Ник</th>
                <th>Всего</th>
            </tr>
            </thead>

            <tbody>
            <?php

            $users = $qb
                ->createQueryBuilder('users')
                ->selectSql()
                ->where('admin_level = 0')
                ->orderBy('st_swim DESC, name ASC')
                ->limit(100)
                ->executeQuery()
                ->getResult()
            ;
            $count = 0;
            foreach ($users as $item) {

                //<td>' . round($item['online_contall'] * 8.5 / 60, 2) . 'ч</td>

                echo '
                <tr>
                    <td>' . ($count + 1) . '.</td>
                    <td>' .  $item['name'] . '</td>
                    <td>' .  number_format(round($item['st_swim'] / 1000, 1), 1) . 'км.</td>
                </tr>';

                $count++;
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="col s12 l9" id="drun">
        <table class="highlight responsive-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Ник</th>
                <th>Всего</th>
            </tr>
            </thead>

            <tbody>
            <?php

            $users = $qb
                ->createQueryBuilder('users')
                ->selectSql()
                ->where('admin_level = 0')
                ->orderBy('st_run DESC, name ASC')
                ->limit(100)
                ->executeQuery()
                ->getResult()
            ;
            $count = 0;
            foreach ($users as $item) {

                //<td>' . round($item['online_contall'] * 8.5 / 60, 2) . 'ч</td>

                echo '
                <tr>
                    <td>' . ($count + 1) . '.</td>
                    <td>' .  $item['name'] . '</td>
                    <td>' .  number_format(round($item['st_run'] / 1000, 1), 1) . 'км.</td>
                </tr>';

                $count++;
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="col s12 l9" id="dwalk">
        <table class="highlight responsive-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Ник</th>
                <th>Всего</th>
            </tr>
            </thead>

            <tbody>
            <?php

            $users = $qb
                ->createQueryBuilder('users')
                ->selectSql()
                ->where('admin_level = 0')
                ->orderBy('st_walk DESC, name ASC')
                ->limit(100)
                ->executeQuery()
                ->getResult()
            ;
            $count = 0;
            foreach ($users as $item) {

                //<td>' . round($item['online_contall'] * 8.5 / 60, 2) . 'ч</td>

                echo '
                <tr>
                    <td>' . ($count + 1) . '.</td>
                    <td>' .  $item['name'] . '</td>
                    <td>' .  number_format(round($item['st_walk'] / 1000, 1), 1) . 'км.</td>
                </tr>';

                $count++;
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="col s12 l9" id="dfly">
        <table class="highlight responsive-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Ник</th>
                <th>Всего</th>
            </tr>
            </thead>

            <tbody>
            <?php

            $users = $qb
                ->createQueryBuilder('users')
                ->selectSql()
                ->where('admin_level = 0')
                ->orderBy('st_fly DESC, name ASC')
                ->limit(100)
                ->executeQuery()
                ->getResult()
            ;
            $count = 0;
            foreach ($users as $item) {

                //<td>' . round($item['online_contall'] * 8.5 / 60, 2) . 'ч</td>

                echo '
                <tr>
                    <td>' . ($count + 1) . '.</td>
                    <td>' .  $item['name'] . '</td>
                    <td>' .  number_format(round($item['st_fly'] / 1000, 1), 1) . 'км.</td>
                </tr>';

                $count++;
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="col s12 l9" id="ddrive">
        <table class="highlight responsive-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Ник</th>
                <th>Всего</th>
            </tr>
            </thead>

            <tbody>
            <?php

            $users = $qb
                ->createQueryBuilder('users')
                ->selectSql()
                ->where('admin_level = 0')
                ->orderBy('st_drive DESC, name ASC')
                ->limit(100)
                ->executeQuery()
                ->getResult()
            ;
            $count = 0;
            foreach ($users as $item) {

                //<td>' . round($item['online_contall'] * 8.5 / 60, 2) . 'ч</td>

                echo '
                <tr>
                    <td>' . ($count + 1) . '.</td>
                    <td>' .  $item['name'] . '</td>
                    <td>' .  number_format(round($item['st_drive'] / 1000, 1), 1) . 'км.</td>
                </tr>';

                $count++;
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="col s12 l9" id="ctatl">
        <table class="highlight responsive-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Ник</th>
                <th>За сегодня</th>
                <th>Всего</th>
            </tr>
            </thead>

            <tbody>
            <?php

            $users = $qb
                ->createQueryBuilder('users')
                ->selectSql()
                ->where('admin_level = 0')
                ->orderBy('st_order_lamar_f DESC, name ASC')
                ->limit(100)
                ->executeQuery()
                ->getResult()
            ;
            $count = 0;
            foreach ($users as $item) {

                //<td>' . round($item['online_contall'] * 8.5 / 60, 2) . 'ч</td>

                echo '
                <tr>
                    <td>' . ($count + 1) . '.</td>
                    <td>' .  $item['name'] . '</td>
                    <td>' .  $item['st_order_lamar_d'] . '</td>
                    <td>' .  $item['st_order_lamar_f'] . '</td>
                </tr>';

                $count++;
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="col s12 l9" id="ctatd">
        <table class="highlight responsive-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Ник</th>
                <th>За сегодня</th>
                <th>Всего</th>
            </tr>
            </thead>

            <tbody>
            <?php

            $users = $qb
                ->createQueryBuilder('users')
                ->selectSql()
                ->where('admin_level = 0')
                ->orderBy('st_order_drug_f DESC, name ASC')
                ->limit(100)
                ->executeQuery()
                ->getResult()
            ;
            $count = 0;
            foreach ($users as $item) {

                //<td>' . round($item['online_contall'] * 8.5 / 60, 2) . 'ч</td>

                echo '
                <tr>
                    <td>' . ($count + 1) . '.</td>
                    <td>' .  $item['name'] . '</td>
                    <td>' .  $item['st_order_drug_d'] . '</td>
                    <td>' .  $item['st_order_drug_f'] . '</td>
                </tr>';

                $count++;
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="col s12 l9" id="ctatm">
        <table class="highlight responsive-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Ник</th>
                <th>За сегодня</th>
                <th>Всего</th>
            </tr>
            </thead>

            <tbody>
            <?php

            $users = $qb
                ->createQueryBuilder('users')
                ->selectSql()
                ->where('admin_level = 0')
                ->orderBy('st_order_atm_f DESC, name ASC')
                ->limit(100)
                ->executeQuery()
                ->getResult()
            ;
            $count = 0;
            foreach ($users as $item) {

                //<td>' . round($item['online_contall'] * 8.5 / 60, 2) . 'ч</td>

                echo '
                <tr>
                    <td>' . ($count + 1) . '.</td>
                    <td>' .  $item['name'] . '</td>
                    <td>' .  $item['st_order_atm_d'] . '</td>
                    <td>' .  $item['st_order_atm_f'] . '</td>
                </tr>';

                $count++;
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="col s12 l9" id="crime">
        <table class="highlight responsive-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Ник</th>
                <th>Преступлений</th>
            </tr>
            </thead>

            <tbody>
            <?php

            $users = $qb
                ->createQueryBuilder('users')
                ->selectSql()
                ->where('admin_level = 0')
                ->orderBy('st_crime DESC, name ASC')
                ->limit(100)
                ->executeQuery()
                ->getResult()
            ;
            $count = 0;
            foreach ($users as $item) {

                //<td>' . round($item['online_contall'] * 8.5 / 60, 2) . 'ч</td>

                echo '
                <tr>
                    <td>' . ($count + 1) . '.</td>
                    <td>' .  $item['name'] . '</td>
                    <td>' .  $item['st_crime'] . '</td>
                </tr>';

                $count++;
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="col s12 l9" id="jail">
        <table class="highlight responsive-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Ник</th>
                <th>Побывал в тюрьме</th>
            </tr>
            </thead>

            <tbody>
            <?php

            $users = $qb
                ->createQueryBuilder('users')
                ->selectSql()
                ->where('admin_level = 0')
                ->orderBy('st_jail DESC, name ASC')
                ->limit(100)
                ->executeQuery()
                ->getResult()
            ;
            $count = 0;
            foreach ($users as $item) {

                //<td>' . round($item['online_contall'] * 8.5 / 60, 2) . 'ч</td>

                echo '
                <tr>
                    <td>' . ($count + 1) . '.</td>
                    <td>' .  $item['name'] . '</td>
                    <td>' .  $item['st_jail'] . '</td>
                </tr>';

                $count++;
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="col s12 l9" id="captn">
        <table class="highlight responsive-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Ник</th>
                <th>Выиграно</th>
                <th>Убийств</th>
                <th>Смертей</th>
                <th>KD</th>
            </tr>
            </thead>

            <tbody>
            <?php

            $users = $qb
                ->createQueryBuilder('users')
                ->selectSql()
                ->where('admin_level = 0 AND st_death > 0')
                ->orderBy('st_capt_m_win DESC, name ASC')
                ->limit(100)
                ->executeQuery()
                ->getResult()
            ;
            $count = 0;
            foreach ($users as $item) {

                //<td>' . round($item['online_contall'] * 8.5 / 60, 2) . 'ч</td>

                echo '
                <tr>
                    <td>' . ($count + 1) . '.</td>
                    <td>' .  $item['name'] . '</td>
                    <td>' .  $item['st_capt_m_win'] . '/' .  $item['st_capt_m'] . '</td>
                    <td>' .  $item['st_kill'] . '</td>
                    <td>' .  $item['st_death'] . '</td>
                    <td>' .  round($item['st_kill'] / $item['st_death'], 2) . '%</td>
                </tr>';

                $count++;
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="col s12 l9" id="capt">
        <table class="highlight responsive-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Ник</th>
                <th>Выиграно</th>
                <th>Убийств</th>
                <th>Смертей</th>
                <th>KD</th>
            </tr>
            </thead>

            <tbody>
            <?php

            $users = $qb
                ->createQueryBuilder('users')
                ->selectSql()
                ->where('admin_level = 0 AND st_death > 0')
                ->orderBy('st_capt_win DESC, name ASC')
                ->limit(100)
                ->executeQuery()
                ->getResult()
            ;
            $count = 0;
            foreach ($users as $item) {

                //<td>' . round($item['online_contall'] * 8.5 / 60, 2) . 'ч</td>

                echo '
                <tr>
                    <td>' . ($count + 1) . '.</td>
                    <td>' .  $item['name'] . '</td>
                    <td>' .  $item['st_capt_win'] . '/' .  $item['st_capt'] . '</td>
                    <td>' .  $item['st_kill'] . '</td>
                    <td>' .  $item['st_death'] . '</td>
                    <td>' .  round($item['st_kill'] / $item['st_death'], 2) . '%</td>
                </tr>';

                $count++;
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="col s12 l9" id="death">
        <table class="highlight responsive-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Ник</th>
                <th>Убийств</th>
                <th>Смертей</th>
                <th>KD</th>
            </tr>
            </thead>

            <tbody>
            <?php

            $users = $qb
                ->createQueryBuilder('users')
                ->selectSql()
                ->where('admin_level = 0')
                ->orderBy('st_death DESC, name ASC')
                ->limit(100)
                ->executeQuery()
                ->getResult()
            ;
            $count = 0;
            foreach ($users as $item) {

                //<td>' . round($item['online_contall'] * 8.5 / 60, 2) . 'ч</td>

                echo '
                <tr>
                    <td>' . ($count + 1) . '.</td>
                    <td>' .  $item['name'] . '</td>
                    <td>' .  $item['st_kill'] . '</td>
                    <td>' .  $item['st_death'] . '</td>
                    <td>' .  round($item['st_kill'] / $item['st_death'], 2) . '%</td>
                </tr>';

                $count++;
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="col s12 l9" id="kill">
        <table class="highlight responsive-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Ник</th>
                <th>Убийств</th>
                <th>Смертей</th>
                <th>KD</th>
            </tr>
            </thead>

            <tbody>
            <?php

            $users = $qb
                ->createQueryBuilder('users')
                ->selectSql()
                ->where('admin_level = 0')
                ->orderBy('st_kill DESC, name ASC')
                ->limit(100)
                ->executeQuery()
                ->getResult()
            ;
            $count = 0;
            foreach ($users as $item) {

                //<td>' . round($item['online_contall'] * 8.5 / 60, 2) . 'ч</td>

                echo '
                <tr>
                    <td>' . ($count + 1) . '.</td>
                    <td>' .  $item['name'] . '</td>
                    <td>' .  $item['st_kill'] . '</td>
                    <td>' .  $item['st_death'] . '</td>
                    <td>' .  round($item['st_kill'] / $item['st_death'], 2) . '%</td>
                </tr>';

                $count++;
            }
            ?>
            </tbody>
        </table>
    </div>
</div>