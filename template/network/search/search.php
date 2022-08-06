<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $userInfo;
global $serverName;
global $search;
global $page;
global $monthN;
global $user;
global $qb;
global $server;

$searchQuery = isset($_GET['user']) ? $_GET['user'] : '';

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

                <form class="row">
                    <div class="input-field col s12">
                        <input id="rp_nationality" required type="text" maxlength="100" class="validate" value="<?php echo $searchQuery ?>" name="user">
                        <label for="rp_nationality">Поиск</label>
                    </div>  
                </form>  

                <?php

                if (isset($_GET['user'])) {
                    $searchInfo = $user->getUserInfo($server->charsString($searchQuery));
                    if (empty($searchInfo))
                        return;

                    $sex = json_decode($searchInfo['skin'])->SEX == 1 ? 'Женский' : 'Мужской';
                    $p1 = !empty($searchInfo['rp_qualities']) ? $searchInfo['rp_qualities'] : 'Нет';
                    $p2 = !empty($searchInfo['rp_distinctive_features']) ? $searchInfo['rp_distinctive_features'] : 'Нет';
                    $p3 = !empty($searchInfo['rp_diseases']) ? $searchInfo['rp_diseases'] : 'Нет';
                    $p4 = !empty($searchInfo['rp_character']) ? $searchInfo['rp_character'] : 'Нет';
                    $p5 = !empty($searchInfo['rp_biography']) ? $searchInfo['rp_biography'] : 'Нет';

                    $phone = '';
                    if ($searchInfo['phone_code'] > 0)
                        $phone = '<label>Телефон: ' . $searchInfo['phone_code'] . '-' . $searchInfo['phone'] . '</label><br>';

                    if ($searchInfo['rp_is_public'] == 0)
                        $phone = '';

                    echo '
                        <div style="display: flex; flex-wrap: wrap;">
                            <img class="circle" style="width: 250px !important; height: 250px !important; object-fit: cover;" src="' . $searchInfo['rp_avatar'] . '">
                            <div style="margin-left: 32px">
                                <h4>' . $searchInfo['rp_name'] . '</h4><br>
                                <label>Пол: ' . $sex . '</label><br>
                                <label>Возраст: ' . $searchInfo['age'] . '</label><br>
                                <label>Вес: ' . $searchInfo['rp_weight'] . 'кг.</label><br>
                                <label>Рост: ' . $searchInfo['rp_growth'] . 'см.</label><br>
                                <label>Национальность: ' . $searchInfo['rp_nationality'] . '</label><br>
                                ' . $phone . '
                            </div>
                            <a class="btn blue" style="margin: auto;" href="/rp/mail/send?to=' . (str_replace(' ', '-', $searchInfo['rp_name']) . '@' . mb_strtolower($serverName) . '.sa') . '">Написать</a>
                        </div>
                        <br><hr><br>
                    ';
                    if ($searchInfo['rp_is_public'] == 1 || $userInfo['admin_level'] > 0 || $userInfo['fraction_id'] == 2) {
                        echo '
                            <div class="row">
                                <div class="col s6 l3" style="overflow-X: hidden; word-break: break-word;">
                                    <b>Личные качества</b><br>
                                    ' . $p1 . '
                                </div>
                                <div class="col s6 l3" style="overflow-X: hidden; word-break: break-word;">
                                    <b>Отличительные черты</b><br>
                                    ' . $p2 . '
                                </div>
                                <div class="col s6 l3" style="overflow-X: hidden; word-break: break-word;">
                                    <b>Болезни</b><br>
                                    ' . $p3 . '
                                </div>
                                <div class="col s6 l3" style="overflow-X: hidden; word-break: break-word;">
                                    <b>Характер</b><br>
                                    ' . $p4 . '
                                </div>
                            </div>
                            <hr><br>
                            <h4>Личная биография</h4>
                            ' . htmlspecialchars_decode(htmlspecialchars_decode($p5)) . '
                        ';
                    }
                    else {
                        echo '
                            <br><h4>Это приватный профиль</h4>
                        ';
                    }
                }
                else {
                    echo '<div class="row">';

                    foreach ($qb->createQueryBuilder('users')->selectSql()->where('rp_biography <> \'Нет\'')->limit(100)->executeQuery()->getResult() as $item) {
                        echo '
                            <div class="col s12 m6">
                                <ul class="collection" style="border-radius: 8px;">
                                    <li style="min-height: 64px" class="collection-item avatar">
                                      <img src="' . $item['rp_avatar'] . '" style="object-fit:cover; width: 42px !important; height: 42px !important;" alt="" class="circle">
                                      <span class="title">' . $item['rp_name'] . '</span>
                                      <p>Возраст: ' . $item['age'] . '</p>
                                      <a href="/rp/search?user=' . $item['id'] . '" class="secondary-content"><i class="material-icons blue-text">send</i></a>
                                    </li>
                                </ul>
                            </div>
                        ';
                    }
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>