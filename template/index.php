<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $logList;
global $userInfo;
global $qb;

$srv1Online = $qb->createQueryBuilder('monitoring')->selectSql()->executeQuery()->getSingleResult();
$srv1Procent = ($srv1Online['online'] / $srv1Online['max_online']) * 100;

?>

<div class="wb" style="position: absolute;z-index: -1;height: 550px;top: -20px;">
    <img style="width: 100%; height: 100%; object-fit: cover; object-position: right; opacity: 0.8; transform: scaleX(-1); filter: blur(10px)" src="https://i.imgur.com/EahoqpI.png">
</div>
<div class="container bw-text">
    <div class="section">
        <div class="row">
            <div class="col s12" style="padding-top: 40px">
                <div style="display: flex; width: 100%"><img style="width: 200px; height: 200px; margin: 0 auto" src="/client/images/logo/logo-w.png"></div>
                <div style="display: flex; width: 100%"><h5 style="margin: 34px auto" class="white-text">Американская мечта прямо на твоих экранах</h5></div>
            </div>

        </div>
    </div>
    <div class="section">
        <div class="row">

            <div class="col s12 m4">
                <div class="center hide">
                    <div class="progress grey darken-4" style="height: 124px; padding: 24px 0;">
                        <div style="z-index: 100; position: absolute; width: 100%;">
                            <h4 class="wd-font" style="margin: 0"><a class="bw-text" href="/servers">Albany</a></h4>
                            <div class="grey-text"><?php echo $srv1Online['ip'] ?></div>
                            <div class="grey-text"><?php echo $srv1Online['online'] ?>/<?php echo $srv1Online['max_online'] ?></div>
                        </div>
                        <div class="determinate grey darken-3" style="width: <?php echo $srv1Procent ?>%;"></div>
                    </div>
                    <div class="grey-text card-panel hide" style="padding-top: 0"><a class="grey-text" href="/servers"><?php echo $srv1Online['ip'] ?></a></div>
                </div>
            </div>
            <div class="col s12 m4">
                <div class="center">
                    <div class="progress grey darken-4" style="height: 124px; padding: 24px 0;">
                        <div style="z-index: 100; position: absolute; width: 100%;">
                            <h4 class="wd-font" style="margin: 0"><a class="bw-text" href="/banlist">Sacramento</a></h4>
                            <div class="grey-text hide"><?php echo $srv1Online['ip'] ?></div>
                            <div class="grey-text"><?php echo $srv1Online['online'] ?>/<?php echo $srv1Online['max_online'] ?></div>
                            <div class="grey-text">Мы ждем тебя!</div>
                        </div>
                        <div class="determinate grey darken-3" style="width: <?php echo $srv1Procent ?>%;"></div>
                    </div>
                    <div class="grey-text card-panel hide" style="padding-top: 0"><a class="grey-text" href="/banlist"><?php echo $srv1Online['ip'] ?></a></div>
                </div>
            </div>
            <div class="col s12 m4">
                <div class="center hide">
                    <div class="progress grey darken-4" style="height: 124px; padding: 24px 0;">
                        <div style="z-index: 100; position: absolute; width: 100%;">
                            <h4 class="wd-font" style="margin: 0"><a class="bw-text" href="/servers">Tallahassee</a></h4>
                            <div class="grey-text"><?php echo $srv1Online['ip'] ?></div>
                            <div class="grey-text"><?php echo $srv1Online['online'] ?>/<?php echo $srv1Online['max_online'] ?></div>
                        </div>
                        <div class="determinate grey darken-3" style="width: <?php echo $srv1Procent ?>%;"></div>
                    </div>
                    <div class="grey-text card-panel hide" style="padding-top: 0"><a class="grey-text" href="/servers"><?php echo $srv1Online['ip'] ?></a></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container" style="margin-top: 120px">
    <div class="section bw-text">
        <div class="row">
            <div class="col s12 center"><h4 class="wd-font">Как начать играть</h4></div>
            <div class="col s12 l4">
                <div class="card">
                    <div class="card-image">
                        <a target="_blank" href="https://playo.ru/goods/gta5/?s=m4d9l06f"><img style="height: 200px; object-fit: cover;" src="https://i.imgur.com/UY8xFdm.png"></a>
                        <span class="card-title">Шаг 1</span>
                    </div>
                    <div class="card-content">
                        <p>Купите лицензионный ключ GTA:V. Мы специально подобрали для вас надеждый и дешевый магазин с ключами GTA:V.</p>
                    </div>
                    <div class="card-action">
                        <a target="_blank" href="https://playo.ru/goods/gta5/?s=m4d9l06f" class="btn z-depth-0 green-text wd-font">КУПИТЬ ЗА 599P</a>
                    </div>
                </div>
            </div>
            <div class="col s12 l4">
                <div class="card">
                    <div class="card-image">
                        <a href="https://rage.mp"><img style="height: 200px; object-fit: cover;" src="https://i.imgur.com/UZNR4At.png"></a>
                        <span class="card-title">Шаг 2</span>
                    </div>
                    <div class="card-content">
                        <p>Скачайте Rage Multiplayer с официального сайта rage.mp для того чтобы вы могли присоеденится к нашему серверу</p>
                    </div>
                    <div class="card-action">
                        <a target="_blank" href="https://rage.mp" class="btn z-depth-0 green-text wd-font">Скачать</a>
                    </div>
                </div>
            </div>
            <div class="col s12 l4">
                <div class="card">
                    <div class="card-image">
                        <a href="https://discord.gg/84VerfZBGT"><img style="height: 200px; object-fit: contain;" src="/client/images/logo/logo-w.png"></a>
                        <span class="card-title">Шаг 3</span>
                    </div>
                    <div class="card-content">
                        <p>Выбери свой путь и наслаждайся игрой, стань полицейским, грабителем или кем угодно! Добро пожаловать в американскую мечту!</p>
                    </div>
                    <div class="card-action">
                        <a target="_blank" href="rage://v/connect?ip=111.111.111.111:22005" class="btn z-depth-0 green-text wd-font">Подключиться</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container hide" style="margin-top: 60px">
    <div class="section">
        <div class="row">
            <div class="col s12 center">
                <h4 class="bw-text wd-font">Решение ошибок и проблем</h4>
            </div>
            <div class="col s12 m6">
                <ul class="collapsible z-depth-1">
                    <li>
                        <div class="collapsible-header"><i class="material-icons">filter_drama</i>Игра отключается после захода на сервер</div>
                        <div class="collapsible-body">
                            <p>
                                Зайдите в папку RAGE MP -> Client resources -> IP_PORT сервера, удалите содержимое папки, либо саму папку
                                <br><br>
                                Либо попробуйте переместить папку с RAGE на другой диск (Например, из C:\ в D:\)
                                <br><br>
                                Если сервер с войс чатом, отключите любые программы, которые блокируют доступ к микрофону или изменяют ваш голос.
                                <br><br>

                            </p>
                        </div>
                    </li>
                    <li>
                        <div class="collapsible-header"><i class="material-icons">filter_drama</i>RageMP зависает, когда я нажимаю на сервер</div>
                        <div class="collapsible-body">
                            <p>
                                Зачастую это происходит когда в папке с гта отсутствует файл GTAV.exe. Если файл присутствует, то проверьте, может ли стим запустить гта 5.
                                <br><br>
                                
                            </p>
                        </div>
                    </li>
                    <li>
                        <div class="collapsible-header"><i class="material-icons">filter_drama</i>(STEAM ONLY) Запускается одиночный режим</div>
                        <div class="collapsible-body">
                            <p>
                                Выйдите из стима, потом запустите RAGE и дайте ему запустить стим самому, после чего вы с большой вероятностью загрузитесь в мультиплеер.
                                <br><br>
                            </p>
                        </div>
                    </li>
                    <li>
                        <div class="collapsible-header"><i class="material-icons">filter_drama</i>Запускается одиночный режим или главное меню</div>
                        <div class="collapsible-body">
                            <p>
                                Убедитесь, что пакет <a target="_blank" href="https://www.microsoft.com/en-us/download/details.aspx?id=48145">Visual C++ redistributable 2015</a> был установлен.
                                Если он установлен, отключите windows defender.
                                <br><br>
                            </p>
                        </div>
                    </li>
                    <li>
                        <div class="collapsible-header"><i class="material-icons">filter_drama</i>RageMP закрывается сразу после открытия
                        </div>
                        <div class="collapsible-body">
                            <p>
                                Выключите любые антивирусы или добавьте папку в исключения, затем перезагрузите компьютер.
                                <br><br>
                                Если это не помогло, то, возможно, ваше подключение блокирует cloudflare, попробуйте подключиться через VPN.
                                <br><br>
                            </p>
                        </div>
                    </li>
                    <li>
                        <div class="collapsible-header"><i class="material-icons">filter_drama</i>GTA V запускается с черным экраном и отключается.</div>
                        <div class="collapsible-body">
                            <p>
                                Возможные программы, из-за которых это происходит:
                                <br>- MSI Afterburner
                                <br>- AI Suite 3
                                <br>- GameFirst V
                                <br>- Malwarebytes
                                <br>- Norton Security / Antivirus Software
                                <br>- RivaTuner Statistics Server (7.2.2)
                                <br>- Microsoft Gaming Overlay (или 'Game Bar')
                                <br><br>
                                Чтобы удостовериться, что это одна из программ выше, откройте файл main_logs.txt в папке GTA V. Если там будет надпись FYDIGF, то необходимо отключить эти программы.
                                <br><br>
                            </p>
                        </div>
                    </li>
                    <li>
                        <div class="collapsible-header"><i class="material-icons">filter_drama</i>GTA V запускается с черным экраном, а затем внезапно закрывается/вылетает после нажатия «Play Now»</div>
                        <div class="collapsible-body">
                            <p>
                                Если это происходит впервые после запуска RageMP или вы только что его переустановили, это происходит из-за того, что кнопка 'Play Now' переподключает к последнему серверу, на котором вы играли. Используйте вкладку 'Servers' иди 'Direct Connect', если подключаетесь впервые, после этого можно использовать «Play Now».
                                <br><br>
                            </p>
                        </div>
                    </li>
                    <li>
                        <div class="collapsible-header"><i class="material-icons">filter_drama</i>Если ничего выше не помогло</div>
                        <div class="collapsible-body">
                            <p>
                                <br>1. Переустановите RAGE Multiplayer.
                                <br>2. Убедитесь, что игра никак не была модифицирована.
                                <br>3. Переустановите GTA V или пройдите верификацию файлов.
                                <br>4. Запустите RAGE Multiplayer от имени администратора.
                                <br>5. Установите необходимые пакеты:
                                <br>
                                <br>a) <a target="_blank" href="https://www.microsoft.com/en-us/download/details.aspx?id=48145">https://www.microsoft.com/en-us/download/details.aspx?id=48145</a>
                                <br>b) <a target="_blank" href="https://www.microsoft.com/en-us/download/details.aspx?id=53344&desc=dotnet462">https://www.microsoft.com/en-us/download/details.aspx?id=53344&desc=dotnet462</a>
                                <br>
                                <br>6. Установите RAGE Multiplayer и GTA V на один диск.
                                <br>7. Установите RageMP и GTA V на разные диски.
                                <br>8. Отключите любые оверлеи (Steam, OBS, Discord overlay, Overwolf).
                                <br>9. Убедитесь, что присутствует файл сохранения (запустите одиночную игру и сохраните ее).
                                <br>10. Удалите любые моды на GTA V.
                                <br>11. Выключите любые антивирусы или добавьте папку в исключения. Добавьте RAGE в исключения Firewall.
                                <br>12. Нажмите пкм на ragemp.exe и выберите в меню "Troubleshoot compatibility"/"Исправление проблем с совместимостью".
                                <br>13. (NVIDIA USERS) отключите Shadowplay.
                                <br>14. (STEAM ONLY) Выйдите из стима, потом запустите RAGE и дайте ему запустить стим самому.
                                <br><br>
                            </p>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="col s12 m6">
                <ul class="collapsible z-depth-1">
                    <li>
                        <div class="collapsible-header"><i class="material-icons">filter_drama</i>System ComponentModel Win32Exception (0x80004005)</div>
                        <div class="collapsible-body">
                            <p>
                                Выключите любые антивирусы или добавьте папку в исключения и попробуйте установить RAGE снова. Эта ошибка чаще всего встречается если антивирус удаляет файлы мультиплеера.
                                <br><br>
                                Если отключение антивируса/windows defender не помогла, нажмите пкм на updater.exe и выберите в меню "Troubleshoot compatibility"/"Исправление проблем с совместимостью".
                                <br><br>
                            </p>
                        </div>
                    </li>
                    <li>
                        <div class="collapsible-header"><i class="material-icons">filter_drama</i>I'm getting frameskipping while playing on any server</div>
                        <div class="collapsible-body">
                            <p>
                                Отключите autohotkey. Если монитор 144Hz, ограничьте fps/hz до 60.
                                <br><br>
                            </p>
                        </div>
                    </li>
                    <li>
                        <div class="collapsible-header"><i class="material-icons">filter_drama</i>ERROR: Your game version is not supported by RAGE Multiplayer.</div>
                        <div class="collapsible-body">
                            <p>
                                Если GTA V только что обновилась и RAGE показывает эту ошибку, подождите обновления мультиплеера. Если же обновления GTA V не было, то вы используете старую версию и необходимо обновиться.
                                <br><br>
                                <a class="btn blue accent-4 waves-effect z-depth-0" target="_blank" href="https://support.rockstargames.com/hc/en-us/articles/115014280127-Verifying-system-files-on-your-PC-for-GTAV">Проверить файлы</a>
                                <br><br>
                            </p>
                        </div>
                    </li>
                    <li>
                        <div class="collapsible-header"><i class="material-icons">filter_drama</i>Failed to retrieve the install directory (Не удалось найти путь установки)</div>
                        <div class="collapsible-body">
                            <p>
                                Это происходит, когда вы указываете неверную папку с GTA V или исполняемый файл гта/любые другие файлы нужно обновить. Во-первых, убедитесь, что вы указали верную папку с GTAV.exe. Если папка указана верно, то запустите верификацию файлов гта.
                                <br><br>
                            </p>
                        </div>
                    </li>
                    <li>
                        <div class="collapsible-header"><i class="material-icons">filter_drama</i>System Net WebException: The remote server returned an error: (403)</div>
                        <div class="collapsible-body">
                            <p>
                                Ваш IP был заблокирован, смените его или используйте VPN.
                                <br><br>
                            </p>
                        </div>
                    </li>
                    <li>
                        <div class="collapsible-header"><i class="material-icons">filter_drama</i>Я запускаю RAGE Multiplayer и ничего не происходит</div>
                        <div class="collapsible-body">
                            <p>
                                Выключите любые антивирусы или добавьте папку в исключения, это происходит, когда не хватает каких либо файлов RAGE. Если у вас нет антивируса, переустановите RAGE Multiplayer.
                                <br><br>
                            </p>
                        </div>
                    </li>
                    <li>
                        <div class="collapsible-header"><i class="material-icons">filter_drama</i>"A legal up to date Grand Theft Auto V copy is required to play RAGE Multiplayer"</div>
                        <div class="collapsible-body">
                            <p>
                                Нужно <a href="https://playo.ru/goods/gta5/?s=m4d9l06f">купить</a> лицензионную копию игры.
                                <br><br>
                                Если вы приобрели игру и все равно видите эту ошибку, верифицируйте файлы. Если это не помогает, перейдите в папку с гта и удалите Файл 'GTA5.exe', затем запустите 'PlayGTA5.exe' и загрузитесь в игру. После этого попробуйте зайти через RAGE повторно.
                                <br><br>
                            </p>
                        </div>
                    </li>
                    <li>
                        <div class="collapsible-header"><i class="material-icons">filter_drama</i>Failed to retrieve the install directory (Не удалось найти путь установки)</div>
                        <div class="collapsible-body">
                            <p>
                                Это происходит, когда вы указываете неверную папку с GTA V или исполняемый файл гта/любые другие файлы нужно обновить. Во-первых, убедитесь, что вы указали верную папку с GTAV.exe. Если папка указана верно, то запустите верификацию файлов гта.
                                <br><br>
                            </p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>