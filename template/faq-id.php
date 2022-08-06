<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $userInfo;

?>

<style>
    img {
        width: 100%;
        object-fit: cover
    }
</style>

<div style="width: 100%; overflow: hidden; position: absolute; height: 100%">
    <img src="<?php echo $this->faq['img'] ?>" style="filter: blur(10px); z-index: -1; position: absolute; top: 0; left: -20px; width: 110%; max-height: 450px;">
</div>
<div class="container" style="margin-top: 50px">
    <div class="section">
        <div class="row">
            <div class="col s12 m1 l2">
            </div>
            <div class="col s12 m10 l8">
                <?php echo ($userInfo['admin_level'] > 0) ? '<a href="#editFaq" class="modal-trigger btn blue accent-4 waves-effect">Редактировать</a>' : '' ?>
                <div class="center-block card-panel">
                    <h4 class="wd-font"><?php echo $this->faq['title'] ?></h4>
                    <?php echo str_replace('<img src', '<img style="margin: 0 -24px;width: calc(100% + 48px);" class="materialboxed" src', nl2br(htmlspecialchars_decode(htmlspecialchars_decode($this->faq['text'])))) ?>
                </div>
            </div>
            <div class="col s12 m1 l2">
            </div>
        </div>
    </div>
</div>

<?php
if ($userInfo['admin_level'] > 0) {
    echo '
        <!-- Modal Structure -->
        <form method="post" id="editFaq" class="modal modal-fixed-footer">
            <div class="modal-content">
                <h4>Написать статью</h4>
                <div class="row">
                    <div class="input-field col s12">
                        <input id="donateSum" required name="id" value="' . $this->faq['id'] . '" type="hidden" class="validate">
                        <input id="donateSum" required name="title" value="' . $this->faq['title'] . '" type="text" class="validate">
                        <label for="money">Заголовок</label>
                    </div>
                    <div class="input-field col s12">
                        <input id="donateSum" required name="img" value="' . $this->faq['img'] . '" type="text" class="validate">
                        <label for="money">Картинка (Url)</label>
                    </div>
                    <div class="input-field col s12">
                        <textarea id="donateSum" required name="text" class="materialize-textarea">' . htmlspecialchars_decode(htmlspecialchars_decode($this->faq['text'])) . '</textarea>
                        <label for="money">Текст</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-close waves-effect btn-flat">Закрыть</a>
                <button name="edit-faq" class="modal-close waves-effect btn-flat">Редактировать</button>
            </div>
        </form>
    ';
}
?>