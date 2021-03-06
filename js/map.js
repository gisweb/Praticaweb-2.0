(function ($) {
    "use strict";
    $(function () {

        Proj4js.defs["EPSG:3003"] = "+proj=tmerc +lat_0=0 +lon_0=9 +k=0.9996 +x_0=1500000 +y_0=0 +ellps=intl +units=m +no_defs";
        var projSource = new Proj4js.Proj("EPSG:4326");
        var projDest = new Proj4js.Proj("EPSG:3003");
        var mapMarker;
        var mapMarkerOptions = {
            icon: 'images/marker32.png',
            draggable: true
        }


        //CALCOLA LA URL PER I TILE A SECONDA DEL TIPO DI LIVELLO WMS O WMTS (TILES IN CACHE)
        function getTileUrl (map, baseUrl,layerName,layerType){
            var fn;
            if(layerType == "WMS"){

                fn = function(tile, zoom) {
                    var projection = map.getProjection();
                    var zpow = Math.pow(2, zoom);
                    var ul = new google.maps.Point(tile.x * 256.0 / zpow, (tile.y + 1) * 256.0 / zpow);
                    var lr = new google.maps.Point((tile.x + 1) * 256.0 / zpow, (tile.y) * 256.0 / zpow);
                    var ulw = projection.fromPointToLatLng(ul);
                    var lrw = projection.fromPointToLatLng(lr);
                    var bbox = ulw.lng() + "," + ulw.lat() + "," + lrw.lng() + "," + lrw.lat();
                    //console.log(baseUrl +  "&LAYERS=" + layerName  + "&SERVICE=WMS&TRANSPARENT=true&VERSION=1.1.1&EXCEPTIONS=XML&REQUEST=GetMap&STYLES=default&FORMAT=image%2Fpng&SRS=EPSG:4326&BBOX=" + bbox + "&width=256&height=256");

                    return baseUrl +  "&LAYERS=" + layerName  + "&SERVICE=WMS&TRANSPARENT=true&VERSION=1.1.1&EXCEPTIONS=XML&REQUEST=GetMap&STYLES=default&FORMAT=image%2Fpng&SRS=EPSG:4326&BBOX=" + bbox + "&width=256&height=256";
                }
            }
            else if(layerType == "WMTS"){
                fn = function (coord, zoom) {
                    return owsBaseURL + "/wmts/" + layerName + "/" + tileGridName + "/" + zoom + "/" + coord.x + "/" + coord.y + ".png";
                };
            }
            return fn
        }

        //MOSTRA LE COORDINATE RELATIVE ALLA POSIZIONE DEL MOUSE
        function onMouseMove(e){
            var position = 'Coordinate' + ': Lng: ' + e.latLng.lng().toFixed(6) + ' Lat: ' + e.latLng.lat().toFixed(6);
            var p = new Proj4js.Point(e.latLng.lng(),e.latLng.lat());
            Proj4js.transform(projSource, projDest, p);
            position = position + ' - X: ' + p.x.toFixed(2) + ' Y: ' + p.y.toFixed(2);
            if($("#coords").length){
                $("#coords").text(position);
            }$("#coords").text(position);
        };

        function writePosition(marker){
            var position = marker.getPosition();
            var lat = position.lat();
            var lng = position.lng();
            var p = new Proj4js.Point(lng,lat);
            Proj4js.transform(projSource, projDest, p);
            var x = p.x.toFixed(2);
            var y = p.y.toFixed(2)
            $("#coordx").val(x);
            $("#coordy").val(y);
            $("#geometry").val(lng.toFixed(6) + ' ' + lat.toFixed(6));

        }

        function initMap() {
            var id = 0;
            var mode = $('#mode').val();
            var addMarker = false;
            if (mode == 'new'){
                addMarker = true;
            }

		
            map = new google.maps.Map(document.getElementById('map'), {
                center: {lat: 44.332, lng: 9.18},
                zoom: 13,
                minZomm: 8
            });
            //if($(map.getDiv()).width()==0) return;
            google.maps.event.addListener(map, 'mousemove', onMouseMove);
            google.maps.event.addListener(map, 'idle', function() {
//                console.log($(map.getDiv()).width());
//                console.log(map.getBounds());
                //var sw = bounds.getSouthWest();
                //var ne = bounds.getNorthEast();
                //alert("minimum lat of current map view: " + sw.lat());
            });
            var drawingManager = new google.maps.drawing.DrawingManager({
                drawingMode: null,
                drawingControl: addMarker,
                drawingControlOptions: {
                    position: google.maps.ControlPosition.TOP_LEFT,
                    drawingModes: ['marker']
                },
                markerOptions: mapMarkerOptions
            });
            drawingManager.setMap(map);

            google.maps.event.addListener(drawingManager, 'overlaycomplete', function(e) {
                drawingManager.setDrawingMode(null);
//                if (mapMarker){
//                    e.overlay.setMap(null);
//                    mapMarker.setPosition(e.overlay.getPosition());
//                }
//                else{
                    mapMarker = e.overlay;
                    writePosition(mapMarker);
                    google.maps.event.addListener(mapMarker, 'dragend', function() {
                        writePosition(mapMarker);
                    })
//                }
            });
            // 
            bounds = new google.maps.LatLngBounds();
            var simpleValues = $("#points").val();
            var foundPoints = 0;
            if (simpleValues && simpleValues != "[]") {
                var values = JSON.parse(simpleValues); 
            }
            else{
                var values = [];
            }
            //Aggiungo il marker salvato se esiste
            if(Array.isArray(values) && values.length){
                foundPoints = 1;
                var id = 0;
                var mode = $('#mode').val();
                
                if (mode == 'edit'){
                    id = $('#id').val();
                }
                
                $.each(values, function( index, value ) {
                    mapMarker = new google.maps.Marker(mapMarkerOptions);
                    var p = value["geometry"].split(' ');
                      if (p.length==2){
                        var position = {lat: parseFloat(p[1]), lng: parseFloat(p[0])}
                         var mapMarkerOptions = {
                            icon: 'images/marker32.png',
                             draggable: ((mode=='edit' && value['id']== id) ? true : false),
                            title: value['note']
                        }
                        mapMarker = new google.maps.Marker(mapMarkerOptions);
                        mapMarker.setPosition(position);
                        mapMarker.setMap(map);
                        var loc = new google.maps.LatLng(mapMarker.position.lat(), mapMarker.position.lng());
                        bounds.extend(position);
                    }
                    google.maps.event.addListener(mapMarker, 'dragend', function() {
                        writePosition(mapMarker);
                     });
                });
                console.log(values);                
            }

            //LAYER DI SFONDO OSM
            var layerOptions = {
                tileSize: new google.maps.Size(256, 256),
                isPng: true,
                name: "OSM",
                maxZoom: 19,
                minZoom: 0,
                getTileUrl: function(coord, zoom) {
                    return "http://tile.openstreetmap.org/" +
                        zoom + "/" + coord.x + "/" + coord.y + ".png";
                }
            }

            var osmMapType = new google.maps.ImageMapType(layerOptions);
            map.mapTypes.set("OSM", osmMapType); //AGGINGO E LO SETTO DI DEFAULT
            var mapTypeIds = ["OSM",google.maps.MapTypeId.ROADMAP,google.maps.MapTypeId.TERRAIN,google.maps.MapTypeId.SATELLITE,google.maps.MapTypeId.HYBRID];
            map.setMapTypeId("OSM");

            map.setOptions({"mapTypeControlOptions": {
                "mapTypeIds": mapTypeIds,
                "style": google.maps.MapTypeControlStyle.DROPDOWN_MENU
            }});

            //OVERLAYS
            var layer,layerOptions;
            for (var i=0; i < mapLayers.length; i++) {
                layer = mapLayers[i];
                layerOptions = {
                    tileSize: new google.maps.Size(256, 256),
                    isPng: true,
                    opacity: layer.opacity || 1,
                    name: layer.name,
                    getTileUrl: getTileUrl(map,layer.url,layer.name,layer.type)
                }
                layer = new google.maps.ImageMapType(layerOptions);
                map.overlayMapTypes.setAt(map.overlayMapTypes.length, layer);

            }
            if (foundPoints){
	        var xy = bounds.getCenter();
                map.fitBounds(bounds);       // auto-zoom
            	map.panToBounds(bounds);     // auto-center
            }
            else{
                map.setCenter({lat: 44.332, lng: 9.18});
                map.setZoom(13);
            }

        }//end initMap

        initMap()
    });
})(jQuery);

/**
 * Created by mamo on 27/03/17.
 */
