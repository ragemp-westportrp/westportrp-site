<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $serverName;

?>

<footer class="grey darken-3" style="padding: 24px">
    <div class="footer-copyright grey-text">
        <div class="container">
            Copyright © <?php echo date('Y'); ?> <a class="grey-text">State 99 Team</a>
        </div>
    </div>
</footer>

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