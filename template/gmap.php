<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $serverName;
global $qb;

$priceFrom = (isset($_GET['pfrom']) ? intval($_GET['pfrom']) : 1);;
$priceTo = (isset($_GET['pto']) ? intval($_GET['pto']) : 200000000);
$onlyFree = (isset($_GET['onlyFree']) ? 1 : 99999999);

$gHousesAllCount = $qb->createQueryBuilder('houses')->selectSql('count(*)')->where("price > '" . intval($priceFrom) . "' AND price < '" . intval($priceTo) . "'")->executeQuery()->getSingleResult();
$gHousesFreeCount = $qb->createQueryBuilder('houses')->selectSql('count(*)')->where("price > '" . intval($priceFrom) . "' AND price < '" . intval($priceTo) . "' AND user_id = 0")->executeQuery()->getSingleResult();

$gCondoAllCount = $qb->createQueryBuilder('condos')->selectSql('count(*)')->where("price > '" . intval($priceFrom) . "' AND price < '" . intval($priceTo) . "'")->executeQuery()->getSingleResult();
$gCondoFreeCount = $qb->createQueryBuilder('condos')->selectSql('count(*)')->where("price > '" . intval($priceFrom) . "' AND price < '" . intval($priceTo) . "' AND user_id = 0")->executeQuery()->getSingleResult();

$gStockAllCount = $qb->createQueryBuilder('stocks')->selectSql('count(*)')->where("price > '" . intval($priceFrom) . "' AND price < '" . intval($priceTo) . "'")->executeQuery()->getSingleResult();
$gStockFreeCount = $qb->createQueryBuilder('stocks')->selectSql('count(*)')->where("price > '" . intval($priceFrom) . "' AND price < '" . intval($priceTo) . "' AND user_id = 0")->executeQuery()->getSingleResult();

$gAllCount = reset($gApartAllCount) + reset($gHousesAllCount) + reset($gCondoAllCount) + reset($gStockAllCount);
$gFreeCount = reset($gApartFreeCount) + reset($gHousesFreeCount) + reset($gCondoFreeCount) + reset($gStockFreeCount);
?>

<style>
    .leaflet-popup-content-wrapper {
        max-height: 300px;
        overflow-x: auto;
    }
</style>

<div style="position: absolute; left: 50px; top: 0px; z-index: 100; display: flex;">
    <a href="/" style="margin: 4px" class="btn btn-floating waves-effect wb bw-text z-depth-1"><i class="bw-text material-icons">home</i></a>
    <a href="/car-list" style="margin: 4px" class="btn btn-floating waves-effect wb bw-text z-depth-1"><i class="bw-text material-icons">directions_car</i></a>
    <a href="#modalFilter" style="margin: 4px" class="btn btn-floating waves-effect wb bw-text z-depth-1 modal-trigger"><i class="bw-text material-icons">filter_list</i></a>
    <div class="card" style="padding: 5px 20px; border-radius: 24px">
        <label>Свободного имущества: <?php echo $gFreeCount; ?> из <?php echo $gAllCount; ?></label>
    </div>
</div>

<form id="modalFilter" class="modal modal" style="z-index: 999">
    <div class="modal-content">
        <h4>Ценовой диапазон</h4>
        <div class="row">
            <div class="input-field col s6">
                <input id="ot" type="number" name="pfrom" placeholder="От" min="1" value="<?php echo $priceFrom; ?>" class="validate">
            </div>
            <div class="input-field col s6">
                <input id="do" type="number" name="pto" placeholder="До" min="1" value="<?php echo $priceTo; ?>" class="validate">
            </div>
            <div class="col s12">
                <div class="switch">
                    <label>
                        <input name="onlyFree" <?php echo (isset($_GET['onlyFree']) ? 'checked="checked"' : ''); ?> type="checkbox">
                        <span class="lever"></span>
                        Только свободные
                    </label>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="modal-close waves-effect waves-green btn-flat">Применить</button>
    </div>
</form>

