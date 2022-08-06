<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}
?>

<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12 l4">
                <!--   Icon Section   -->
                <div style="height: 50px; width: 100%;">
                    <h4 class="black-text">Авторизация</h4>
                </div>
                <div class="card-panel row">
                    <form class="row" method="post">
                        <div class="input-field col s12">
                            <input id="login" type="text" class="validate" name="rp-email-name">
                            <label for="login">Логин</label>
                        </div>
                        <div class="input-field col s12">
                            <input id="password" type="password" class="validate" name="rp-email-password">
                            <label for="password">Пароль</label>
                        </div>
                        <div class="input-field col s12">
                            <button class="btn waves-effect waves-light blue right" type="submit" value="true" name="rp-email-login">Войти
                                <i class="material-icons right">send</i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col l1 hide-on-med-and-down"></div>
            <div class="col s12 l4">
                <!--   Icon Section   -->
                <div style="height: 50px; width: 100%;">
                    <h4 class="black-text">Регистрация</h4>
                </div>
                <div class="card-panel row">
                    <form class="row" method="post">
                        <div class="input-field col s12">
                            <input id="login" type="text" class="validate" name="rp-email-name" onkeyup="this.value = this.value.replace(/ /g,'')">
                            <label for="login">Логин</label>
                        </div>
                        <div class="input-field col s12">
                            <input id="password" type="password" class="validate" name="rp-email-password-1">
                            <label for="password">Пароль</label>
                        </div>
                        <div class="input-field col s12">
                            <input id="password" type="password" class="validate" name="rp-email-password-2">
                            <label for="password">Повторите пароль</label>
                        </div>
                        <div class="input-field col s12">
                            <button class="btn waves-effect waves-light blue right" type="submit" value="true" name="rp-email-reg">Регистрация
                                <i class="material-icons right">send</i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>