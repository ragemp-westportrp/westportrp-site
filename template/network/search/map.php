<?php
global $serverName;
?>


<div class="section">
    <div class="row">
        <div class="col s12">
            <h4 class="black-text">Карты</h4>
        </div>
        <div class="col s12">
            <div class="card-panel row" style="padding: 0;" id="map-container">
                <div id="map" class="reset leaflet-container leaflet-fade-anim" style="position: relative; background-color: rgb(15, 168, 210);" tabindex="0"></div>
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
                CRS = require('http://grandtheftauto.net/gta5/map/crs'),
                BaseLayerControl = require('http://grandtheftauto.net/gta5/map/control/baselayer'),
                OverlayControl = require('http://grandtheftauto.net/gta5/map/control/overlay'),
                ExitControl = require('http://grandtheftauto.net/gta5/map/control/exit');

            $(document).ready(function () {

                $('#map-container').height(screen.height - 350);

                var container = $('#map')[0];

                var map = L.map(container, {
                    crs: CRS,
                    attributionControl: false,
                    minZoom: 0,
                    maxZoom: 6,
                    maxNativeZoom: 5
                });

                var atlas_layer = L.tileLayer('http://grandtheftauto.net/images/maps/gta5/tiles/atlas/{z}/{x}-{y}.jpg', {
                    continuousWorld: true,
                    noWrap: true,
                    maxNativeZoom: 5,
                    maxZoom: 6,
                    background: '#0fa8d2',
                    title: 'Atlas'
                }).addTo(map);

                var roadmap_layer = L.tileLayer('http://grandtheftauto.net/images/maps/gta5/tiles/roadmap/{z}/{x}-{y}.jpg', {
                    continuousWorld: true,
                    noWrap: true,
                    maxNativeZoom: 5,
                    maxZoom: 6,
                    background: '#1862ad',
                    title: 'Roads'
                });

                var satellite_layer = L.tileLayer('http://grandtheftauto.net/images/maps/gta5/tiles/satellite/{z}/{x}-{y}.jpg', {
                    continuousWorld: true,
                    noWrap: true,
                    maxNativeZoom: 5,
                    maxZoom: 6,
                    background: '#143d6b',
                    title: 'Satellite'
                });

                var layers = [atlas_layer, roadmap_layer, satellite_layer];

                // Leaflet uses grey background by default when there are no more tiles to display.
                // This changes it to match the background colour of our tiles.
                map.on('baselayerchange', function (e) {
                    this.getContainer().style.backgroundColor = e.layer.options.background;
                });

                map.setView([0, 2000], 2);
                map.addControl(new BaseLayerControl(layers));
                map.addControl(new ExitControl({ text: 'Copyright 2015 GrandTheftAuto.net and Rockstar Games' }));


                var rIcon = L.icon({
                    iconUrl: '/Client/map/house_r.png',
                    iconSize:     [24, 24], // size of the icon
                    iconAnchor:   [12, 24], // point of the icon which will correspond to marker's location
                    popupAnchor:  [0, -24] // point from which the popup should open relative to the iconAnchor
                });
                var gIcon = L.icon({
                    iconUrl: '/Client/map/house_g.png',
                    iconSize:     [24, 24], // size of the icon
                    iconAnchor:   [12, 24], // point of the icon which will correspond to marker's location
                    popupAnchor:  [0, -24] // point from which the popup should open relative to the iconAnchor
                });
                var prisonIcon = L.icon({
                    iconUrl: '/Client/map/prison.png',
                    iconSize:     [24, 24], // size of the icon
                    iconAnchor:   [12, 24], // point of the icon which will correspond to marker's location
                    popupAnchor:  [0, -24] // point from which the popup should open relative to the iconAnchor
                });
                L.marker([1830.489, 2603.093], {icon: prisonIcon}).addTo(map).bindPopup("Федеральная тюрьма");

                var policeIcon = L.icon({
                    iconUrl: '/Client/map/police.png',
                    iconSize:     [24, 24], // size of the icon
                    iconAnchor:   [12, 24], // point of the icon which will correspond to marker's location
                    popupAnchor:  [0, -24] // point from which the popup should open relative to the iconAnchor
                });
                L.marker([111.5687, -749.9395], {icon: policeIcon}).addTo(map).bindPopup("Federal Investigation Bureau");
                L.marker([437.5687, -982.9395], {icon: policeIcon}).addTo(map).bindPopup("Los Santos Police Departament");

                var clothIcon = L.icon({
                    iconUrl: '/Client/map/cloth.png',
                    iconSize:     [24, 24], // size of the icon
                    iconAnchor:   [12, 24], // point of the icon which will correspond to marker's location
                    popupAnchor:  [0, -24] // point from which the popup should open relative to the iconAnchor
                });
                L.marker([-717.0063, -156.8642], {icon: clothIcon}).addTo(map).bindPopup("Магазин дорогой одежды");
                L.marker([126.6739, -212.8178], {icon: clothIcon}).addTo(map).bindPopup("Магазин дешевой одежды");

                var bankIcon = L.icon({
                    iconUrl: '/Client/map/bank.png',
                    iconSize:     [24, 24], // size of the icon
                    iconAnchor:   [12, 24], // point of the icon which will correspond to marker's location
                    popupAnchor:  [0, -24] // point from which the popup should open relative to the iconAnchor
                });
                L.marker([235.5093, 216.8752], {icon: bankIcon}).addTo(map).bindPopup("Банк");

                var govIcon = L.icon({
                    iconUrl: '/Client/map/gov.png',
                    iconSize:     [24, 24], // size of the icon
                    iconAnchor:   [12, 24], // point of the icon which will correspond to marker's location
                    popupAnchor:  [0, -24] // point from which the popup should open relative to the iconAnchor
                });
                L.marker([-116.8427, -604.7336], {icon: govIcon}).addTo(map).bindPopup("Офис правительства");

                var licIcon = L.icon({
                    iconUrl: '/Client/map/lic.png',
                    iconSize:     [24, 24], // size of the icon
                    iconAnchor:   [12, 24], // point of the icon which will correspond to marker's location
                    popupAnchor:  [0, -24] // point from which the popup should open relative to the iconAnchor
                });
                L.marker([-1581.689, -557.913], {icon: licIcon}).addTo(map).bindPopup("Автошкола");

                var mehIcon = L.icon({
                    iconUrl: '/Client/map/meh.png',
                    iconSize:     [24, 24], // size of the icon
                    iconAnchor:   [12, 24], // point of the icon which will correspond to marker's location
                    popupAnchor:  [0, -24] // point from which the popup should open relative to the iconAnchor
                });
                L.marker([-1144.276, -1989.988], {icon: mehIcon}).addTo(map).bindPopup("Автомастерская");

                var newsIcon = L.icon({
                    iconUrl: '/Client/map/news.png',
                    iconSize:     [24, 24], // size of the icon
                    iconAnchor:   [12, 24], // point of the icon which will correspond to marker's location
                    popupAnchor:  [0, -24] // point from which the popup should open relative to the iconAnchor
                });
                L.marker([-1082, -250], {icon: newsIcon}).addTo(map).bindPopup("Офис Invader News");

                var usmcIcon = L.icon({
                    iconUrl: '/Client/map/usmc.png',
                    iconSize:     [24, 24], // size of the icon
                    iconAnchor:   [12, 24], // point of the icon which will correspond to marker's location
                    popupAnchor:  [0, -24] // point from which the popup should open relative to the iconAnchor
                });
                L.marker([814.7399, -2127.135], {icon: usmcIcon}).addTo(map).bindPopup("Призывной пункт");

                /*$.getJSON('...').then(function (data) { disabled due cors problems. we dont need it btw
                 var markerLayers = data;

                 var overlayControl = new OverlayControl(markerLayers, {});

                 map.addControl(overlayControl);

                 function hashToggle() {
                 var hash = window.location.hash.substring(1);

                 $.each(overlayControl.layers, function (hashIndex, hashLayer) {
                 var hashSlug = hashLayer.title.toLowerCase().replace(' ', '-');

                 if (hashSlug == hash) {
                 overlayControl.layerOn(hashLayer);
                 } else {
                 overlayControl.layerOff(hashLayer);
                 }
                 });
                 }

                 hashToggle();

                 $(window).on('hashchange', hashToggle);
                 });*/
            });

        }).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
    },{"http://grandtheftauto.net/gta5/map/control/baselayer":2,"http://grandtheftauto.net/gta5/map/control/exit":3,"http://grandtheftauto.net/gta5/map/control/overlay":4,"http://grandtheftauto.net/gta5/map/crs":5}],2:[function(require,module,exports){
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
                        preview_map_element.style.backgroundImage = 'url(' + substitutePlaceholders(layer._url, 2, 2, 2) + ')';

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

            L.Icon.Default.imagePath = 'http://grandtheftauto.net/gta5/assets/lib/leaflet';

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

                transformation: new L.Transformation(1 / 12446, 3756 / 8192, -1 / 12446, 5525 / 8192)
            });

        }).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
    },{}]},{},[1]);
</script>
</body>
</html>