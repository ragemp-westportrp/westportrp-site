<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
global $user;
global $ranks;
global $fractionId;


$userList = $qb->createQueryBuilder('users')->selectSql()->where('fraction_id = ' . $fractionId)->orderBy('is_leader DESC, is_sub_leader DESC, is_online DESC, login_date DESC')->executeQuery()->getResult();


?>
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12">

                <?php

                if (empty($userList)) {
                    echo '
                    <div style="height: 50px; width: 100%; margin-bottom: 50px;">
                        <h4 class="grey-text text-darken-2">Сотрудники | Список пуст</h4>
                    </div>
                    ';
                }
                else {
                    echo '
                        <div style="height: 50px; width: 100%;">
                            <h4 class="grey-text text-darken-2">Сотрудники | Всего: ' . count($userList) . '</h4>
                        </div>
                        <div class="card-panel" style="margin-bottom: 50px;">
                            <table class="highlight">
                                <thead>
                                <tr>
                                    <th style="width: 70px">#</th>
                                    <th>Имя</th>
                                    <th>За сегодня</th>
                                    <th>Активность</th>
                                    <th>Последний вход</th>
                                    <th>Онлайн</th>
                                </tr>
                                </thead>
                                <tbody>';
                                $count = 0;
                                foreach ($userList as $item) {
                                    echo '
                                        <tr>
                                            <td><img style="width: 40px; height: 40px; border-radius: 50%" src="https://a.rsg.sc//n/' . strtolower($item['social']) . '"></td>
                                            <td>' . $item['name'] . '</td>
                                            <td>' . round($item['online_cont'] * 8.5 / 60, 2) . 'ч.</td>
                                            <td>' . round($item['online_time'] * 8.5 / 60, 2) . 'ч.</td>
                                            <td>' . date('d/m/y H:i', $item['login_date']) . '</td>
                                            <td>' . ($item['is_online'] ? '<span class="green-text">На службе</span>' : '<span class="red-text">Не на службе</span>') . '</td>
                                        </tr>';
                                }
                                echo '
                                </tbody>
                            </table>
                        </div>
                    ';
                }

                ?>
            </div>
        </div>
    </div>
</div>