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
global $modal;
global $server;
global $user;

if (isset($_POST['edit-user-rp-info'])) {
    $_POST['rp_nationality'] = $server->charsString($_POST['rp_nationality']);
    $_POST['rp_growth'] = $server->charsString($_POST['rp_growth']);
    $_POST['rp_weight'] = $server->charsString($_POST['rp_weight']);
    $_POST['rp_avatar'] = $server->charsString($_POST['rp_avatar']);
    $_POST['rp_character'] = $server->charsString($_POST['rp_character']);
    $_POST['rp_diseases'] = $server->charsString($_POST['rp_diseases']);
    $_POST['rp_distinctive_features'] = $server->charsString($_POST['rp_distinctive_features']);
    $_POST['rp_qualities'] = $server->charsString($_POST['rp_qualities']);

    $isPublic = 1;
    if (!isset($_POST['rp_is_public']))
        $isPublic = 0;

    $success = $qb
        ->createQueryBuilder('users')
        ->updateSql(
            ['rp_nationality', 'rp_growth', 'rp_weight', 'rp_avatar', 'rp_character', 'rp_diseases', 'rp_distinctive_features', 'rp_qualities', 'rp_is_public'],
            [$_POST['rp_nationality'], $_POST['rp_growth'], $_POST['rp_weight'], $_POST['rp_avatar'], $_POST['rp_character'], $_POST['rp_diseases'], $_POST['rp_distinctive_features'], $_POST['rp_qualities'], $isPublic]
        )
        ->where('id = \'' . intval($userInfo['id']) . '\'')
        ->executeQuery()
        ->getSingleResult()
    ;

    $modal['show'] = true;
    $modal['title'] = 'Поздравляем!';
    $modal['text'] = 'Вы отредактировали профиль!';
    $modal['success'] = $success;

    $userInfo = $user->getUserInfo($userInfo['id']);
}

if (isset($_POST['edit-user-rp-bio'])) {
    $_POST['rp_biography'] = $server->charsString($_POST['rp_biography']);

    $success = $qb
        ->createQueryBuilder('users')
        ->updateSql(
            ['rp_biography'],
            [$_POST['rp_biography']]
        )
        ->where('id = \'' . intval($userInfo['id']) . '\'')
        ->executeQuery()
        ->getSingleResult()
    ;

    $modal['show'] = true;
    $modal['title'] = 'Поздравляем!';
    $modal['text'] = 'Вы отредактировали биографию!';
    $modal['success'] = $success;

    $userInfo = $user->getUserInfo($userInfo['id']);
}

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

<script src="/сlient/ckeditor/ckeditor.js"></script>
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
                <div style="width: 100%;">
                    <h4 class="black-text"><?php echo $userInfo['rp_name']; ?></h4><label>(( Информация должна быть IC | ФОТОГРАФИИ ИЗ РЕАЛЬНОЙ ЖИЗНИ ЗАПРЕЩЕНЫ ))<br>(( Любые арты, рисунки и фотографии из игры - разрешены. ))</label>
                </div>
                <?php
                echo '
                    <div class="card-panel">
                        <form class="row" method="post">
                            <div class="input-field col s12">
                                <input id="rp_nationality" required type="text" maxlength="100" class="validate" value="' . htmlspecialchars_decode($userInfo['rp_nationality']) . '" name="rp_nationality">
                                <label for="rp_nationality">Национальность</label>
                            </div>
                            <div class="input-field col s12">
                                <input id="rp_growth" required type="number" max="200" min="170" class="validate" value="' . htmlspecialchars_decode($userInfo['rp_growth']) . '" name="rp_growth">
                                <label for="rp_growth">Рост</label>
                            </div>
                            <div class="input-field col s12">
                                <input id="rp_weight" required type="number" min="45" max="130" class="validate" value="' . htmlspecialchars_decode($userInfo['rp_weight']) . '" name="rp_weight">
                                <label for="rp_weight">Вес</label>
                            </div>
                            <div class="input-field col s12">
                                <input id="rp_avatar" required type="text" maxlength="450" class="validate" value="' . htmlspecialchars_decode($userInfo['rp_avatar']) . '" name="rp_avatar">
                                <label for="rp_avatar">Аватар (URL) | ФОТОГРАФИИ ИЗ РЕАЛЬНОЙ ЖИЗНИ ЗАПРЕЩЕНЫ</label>
                            </div>
                            <div class="input-field col s12">
                                <input id="rp_character" required type="text" maxlength="200" class="validate" value="' . htmlspecialchars_decode($userInfo['rp_character']) . '" name="rp_character">
                                <label for="rp_character">Характер</label>
                            </div>
                            <div class="input-field col s12">
                                <input id="rp_diseases" type="text" class="validate" maxlength="200" value="' . htmlspecialchars_decode($userInfo['rp_diseases']) . '" name="rp_diseases">
                                <label for="rp_diseases">Болезни</label>
                            </div>
                            <div class="input-field col s12">
                                <input id="rp_distinctive_features" type="text" maxlength="200" class="validate" value="' . htmlspecialchars_decode($userInfo['rp_distinctive_features']) . '" name="rp_distinctive_features">
                                <label for="rp_distinctive_features">Отличительные черты</label>
                            </div>
                            <div class="input-field col s12">
                                <input id="rp_qualities" required type="text" maxlength="200" class="validate" value="' . htmlspecialchars_decode($userInfo['rp_qualities']) . '" name="rp_qualities">
                                <label for="rp_qualities">Личные качества</label>
                            </div>
                            <div class="col s12">
                                <p>
                                  <label>
                                    <span>Публичный профиль</span>
                                  </label>
                                </p>
                            </div>
                            <div class="col s12">
                                <div class="switch">
                                    <label>
                                      Выкл
                                      <input name="rp_is_public" ' . ($userInfo['rp_is_public'] == 1 ? 'checked="checked"' : '') . ' type="checkbox">
                                      <span class="lever"></span>
                                      Вкл
                                    </label>
                                </div>
                            </div>
                            <div class="input-field col s12">
                                <button class="btn waves-effect waves-light blue right" type="submit" value="true" name="edit-user-rp-info">Сохранить</button>
                            </div>
                        </form>
                    </div>
                ';
                ?>
                <div style="width: 100%;">
                    <h4 class="black-text">Личная биография</h4><label>(( Информация должна быть IC | ФОТОГРАФИИ ИЗ РЕАЛЬНОЙ ЖИЗНИ ЗАПРЕЩЕНЫ ))<br>(( Любые арты, рисунки и фотографии из игры - разрешены. ))</label>
                </div>
                <?php
                echo '
                    <div class="card-panel">
                        <form class="row" method="post">
                            <div class="input-field col s12">
                                <textarea required id="textarea1" class="materialize-textarea" name="rp_biography">' . htmlspecialchars_decode($userInfo['rp_biography']) . '</textarea>
                                <label for="textarea1"></label>
                            </div>
                            <div class="input-field col s12">
                                <button class="btn waves-effect waves-light blue right" type="submit" value="true" name="edit-user-rp-bio">Сохранить</button>
                            </div>
                        </form>
                    </div>
                ';
                ?>
            </div>
        </div>
    </div>
</div>
<script>CKEDITOR.replace( "textarea1" );</script>