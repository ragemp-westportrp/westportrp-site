<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}
?>
</main>

<!--  Scripts-->
<!-- Compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script src="/client/js/extended.js"></script>
<script src="/client/js/material-charts.js"></script>
<script src="/client/js/main.js?v=31"></script>

<footer class="wd-font page-footer transparent">
    <div class="footer-copyright transparent">
        <div class="container grey-text">
            <div class="row">
                <div class="col s12 m6 l4">
                    <a class="grey-text" target="_blank" href="https://vk.com/gtav.state">Мы в VK</a>
                </div>
                <div class="col s12 m6 l4 center">
                    <a class="grey-text" style="margin: 0 4px"  href="/renouncement">Ответственность</a>
                    •
                    <a class="grey-text" style="margin: 0 4px" href="/personal">Политика</a>
                </div>
                <div class="col s12 m6 l4 center hide">
                    <div class="switch">
                        <label>
                            Night
                            <input onchange="$.enableLightTheme(this.checked)" <?php echo isset($_COOKIE['theme']) ? 'checked' : '' ?> name="changeside" type="checkbox">
                            <span class="lever"></span>
                            Day
                        </label>
                    </div>
                </div>
                <div class="col s12 m6 l4"><a class="grey-text right" href="https://vk.com/gtav.state">Copyright © <?php echo date('Y') ?> State99 Team</a></div>

            </div>
        </div>
    </div>
</footer>

<a id="scrollup" class="z-depth-4 animated btn-floating btn-large waves-effect blue accent-4 hide"><i class="material-icons white-text" style="font-size: 56px;">keyboard_arrow_up</i></a>

<?php
if($this->modal['show']) {
    //echo '<script type="text/javascript">$(document).ready(function(){ $("#modalInfo").openModal(); });</script>';
    echo '<script type="text/javascript">M.toast({html: \'' . $this->modal['text'] . '\', classes: \'rounded\'});</script>';
}
?>

<!-- Yandex.Metrika counter -->
<script type="text/javascript" >

    (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
        m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
    (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

    ym(59018944, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true
    });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/59018944" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

</body>
</html>