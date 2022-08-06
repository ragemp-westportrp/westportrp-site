<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
global $user;
global $userInfo;
global $reportStatusHtml;
global $ratingStatusHtml;
global $ratingStatusHtml1;

$countReports =  $qb->createQueryBuilder('report_user')->selectSql('COUNT(*)')->where('status = 2 OR status = 3')->executeQuery()->getSingleResult();
$rating1 =  $qb->createQueryBuilder('report_user')->selectSql('COUNT(*)')->where('rating = 1')->executeQuery()->getSingleResult();
$rating2 =  $qb->createQueryBuilder('report_user')->selectSql('COUNT(*)')->where('rating = 2 OR rating = 0')->executeQuery()->getSingleResult();
$rating3 =  $qb->createQueryBuilder('report_user')->selectSql('COUNT(*)')->where('rating = 3')->executeQuery()->getSingleResult();

$countReports = reset($countReports);
$rating1 = reset($rating1);
$rating2 = reset($rating2);
$rating3 = reset($rating3);

?>

<script src="/client/ckeditor/ckeditor.js"></script>
<div class="row <?php echo $user->isLogin() ? '' : 'hide' ?>" style="margin-bottom: 8px;">
    <div class="col s12 wb" style="padding: 0;">
        <div class="container">
            <ul class="tabs" style="overflow: hidden; background: transparent">
                <li class="tab col s6"><a class="bw-text wd-font" href="#p1">Список жалоб</a></li>
                <li class="tab col s6"><a class="bw-text wd-font" href="#p2">Подача жалобы</a></li>
            </ul>
        </div>
    </div>
</div>
<div class="row <?php echo $user->isLogin() ? 'hide' : '' ?>" style="margin-bottom: 8px;">
    <div class="col s12">
        <div class="container center">
            <h4 class="wd-font bw-text">Для подачи жалобы, необходимо авторизоваться</h4>
            <a class="btn blue accent-4 wd-font" href="/login">Войти в аккаунт</a>
        </div>
    </div>
