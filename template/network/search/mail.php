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

$email = mb_strtolower(str_replace(' ', '-', $userInfo['rp_name']) . '@' . $serverName . '.sa');
$reply = isset($_GET['to']) ? mb_strtolower($_GET['to']) : '';
$sub = isset($_GET['sub']) ? mb_strtolower($_GET['sub']) : '';


$unReadCount = $qb
    ->createQueryBuilder('rp_email_msg')
    ->selectSql('COUNT(*)')
    ->where('email_id_to = \'' . $email . '\' AND is_read = 0')
    ->executeQuery()
    ->getSingleResult()
;

$unReadCount = reset($unReadCount);
?>

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
                <div style="height: 50px; width: 100%;">
                    <h4 class="black-text"><?php echo $email; ?></h4>
                </div>
                <?php
                if($page['p'] == 'rp/mail/send') {
                    echo '
                        <div class="card-panel">
                            <form class="row" method="post">
                                <div class="input-field col s12">
                                    <input id="email-to" required type="email" value="' . $reply . '" class="validate" name="email-to">
                                    <label for="email-to">Получатель (Email)</label>
                                </div>
                                <div class="input-field col s12">
                                    <input id="title" required type="text" value="' . $sub . '" class="validate" name="title">
                                    <label for="title">Заголовок</label>
                                </div>
                                <div class="input-field col s12">
                                    <textarea id="txt" required class="materialize-textarea" name="text"></textarea>
                                    <label for="txt">Текст</label>
                                </div>
                                <div class="input-field col s12">
                                    <button class="btn waves-effect waves-light blue right" type="submit" value="true" name="send-rp-email">Отправить
                                        <i class="material-icons right">send</i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    ';
                }
                else {
                    switch ($page['p']) {
                        case 'rp/mail/out':
                            $emails = $search->getMessageFromEmail($email);
                            break;
                        case 'rp/mail/in':
                            $emails = $search->getMessageToEmail($email);
                            break;
                        default:
                            $emails = $search->getMessageAllEmail($email);
                    }

                    if(empty($emails)) {
                        echo '
                            <div class="card-panel row">
                                <h4>Почтовый ящик - пуст.</h4>
                            </div>
                        ';
                    }
                    else {
                        echo '<div class="row"><div class="col s12">';

                        foreach ($emails as $item) {

                            $item['title'] = htmlspecialchars_decode($item['title']);
                            $item['text'] = htmlspecialchars_decode($item['text']);
                            $timestamp = $item['timestamp'] + 3600 * 3;
                            $time = gmdate('d',$timestamp) . ' ' . $monthN[gmdate('m', $timestamp)] . ' ' . gmdate('Y',$timestamp) . ', ' . gmdate('H:i', $timestamp);
                            $timeBadge = gmdate('d',$timestamp) . ' ' . $monthN[gmdate('m', $timestamp)] . ' ' . gmdate('Y',$timestamp) . 'г.';

                            $isRead = '';
                            if ($item['email_id_to'] != $email)
                                $isRead = $item['is_read'] == 1 ? '' : '<b class="grey-text">UNREAD</b>';
                            else
                                $isRead = $item['is_read'] == 1 ? '' : '<b class="blue-text">NEW!</b>';

                            echo '
                            <ul class="collapsible" data-collapsible="accordion" style="border: none;">
                            <li>
                                <div class="collapsible-header" style="border-botton: none"><i class="material-icons">mail</i>' . $item['title'] . '<span class="badge">' . $timeBadge . ' ' . $isRead . '</span></div>
                                <div class="collapsible-body">
                                    <span>
                                    ' . nl2br($item['text']) . '
                                    <hr>
                                    <label class="left">Кому отправлено: ' . $item['email_id_to'] . '</label><label class="right">' . $time . '</label><br>
                                    <label>Кто отправил: ' . $item['email_id_from'] . '</label>
                                    </span><br><br>
                                    <a class="btn blue z-depth-0" href="/rp/mail/send?to=' . ($item['email_id_from'] == $email ? $item['email_id_to'] : $item['email_id_from']) . '&sub=' . $item['title'] . '">Ответить</a>
                                </div>
                            </li>
                            </ul>
                            ';
                        }

                        echo '</div></div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>