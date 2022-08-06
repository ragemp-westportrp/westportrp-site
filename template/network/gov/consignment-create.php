<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
global $userInfo;

$isEdit = false;
$editItem = null;
if (isset($_GET['edit'])) {
    $editItem = $qb
        ->createQueryBuilder('rp_gov_party_list')
        ->selectSql()
        ->where('id = ' . intval($_GET['edit']) . ' AND user_id = ' . intval($userInfo['id']))
        ->executeQuery()
        ->getSingleResult()
    ;

    if (!empty($editItem))
        $isEdit = true;
}

?>
<script src="/client/ckeditor/ckeditor.js"></script>
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12">
                <h4 class="grey-text"><?php echo ($isEdit ? 'Редактировать' : 'Создать') ?> партию</h4>
                <br><label>(( Запрещено публиковать изображения из реальной жизни. Разрешены арты, рисунки или скриншоты только связанные с ГТА ))</label>
            </div>
            <div class="col s12 l8">
                <div class="card-panel" style="margin-bottom: 50px;">
                    <form method="post" class="row">
                        <div class="input-field col s12">
                            <?php echo ($isEdit ? '<input type="hidden" name="pid" value="' . $editItem['id'] . '">' : '') ?>
                            <input id="title" required type="text" class="validate" name="title" <?php echo ($isEdit ? 'value="' . htmlspecialchars_decode($editItem['title']) . '"' : '') ?>>
                            <label for="title">Название партии</label>
                        </div>
                        <div class="input-field col s12">
                            <input id="email" required type="text" class="validate" name="img" <?php echo ($isEdit ? 'value="' . htmlspecialchars_decode($editItem['img']) . '"' : '') ?>>
                            <label for="email">URL Изображения</label>
                        </div>
                        <div class="input-field col s12">
                            <input id="usermain" required type="text" class="validate" name="usermain" <?php echo ($isEdit ? 'value="' . htmlspecialchars_decode($editItem['user_owner']) . '"' : '') ?>>
                            <label for="usermain">Глава партии</label>
                        </div>
                        <div class="input-field col s12">
                            <textarea required id="textarea2" class="materialize-textarea" maxlength="1000" name="desc"><?php echo ($isEdit ? htmlspecialchars_decode($editItem['content_desc']) : '') ?></textarea>
                            <label for="textarea2">Краткое описание партии</label>
                        </div>
                        <div class="input-field col s12">
                            <textarea required id="textarea1" class="materialize-textarea" name="text"><?php echo ($isEdit ? htmlspecialchars_decode($editItem['content']) : '') ?></textarea>
                            <label for="textarea1"></label>
                        </div>
                        <div class="col s12" style="margin-top: 10px;">
                            <button class="btn waves-effect waves-light blue accent-4 z-depth-0" type="submit" name="<?php echo ($isEdit ? 'edit' : 'send') ?>-consignment">Опубликовать</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col s12 l4 grey-text">
                <h4>Правила создания партии</h4><br>
                1. Полное наименование партии:<br>
                2. Лозунг партии:<br>
                3. Логотип партии:<br>
                4. Политическое направление партии:<br>
                5. Идеология партии:<br>
                6. Устав партии:<br>
                7. Лидер партии:<br>
            </div>
        </div>
    </div>
</div>
<script>CKEDITOR.replace( "textarea1" );</script>