</div>
<div class="container" id="p1">
    <div class="section">
        <div class="row">
            <div class="col s12" style="margin-bottom: 50px">
                <div style="margin: 16px 0" class="center">
                    <div>
                        <label>
                            Проверено жалоб
                            <h4 style="margin: 0"><?php echo number_format($countReports) ?></h4>
                        </label>
                    </div>
                </div>
                <div style="display: flex; margin: 16px 0" class="center">
                    <div style="margin: auto; display: flex; user-select: none;">
                        <div class="center" style="margin: 0 14px;">
                            <i class="material-icons red-text" style="font-size: 4rem">
                                thumb_down
                            </i><br>
                            <label>
                                Оценок
                                <h5 style="margin: 0"><?php echo number_format($rating1) ?></h5>
                            </label>
                        </div>
                        <div class="center" style="margin: 0 14px">
                            <i class="material-icons grey-text" style="font-size: 4rem">
                                thumbs_up_down
                            </i><br>
                            <label>
                                Оценок
                                <h5 style="margin: 0"><?php echo number_format($rating2) ?></h5>
                            </label>
                        </div>
                        <div class="center" style="margin: 0 14px">
                            <i class="material-icons green-text" style="font-size: 4rem">
                                thumb_up
                            </i><br>
                            <label>
                                Оценок
                                <h5 style="margin: 0"><?php echo number_format($rating3) ?></h5>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col s12" id="userList">
                <table class="striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Тип</th>
                        <th>Время</th>
                        <th>Статус</th>
                        <th>Рейтинг</th>
                        <th></th>
                    </tr>
                    </thead>

                    <tbody>

                    <?php
                    $reportList =  $qb->createQueryBuilder('report_user')->selectSql()->limit(100)->where('status < 2')->orderBy('id DESC')->executeQuery()->getResult();
                    $reportListDone =  $qb->createQueryBuilder('report_user')->selectSql()->limit(1000)->where('status > 1')->orderBy('id DESC')->executeQuery()->getResult();

                    if(!empty($reportList)) {

                        foreach ($reportList as $item) {

                            echo '
                                <tr>
                                    <td>#' . $item['id'] . '</td>
                                    <td>' . $item['target'] . '</td>
                                    <td>' . gmdate("H:i:s, Y-m-d", $item['timestamp']) . '</td>
                                    <td>' . $reportStatusHtml[$item['status']] . '</td>
                                    <td>' . $ratingStatusHtml1[$item['rating']] . '</td>
                                    <td><a href="/report-id-' . $item['id'] . '" class="waves-effect right blue accent-4 white-text btn z-depth-0">Открыть</a></td>
                                </tr>
                            ';
                        }
                    }
                    if(!empty($reportListDone)) {

                        foreach ($reportListDone as $item) {

                            echo '
                                <tr>
                                    <td>#' . $item['id'] . '</td>
                                    <td>' . $item['target'] . '</td>
                                    <td>' . gmdate("H:i:s, Y-m-d", $item['timestamp']) . '</td>
                                    <td>' . $reportStatusHtml[$item['status']] . '</td>
                                    <td>' . $ratingStatusHtml1[$item['rating']] . '</td>
                                    <td><a href="/report-id-' . $item['id'] . '" class="waves-effect right blue accent-4 white-text btn z-depth-0">Открыть</a></td>
                                </tr>
                            ';
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="container" id="p2">
    <div class="section">
        <div class="row">
            <div class="col s12 l7">
                <div class="card-panel">
                    <div class="row">
                        <form method="post" class="col s12">
                            <div class="row">
                                <div class="input-field col s12">
                                    <span class="red-text"><b>ВНИМАНИЕ!</b></span><br>
                                    <span>Обязательно нажмите ENTER после ввода ID</span>
                                    <div class="chips chips-placeholder"></div>
                                </div>
                                <input class="inputChipSend" required type="hidden" name="ids">
                                <div class="input-field col s12">
                                    <input required="" name="date" type="text" placeholder="01/01/2021, 22:30 МСК" class="validate">
                                    <label for="ids">Дата и время проишествия</label>
                                </div>
                                <div class="input-field col s12">
                                    <input required="" name="links" type="text" placeholder="https://imgur.com/a/BOfqHT6, https://youtu.be/dQw4w9WgXcQ" class="validate">
                                    <label for="ids">Ссылки на доказательства (imgur.com / youtube / twitch)</label>
                                </div>
                                <div class="input-field col s12">
                                    <textarea name="text" id="text_msg" required="" placeholder="Опишите полностью проблему, которая с вами произошла." class="materialize-textarea"></textarea>
                                    <label for="text_msg">Опишите проблему</label>
                                </div>
                                <button name="report-send" disabled id="btnSendReport" class="waves-effect white-text blue accent-4 wd-font btn bt center-block z-depth-0" style="display: block;">Отправить</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col s12 l5">
                <div style="margin-top: 10px">
                    <h6 class="wd-font bw-text">Общее</h6>
                    <label>1. Время рассмотрении жалобы до 24 часов<br>
                    <label>2. Скриншоты заливать только на сайт <a href="https://imgur.com" target="_blank">imgur.com</a></label><br>
                    <label>2.1. В случае если у вас несколько скриншотов, сайт позволяет залить это всё в один альбом</label><br>
                    <label>3. Видео загружаются только на <a target="_blank" href="https://twitch.tv">Twitch</a> или <a href="https://www.youtube.com" target="_blank">YouTube</a></label><br>
                    <label>3.1 Запрещено использовать видео материал 3-го лица без разрешения<br>
                    <label>3.2 Если видео длится более 2-х минут,прикрепите тайм-коды в описании<br>
                    <label>4. Если у вас несколько ссылок, укажите через запятую или прикрепите в описании</label><br>
                    <label>5. На скриншоте/видео должно быть чётко видно дату и время (в правом верхнем углу).</label><br><br>

                    <h6 class="wd-font bw-text">Правила составления жалобы</h6>
                    <label>1. Обязательно укажите ID игрока или игроков через Enter, на которых вы оставляете жалобу</label><br>
                    <label>2. Видео без звука и/или с монтажом отклоняются (Бывают исключения)</label><br>
                    <label>3. Запрещены любые оскорбления в тексте жалобы</label><br>
                    <label>4. Скриншот не должен быть обрезан и как либо отредактирован</label><br>
                    <label>5. Доказательства которым более 3 дней, не принимаются</label><br>
                    <label>6. Жалобы не по форме отклоняются без проверки материала</label><br>
                    <label>7. Укажите временной промежуток нарушения (Дата, время по МСК)</label><br>
                    <label>8. Обязательно укажите какие <a target="_blank" href="/rules">пункты правил</a> нарушил игрок(и) в описании</label><br>
                    <label>9. При предоставлении видео доказательств обязательно наличие полного фрагмента рп ситуации.</label><br><br>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container hide" id="p3">
    <div class="section">
        <div class="row">

            <div class="col s12">
                <div style="margin-top: 10px">
                    <h6 class="wd-font bw-text">Ввод</h6>
                    <div class="bw-text">Screenshot Situation (SS) - Скриншот Ситуации или любое грамотно оформленное RP действие, которое может привести к итогам. При создании ССки вы должны полностью обдумать ее план и развитие. Создав RP ситуацию, вы можете показать всем свой уровень RP, талант и находчивость.</div>
                    <br>
                    <h6 class="wd-font bw-text">Общее</h6>
                    <label>1. Время рассмотрении СС-ки до 72 часов<br>
                    <label>2. Скриншоты заливать только на сайт <a href="https://imgur.com" target="_blank">imgur.com</a></label><br>
                    <label>2.1. В случае если у вас несколько скриншотов, сайт позволяет залить это всё в один альбом</label><br>
                    <label>3. Видео загружаются только на <a target="_blank" href="https://twitch.tv">Twitch</a> или <a href="https://www.youtube.com" target="_blank">YouTube</a></label><br>
                    <label>4. Если у вас несколько ссылок, укажите через запятую или прикрепите в описании</label><br>
                    <label>5. На скриншоте/видео должно быть чётко видно дату и время (в правом верхнем углу).</label><br><br>

                    <h6 class="wd-font bw-text">Правила составления СС-ки</h6>
                    <label>1. Ситуация должна иметь логическую завязку и конец.</label><br>
                    <label>2. Ситуация должна быть полностью оформлена от начала и до развязки.</label><br>
                    <label>3. Ситуация должна полностью соответствовать лору и нести смысловую нагрузку.</label><br>
                    <label>4. Ситуация должна нести исключительно творческий характер и не должна быть способом заработка.</label><br>
                    <label>[Примечание]: После окончания ситуации администрация не выдаёт никаких особых призов по типу недвижимости, оружия и денег (После $500.000).</label><br>
                    <label>5. Запрещено отыгрывать более чем 1 роль в ходе ситуации.</label><br>
                    <label>6. Запрещено отыгрывать несуществующих людей.</label><br>
                    <label>[Примечание]: Все участники разыгрываемой ситуации должны быть реальными и существовать на сервере.</label><br>
                    <label>7. Запрещено использовать ООС информацию.</label><br>
                    <label>8. Запрещено идти вразрез РП логике и отыгрывать персонажа, который не совпадает с моделью поведения, описанной в личной биографии.</label><br>
                    <label>9. Все денежные средства, которые используются во время РП ситуации, удаляются либо возвращаются администрации проекта.</label><br>
                    <label>10. Ситуация должна быть полностью оформлена в рамках проекта и правил DEDNET.</label><br><br>
                </div>
            </div>
            <div class="col s12">
                <div class="card-panel">
                    <div class="row">
                        <form method="post" class="col s12">
                            <div class="row">
                                <div class="input-field col s12 hide">
                                    <input required="" name="ids" type="hidden" value="Заяка на СС" class="validate">
                                    <input required="" name="date" type="text" value="SS" placeholder="01/01/2021, 22:30 МСК" class="validate">
                                    <label for="ids">Дата и время проишествия</label>
                                </div>
                                <div class="input-field col s12">
                                    <input name="links" type="text" placeholder="https://imgur.com/a/BOfqHT6, https://youtu.be/dQw4w9WgXcQ" class="validate">
                                    <label for="ids">Доп информация (imgur.com / youtube / twitch)</label>
                                </div>
                                <div class="input-field col s12">
                                    <div>Распишите вашу скрин-стори</div><br>
                                    <textarea name="text" id="text_msg-ss" required="" placeholder="Опишите полностью проблему, которая с вами произошла." class="materialize-textarea"></textarea>
                                </div>
                                <button name="report-send" class="waves-effect white-text blue accent-4 wd-font btn bt center-block z-depth-0" style="display: block;">Отправить</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>CKEDITOR.replace( "text_msg-ss" );</script>