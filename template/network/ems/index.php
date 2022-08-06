<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}


global $qb;
global $user;
global $userInfo;
global $serverName;
global $server;
global $fractionId;

$newsLast = $qb->createQueryBuilder('rp_news')->selectSql()->where('fraction = ' . $fractionId)->limit(1000)->orderBy('id DESC')->executeQuery()->getResult();
$result = $qb->createQueryBuilder('users')->selectSql()->where('fraction_id = ' . $fractionId)->andWhere('is_leader = 1')->andWhere('admin_level = 0')->limit(1)->executeQuery()->getSingleResult();
$leaderName = empty($result['name']) ? 'Отсутствует' : $result['name'];

$coffer = $qb->createQueryBuilder('official_bank')->selectSql()->where('id = 6')->limit(1)->executeQuery()->getSingleResult();

$qb
    ->createQueryBuilder('')
    ->otherSql("set session sql_mode=''", false)
    ->executeQuery()
    ->getResult()
;


if (isset($_GET['newsId']) && is_numeric($_GET['newsId'])) {

    $item = $qb->createQueryBuilder('rp_news')->selectSql()->where('id = ' . intval($_GET['newsId']))->orderBy('id DESC')->executeQuery()->getSingleResult();

    if (empty($item)) {
        echo '<div class="container">
                <div class="section"><h4 class="grey-text" style="margin: 150px 0;">Новость не найдена</h4></div></div>';
    }
    else {
        $time = date("H:i, d-m-Y", $item['timestamp'] + 3 * 3600);
        echo '
            <div class="container">
                <div class="section">
                    <div class="row">
                        <div class="col s12">
                            <a href="/network/ems?newsId=' . $item['id'] . '"><img loading="lazy" style="width: 100%; border-radius: 12px; object-fit: contain;" src="' . htmlspecialchars_decode($item['img']) . '"></a>
                        </div>
                        <div class="col s12" style="margin-top: 30px">
                            <div class="black-text">
                                <label style="font-size: 2rem; font-weight: 300" class="grey-text text-darken-3">' . htmlspecialchars_decode($item['title']) . '</label>
                            </div>
                            <label class="black-text" style="font-size: 1rem">
                               ' . htmlspecialchars_decode(htmlspecialchars_decode(htmlspecialchars_decode($item['text']))) . '
                               <hr>
                               Автор: ' . htmlspecialchars_decode($item['author_name']) . '
                               <br>
                               <label>(( Дата: ' . $time . ' ))</label>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        ';
    }

    return;
}
?>

<img src="https://lovegta.ru/file/wp-content/uploads/2019/07/38cf22-Ambulance_7.jpg" style="position: absolute; z-index: -1; width: 100%; height: 400px; object-fit: cover;">

<div class="container">
    <div class="section">
        <div class="row" style="margin-top: 20px">
            <div class="col s12 m6 l7 white-text">
                <img style="width: 120px;" src="https://i.imgur.com/ZODcxTy.png">
                <h3>Официальный сайт службы спасения</h3>
                <div><label style="color: rgba(255,2552,255,0.7)">Начальник департамента:</label> <?php echo $leaderName; ?></div>
            </div>
            <div class="col s12 m6 l5">
                <div class="center" style="margin-top: 80px">
                    <i style="font-size: 4rem" class="material-icons white-text">account_balance</i>
                    <div class="center" style="color: rgba(255,2552,255,0.7)">Бюджет организации</div>
                    <h3 class="center white-text">$<?php echo number_format($coffer['money']); ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container" style="padding: 100px 0;">
    <div class="section">
        <div class="row">
            <div class="col s12"><h4>Новости</h4></div>
            <?php


            if((($user->isLeader() && $user->isEms()) || $user->isAdmin(5)) && $user->isLogin())
            {
                echo '
                        <script src="/client/ckeditor/ckeditor.js"></script>
                        <div class="col s12">
                            <div class="card-panel">
                                <form method="post" class="row">
                                    <div class="input-field col s12 m6">
                                        <input required id="title" type="text" class="validate" name="title">
                                        <label for="title">Заголовок</label>
                                    </div>
                                    <div class="input-field col s12 m6">
                                        <input required id="url" type="text" class="validate" name="img">
                                        <label for="url">URL Картинки</label>
                                    </div>
                                    <div class="input-field col s12">
                                        <textarea required id="textar" name="text"></textarea>
                                        <input type="hidden" class="validate" name="id" value="' . $fractionId . '">
                                    </div>
                                    <div class="col s12">
                                        <button class="btn z-depth-0 waves-effect right blue accent-4" name="send-network-news">
                                            Отправить
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <script>CKEDITOR.replace( "textar" );</script>
                    ';
            }
            echo '<div class="col s12">';
            foreach ($newsLast as $item) {
                $time = date("H:i, d-m-Y", $item['timestamp'] + 3 * 3600);
                echo '
                        <div class="row" style="margin-top: 80px">
                            <div class="col s12 l4">
                                <img loading="lazy" style="width: 100%; border-radius: 12px; object-fit: contain;" src="' . htmlspecialchars_decode($item['img']) . '">
                            </div>
                            <div class="col s12 l8">
                                <div class="black-text">
                                    <label style="font-size: 2rem; font-weight: 300" class="grey-text text-darken-3"><a class="grey-text text-darken-3" href="/network/ems?newsId=' . $item['id'] . '">' . htmlspecialchars_decode($item['title']) . '</a></label>';
                if($user->isLeader() && $user->isEms() || $user->isAdmin(2))
                {
                    echo '<form class="right" method="post">
		                                <button name="delete-network-news" class="btn-floating btn waves-effect waves-light red tooltipped" data-position="bottom" data-delay="50" data-tooltip="Удалить новость" type="submit">
		                                    <i class="material-icons">delete</i>
		                                </button>
		                                <input type="hidden" name="id" value="' . $item['id'] . '"></form>';
                }
                echo '
                                </div>
                                <label class="black-text" style="font-size: 1rem">
                                   Автор: ' . htmlspecialchars_decode($item['author_name']) . '
                                   <br>
                                   <label>(( Дата: ' . $time . ' ))</label>
                                </label>
                            </div>
                        </div>
                        <hr>
                    ';
            }
            echo '</div>';
            ?>
        </div>
    </div>
</div>
