<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $user;
global $qb;

if (isset($_GET['hash']) && isset($_GET['login'])) {

    $accInfo = $user->getAccountInfo($_GET['login']);
    $hash = hash('sha256', $accInfo['login'] . '_' . $accInfo['social'] . '_' . $accInfo['id']);

    if ($hash != $_GET['hash']) {

        echo '
            <div class="container" style="margin-top: 5%; margin-bottom: 5%">
                <div class="section">
                    <h2 class="wd-font bw-text">Ошибка доступа</h2>
                </div>
            </div>
        ';

        return;
    }

    $hashByHash = hash('sha256', time() . $hash);
    $qb->createQueryBuilder('accounts')->updateSql(['hash_acc'], [$hashByHash])->where('id = ' . $accInfo['id'])->executeQuery()->getResult();

    echo '
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
                                    <div class="col s12">
                                        <h5 class="wd-font">Ваш логин: ' . $_GET['login'] . '</h5>
                                    </div>
                                    <div class="input-field col s12">
                                        <input value="' . $_GET['login'] . '" name="login" required type="hidden" class="validate">
                                        <input value="' . $hashByHash . '" name="hash" required type="hidden" class="validate">
                                        <input onkeypress="return validateKeypress(alphanumeric)" id="password1" name="password1" required type="password" class="validate">
                                        <label for="password1">Пароль</label>
                                    </div>
                                    <div class="input-field col s12">
                                        <input onkeypress="return validateKeypress(alphanumeric)" id="password2" required name="password2" type="password" class="validate">
                                        <label for="password2">Повторите пароль</label>
                                        <button name="act-change-pass" class="btn wd-font waves-effect z-depth-0 right blue accent-4">Сменить</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    ';

    return;
}
?>
<div class="container" style="margin-top: 5%; margin-bottom: 5%">
    <div class="section">
        <h2 class="wd-font bw-text">Произошла ошибка, попробуйте еще раз</h2>
    </div>
</div>