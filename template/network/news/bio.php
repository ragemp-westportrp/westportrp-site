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

?>
<style>
    img {
        max-width: 100% !important;
        height: auto !important;
    }
    pre {
        width: 100% !important;
        white-space: pre-wrap;
    }
    .logo {
        max-width: 100% !important;
        height: 48px !important;
    }
</style>
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12">

                    <form class="row">
                        <div class="input-field col s12 l8">
                            <input id="rp_nationality" required type="text" maxlength="100" class="validate" value="<?php echo $searchQuery ?>" name="user">
                            <label for="rp_nationality">Поиск</label>
                        </div>
                        <div class="input-field col s12 l4">
                            <a href="/network/news/bioedit" style="width: 100%" class="btn red accent-3">Редактировать биографию</a>
                        </div>
                    </form>

                <?php

                if (isset($_GET['user'])) {
                    $searchInfo = $user->getUserInfo($server->charsString($searchQuery));
                    if (empty($searchInfo))
                        return;

                    $sex = json_decode($searchInfo['skin'])->SKIN_SEX == 1 ? 'Женский' : 'Мужской';
                    $p1 = !empty($searchInfo['rp_qualities']) ? $searchInfo['rp_qualities'] : 'Нет';
                    $p2 = !empty($searchInfo['rp_distinctive_features']) ? $searchInfo['rp_distinctive_features'] : 'Нет';
                    $p3 = !empty($searchInfo['rp_diseases']) ? $searchInfo['rp_diseases'] : 'Нет';
                    $p4 = !empty($searchInfo['rp_character']) ? $searchInfo['rp_character'] : 'Нет';
                    $p5 = !empty($searchInfo['rp_biography']) ? $searchInfo['rp_biography'] : 'Нет';

                    echo '
                        <div style="display: flex; flex-wrap: wrap;">
                            <img class="circle" style="width: 200px !important; height: 200px !important; object-fit: cover;" src="https://a.rsg.sc//n/' . strtolower($searchInfo['social']) . '">
                            <div style="margin-left: 32px">
                                <h4 style="margin-top: 0">' . $searchInfo['name'] . '</h4>
                                <label>Пол: ' . $sex . '</label><br>
                                <label>Возраст: ' . $searchInfo['age'] . '</label><br>
                                <label>Супруг(а): ' . $searchInfo['partner'] . '</label><br>
                                <label>Вес: ' . $searchInfo['rp_weight'] . 'кг.</label><br>
                                <label>Рост: ' . $searchInfo['rp_growth'] . 'см.</label><br>
                                <label>Национальность: ' . $searchInfo['national'] . '</label><br>
                           
                            </div>
                        </div>
                        <br><hr><br>
                    ';
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
                    echo '<div class="row">';

                    foreach ($qb->createQueryBuilder('users')->selectSql()->where('rp_biography <> \' \'')->limit(500)->executeQuery()->getResult() as $item) {
                        echo '
                            <div class="col s12 m6 l4">
                                <ul class="collection" style="border-radius: 8px;">
                                    <li style="min-height: 64px" class="collection-item avatar">
                                      <a href="/network/news/bio?user=' . $item['name'] . '"><img src="https://a.rsg.sc//n/' . strtolower($item['social']) . '" style="object-fit:cover; width: 42px !important; height: 42px !important;" alt="" class="circle"></a>
                                      <span class="title">' . $item['name'] . '</span>
                                      <p>Возраст: ' . $item['age'] . '</p>
                                      <a href="/network/news/bio?user=' . $item['name'] . '" class="secondary-content"><i class="material-icons blue-text text-accent-4">local_library</i></a>
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