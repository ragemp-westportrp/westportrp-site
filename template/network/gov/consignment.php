<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
?>

<div class="container">
    <div class="section">
        <div class="section">
            <div class="row">
                <div class="col s12">
                    <div class="black-text">
                        <a class="waves-effect waves-light btn blue accent-4 white-text right" target="_blank" href="/network/gov/consignment-create">Создать партию</a>
                    </div>
                </div>
            </div>
            <?php
                $result = $qb
                    ->createQueryBuilder('rp_gov_party_list')
                    ->selectSql()
                    ->orderBy('views DESC')
                    ->executeQuery()
                    ->getResult()
                ;
                foreach ($result as $item) {
                    echo '
                        <div class="row" style="margin-top: 80px">
                            <div class="col s12 l5">
                                <img onerror="this.src=\'https://i.imgur.com/O5JDV6b.png\'" style="width: 100%; height: 250px; object-fit: contain;" src="' . htmlspecialchars_decode($item['img']) . '">
                            </div>
                            <div class="col s12 l7">
                                <div class="black-text">
                                    <label style="font-size: 2rem; font-weight: 300" class="grey-text text-darken-3">' . htmlspecialchars_decode($item['title']) . '</label>
                                </div><br>
                                <label class="black-text" style="font-size: 1rem">
                                   ' . nl2br(htmlspecialchars_decode($item['content_desc'])) . '
                                   <hr>
                                   Глава партии: ' . htmlspecialchars_decode($item['user_owner']) . '
                                   <br>
                                   <br>
                                   <a class="waves-effect waves-light btn blue accent-4 white-text" href="/network/gov/consignment-info?id=' . $item['id'] . '">Подробнее</a>
                                </label>
                            </div>
                        </div>
                        <hr>
                    ';
                }
            ?>
        </div>
    </div>
</div>