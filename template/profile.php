<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
global $user;
global $userInfo;
global $server;
global $serverName;
global $page;
global $typeLogUser;
global $avatars;
global $tmp;
global $view;

//

if($page['p'] == 'admin/users') {
    //print_r($this->userInfo);
}
$accounts = $qb->createQueryBuilder('users')->selectSql()->where('social = \'' . $userInfo['social'] . '\'')->executeQuery()->getResult();
$idx = 0;


$hashByHash = hash('sha256', time() . $userInfo['login'] . '_' . $userInfo['social'] . '_' . $userInfo['id']);
$qb->createQueryBuilder('accounts')->updateSql(['hash_acc'], [$hashByHash])->where('id = ' . $userInfo['id'])->executeQuery()->getResult();

?>

<div class="hide" style="width: 100%; overflow: hidden; position: absolute; height: 100%; z-index: -1;">
    <img src="https://i.imgur.com/lR9SKql.jpg" style="filter: blur(10px); z-index: -1; position: absolute; top: 0; left: -20px; width: 110%; max-height: 460px; object-fit: cover;">
</div>

<div class="container bw-text" style="padding-top: 40px">
    <div class="section">
        <div class="row">
            <?php

            for ($i = 0; $i < 3; $i++) {
                if (!isset($accounts[$i])) {

                    $avatarsList = $avatars[rand(0, 1)];

                    echo '
                        <div class="col s12 l4">
                            <div style="border: 1px solid #333333;border-radius: 18px;padding-bottom: 10px;">
                                <h5 class="wd-font grey-text center">Отсуствует</h5>
                                <img style="width: 100%; height: 172px; object-fit: contain; object-position: top; opacity: 0.6" src="/client/images/logo/logo-w.png">
                                <div class="center">
                                    <label>В игре</label><br>Нет информации
                                    <hr>
                                    <label>Средний онлайн</label><br>Нет информации
                                    <hr>
                                    <label>Финансы</label><br>Нет информации
                                    <hr>
                                    <div class="grey-text">...</div>
                                </div>
                            </div>
                        </div>
                    ';
                } else {
                    $account = $accounts[$i];

                    $sex = json_decode($account['skin'])->SKIN_SEX;

                    $avatarsList = $avatars[$sex];
                    $days = $server->getDaysFromTime($account['reg_timestamp']);
                    echo '
                        <div class="col s12 l4">
                             <a href="/account-info-' . $account['id'] . '" style="display: block; border: 1px solid #333333;border-radius: 18px;padding-bottom: 10px;">
                                <h5 class="wd-font bw-text center">' . $account['name'] . '</h5>
                                <img style="width: 100%; height: 172px; object-fit: contain; object-position: top" src="' . $avatarsList[rand(0, count($avatarsList) - 1)] . '">
                                <div class="center white-text">
                                    <label>В игре</label><br>' . round(($account['online_time'] * 8.5 / 60), 2) . 'ч
                                    <hr>
                                    <label>Средний онлайн</label><br>' . round(($account['online_time'] * 8.5 / 60) /  $days, 2) . 'ч
                                    <hr>
                                    <label>Финансы</label><br>$' . number_format($account['money'] + $account['money_bank']) . '
                                    <hr>
                                    <div class="amber-text">Подробная статистика</div>
                                </div>
                            </a>
                        </div>
                    ';
                }
            }
            ?>
        </div>
    </div>
</div>
<div id="settings">
    <div class="container bw-text" style="margin-top: 100px">
        <div class="section">
            <div class="row">
                <div class="col s12 m3 l4"></div>
                <div class="col s12 m6 l4">
                    <form method="post" class="card">
                        <div class="card-content">
                            <div class="row">
                                <div class="col s12">
                                    <h5 class="wd-font">Сменить пароль</h5>
                                </div>
                                <div class="input-field col s12">
                                    <input value="<?php echo $userInfo['login'] ?>" name="login" required type="hidden" class="validate">
                                    <input value="<?php echo $hashByHash ?>" name="hash" required type="hidden" class="validate">
                                    <input onkeypress="return validateKeypress(alphanumeric)" id="password1" name="password1" required type="password" class="validate">
                                    <label for="password1">Пароль</label>
                                </div>
                                <div class="input-field col s12">
                                    <input onkeypress="return validateKeypress(alphanumeric)" id="password2" required name="password2" type="password" class="validate">
                                    <label for="password2">Повторите пароль</label>
                                    <br><br><button name="act-change-pass" class="btn wd-font waves-effect z-depth-0 right border-amber border-accent-4">Сменить</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>