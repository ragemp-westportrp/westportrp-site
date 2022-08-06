<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
global $userInfo;
global $reportStatusHtml;
global $ratingStatusHtml;
global $page;
global $user;

$reportId = intval($page['report-id-']);

$report = $qb->createQueryBuilder('report_user')->selectSql()->where('id = ' . $reportId)->executeQuery()->getSingleResult();

$userSender = $user->getAccountInfo($report['user_id']);
$targetString = $report['target'];

if ($report['user_id'] != $userInfo['id'] && $userInfo['admin_level'] == 0) {
    echo 'Просматривать жалобы могут только администрация и заявитель';
    return;
}

if ($userInfo['admin_level'] > 0) {
    if (!empty($report['target'])) {
        $targetString = '';
        $ids = explode(",", $report['target']);
        foreach ($ids as $item) {
            if (end($ids) == $item)
                $targetString .= '<a target="_blank" href="/admin/log-log_connect?q=game_id===' . intval($item) . '">' . $item . '</a>';
            else
                $targetString .= '<a target="_blank" href="/admin/log-log_connect?q=game_id===' . intval($item) . '">' . $item . '</a>, ';
        }
    }
}

$links = $report['links'];

if (!empty($report['links'])) {
    $links = '';
    $ids = explode(" ", $report['links']);
    foreach ($ids as $item) {
        if (end($ids) == $item)
            $links .= '<a target="_blank" href="' . htmlspecialchars_decode($item) . '">' . htmlspecialchars_decode($item) . '</a>';
        else
            $links .= '<a target="_blank" href="' . htmlspecialchars_decode($item) . '">' . htmlspecialchars_decode($item) . '</a>, ';
    }
}

if ($report['user_id'] == $userInfo['id']) {
    $qb
        ->createQueryBuilder('report_user_answer')
        ->updateSql(['is_read'], [1])
        ->where('id = \'' . $reportId . '\'')
        ->executeQuery()
        ->getResult()
    ;
}

$reportAnswerList = $qb->createQueryBuilder('report_user_answer')->selectSql()->where('report_id = ' . $reportId)->executeQuery()->getResult();

?>
<style>
    .indicator {
        background: #fff !important;
    }
    .blackIndicator .indicator {
        background: #000 !important;
    }
    img {
        max-width: 100% !important;
        height: auto !important;
    }
    .logo {
        max-width: 100% !important;
        height: 48px !important;
    }
</style>
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12 l8">
                <h5 class="wd-font bw-text">Жалоба #<?php echo $report['id'] ?></h5>
                <div class="card-panel" style="overflow: hidden;">
                    <table>
                        <tbody class="highlight">
                        <tr>
                            <td>Отправитель</td>
                            <td><?php echo $userSender['login'] ?> (<?php echo $userSender['id'] ?>)</td>
                        </tr>
                        <tr class="<?php echo $report['datetime'] == 'SS' ? 'hide' : '' ?>">
                            <td>Нарушители</td>
                            <td><?php echo $targetString ?></td>
                        </tr>
                        <tr>
                            <td>Доказательства</td>
                            <td><?php echo $links ?></td>
                        </tr>
                        <tr>
                            <td>Дата подачи</td>
                            <td><?php echo gmdate("H:i:s, Y-m-d", $report['timestamp']) ?></td>
                        </tr>
                        <tr class="<?php echo $report['datetime'] == 'SS' ? 'hide' : '' ?>">
                            <td>Дата нарушения</td>
                            <td><?php echo $report['datetime'] ?></td>
                        </tr>
                        <tr>
                            <td>Статус</td>
                            <td><?php echo $reportStatusHtml[$report['status']] ?></td>
                        </tr>
                        </tbody>
                    </table>
                    <div style="padding: 10px 5px">
                        <hr>
                        <?php echo $report['datetime'] == 'SS' ? htmlspecialchars_decode(htmlspecialchars_decode($report['text'])) : nl2br(htmlspecialchars_decode($report['text'])) ?>
                    </div>
                </div>
            </div>
            <div class="col s12 l4">
                <?php
                if ($userInfo['id'] == $report['user_id'] && $report['status'] > 1) {
                    echo '
                    <h5 class="wd-font bw-text">Оценка</h5>
                    <form method="post">
                        <input type="hidden" name="id" value="' . $report['id'] . '">
                        <button name="report-rating1" class="btn waves-effect btn-floating red z-depth-0">
                            <i class="material-icons">
                                thumb_down
                            </i>
                        </button>
                        <button name="report-rating2" class="btn waves-effect btn-floating grey z-depth-0">
                            <i class="material-icons">
                                thumbs_up_down
                            </i>
                        </button>
                        <button name="report-rating3" class="btn waves-effect btn-floating green z-depth-0">
                            <i class="material-icons">
                                thumb_up
                            </i>
                        </button>
                    </form>
                    ';
                }
                if ($userInfo['admin_level'] > 0) {
                    echo '
                    <h5 class="wd-font bw-text">Действие над жалобой</h5>
                    <form method="post">
                        <input type="hidden" name="id" value="' . $report['id'] . '">
                        <button name="report-status1" class="btn waves-effect blue accent-4 z-depth-0">На рассмотрении</button>
                        <br><br>
                        <button name="report-status3" class="btn waves-effect green z-depth-0 ' . ($report['status'] != 1 ? 'hide' : '') . '">Одобрить</button>
                        <br><br>
                        <button name="report-status2" class="btn waves-effect red z-depth-0 ' . ($report['status'] != 1 ? 'hide' : '') . '">Отклонить</button>
                    </form>
                   
                    ';
                }
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col s12 l8 <?php echo ($report['status'] > 1 || ($userInfo['admin_level'] < 1 && $userInfo['id'] != $userSender['id']) ? 'hide' : '') ?>">
                <div class="card-panel">
                    <form method="post">
                        <div class="row">
                            <div class="input-field col s12">
                                <input type="hidden" name="id" value="<?php echo $report['id'] ?>">
                                <input type="hidden" name="omgwtf" value="<?php echo $report['user_id'] == $userInfo['id'] ?>">
                                <textarea name="text" id="text_msg" required="" class="materialize-textarea"></textarea>
                                <label for="text_msg">Текст ответа...</label>
                            </div>
                            <button name="report-answer" class="waves-effect blue accent4 white-text btn center-block z-depth-0" style="display: block;">Отправить</button>
                        </div>
                    </form>
                </div><br><br>
            </div>

            <?php
            foreach ($reportAnswerList as $item) {
                echo '
                     <div class="col s12 l8">
                         <div style="display: flex; width: 100%">
                            <div><img style="width: 50px; border-radius: 50%; border: 4px solid #000; margin-top: 8px;" src="https://a.rsg.sc//n/' . strtolower($item['social_from']) . '"></div>
                            <div style="width: calc(100% - 50px)">
                             <div class="card-panel z-depth-0" style="margin-bottom: 4px; padding-top: 0; background: transparent !important;">
                                <h5 class="grey-text">' . strtok($item['name_from'], ' ') . '</h5>
                                 <div>
                                     ' . nl2br(htmlspecialchars_decode($item['text'])) . '
                                     <br><br><label>С уважением ' . $item['name_from'] . '.</label>
                                     <br><label>' . gmdate("H:i, Y-m-d", $report['timestamp']) . '</label>
                                 </div>
                             </div>
                            </div>
                         </div>
                         <hr><br>
                     </div>
                ';
            }
            ?>
        </div>
    </div>
</div>