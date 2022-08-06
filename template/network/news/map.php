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
    <a href="/network/news" style="margin: 4px" class="btn btn-floating waves-effect wb bw-text z-depth-1"><i class="bw-text material-icons">home</i></a>
    <a href="/network/vehicles" style="margin: 4px" class="hide btn btn-floating waves-effect wb bw-text z-depth-1"><i class="bw-text material-icons">directions_car</i></a>
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
                map.addControl(new ExitControl({ text: 'Copyright <?php echo date('Y') ?> DEDNET & GrandTheftAuto.net & gta-5-map.com' }));


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
        $gangWars = $qb->createQueryBuilder('gang_war')->selectSql('*, gang_war.id as idx')->leftJoin('fraction_list on gang_war.fraction_id = fraction_list.id')->executeQuery()->getResult();

        $r = 75;

        foreach ($gangWars as $item) {

            echo 'latlngs = [[' . ($item['x'] - $r) . ', ' . ($item['y'] - $r) . '], [' . ($item['x'] - $r) . ', ' . ($item['y'] + $r) . '], [' . ($item['x'] + $r) . ', ' . ($item['y'] + $r) . '], [' . ($item['x'] + $r) . ', ' . ($item['y'] - $r) . ']];';

            $ownerName = $item['fraction_name'] === '' ? 'Отсутствует' : $item['fraction_name'];
            $color = $item['color'];
            $desc = $item['cant_war'] ? '<b>Титульная улица</b><br>' : '';
            $desc = '<b>ID: ' . $item['idx'] . '</b><br>' . $desc;

            echo 'L.polygon(latlngs, {color: \'' . $color . '\', weight: 1}).bindPopup(\'' . $desc . 'Контроль: ' . $ownerName . '\').addTo(map);';
        }


        /*$gangWars = $qb->createQueryBuilder('gang_war_2')->selectSql('*, gang_war_2.id as idx')->leftJoin('fraction_list on gang_war_2.fraction_id = fraction_list.id')->executeQuery()->getResult();

        $r = 75;

        foreach ($gangWars as $item) {

            echo 'latlngs = [[' . ($item['x'] - $r) . ', ' . ($item['y'] - $r) . '], [' . ($item['x'] - $r) . ', ' . ($item['y'] + $r) . '], [' . ($item['x'] + $r) . ', ' . ($item['y'] + $r) . '], [' . ($item['x'] + $r) . ', ' . ($item['y'] - $r) . ']];';

            $ownerName = $item['fraction_name'] === '' ? 'Государство' : $item['fraction_name'];
            $color = $item['fraction_name'] === '' ? '#f3c368' : $item['color'];

            $desc = '<b>ID: ' . $item['idx'] . '</b><br>';

            echo 'L.polygon(latlngs, {color: \'' . $color . '\', weight: 2}).bindPopup(\'' . $desc . 'Под контролем: ' . $ownerName . '\').addTo(map);';
        }*/

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

        <?php

        global $qb;
        $houses = $qb->createQueryBuilder('houses')->selectSql()->where("price > '" . intval($priceFrom) . "' AND price < '" . intval($priceTo) . "' AND user_id < " . $onlyFree)->executeQuery()->getResult();
        $stocks = $qb->createQueryBuilder('stocks')->selectSql()->where("price > '" . intval($priceFrom) . "' AND price < '" . intval($priceTo) . "' AND user_id < " . $onlyFree)->executeQuery()->getResult();
        $condos = $qb->createQueryBuilder('condos_big')->selectSql()->executeQuery()->getResult();

        foreach ($houses as $item) {
            $garageCount = 0;
            if ($item['ginterior1'] >= 0)
                $garageCount++;
            if ($item['ginterior2'] >= 0)
                $garageCount++;
            if ($item['ginterior3'] >= 0)
                $garageCount++;

            $userName = '<span class=\"green-text\">Нет</span>';
            $icon = 'gIcon';
            if($item['user_id'] > 0) {
                $icon = 'rIcon';
                $userName = $item['user_name'];
            }
            echo 'L.marker([' . $item['x'] . ', ' . $item['y'] . '], {icon: ' . $icon . '}).addTo(map).bindPopup("<b>Дом ' . $item['id'] .  '</b><br>Владелец: ' . $userName . '<br>Цена: $' . number_format($item['price']) . '<br>Адрес: ' . $item['address'] . ' №' . $item['number'] . '<br>Кол-во гаражей: ' . $garageCount . '<br>Жилых мест: ' . $item['max_roommate'] . '");';
        }


        foreach ($condos as $item) {

            $condosSmall = $qb->createQueryBuilder('condos')->selectSql()->where("price > '" . intval($priceFrom) . "' AND price < '" . intval($priceTo) . "' AND user_id < " . $onlyFree)->andWhere('condo_big_id = \'' . $item['id'] . '\'')->executeQuery()->getResult();
            $condosList = '<table class=\'striped\'><thead><tr><th>#</th><th>Владелец</th><th>Цена</th></tr></thead><tbody>';

            foreach ($condosSmall as $itemS) {
                $condosList .= '<tr style=\'line-height: 10px;\'>';
                $condosList .= '<td>' . $itemS['number'] . '</td>';
                $condosList .= '<td>' . ($itemS['user_name'] == '' ? 'Нет' : $itemS['user_name']) . '</td>';
                $condosList .= '<td>$' . number_format($itemS['price']) . '</td>';
                $condosList .= '</tr>';
            }
            $condosList .= '</tbody></table>';

            if (count($condosSmall) > 0)
                echo 'L.marker([' . $item['x'] . ', ' . $item['y'] . '], {icon: hIcon}).addTo(map).bindPopup("<b>Многоквартирный дом  №' . $item['id'] . '</b><br>Адрес: ' . $item['address'] . ' #' . $item['number'] . $condosList . '");';
        }

        foreach ($stocks as $item) {
            $interiorName = 'Маленький';
            if ($item['interior'] == 1)
                $interiorName = 'Средний';
            if ($item['interior'] == 2)
                $interiorName = 'Большой';
            if($item['user_id'] > 0)
                echo 'L.marker([' . $item['x'] . ', ' . $item['y'] . '], {icon: sIcon}).addTo(map).bindPopup("<b>Склад ' . $item['id'] . '</b><br>Тип: ' . $interiorName . '<br>Владелец: ' . $item['user_name'] . '<br>Цена: $' . number_format($item['price']) . '<br>Адрес: ' . $item['address'] . ' №' . $item['number'] . '");';
            else
                echo 'L.marker([' . $item['x'] . ', ' . $item['y'] . '], {icon: sIcon}).addTo(map).bindPopup("<b>Склад ' . $item['id'] . '</b><br>Тип: ' . $interiorName . '<br>Владелец: <span class=\"green-text\">Нет</span><br>Цена: $' . number_format($item['price']) . '<br>Адрес: ' . $item['address'] . ' №' . $item['number'] . '");';
        }

        /*foreach ($condos as $item) {
            if($item['id_user'] > 0)
                echo 'L.marker([' . $item['x'] . ', ' . $item['y'] . '], {icon: hIcon}).addTo(map).bindPopup("<b>Квартира</b><br>Владелец: ' . $item['user_name'] . '<br>Цена: $' . number_format($item['price']) . '<br>Адрес: ' . $item['address'] . ' №' . $item['id'] . '");';
            else
                echo 'L.marker([' . $item['x'] . ', ' . $item['y'] . '], {icon: hIcon}).addTo(map).bindPopup("<b>Квартира</b><br>Владелец: <span class=\"green-text\">Нет</span><br>Цена: $' . number_format($item['price']) . '<br>Адрес: ' . $item['address'] . ' №' . $item['id'] . '");';
        }
        foreach ($builds as $item) {

            //SELECT AVG(`price`) FROM `apartment` WHERE 1
            //
            $apart = $qb->createQueryBuilder('apartment')->selectSql('AVG(`price`) as price')->where("build_id = '" . intval($item['id'] - 1) . "'")->executeQuery()->getSingleResult();
            $apartAllCount = $qb->createQueryBuilder('apartment')->selectSql('count(*)')->where("build_id = '" . intval($item['id'] - 1) . "'")->executeQuery()->getSingleResult();
            $apartFreeCount = $qb->createQueryBuilder('apartment')->selectSql('count(*)')->where("build_id = '" . intval($item['id'] - 1) . "' and user_id = 0")->executeQuery()->getSingleResult();

            if (intval(reset($apart)) > intval($priceFrom) && intval(reset($apart)) < intval($priceTo))
            	echo 'L.marker([' . $item['x'] . ', ' . $item['y'] . '], {icon: aIcon}).addTo(map).bindPopup("<b>Здание апартаментов №' . $item['id'] . '</b><br>Средняя цена на апартаменты: $' . number_format(intval(reset($apart))) . '<br>Свободных квартир: ' . reset($apartFreeCount) . '/' . reset($apartAllCount) . '<br>Этажей: ' . $item['floors'] . '");';
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
