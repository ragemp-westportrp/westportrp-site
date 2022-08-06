<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}
?>
<div class="container" style="margin-top: 5%; margin-bottom: 5%">
    <div class="section">
        <div class="row">
            <div class="col s12 m2 l4">
                <form method="post" class="card hide">
                    <div class="card-content">
                        <div class="row">
                            <div class="col s12">
                                <h5 class="wd-font">Восстановление через почту</h5>
                            </div>
                            <div class="input-field col s12">
                                <input id="email" required name="email" type="email" class="validate">
                                <label for="email">Email</label>
                                <button name="act-unk" class="btn wd-font waves-effect z-depth-0 right blue accent-4">Восстановить</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col s12 m6 l4">
                <form method="post" class="card">
                    <div class="card-content">
                        <div class="row">
                            <div class="col s12">
                                <h5 class="wd-font">Быстрое восстановление</h5>
                            </div>
                            <div class="input-field col s12">
                                <input id="login" name="login" required type="text" class="validate">
                                <label for="login">SocialClub (Ник socialclub.rockstargames.com)</label>
                            </div>
                            <div class="input-field col s12">
                                <input id="email" required name="email" type="email" class="validate">
                                <label for="email">Email</label>
                                <button name="act-get-login" class="btn wd-font waves-effect z-depth-0 right blue accent-4">Восстановить</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col s12 m6 l4">
                <label>Нет, если ты и это забыл, то напиши техническим администраторам в <a href="https://discord.gg/84VerfZBGT">дискорде</a></label>
            </div>
        </div>
    </div>
</div>