<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12">
                <div class="card-panel black-text" style="padding: 0px; position: absolute;top: 0;bottom: 0;left: 0;right: 0;margin: 0px;" id="map-container">
                    <div id="map" class="reset leaflet-container leaflet-fade-anim black-text" style="position: relative; background-color: rgb(15, 168, 210);" tabindex="0"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    (function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
        (function (global){
            'use strict';

            var $ = (typeof window !== "undefined" ? window.$ : typeof global !== "undefined" ? global.$ : null),
                ko = (typeof window !== "undefined" ? window.K : typeof global !== "undefined" ? global.K : null),
                L = (typeof window !== "undefined" ? window.L : typeof global !== "undefined" ? global.L : null),
                CRS = require('/map/crs'),
                BaseLayerControl = require('/map/control/baselayer'),
                OverlayControl = require('/map/control/overlay'),
                ExitControl = require('/map/control/exit');

            $(document).ready(function () {

                var container = $('#map')[0];

                var map = L.map(container, {
                    crs: CRS,
                    attributionControl: false,
                    minZoom: 3,
                    maxZoom: 8,
                    maxNativeZoom: 5
                });

                var atlas_layer = L.tileLayer('https://gta-5-map.com/images/tiles/gta5/los-santos/atlas/{z}/{x}/{y}.png', {
                    continuousWorld: true,
                    noWrap: true,
                    maxNativeZoom: 5,
                    maxZoom: 8,
                    background: '#0fa8d2',
                    title: 'Atlas'
                }).addTo(map);

                var roadmap_layer = L.tileLayer('https://gta-5-map.com/images/tiles/gta5/los-santos/road/{z}/{x}/{y}.png', {
                    continuousWorld: true,
                    noWrap: true,
                    maxNativeZoom: 5,
                    maxZoom: 8,
                    background: '#1862ad',
                    title: 'Roads'
                });

                var satellite_layer = L.tileLayer('https://gta-5-map.com/images/tiles/gta5/los-santos/satellite/{z}/{x}/{y}.png', {
                    continuousWorld: true,
                    noWrap: true,
                    maxNativeZoom: 5,
                    maxZoom: 8,
                    background: '#143d6b',
                    title: 'Satellite'
                });

                var uv_layer = L.tileLayer('https://gta-5-map.com/images/tiles/gta5/los-santos/uv/{z}/{x}/{y}.png', {
                    continuousWorld: true,
                    noWrap: true,
                    maxNativeZoom: 5,
                    maxZoom: 6,
                    background: '#faedc4',
                    title: 'UV'
                });

                var layers = [atlas_layer, roadmap_layer, satellite_layer, uv_layer];

                // Leaflet uses grey background by default when there are no more tiles to display.
                // This changes it to match the background colour of our tiles.
                map.on('baselayerchange', function (e) {
                    this.getContainer().style.backgroundColor = e.layer.options.background;
                });

                map.setView([0, 2000], 2);
                map.addControl(new BaseLayerControl(layers));
                map.addControl(new ExitControl({ text: 'Copyright <?php echo date('Y') ?> STATE 99 & GrandTheftAuto.net & gta-5-map.com' }));


                var rIcon = L.icon({
                    iconUrl: '/client/map/house_r.png',
                    iconSize:     [14, 14], // size of the icon
                    iconAnchor:   [8, 16], // point of the icon which will correspond to marker's location
                    popupAnchor:  [0, -16] // point from which the popup should open relative to the iconAnchor
                });
                var gIcon = L.icon({
                    iconUrl: '/client/map/house_g.png',
                    iconSize:     [14, 14], // size of the icon
                    iconAnchor:   [8, 16], // point of the icon which will correspond to marker's location
                    popupAnchor:  [0, -16] // point from which the popup should open relative to the iconAnchor
                });
                var hIcon = L.icon({
                    iconUrl: '/client/map/house.png',
                    iconSize:     [14, 14], // size of the icon
                    iconAnchor:   [8, 16], // point of the icon which will correspond to marker's location
                    popupAnchor:  [0, -16] // point from which the popup should open relative to the iconAnchor
                });
                var sIcon = L.icon({
                    iconUrl: '/client/map/stock.png',
                    iconSize:     [14, 14], // size of the icon
                    iconAnchor:   [8, 16], // point of the icon which will correspond to marker's location
                    popupAnchor:  [0, -16] // point from which the popup should open relative to the iconAnchor
                });
                var aIcon = L.icon({
                    iconUrl: '/client/map/apartb.png',
                    iconSize:     [14, 14], // size of the icon
                    iconAnchor:   [8, 16], // point of the icon which will correspond to marker's location
                    popupAnchor:  [0, -16] // point from which the popup should open relative to the iconAnchor
                });

                map.preferCanvas = true;

                /*latlngs = [
                 [1.1349074707032742, -1550.6512724609386],
                 [88.23031994628921, -1444.8925573120127],
                 [47.51038684082046, -1410.3937250976574],
                 [-106.32047155761704, -1417.7459352417002],
                 [-127.81154736328111, -1435.2781286621105]
                 ];
                 L.polygon(latlngs, {color: 'red'}).bindPopup('Гетто1').addTo(map);

                 latlngs = [ //3
                 [-53.158336669921724, -1615.6900545043957],
                 [0.003798217773586733, -1550.6512724609386],
                 [-128.37710198974594, -1435.2781286621105],
                 [-183.2359007568358, -1505.406902343751]
                 ];
                 L.polygon(latlngs, {color: 'red'}).bindPopup('Гетто3').addTo(map);

                 latlngs = [ //2
                 [-183.2359007568358, -1506.5380115966807],
                 [-238.66025415039047, -1576.6667852783214],
                 [-240.92247265624985, -1710.1376771240245],
                 [-178.14590911865218, -1761.6031481323253],
                 [-52.592782043456886, -1616.2556091308604]
                 ];
                 L.polygon(latlngs, {color: 'red'}).bindPopup('Гетто2').addTo(map);

                 latlngs = [ //4
                 [-178.14590911865218, -1762.1687027587902],
                 [-46.93723577880844, -1608.3378443603526],
                 [24.322647155761867, -1667.1555255126964],
                 [-98.40270678710922, -1716.358778015138],
                 [-150.9992870483397, -1781.9631146850597]
                 ];
                 L.polygon(latlngs, {color: 'red'}).bindPopup('Гетто4').addTo(map);

                 latlngs = [ //5
                 [24.322647155761867, -1666.5899708862314],
                 [75.78811816406265, -1711.834341003419],
                 [13.011554626464994, -1791.0119887084973],
                 [-41.28168951416001, -1739.5465177001963],
                 [-89.91938739013658, -1757.0787111206066],
                 [-122.15600109863267, -1802.888635864259],
                 [-150.43373242187485, -1781.9631146850597],
                 [-100.09937066650376, -1714.6621141357432]
                 ];
                 L.polygon(latlngs, {color: 'red'}).bindPopup('Гетто5').addTo(map);

                 latlngs = [ //5
                 [-41.28168951416001, -1741.8087362060558],
                 [-89.91938739013658, -1757.6442657470714],
                 [-122.7215557250975, -1802.888635864259],
                 [20.929319396972804, -1922.220662048341],
                 [82.00921905517593, -1995.7427634887706],
                 [95.01697546386734, -2004.2260828857432],
                 [119.33582440185562, -1986.693889465333],
                 [127.25358917236343, -1969.161696044923],
                 [137.99912707519547, -1943.1461832275402],
                 [145.91689184570328, -1930.7039814453135],
                 [141.95800946044938, -1916.5651157836924],
                 [123.86026141357436, -1904.6884686279307],
                 [104.06584948730485, -1895.0740399780284],
                 [67.30479876708999, -1856.616325378419],
                 [13.011554626464994, -1791.577543334962],
                 [-41.28168951416001, -1740.1120723266613],
                 ];
                 L.polygon(latlngs, {color: 'red'}).bindPopup('Гетто9').addTo(map);

                 latlngs = [ //5
                 [94.4514208374025, -1688.0810466918956],
                 [75.22256353759781, -1711.834341003419],
                 [13.011554626464994, -1790.4464340820323],
                 [73.52589965820327, -1863.4029808959972],
                 [137.99912707519547, -1788.1842155761728],
                 [169.10463153076188, -1822.6830477905285],
                 [190.0301527099611, -1854.9196614990244],
                 [229.0534219360353, -1882.631838195802],
                 [270.90446429443375, -1838.518577331544],
                 [189.46459808349624, -1768.3898036499033],
                 [167.40796765136733, -1750.8576102294933]
                 ];
                 L.polygon(latlngs, {color: 'red'}).bindPopup('Гетто6').addTo(map);

                 latlngs = [ //5
                 [236.97118670654314, -1649.0577774658213],
                 [149.87577423095718, -1735.0220806884777],
                 [229.0534219360353, -1803.4541904907237],
                 [309.92773352050796, -1709.0065678710948]
                 ];
                 L.polygon(latlngs, {color: 'red'}).bindPopup('Гетто7').addTo(map);

                 latlngs = [ //5
                 [138.5646817016603, -1789.3153248291026],
                 [74.65700891113296, -1862.8374262695322],
                 [104.06584948730485, -1894.5084853515636],
                 [124.42581604003921, -1905.8195778808604],
                 [142.5235640869142, -1916.5651157836924],
                 [145.91689184570328, -1930.1384268188488],
                 [167.9735222778322, -1922.7862166748057],
                 [198.51347210693373, -1894.5084853515636],
                 [212.08678314208998, -1869.6240817871105],
                 [188.8990434570314, -1853.788552246095],
                 [169.10463153076188, -1822.6830477905285]
                 ];
                 L.polygon(latlngs, {color: 'red'}).bindPopup('Гетто8').addTo(map);

                 latlngs = [ //5
                 [211.52122851562515, -1870.75519104004],
                 [199.64458135986342, -1893.377376098634],
                 [169.10463153076188, -1922.7862166748057],
                 [146.4824464721681, -1930.1384268188488],
                 [119.33582440185562, -1987.8249987182628],
                 [94.4514208374025, -2004.2260828857432],
                 [117.07360589599625, -2030.807150329591],
                 [150.44132885742204, -2027.4138225708018],
                 [183.80905181884782, -2030.807150329591],
                 [204.734572998047, -2039.2904697265635],
                 [248.84783386230484, -1990.0872172241222],
                 [260.72448101806657, -1964.0717044067394],
                 [311.6243973999025, -1910.3440148925793],
                 [253.93782550048843, -1858.3129892578136],
                 [228.48786730957048, -1882.066283569337],
                 ];
                 L.polygon(latlngs, {color: 'red'}).bindPopup('Гетто10').addTo(map);

                 latlngs = [ //5
                 [248.84783386230484, -1991.2183264770517],
                 [284.47777532959, -2015.53717541504],
                 [301.4444141235353, -1998.00498199463],
                 [389.1053812255861, -2066.437091796876],
                 [447.357507751465, -1996.3083181152354],
                 [372.7042970581056, -1920.5239981689463],
                 [272.0355735473634, -1839.0841319580088],
                 [253.3722708740236, -1858.3129892578136],
                 [311.0588427734377, -1911.475124145509],
                 [260.72448101806657, -1965.2028136596691],
                 ];
                 L.polygon(latlngs, {color: 'red'}).bindPopup('Гетто11').addTo(map);

                 latlngs = [ //5
                 [447.92306237792985, -1997.439427368165],
                 [508.43740740966814, -2073.223747314454],
                 [480.7252307128908, -2088.493722229005],
                 [407.76868389892593, -2165.9747060546883],
                 [353.4754397583009, -2185.769117980958],
                 [320.10771679687514, -2169.3680338134777],
                 [274.2977920532228, -2129.2136553344735],
                 [227.35675805664079, -2086.2315037231456],
                 [200.7756906127931, -2061.3471001586927],
                 [203.60346374511735, -2039.2904697265635],
                 [247.71672460937515, -1990.652771850587],
                 [283.9122207031252, -2015.53717541504],
                 [301.4444141235353, -1999.1360912475598],
                 [389.67093585205095, -2067.568201049806]
                 ];
                 L.polygon(latlngs, {color: 'red'}).bindPopup('Гетто12').addTo(map);

                 latlngs = [ //5
                 [498.25742413330096, -1677.9010634155284],
                 [465.45525579834, -1715.2276687622082],
                 [445.6608438720705, -1760.4720388793955],
                 [430.95642358398453, -1780.266450805665],
                 [390.8020451049806, -1823.8141570434582],
                 [328.02548156738294, -1884.3285020751964],
                 [389.67093585205095, -1938.0561915893566],
                 [480.7252307128908, -1861.7063170166027],
                 [502.78186114501966, -1851.5263337402355],
                 [531.6251470947267, -1825.5108209228526],
                 [536.7151387329103, -1806.8475182495129],
                 [535.0184748535157, -1776.8731230468761],
                 [559.902878417969, -1722.579878906251]
                 ];
                 L.polygon(latlngs, {color: 'red'}).bindPopup('Гетто13').addTo(map);

                 latlngs = [ //5
                 [1402.579271850586, -1754.8164926147472],
                 [1318.8771871337892, -1776.8731230468761],
                 [1188.2340684204103, -1823.8141570434582],
                 [1201.2418248291017, -1863.4029808959972],
                 [1415.5870282592775, -1804.5852997436534]
                 ];
                 L.polygon(latlngs, {color: 'red'}).bindPopup('Гетто14').addTo(map);

                 latlngs = [ //5
                 [1332.4504981689454, -1632.091138671876],
                 [1379.9570867919924, -1695.4332568359387],
                 [1403.7103811035158, -1754.2509379882822],
                 [1321.7049602661134, -1776.3075684204111],
                 [1187.6685137939455, -1823.2486024169932],
                 [1154.8663454589846, -1729.932089050294],
                 [1247.051749572754, -1686.949937438966]
                 ];
                 L.polygon(latlngs, {color: 'red'}).bindPopup('Гетто15').addTo(map);

                 latlngs = [ //5
                 [1332.4504981689454, -1632.656693298341],
                 [1248.1828588256838, -1688.0810466918956],
                 [1154.8663454589846, -1731.0631983032238],
                 [1129.4163872680665, -1656.9755422363291],
                 [1165.6118833618166, -1610.600062866212],
                 [1212.5529173583986, -1571.5767936401378],
                 [1273.0672623901369, -1536.5124067993174],
                 [1295.1238928222658, -1576.1012306518564]
                 ];
                 L.polygon(latlngs, {color: 'red'}).bindPopup('Гетто16').addTo(map);

                 latlngs = [ //5
                 [1273.6328170166016, -1535.9468521728527],
                 [1338.6715990600587, -1501.448019958497],
                 [1419.5459106445314, -1468.6458516235361],
                 [1466.4869446411135, -1500.8824653320323],
                 [1339.2371536865237, -1597.0267518310557],
                 [1318.8771871337892, -1612.2967267456065]
                 ];
                 L.polygon(latlngs, {color: 'red'}).bindPopup('Гетто17').addTo(map);

                 latlngs = [ //5
                 [498.82297875976576, -1677.3355087890636],
                 [464.8897011718752, -1714.6621141357432],
                 [445.6608438720705, -1762.1687027587902],
                 [414.55533941650407, -1797.2330895996104],
                 [329.15659082031266, -1884.8940567016612],
                 [272.0355735473634, -1838.518577331544],
                 [230.184531188965, -1802.888635864259],
                 [309.92773352050796, -1708.4410132446299],
                 [350.0821119995119, -1744.63650933838],
                 [443.3986253662111, -1632.656693298341]
                 ];
                 L.polygon(latlngs, {color: 'red'}).bindPopup('Гетто18').addTo(map);

                 latlngs = [ //5
                 [1318.8771871337892, -1612.2967267456065],
                 [1331.8849435424806, -1631.5255840454113],
                 [1380.5226414184572, -1695.4332568359387],
                 [1403.7103811035158, -1753.6853833618175],
                 [1415.5870282592775, -1804.5852997436534],
                 [1201.8073794555667, -1863.968535522462],
                 [1264.0183883666994, -2060.2159909057627],
                 [1361.2937841186524, -2018.3649485473643],
                 [1418.9803560180667, -1958.4161581420908],
                 [1446.126978088379, -1885.4596113281261],
                 [1441.6025410766604, -1793.8397618408214],
                 [1408.8003727416994, -1697.1299207153331],
                 [1343.7615906982423, -1594.1989786987315]
                 ];
                 L.polygon(latlngs, {color: 'red'}).bindPopup('Гетто19').addTo(map);*/

                //map.addTo(path);

                //var myRenderer = L.canvas({ padding: 0.5 });
                //L.circle( [1000,1000], { renderer: myRenderer } ).addTo(map);

                var lat, lng;

                map.addEventListener('mousemove', function(ev) {
                    lat = ev.latlng.lat;
                    lng = ev.latlng.lng;
                });

                document.getElementById("map").addEventListener("contextmenu", function (event) {
                    // Prevent the browser's context menu from appearing
                    event.preventDefault();
                    console.log([lat, lng]);

                    return false; // To disable default popup.
                });


                DrawMarkers(L, map, rIcon, gIcon, sIcon, hIcon, aIcon);

                setTimeout(function() { document.querySelector("#delete-item").remove(); }, 1000);
            });

        }).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
    },{"/map/control/baselayer":2,"/map/control/exit":3,"/map/control/overlay":4,"/map/crs":5}],2:[function(require,module,exports){
        (function (global){
            /**
             * Custom base layer control.
             */
            'use strict';

            var L = (typeof window !== "undefined" ? window.L : typeof global !== "undefined" ? global.L : null);

            var px = function px(mixed) {
                return parseInt(('' + mixed).replace(/[^0-9]/g, ''), 10);
            };

            var CSS = {
                CONTAINER: 'control-baselayers-container',
                LAYER: 'control-baselayers-layer',
                LAYER_ACTIVE: 'control-baselayers-layer-active',
                LAYER_MINIMAP: 'control-baselayers-layer-minimap',
                LAYER_TITLE: 'control-baselayers-layer-title'
            };

            var substitutePlaceholders = function substitutePlaceholders(url, z, x, y) {
                return url.replace('{z}', z).replace('{y}', y).replace('{x}', x);
            };

            module.exports = L.Control.extend({

                initialize: function initialize(layers, options) {
                    this.layers = layers;
                    L.Util.setOptions(this, options);
                },

                onAdd: function onAdd(map) {
                    this.map = map;

                    var container = L.DomUtil.create('div', CSS.CONTAINER);

                    var self = this;

                    this.layers.forEach(function (layer, index) {

                        var preview_container = L.DomUtil.create('div', CSS.LAYER, container);

                        var preview_map_element = L.DomUtil.create('div', CSS.LAYER_MINIMAP, preview_container);
                        preview_map_element.style.backgroundImage = 'url(' + substitutePlaceholders(layer._url, 1, 0, 0) + ')';

                        var preview_title_element = L.DomUtil.create('div', CSS.LAYER_TITLE, preview_container);
                        preview_title_element.innerHTML = layer.options.title;

                        layer._previewElement = preview_container;

                        L.DomEvent.addListener(layer._previewElement, 'click', self.setBaseLayer.bind(self, index));
                    });

                    this.setBaseLayer(0);

                    if (!L.Browser.touch) {
                        L.DomEvent.disableClickPropagation(container).disableScrollPropagation(container);
                    } else {
                        L.DomEvent.on(container, 'click', L.DomEvent.stopPropagation);
                    }

                    return container;
                },

                setBaseLayer: function setBaseLayer(index_to_show) {
                    var self = this;

                    this.layers.forEach(function (layer, index) {

                        var is_on_map = self.map.hasLayer(layer);
                        var is_active = L.DomUtil.hasClass(layer._previewElement, CSS.LAYER_ACTIVE);

                        if (index == index_to_show) {

                            if (!is_on_map) {
                                self.map.addLayer(layer);
                            }

                            if (!is_active) {
                                L.DomUtil.addClass(layer._previewElement, CSS.LAYER_ACTIVE);
                            }

                            // Update main map's background to match the chosen tileset
                            self.map.getContainer().style.backgroundColor = layer.options.background;
                        } else {

                            if (is_on_map) {
                                self.map.removeLayer(layer);
                            }

                            if (is_active) {
                                L.DomUtil.removeClass(layer._previewElement, CSS.LAYER_ACTIVE);
                            }
                        }
                    });
                }
            });

        }).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
    },{}],3:[function(require,module,exports){
        (function (global){
            /**
             * Custom exit / 'return to main site' control.
             */
            'use strict';

            var L = (typeof window !== "undefined" ? window.L : typeof global !== "undefined" ? global.L : null);
//$ = require('jquery');

            var CSS = {
                PANEL: 'control-exit-panel',
                LINK: 'control-exit-link'
            };

            module.exports = L.Control.extend({

                options: {
                    position: 'bottomleft',
                    url: '/',
                    text: 'Back to site'
                },

                initialize: function initialize(options) {
                    L.Util.setOptions(this, options);
                },

                onAdd: function onAdd(map) {
                    this.map = map;

                    var container = L.DomUtil.create('div', CSS.PANEL);

                    this.link = L.DomUtil.create('a', CSS.LINK, container);

                    $(this.link).attr('href', this.options.url).text(this.options.text);

                    return container;
                } });

        }).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
    },{}],4:[function(require,module,exports){
        (function (global){
            /**
             * Custom overlay layer control.
             */
            'use strict';

            var L = (typeof window !== "undefined" ? window.L : typeof global !== "undefined" ? global.L : null);
//$ = require('jquery');

            /**
             * CSS classes referenced in the scripts.
             * @type {Object}
             */
            var CSS = {
                CONTAINER: 'control-overlays',
                PANEL: 'control-overlays-panel',
                PANEL_TOGGLER: 'control-overlays-panel-toggle',
                PANEL_HIDDEN: 'control-overlays-hidden',
                LAYER_LIST: 'control-overlays-list',
                LAYER_LIST_ITEM: 'control-overlays-list-item',
                LAYER_LIST_ITEM_ACTIVE: 'control-overlays-list-item-active'
            };

            //L.Icon.Default.imagePath = 'http://grandtheftauto.net/gta5/assets/lib/leaflet';

            /**
             * Export an extended (using Leaflet's OOP framework) L.Control class object.
             */
            module.exports = L.Control.extend({

                /**
                 * Default options
                 * @type {Object}
                 */
                options: {
                    hide_menu_label: '&raquo;',
                    show_menu_label: '&laquo;'
                },

                /**
                 * Store constructor arguments
                 * @param  {array}  layers
                 * @param  {object} options
                 * @return {void}
                 */
                initialize: function initialize(layers, options) {
                    this.layers = layers;
                    L.Util.setOptions(this, options);
                },

                /**
                 * Callback for adding this control to a map instance.
                 * @param  {L.Map} map
                 * @return {HTMLElement} the control's top level container element
                 */
                onAdd: function onAdd(map) {
                    this.map = map;

                    this._createLayoutHtml();
                    this._blockPropagationToMap(this._container);
                    this._addEventListeners();

                    this.setToggleLabel(this.options.hide_menu_label);
                    this.layers.forEach(this._createLayerHtml.bind(this));

                    return this._container;
                },

                /**
                 * Create the initial markup elements
                 * @return void
                 */
                _createLayoutHtml: function _createLayoutHtml() {
                    this._container = L.DomUtil.create('div', CSS.CONTAINER);
                    this._panel = L.DomUtil.create('div', CSS.PANEL, this._container);
                    this._list = L.DomUtil.create('ul', CSS.LAYER_LIST, this._panel);
                    this._toggler = L.DomUtil.create('div', CSS.PANEL_TOGGLER, this._panel);
                },

                /**
                 * Create markup for a given layer.
                 * @param  {L.ILayer} layer
                 * @return {void}
                 */
                _createLayerHtml: function _createLayerHtml(layer) {
                    var li = L.DomUtil.create('li', CSS.LAYER_LIST_ITEM, this._list);
                    var icon = L.DomUtil.create('span', 'fa fa-fw', li);

                    if (layer.icon) {
                        L.DomUtil.addClass(icon, layer.icon);
                        icon.setAttribute('title', layer.title);
                    }

                    li.appendChild(document.createTextNode(layer.title));

                    L.DomEvent.on(li, 'click', this.toggleLayer.bind(this, layer));

                    layer._controlElement = li;
                },

                /**
                 * Prevent event propogation from our control container up to the map instance.
                 * @param {HTMLElement} container
                 * @return {void}
                 */
                _blockPropagationToMap: function _blockPropagationToMap(container) {
                    if (!L.Browser.touch) {
                        L.DomEvent.disableClickPropagation(container).disableScrollPropagation(container);
                    } else {
                        L.DomEvent.on(container, 'click', L.DomEvent.stopPropagation);
                    }
                },

                /**
                 * Add UI event listeners.
                 * @return {void}
                 */
                _addEventListeners: function _addEventListeners() {
                    L.DomEvent.on(this._toggler, 'click', this.toggleSidebar.bind(this));
                },

                /**
                 * Set the content for the toggle element.
                 * @param {string} content
                 * @return {void}
                 */
                setToggleLabel: function setToggleLabel(content) {
                    this._toggler.innerHTML = content;
                },

                /**
                 * Toggle the visible / hidden state of the sidebar.
                 * @return {void}
                 */
                toggleSidebar: function toggleSidebar() {
                    if (L.DomUtil.hasClass(this._container, CSS.PANEL_HIDDEN)) {
                        this.showSidebar();
                    } else {
                        this.hideSidebar();
                    }
                },

                /**
                 * Show the sidebar.
                 * @return {void}
                 */
                showSidebar: function showSidebar() {
                    L.DomUtil.removeClass(this._container, CSS.PANEL_HIDDEN);
                    this.setToggleLabel(this.options.hide_menu_label);
                },

                /**
                 * Hide the sidebar.
                 * @return {void}
                 */
                hideSidebar: function hideSidebar() {
                    L.DomUtil.addClass(this._container, CSS.PANEL_HIDDEN);
                    this.setToggleLabel(this.options.show_menu_label);
                },

                /**
                 * Toggle an overlay layer.
                 * @param {object} layer
                 * @return {void}
                 */
                toggleLayer: function toggleLayer(layer) {
                    if (L.DomUtil.hasClass(layer._controlElement, CSS.LAYER_LIST_ITEM_ACTIVE)) {
                        this.layerOff(layer);
                    } else {
                        this.layerOn(layer);
                    }
                },

                /**
                 * Remove a marker layer from the map
                 * @param  {Object} layer
                 * @return {void}
                 */
                layerOff: function layerOff(layer) {
                    if (layer._markers) {
                        layer._markers.forEach(this.removeMarkerFromMap.bind(this));
                    }

                    if (layer._controlElement) {
                        L.DomUtil.removeClass(layer._controlElement, CSS.LAYER_LIST_ITEM_ACTIVE);
                    }
                },

                /**
                 * Add a marker layer to the map
                 * @param  {Object} layer
                 * @return {void}
                 */
                layerOn: function layerOn(layer) {
                    if (!layer._markers) {
                        layer._markers = this.createMarkersForLayer(layer);
                    }

                    layer._markers.forEach(this.addMarkerToMap.bind(this));

                    L.DomUtil.addClass(layer._controlElement, CSS.LAYER_LIST_ITEM_ACTIVE);
                },

                createMarkersForLayer: function createMarkersForLayer(layer) {
                    var icon = L.divIcon({ className: 'fa fa-lg ' + layer.icon });

                    return layer.points.map(function (point) {
                        return L.marker([point.x, point.y], {
                            title: point.title,
                            icon: icon
                        });
                    });
                },

                addMarkerToMap: function addMarkerToMap(marker) {
                    this.map.addLayer(marker);
                },

                removeMarkerFromMap: function removeMarkerFromMap(marker) {
                    this.map.removeLayer(marker);
                }
            });

        }).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
    },{}],5:[function(require,module,exports){
        (function (global){
            /**
             * Get a custom coordinate reference system (CRS) for the game world.
             * @return {L.CRS}
             */
            'use strict';

            var L = (typeof window !== "undefined" ? window.L : typeof global !== "undefined" ? global.L : null);

            module.exports = L.Util.extend({}, L.CRS, {

                projection: {
                    project: function project(latlng) {
                        return new L.Point(latlng.lat, latlng.lng);
                    },
                    unproject: function unproject(point) {
                        return new L.LatLng(point.x, point.y);
                    }
                },

                //transformation: new L.Transformation(1 / 12446, 3756 / 8192, -1 / 12446, 5525 / 8192)
                transformation: new L.Transformation(1 / (12446 * 2.978), 3756 / (8192 * 2.978), -1 / (12446 * 2.978), 5525 / (8192 * 2.978))
            });

        }).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
    },{}]},{},[1]);
