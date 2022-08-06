<?php
global $user;

if (!$user->isLogin() && isset($_GET['login']) && isset($_GET['password'])) {
    //header('Location: /browser?login=' . $_GET['login'] . '&password=' . $_GET['password']);
    //die("Reloading...");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0">
    <title>Browser</title>

    <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="/client/css/extended.css" type="text/css" rel="stylesheet" media="screen,projection">

    <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="/client/js/extended.js"></script>
    <script src="/client/js/main.js?v=31"></script>

    <style>
        body {
            overflow: hidden;
        }
    </style>

    <script type="text/javascript">
        $document.ready(function() {
            let currentUrl = 'https://state-99.com/network/search';

            let history = [['Главная', 'https://state-99.com/network/search']];

            $(".dropdown-button").dropdown();

            $('.modal').modal();

            $('#iframeid').load(function() {
                currentUrl = $('#iframeid').contents().get(0).location.href;
                //currentUrl = window.self.location;
                $('#refresh').attr('data', currentUrl);
                $('#search').val(currentUrl.replace('https://state-99.com/network', 'network.sa'));

                var title = $("#iframeid").contents().find("title").html();
                $(document).find("title").html(title);

                $('#history-list').html('<a href="#!" onclick="$(\'#iframeid\').attr(\'src\', \'' + currentUrl + '\')" class="collection-item blue-text">' + title + '</a>' + $('#history-list').html());

                history.push([title, currentUrl]);
            });

            $('#back').click(function(){
                if (history.length > 1)
                    $('#iframeid').attr('src', history[history.length-2][1]);
                else
                    $('#iframeid').attr('src', history[history.length-1][1]);
            });

            $('#home').click(function(){
                $('#iframeid').attr('src', '/network/search');
            });

            $('#refresh').click(function(){
                $('#iframeid').attr('src', $('#refresh').attr('data'));
            });

            $('#search').keyup(function(e){
                if(e.keyCode == 13)
                {
                    let val = $('#search').val();
                    if (val.indexOf('state-99.com') >= 0)
                        $('#iframeid').attr('src', val);
                    else if (val.indexOf('network.sa') >= 0)
                        $('#iframeid').attr('src', val.replace('network.sa', 'https://state-99.com/network'));
                    else
                        $('#iframeid').attr('src', 'https://state-99.com/network/search?q=' + val);
                }
            });
        });

    </script>
</head>
<body style="position: absolute; margin: 0; right: 0; top: 0; bottom: 0; left: 0; overflow: hidden;">
<div class="white z-depth-0" style="width: 100%; height: 56px; z-index: 9999;position: absolute; border-bottom: 1px solid #efefef">
    <div style="margin: 8px 16px; display: flex; width: 100%;">
        <a class="waves-effect waves-light btn btn-floating z-depth-0 blue accent-4" id="back" data="/network/search" style="margin-right: 6px;"><i class="material-icons">arrow_back</i></a>
        <a class="waves-effect waves-light btn btn-floating z-depth-0 blue accent-4" id="refresh" data="/network/search" style="margin-right: 6px;"><i class="material-icons">refresh</i></a>
        <a class="waves-effect waves-light btn btn-floating z-depth-0 blue accent-4" onclick="$('#iframeid').attr('src', '/network/search/bookmarks')" style="margin-right: 6px;"><i class="material-icons">bookmarks</i></a>
        <a class="waves-effect waves-light btn btn-floating z-depth-0 blue accent-4" id="home" data="/network/search" style="margin-right: 6px;"><i class="material-icons">home</i></a>
        <div class="input-field" style="margin: 0; width: calc(100% - 270px); margin-right: 18px;">
            <input id="search" class="grey lighten-5" type="search" name="q" placeholder="Введите URL или запрос" required="" style="height: 40px; box-shadow: none; border-radius: 6px; border: 1px #ececec solid; padding-left: 10px">
        </div>
        <a class="waves-effect waves-light btn btn-floating z-depth-0 blue accent-4 modal-trigger" href="#modalHistory" style="margin-left: 0px;"><i class="material-icons">history</i></a>
    </div>
</div>
<div style="position: absolute; z-index: 9999; bottom: 24px; left: 24px">
    <label>ESC - Закрыть браузер</label>
</div>

<iframe id="iframeid" src="/network/search?google" style="width: 100%; height: calc(100% - 56px); border: 0; margin-top: 56px;"></iframe>

<div id="modalHistory" class="modal modal-fixed-footer">
    <div class="modal-content">
        <h4>История</h4>
        <ul class="collection" id="history-list">
        </ul>
    </div>
    <div class="modal-footer">
        <a class="modal-close waves-effect btn-flat">Закрыть</a>
    </div>
</div>

<script>

    $('.dropdown-button-new').dropdown({
            inDuration: 300,
            outDuration: 225,
            constrainWidth: false,
            hover: false,
            gutter: 0,
            belowOrigin: false,
            alignment: 'left',
            stopPropagation: false
        }
    );
</script>

</body>
</html>