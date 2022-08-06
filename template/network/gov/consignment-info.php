<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}
global $userInfo;
?>
<style>
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
            <div class="col s12">
                <div class="row">
                    <div class="col s12 m5 l3">
                        <img onerror="this.src='https://i.imgur.com/O5JDV6b.png'" style="width: 100%; object-fit: cover;" src="<?php echo htmlspecialchars_decode($this->news['img']) ?>">
                    </div>
                    <div class="col s12 m7 l9">
                        <h4 style="margin-top: 80px" class="black-text"><?php echo htmlspecialchars_decode($this->news['title']) ?>
                        <br>
                        <div style="font-size: 16px" class="grey-text">Глава партии: <?php echo htmlspecialchars_decode($this->news['user_owner']) ?></div>
                        </h4>
                    </div>
                </div>
            </div>
            <div class="col s12">
                <div style="margin-bottom: 50px;">
                    <?php echo htmlspecialchars_decode(htmlspecialchars_decode($this->news['content'])) ?>
                    <hr>
                    <label style="display: flex;"><i style="font-size: 0.9rem; margin: 2px" class="material-icons">person</i> <?php echo htmlspecialchars_decode($this->news['user_owner']) ?></label>
                    <?php
                        if ($userInfo['id'] == $this->news['user_id'] || $userInfo['admin_level'] > 3) {
                            echo '<br>
                                <form method="post" action="/network/gov/consignment-create">
                                    <input type="hidden" name="id" value="' . $this->news['id'] . '">
                                    <button class="btn waves-effect waves-light red accent-4 z-depth-0" type="submit" name="delete-consignment">Удалить</button>
                                    <a href="/network/gov/consignment-create?edit=' . $this->news['id'] . '" class="btn waves-effect waves-light green accent-4 z-depth-0" type="submit">Редактировать</a>
                                </form>
                            ';
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>