</script>
<script type="text/javascript" id="delete-item">
    function DrawMarkers(L, map, rIcon, gIcon, sIcon, hIcon, aIcon) {

        var latlngs = [];

        <?php
        $gangWars = $qb->createQueryBuilder('gang_war_2')->selectSql('*, gang_war_2.id as idx')->leftJoin('fraction_list on gang_war_2.fraction_id = fraction_list.id')->executeQuery()->getResult();

        $r = 48;

        foreach ($gangWars as $item) {

            echo 'latlngs = [[' . ($item['x'] - $r) . ', ' . ($item['y'] - $r) . '], [' . ($item['x'] - $r) . ', ' . ($item['y'] + $r) . '], [' . ($item['x'] + $r) . ', ' . ($item['y'] + $r) . '], [' . ($item['x'] + $r) . ', ' . ($item['y'] - $r) . ']];';

            $ownerName = $item['fraction_name'] === '' ? 'Нет' : $item['fraction_name'];
            $color = $item['color'];
            $desc = $item['cant_war'] ? '<b>Главная улица</b><br>' : '';
            $desc = '<b>ID: ' . $item['idx'] . '</b><br>' . $desc;

            echo 'L.polygon(latlngs, {color: \'' . $color . '\', weight: 2}).bindPopup(\'' . $desc . 'Район: ' . $item['zone'] . '<br>Улица: ' . $item['street'] . '<br>Под контролем: ' . $ownerName . '\').addTo(map);';
        }

        ?>

        <?php
        /*$gangWars = $qb->createQueryBuilder('gang_war')->selectSql('*, gang_war.id as idx')->leftJoin('fraction_list on gang_war.fraction_id = fraction_list.id')->executeQuery()->getResult();

        foreach ($gangWars as $item) {


            echo 'latlngs = [];';

            foreach (json_decode($item['on_map']) as $map) {
                echo 'latlngs.push([' . $map[0] . ', ' . $map[1] . ']);';
            }

            $ownerName = $item['fraction_name'] === '' ? 'Нет' : $item['fraction_name'];
            $color = $item['color'];
            $desc = $item['cant_war'] ? '<b>Главная улица</b><br>' : '';
            $desc = '<b>ID: ' . $item['idx'] . '</b><br>' . $desc;

            echo 'L.polygon(latlngs, {color: \'' . $color . '\', weight: 2}).bindPopup(\'' . $desc . 'Район: ' . $item['zone'] . '<br>Улица: ' . $item['street'] . '<br>Под контролем: ' . $ownerName . '\').addTo(map);';
        }*/

        ?>
    }
</script>

<!--  Scripts-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script src="/client/js/extended.js"></script>
<script src="/client/js/material-charts.js"></script>
<script src="/client/js/main.js?v=4"></script>

</body>
</html>
