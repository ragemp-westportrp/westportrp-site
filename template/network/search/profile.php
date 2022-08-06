<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $userInfo;
global $serverName;
global $search;
global $page;
global $monthN;
global $qb;

$sex = json_decode($userInfo['skin'])->SEX == 1 ? 'Женский' : 'Мужской';
$p1 = !empty($userInfo['rp_qualities']) ? $userInfo['rp_qualities'] : 'Нет';
$p2 = !empty($userInfo['rp_distinctive_features']) ? $userInfo['rp_distinctive_features'] : 'Нет';
$p3 = !empty($userInfo['rp_diseases']) ? $userInfo['rp_diseases'] : 'Нет';
$p4 = !empty($userInfo['rp_character']) ? $userInfo['rp_character'] : 'Нет';
$p5 = !empty($userInfo['rp_biography']) ? $userInfo['rp_biography'] : 'Нет';

$email = mb_strtolower(str_replace(' ', '-', $userInfo['rp_name']) . '@' . $serverName . '.sa');

$unReadCount = $qb
    ->createQueryBuilder('rp_email_msg')
    ->selectSql('COUNT(*)')
    ->where('email_id_to = \'' . $email . '\' AND is_read = 0')
    ->executeQuery()
    ->getSingleResult()
;

$unReadCount = reset($unReadCount);
?>

<style>img { width: 100% !important; height: auto !important; }</style>
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12 l3 hide-on-med-and-down">
                <div style="height: 50px; width: 100%;">
                    <h4 class="black-text">Меню</h4>
                </div>
                <div class="card-panel collection" style="border: 0; padding: 0;">
                    <a href="/rp/profile" class="blue-text collection-item">Профиль</a>
                    <a href="/rp/profile/edit" class="blue-text collection-item">Редактировать профиль</a>
                    <a href="/rp/search" class="blue-text collection-item">Поиск биографии</a>
                </div>
                <div style="height: 50px; width: 100%;">
                    <h4 class="black-text">Почта</h4>
                </div>
                <div class="card-panel collection" style="border: 0; padding: 0;">
                    <a href="/rp/mail/send" class="blue-text collection-item">Написать письмо</a>
                    <a href="/rp/mail" class="blue-text collection-item">Все письма</a>
                    <a href="/rp/mail/in" class="blue-text collection-item">Входящие <?php echo ($unReadCount > 0 ? '<span class="new badge blue">' . $unReadCount . '</span>' : '') ?></a>
                    <a href="/rp/mail/out" class="blue-text collection-item">Исходящие</a>
                </div>
            </div>
            <div class="col s1 hide-on-med-and-down"></div>
            <div class="col s12 l8">
                <?php

                $phone = '';
                if ($userInfo['phone_code'] > 0)
                    $phone = '<label>Телефон: ' . $userInfo['phone_code'] . '-' . $userInfo['phone'] . '</label><br>';

                echo '
                    <div style="display: flex; flex-wrap: wrap;">
                        <img class="circle" style="width: 250px !important; height: 250px !important; object-fit: cover;" src="' . $userInfo['rp_avatar'] . '">
                        <div style="margin-left: 32px">
                            <h4>' . $userInfo['rp_name'] . '</h4><br>
                            <label>Пол: ' . $sex . '</label><br>
                            <label>Возраст: ' . $userInfo['age'] . '</label><br>
                            <label>Вес: ' . $userInfo['rp_weight'] . 'кг.</label><br>
                            <label>Рост: ' . $userInfo['rp_growth'] . 'см.</label><br>
                            <label>Национальность: ' . $userInfo['rp_nationality'] . '</label><br>
                                ' . $phone . '
                        </div>
                    </div>
                    <br><hr><br>
                    <div class="row">
                        <div class="col s6 l3">
                            <b>Личные качества</b><br>
                            ' . $p1 . '
                        </div>
                        <div class="col s6 l3">
                            <b>Отличительные черты</b><br>
                            ' . $p2 . '
                        </div>
                        <div class="col s6 l3">
                            <b>Болезни</b><br>
                            ' . $p3 . '
                        </div>
                        <div class="col s6 l3">
                            <b>Характер</b><br>
                            ' . $p4 . '
                        </div>
                    </div>
                    <hr><br>
                    <h4>Личная биография</h4>
                    ' . htmlspecialchars_decode(htmlspecialchars_decode($p5)) . '
                ';
                ?>
            </div>
        </div>
    </div>
</div>