<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
global $userInfo;

$result = $qb->createQueryBuilder('faq_list')->selectSql()->orderBy('orderby DESC')->executeQuery()->getResult();
?>

<div class="container">
    <div class="section">

        <a href="/dev" class="btn blue accent-4 waves-effect">Прогресс разработки</a>

        <?php echo ($userInfo['admin_level'] > 0) ? '<a href="#createFaq" class="modal-trigger btn blue accent-4 waves-effect">Написать</a>' : '' ?>
        <div class="row">
            <?php
                foreach ($result as $item) {
                    echo '
                        <div class="col s12 m6 l4">
                            <div class="card medium" style="height: 270px">
                                <div class="card-image" style="height: 220px; max-height: 270px">
                                    <img src="' . $item['img'] . '" style="height: 220px; object-fit: cover">
                                    <span class="card-title wd-font">' . htmlspecialchars_decode($item['title']) . '</span>
                                </div>
                                <div class="card-action">
                                    <a class="btn btn-flat bw-text" href="/faq-' . $item['id'] . '">Подробнее</a>
                                </div>
                            </div>
                        </div>
                    ';
                }
            ?>
            <div class="col s12 m6 l4">

            </div>
        </div>
    </div>
</div>

<?php
if ($userInfo['admin_level'] > 0) {
    echo '
        <!-- Modal Structure -->
        <form method="post" id="createFaq" class="modal modal-fixed-footer">
            <div class="modal-content">
                <h4>Написать статью</h4>
                <div class="row">
                    <div class="input-field col s12">
                        <input id="donateSum" required name="title" type="text" class="validate">
                        <label for="money">Заголовок</label>
                    </div>
                    <div class="input-field col s12">
                        <input id="donateSum" required name="img" type="text" class="validate">
                        <label for="money">Картинка (Url)</label>
                    </div>
                    <div class="input-field col s12">
                        <textarea id="donateSum" required name="text" class="materialize-textarea"></textarea>
                        <label for="money">Текст</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-close waves-effect btn-flat">Закрыть</a>
                <button name="send-faq" class="modal-close waves-effect btn-flat">Отправить</button>
            </div>
        </form>
    ';
}
?>