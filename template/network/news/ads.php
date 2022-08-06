<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}
global $user;
global $qb;

$newsLast = $qb->createQueryBuilder('rp_inv_ad')->selectSql()->limit(1000)->orderBy('id DESC')->executeQuery()->getResult();
?>

<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12">
                <h4 class="grey-text">Рекламные объявления</h4>
                <div class="row">
                    <?php

                    if(!empty($newsLast)) {

                        foreach ($newsLast as $item) {

                            if (strpos($item['text'], 'HUD'))
                                continue;

                            $color = 'blue';
                            if ($item['title'] == 'Продажа')
                                $color = 'green';
                            if ($item['title'] == 'Покупка')
                                $color = 'amber';

                            echo '
	                        <div class="col s12 m6 l4">
	                            <div class="card small" style="height: 290px;">
	                                <div class="card-content black-text">
							          <span class="card-title" style="background: none">' . $item['title'] . '</span>
							          <p>' . $item['text'] . '</p>
							          <label>Телефон: ' . $item['phone'] . '</label><br>
							          <label>Отправитель: ' . $item['name'] . '</label><br>
							          <label>Отредактировал: ' . $item['editor'] . '</label>
							        </div>
	                                <div class="card-action ' . $color . ' lighten-4">
	                                    <div class="btn z-depth-0 transparent" style="padding-left: 0">
	                                    <label class="' . $color . '-text text-darken-2">' . gmdate("d/m H:i", $item['timestamp'] + 3600 * 3) . '</label>
	                                    </div>
	                                </div>
	                            </div>
	                        </div>
	                    ';
                        }
                    }
                    else {
                        echo '<div class="col s12" style="padding: 25px; margin-bottom: 450px;"><h4 class="center grey-text" style="padding: 100px;">Список пуст.</h4></div>';
                    }

                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
