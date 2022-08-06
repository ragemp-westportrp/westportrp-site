<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
global $user;
global $userInfo;
global $serverName;
global $server;

$rules = $qb->createQueryBuilder('rp_rules')->selectSql()->executeQuery()->getResult();

?>

<style>
    .indicator {
        background: #000 !important;
    }
</style>
<script src="/client/ckeditor/ckeditor.js"></script>
<div class="row" style="margin-bottom: 8px;">
    <div class="col s12" style="padding: 0;">
        <ul class="tabs white" style="height: 144px; box-shadow: 0 0 0 0 rgba(0,0,0,.14), 0 0 0 0 rgba(0,0,0,.12), 0 3px 1px -2px rgba(0,0,0,.2);">
            <?php
                foreach ($rules as $item)
                    echo '<li class="tab col s3"><a class="black-text" href="#k' . $item['id'] . '">' . $item['title'] . '</a></li>';
            ?>
        </ul>
    </div>
</div>

<?php

foreach ($rules as $item) {

    if ($user->isAdmin(2) && isset($_GET['edit'])) {
        echo '
            <div class="container" id="k' . $item['id'] . '">
                <div class="section">
                    <div class="row" style="/*-webkit-user-select: none; -moz-user-select: none;-ms-user-select: none; user-select: none;*/">
                        <div class="col s12">
                            <img style="width: 100px;" src="https://images.vexels.com/media/users/3/128978/isolated/preview/bda6ac6e5565b962161be4f66c8868ff-usa-flag-print-map-by-vexels.png">
                            <h4>' . $item['title'] . ' штата Сан Андреас</h4>
                            ' . ($user->isAdmin(2) ? '<a href="/network/gov/rules?edit" class="btn blue accent-4">Редактировать</a><br><br>' : '') . '
                        </div>
                        <form method="post" class="col s12">
                            <input type="hidden" name="id" value="' . $item['id'] . '">
                            <textarea name="text" id="textarea' . $item['id'] . '">
                            ' . htmlspecialchars_decode($item['text']) . '
                            </textarea>
                            <script>CKEDITOR.replace( "textarea' . $item['id'] . '" );</script>
                            <button class="btn blue accent-4 waves-effect" name="network-save-rules">Сохранить</button>
                        </form>
                    </div>
                </div>
            </div>
        ';
    }
    else {
        echo '
        <div class="container" id="k' . $item['id'] . '">
            <div class="section">
                <div class="row" style="/*-webkit-user-select: none; -moz-user-select: none;-ms-user-select: none; user-select: none;*/">
                    <div class="col s12">
                        <img style="width: 100px;" src="https://images.vexels.com/media/users/3/128978/isolated/preview/bda6ac6e5565b962161be4f66c8868ff-usa-flag-print-map-by-vexels.png">
                        <h4>' . $item['title'] . ' штата Сан Андреас</h4>
                        ' . ($user->isAdmin(2) ? '<a href="/network/gov/rules?edit" class="btn blue accent-4">Редактировать</a><br><br>' : '') . '
                    </div>
                    <div class="col s12">
                        ' . htmlspecialchars_decode(htmlspecialchars_decode($item['text'])) . '
                    </div>
                </div>
            </div>
        </div>
    ';
    }
}
?>
