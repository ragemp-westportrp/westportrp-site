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
<script src="/client/ckeditor/ckeditor.js"></script>
<div class="container">
    <div class="section">

        <div class="row">
            <div class="col s12">
                <ul class="tabs z-depth-0 transparent">

                    <?php

                    foreach ($user->getPlayers() as $player) {
                        echo '<li class="tab col s4"><a style="font-size: 16px" class="bw-text wd-font" href="#player' . $player['id'] . '">' . $player['name'] . '</a></li>';
                    }

                    ?>
                </ul>
            </div>
        </div>

        <?php
            foreach ($user->getPlayers() as $player) {
                ?>

                <div class="row">
                    <div class="col s12">
                        <div style="width: 100%;">
                            <h4 class="bw-text wd-font">Личные качества <a target="_blank" href="/network/news/bio?user=<?php echo $player['name'] ?>" class="btn grey right">Посмотреть биографию</a></h4><label class="hide">(( Информация должна быть IC | ФОТОГРАФИИ ИЗ РЕАЛЬНОЙ ЖИЗНИ ЗАПРЕЩЕНЫ ))<br>(( Любые арты, рисунки и фотографии из игры - разрешены. ))</label>
                        </div>
                        <?php
                        echo '
                    <div class="card transparent">
                        <form class="row" method="post">
                            <input type="hidden" name="id" value="' . $player['id'] . '">
                            <div class="input-field col s12 m4">
                                <input id="rp_growth" required type="number" max="220" min="160" class="validate" value="' . htmlspecialchars_decode($player['rp_growth']) . '" name="rp_growth">
                                <label for="rp_growth">Рост</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <input id="rp_weight" required type="number" min="45" max="130" class="validate" value="' . htmlspecialchars_decode($player['rp_weight']) . '" name="rp_weight">
                                <label for="rp_weight">Вес</label>
                            </div>
                            <div class="input-field col s12 hide">
                                <input id="rp_avatar" type="text" maxlength="450" class="validate" value="' . htmlspecialchars_decode($player['rp_avatar']) . '" name="rp_avatar">
                                <label for="rp_avatar">Аватар (URL) | ФОТОГРАФИИ ИЗ РЕАЛЬНОЙ ЖИЗНИ ЗАПРЕЩЕНЫ</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <input id="rp_character" required type="text" maxlength="200" class="validate" value="' . htmlspecialchars_decode($player['rp_character']) . '" name="rp_character">
                                <label for="rp_character">Характер</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <input id="rp_diseases" type="text" class="validate" maxlength="200" value="' . htmlspecialchars_decode($player['rp_diseases']) . '" name="rp_diseases">
                                <label for="rp_diseases">Болезни</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <input id="rp_distinctive_features" type="text" maxlength="200" class="validate" value="' . htmlspecialchars_decode($player['rp_distinctive_features']) . '" name="rp_distinctive_features">
                                <label for="rp_distinctive_features">Отличительные черты</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <input id="rp_qualities" required type="text" maxlength="200" class="validate" value="' . htmlspecialchars_decode($player['rp_qualities']) . '" name="rp_qualities">
                                <label for="rp_qualities">Личные качества</label>
                            </div>
                           
                            <div class="input-field col s12">
                                <button class="btn waves-effect waves-light blue accent-4 right z-depth-0" type="submit" value="true" name="edit-user-rp-info">Сохранить</button>
                            </div>
                        </form>
                    </div>
                ';
                        ?>
                        <div style="width: 100%;">
                            <h4 class="bw-text wd-font">Личная биография</h4><label class="hide">(( Информация должна быть IC | ФОТОГРАФИИ ИЗ РЕАЛЬНОЙ ЖИЗНИ ЗАПРЕЩЕНЫ ))<br>(( Любые арты, рисунки и фотографии из игры - разрешены. ))</label>
                        </div>
                        <?php
                        echo '
                    <div class="card transparent">
                        <form class="row" method="post">
                            <div class="input-field col s12">
                                <input type="hidden" name="id" value="' . $player['id'] . '">
                                <textarea required id="textarea' . $player['id'] . '" class="materialize-textarea" name="rp_biography">' . htmlspecialchars_decode($player['rp_biography']) . '</textarea>
                                <label for="textarea' . $player['id'] . '"></label>
                            </div>
                            <div class="input-field col s12">
                                <button class="btn waves-effect waves-light blue accent-4 right z-depth-0" type="submit" value="true" name="edit-user-rp-bio">Сохранить</button>
                            </div>
                        </form>
                    </div>
                ';
                        ?>
                    </div>
                    <script>CKEDITOR.replace( "textarea<?php echo $player['id'] ?>" );</script>
                </div>
                
                <?
            }
        ?>
    </div>
</div>