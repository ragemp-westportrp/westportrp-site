<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}
?>

<script>
    var alpha = /[ A-Za-z]/;
    var numeric = /[0-9]/;
    var alphanumeric = /[ A-Za-z0-9]/;

    function validateKeypress(validChars) {
        var keyChar = String.fromCharCode(event.which || event.keyCode);
        return validChars.test(keyChar) ? keyChar : false;
    }
</script>
<div class="container" style="margin-top: 5%; margin-bottom: 5%">
    <div class="section">
        <div class="row">
            <div class="col s12 m2 l4"></div>
            <div class="col s12 m6 l4">
                <form method="post" class="card">
                    <div class="card-content">
                        <div class="row">
                            <div class="input-field col s12">
                                <input id="login" name="login" required type="text" class="validate">
                                <label for="login">Логин</label>
                            </div>
                            <div class="input-field col s12">
                                <input id="passw" required onkeypress="return validateKeypress(alphanumeric)" name="pass" type="password" class="validate">
                                <label for="passw">Пароль</label>
                                <br>
                                <br>
                                <button name="act-login" class="btn wd-font waves-effect right border-amber border-accent-4">Войти</button>
                                <a href="/wheremypass" class="btn wd-font waves-effect right border-grey" style="margin: 0 8px">Забыл пароль</a>
                            </div>
                            <div class="col s12 right-align hide">
                                <label><a class="grey-text" href="/wheremypass">Забыл пароль :c</a></label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>