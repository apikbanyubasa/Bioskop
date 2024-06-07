<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <link rel="shortcut icon" href="../images/movie.png" type="">

  <title> Bogor Cinemap </title>
  <link rel="stylesheet" type="text/css" href="../css/bootstrap.css" />
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/css/nice-select.min.css" integrity="sha512-CruCP+TD3yXzlvvijET8wV5WxxEh5H8P4cmz0RFbKK6FlZ2sYl3AEsKlLPHbniXKSrDdFewhbmBK5skbdsASbQ==" crossorigin="anonymous" />
  <link href="../css/font-awesome.min.css" rel="stylesheet" />
  <link href="../css/style.css" rel="stylesheet" />
  <link href="../css/responsive.css" rel="stylesheet" />

  <meta name="viewport" content="initial-scale=1,user-scalable=no,maximum-scale=1,width=device-width">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <link rel="stylesheet" href="css/leaflet.css">
  <link rel="stylesheet" href="css/L.Control.Layers.Tree.css">
  <link rel="stylesheet" href="css/L.Control.Locate.min.css">
  <link rel="stylesheet" href="css/qgis2web.css">
  <link rel="stylesheet" href="css/fontawesome-all.min.css">
  <link rel="stylesheet" href="css/leaflet-search.css">
  <link rel="stylesheet" href="css/leaflet-control-geocoder.Geocoder.css">
  <style>
    #map,
    body,
    html {
      width: 100%;
      height: 100%;
    }
  </style>
</head>

<body class="sub_page">

  <div class="hero_area">
    <div class="bg-box">
      <img src="../images/bioskop1.png" alt="">
    </div>
    <!-- header section strats -->
    <header class="header_section">
      <div class="container">
        <nav class="navbar navbar-expand-lg custom_nav-container ">
          <a class="navbar-brand" href="index.php">
            <span>
              Bogor Cinemap
            </span>
          </a>

          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class=""> </span>
          </button>

          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav  ml-auto ">
              <li class="nav-item">
                <a class="nav-link" href="../index.php">Home </a>
              </li>
              <li class="nav-item active">
                <a class="nav-link" href="lokasi.php">Lokasi <span class="sr-only">(current)</span> </a>
              </li>
            </ul>
          </div>
        </nav>
      </div>
    </header>
    <!-- end header section -->
  </div>

  <!-- lokasi section -->
  <section class="book_section layout_padding">
    <div class="container">
      <h2 class="text-center mt-n5">Cari Bioskop di SekiarMu</h2>
      <div class="map_container">
        <div id="map">
        </div>
        <script src="js/qgis2web_expressions.js"></script>
        <script src="js/leaflet.js"></script>
        <script src="js/L.Control.Layers.Tree.min.js"></script>
        <script src="js/L.Control.Locate.min.js"></script>
        <script src="js/leaflet-svg-shape-markers.min.js"></script>
        <script src="js/leaflet.rotatedMarker.js"></script>
        <script src="js/leaflet.pattern.js"></script>
        <script src="js/leaflet-hash.js"></script>
        <script src="js/Autolinker.min.js"></script>
        <script src="js/rbush.min.js"></script>
        <script src="js/labelgun.min.js"></script>
        <script src="js/labels.js"></script>
        <script src="js/leaflet-control-geocoder.Geocoder.js"></script>
        <script src="js/leaflet-search.js"></script>
        <script src="data/Cinepolis_1.js"></script>
        <script src="data/CGV_2.js"></script>
        <script src="data/XXI_3.js"></script>
        <script>
          var highlightLayer;

          function highlightFeature(e) {
            highlightLayer = e.target;

            if (e.target.feature.geometry.type === 'LineString' || e.target.feature.geometry.type === 'MultiLineString') {
              highlightLayer.setStyle({
                color: '#ffff00',
              });
            } else {
              highlightLayer.setStyle({
                fillColor: '#ffff00',
                fillOpacity: 1
              });
            }
          }
          var map = L.map('map', {
            zoomControl: true,
            maxZoom: 28,
            minZoom: 1
          }).fitBounds([
            [-6.690378006492341, 106.54292633006759],
            [-6.287023152489513, 107.32294904395206]
          ]);
          var hash = new L.Hash(map);
          map.attributionControl.setPrefix('<a href="https://github.com/tomchadwin/qgis2web" target="_blank">qgis2web</a> &middot; <a href="https://leafletjs.com" title="A JS library for interactive maps">Leaflet</a> &middot; <a href="https://qgis.org">QGIS</a>');
          var autolinker = new Autolinker({
            truncate: {
              length: 30,
              location: 'smart'
            }
          });

          function removeEmptyRowsFromPopupContent(content, feature) {
            var tempDiv = document.createElement('div');
            tempDiv.innerHTML = content;
            var rows = tempDiv.querySelectorAll('tr');
            for (var i = 0; i < rows.length; i++) {
              var td = rows[i].querySelector('td.visible-with-data');
              var key = td ? td.id : '';
              if (td && td.classList.contains('visible-with-data') && feature.properties[key] == null) {
                rows[i].parentNode.removeChild(rows[i]);
              }
            }
            return tempDiv.innerHTML;
          }
          document.querySelector(".leaflet-popup-pane").addEventListener("load", function(event) {
            var tagName = event.target.tagName,
              popup = map._popup;
            // Also check if flag is already set.
            if (tagName === "IMG" && popup && !popup._updated) {
              popup._updated = true; // Set flag to prevent looping.
              popup.update();
            }
          }, true);
          L.control.locate({
            locateOptions: {
              maxZoom: 19
            }
          }).addTo(map);
          var bounds_group = new L.featureGroup([]);

          function setBounds() {}
          map.createPane('pane_OpenStreetMap_0');
          map.getPane('pane_OpenStreetMap_0').style.zIndex = 400;
          var layer_OpenStreetMap_0 = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            pane: 'pane_OpenStreetMap_0',
            opacity: 1.0,
            attribution: '',
            minZoom: 1,
            maxZoom: 28,
            minNativeZoom: 0,
            maxNativeZoom: 19
          });
          layer_OpenStreetMap_0;
          map.addLayer(layer_OpenStreetMap_0);

          function pop_Cinepolis_1(feature, layer) {
            layer.on({
              mouseout: function(e) {
                for (var i in e.target._eventParents) {
                  if (typeof e.target._eventParents[i].resetStyle === 'function') {
                    e.target._eventParents[i].resetStyle(e.target);
                  }
                }
              },
              mouseover: highlightFeature,
            });
            var popupContent = '<table>\
                    <tr>\
                        <th scope="row">Nama Mall</th>\
                        <td>' + (feature.properties['Nama Mall'] !== null ? autolinker.link(feature.properties['Nama Mall'].toLocaleString()) : '') + '</td>\
                    </tr>\
                    <tr>\
                        <th scope="row">Bioskop</th>\
                        <td>' + (feature.properties['Bioskop'] !== null ? autolinker.link(feature.properties['Bioskop'].toLocaleString()) : '') + '</td>\
                    </tr>\
                    <tr>\
                        <th scope="row">Alamat</th>\
                        <td>' + (feature.properties['Alamat'] !== null ? autolinker.link(feature.properties['Alamat'].toLocaleString()) : '') + '</td>\
                    </tr>\
                    <tr>\
                        <td colspan="2">' + (feature.properties['Foto'] !== null ? '<img style="height : 200px; width : 290px;" src="images/' + String(feature.properties['Foto']).replace(/[\\\/:]/g, '_').trim() + '">' : '') + '</td>\
                    </tr>\
                </table>';
            layer.bindPopup(popupContent, {
              maxHeight: 400
            });
            var popup = layer.getPopup();
            var content = popup.getContent();
            var updatedContent = removeEmptyRowsFromPopupContent(content, feature);
            popup.setContent(updatedContent);
          }

          function style_Cinepolis_1_0(feature) {
            switch (String(feature.properties['Nama Mall'])) {
              case 'Lippo Plaza Ekalosari':
                return {
                  pane: 'pane_Cinepolis_1',
                    shape: 'triangle',
                    radius: 8.000000000000004,
                    opacity: 1,
                    color: 'rgba(35,35,35,1.0)',
                    dashArray: '',
                    lineCap: 'butt',
                    lineJoin: 'miter',
                    weight: 1,
                    fill: true,
                    fillOpacity: 1,
                    fillColor: 'rgba(50,50,255,1.0)',
                    interactive: true,
                }
                break;
              case 'Lippo Plaza Kebun Raya Bogor':
                return {
                  pane: 'pane_Cinepolis_1',
                    shape: 'triangle',
                    radius: 8.000000000000004,
                    opacity: 1,
                    color: 'rgba(35,35,35,1.0)',
                    dashArray: '',
                    lineCap: 'butt',
                    lineJoin: 'miter',
                    weight: 1,
                    fill: true,
                    fillOpacity: 1,
                    fillColor: 'rgba(50,50,255,1.0)',
                    interactive: true,
                }
                break;
              default:
                return {
                  pane: 'pane_Cinepolis_1',
                    shape: 'triangle',
                    radius: 8.000000000000004,
                    opacity: 1,
                    color: 'rgba(35,35,35,1.0)',
                    dashArray: '',
                    lineCap: 'butt',
                    lineJoin: 'miter',
                    weight: 1,
                    fill: true,
                    fillOpacity: 1,
                    fillColor: 'rgba(255,255,255,1.0)',
                    interactive: true,
                }
                break;
            }
          }
          map.createPane('pane_Cinepolis_1');
          map.getPane('pane_Cinepolis_1').style.zIndex = 401;
          map.getPane('pane_Cinepolis_1').style['mix-blend-mode'] = 'normal';
          var layer_Cinepolis_1 = new L.geoJson(json_Cinepolis_1, {
            attribution: '',
            interactive: true,
            dataVar: 'json_Cinepolis_1',
            layerName: 'layer_Cinepolis_1',
            pane: 'pane_Cinepolis_1',
            onEachFeature: pop_Cinepolis_1,
            pointToLayer: function(feature, latlng) {
              var context = {
                feature: feature,
                variables: {}
              };
              return L.shapeMarker(latlng, style_Cinepolis_1_0(feature));
            },
          });
          bounds_group.addLayer(layer_Cinepolis_1);
          map.addLayer(layer_Cinepolis_1);

          function pop_CGV_2(feature, layer) {
            layer.on({
              mouseout: function(e) {
                for (var i in e.target._eventParents) {
                  if (typeof e.target._eventParents[i].resetStyle === 'function') {
                    e.target._eventParents[i].resetStyle(e.target);
                  }
                }
              },
              mouseover: highlightFeature,
            });
            var popupContent = '<table>\
                    <tr>\
                        <th scope="row">Nama Mall</th>\
                        <td>' + (feature.properties['Nama Mall'] !== null ? autolinker.link(feature.properties['Nama Mall'].toLocaleString()) : '') + '</td>\
                    </tr>\
                    <tr>\
                        <th scope="row">Bioskop</th>\
                        <td>' + (feature.properties['Bioskop'] !== null ? autolinker.link(feature.properties['Bioskop'].toLocaleString()) : '') + '</td>\
                    </tr>\
                    <tr>\
                        <th scope="row">Alamat</th>\
                        <td>' + (feature.properties['Alamat'] !== null ? autolinker.link(feature.properties['Alamat'].toLocaleString()) : '') + '</td>\
                    </tr>\
                    <tr>\
                        <td colspan="2">' + (feature.properties['Foto'] !== null ? '<img style="height : 200px; width : 290px;" src="images/' + String(feature.properties['Foto']).replace(/[\\\/:]/g, '_').trim() + '">' : '') + '</td>\
                    </tr>\
                </table>';
            layer.bindPopup(popupContent, {
              maxHeight: 400
            });
            var popup = layer.getPopup();
            var content = popup.getContent();
            var updatedContent = removeEmptyRowsFromPopupContent(content, feature);
            popup.setContent(updatedContent);
          }

          function style_CGV_2_0(feature) {
            switch (String(feature.properties['Nama Mall'])) {
              case 'Vivo Sentul':
                return {
                  pane: 'pane_CGV_2',
                    shape: 'triangle',
                    radius: 8.0,
                    opacity: 1,
                    color: 'rgba(35,35,35,1.0)',
                    dashArray: '',
                    lineCap: 'butt',
                    lineJoin: 'miter',
                    weight: 1,
                    fill: true,
                    fillOpacity: 1,
                    fillColor: 'rgba(190,0,3,1.0)',
                    interactive: true,
                }
                break;
              default:
                return {
                  pane: 'pane_CGV_2',
                    shape: 'triangle',
                    radius: 8.0,
                    opacity: 1,
                    color: 'rgba(35,35,35,1.0)',
                    dashArray: '',
                    lineCap: 'butt',
                    lineJoin: 'miter',
                    weight: 1,
                    fill: true,
                    fillOpacity: 1,
                    fillColor: 'rgba(255,255,255,1.0)',
                    interactive: true,
                }
                break;
            }
          }
          map.createPane('pane_CGV_2');
          map.getPane('pane_CGV_2').style.zIndex = 402;
          map.getPane('pane_CGV_2').style['mix-blend-mode'] = 'normal';
          var layer_CGV_2 = new L.geoJson(json_CGV_2, {
            attribution: '',
            interactive: true,
            dataVar: 'json_CGV_2',
            layerName: 'layer_CGV_2',
            pane: 'pane_CGV_2',
            onEachFeature: pop_CGV_2,
            pointToLayer: function(feature, latlng) {
              var context = {
                feature: feature,
                variables: {}
              };
              return L.shapeMarker(latlng, style_CGV_2_0(feature));
            },
          });
          bounds_group.addLayer(layer_CGV_2);
          map.addLayer(layer_CGV_2);

          function pop_XXI_3(feature, layer) {
            layer.on({
              mouseout: function(e) {
                for (var i in e.target._eventParents) {
                  if (typeof e.target._eventParents[i].resetStyle === 'function') {
                    e.target._eventParents[i].resetStyle(e.target);
                  }
                }
              },
              mouseover: highlightFeature,
            });
            var popupContent = '<table>\
                    <tr>\
                        <th scope="row">Nama Mall</th>\
                        <td>' + (feature.properties['Nama Mall'] !== null ? autolinker.link(feature.properties['Nama Mall'].toLocaleString()) : '') + '</td>\
                    </tr>\
                    <tr>\
                        <th scope="row">Bioskop</th>\
                        <td>' + (feature.properties['Bioskop'] !== null ? autolinker.link(feature.properties['Bioskop'].toLocaleString()) : '') + '</td>\
                    </tr>\
                    <tr>\
                        <th scope="row">Alamat</th>\
                        <td>' + (feature.properties['Alamat'] !== null ? autolinker.link(feature.properties['Alamat'].toLocaleString()) : '') + '</td>\
                    </tr>\
                    <tr>\
                        <td colspan="2">' + (feature.properties['Foto'] !== null ? '<img style="height : 200px; width : 290px;" src="images/' + String(feature.properties['Foto']).replace(/[\\\/:]/g, '_').trim() + '">' : '') + '</td>\
                    </tr>\
                </table>';
            layer.bindPopup(popupContent, {
              maxHeight: 400
            });
            var popup = layer.getPopup();
            var content = popup.getContent();
            var updatedContent = removeEmptyRowsFromPopupContent(content, feature);
            popup.setContent(updatedContent);
          }

          function style_XXI_3_0(feature) {
            switch (String(feature.properties['Nama Mall'])) {
              case 'AEON Mall Sentul City':
                return {
                  pane: 'pane_XXI_3',
                    shape: 'triangle',
                    radius: 8.000000000000004,
                    opacity: 1,
                    color: 'rgba(35,35,35,1.0)',
                    dashArray: '',
                    lineCap: 'butt',
                    lineJoin: 'miter',
                    weight: 1,
                    fill: true,
                    fillOpacity: 1,
                    fillColor: 'rgba(243,203,81,1.0)',
                    interactive: true,
                }
                break;
              case 'Bogor Square':
                return {
                  pane: 'pane_XXI_3',
                    shape: 'triangle',
                    radius: 8.000000000000004,
                    opacity: 1,
                    color: 'rgba(35,35,35,1.0)',
                    dashArray: '',
                    lineCap: 'butt',
                    lineJoin: 'miter',
                    weight: 1,
                    fill: true,
                    fillOpacity: 1,
                    fillColor: 'rgba(242,255,4,1.0)',
                    interactive: true,
                }
                break;
              case 'Bogor Trade Mall':
                return {
                  pane: 'pane_XXI_3',
                    shape: 'triangle',
                    radius: 8.000000000000004,
                    opacity: 1,
                    color: 'rgba(35,35,35,1.0)',
                    dashArray: '',
                    lineCap: 'butt',
                    lineJoin: 'miter',
                    weight: 1,
                    fill: true,
                    fillOpacity: 1,
                    fillColor: 'rgba(242,255,4,1.0)',
                    interactive: true,
                }
                break;
              case 'Botani Square':
                return {
                  pane: 'pane_XXI_3',
                    shape: 'triangle',
                    radius: 8.000000000000004,
                    opacity: 1,
                    color: 'rgba(35,35,35,1.0)',
                    dashArray: '',
                    lineCap: 'butt',
                    lineJoin: 'miter',
                    weight: 1,
                    fill: true,
                    fillOpacity: 1,
                    fillColor: 'rgba(242,255,4,1.0)',
                    interactive: true,
                }
                break;
              case 'Boxies 123 Mall Bogor':
                return {
                  pane: 'pane_XXI_3',
                    shape: 'triangle',
                    radius: 8.000000000000004,
                    opacity: 1,
                    color: 'rgba(35,35,35,1.0)',
                    dashArray: '',
                    lineCap: 'butt',
                    lineJoin: 'miter',
                    weight: 1,
                    fill: true,
                    fillOpacity: 1,
                    fillColor: 'rgba(242,255,4,1.0)',
                    interactive: true,
                }
                break;
              case 'Cibinong City Mall':
                return {
                  pane: 'pane_XXI_3',
                    shape: 'triangle',
                    radius: 8.000000000000004,
                    opacity: 1,
                    color: 'rgba(35,35,35,1.0)',
                    dashArray: '',
                    lineCap: 'butt',
                    lineJoin: 'miter',
                    weight: 1,
                    fill: true,
                    fillOpacity: 1,
                    fillColor: 'rgba(243,203,81,1.0)',
                    interactive: true,
                }
                break;
              case 'Ciplaz Parung':
                return {
                  pane: 'pane_XXI_3',
                    shape: 'triangle',
                    radius: 8.000000000000004,
                    opacity: 1,
                    color: 'rgba(35,35,35,1.0)',
                    dashArray: '',
                    lineCap: 'butt',
                    lineJoin: 'miter',
                    weight: 1,
                    fill: true,
                    fillOpacity: 1,
                    fillColor: 'rgba(242,255,4,1.0)',
                    interactive: true,
                }
                break;
              case 'Jambu Dua Plaza':
                return {
                  pane: 'pane_XXI_3',
                    shape: 'triangle',
                    radius: 8.000000000000004,
                    opacity: 1,
                    color: 'rgba(35,35,35,1.0)',
                    dashArray: '',
                    lineCap: 'butt',
                    lineJoin: 'miter',
                    weight: 1,
                    fill: true,
                    fillOpacity: 1,
                    fillColor: 'rgba(242,255,4,1.0)',
                    interactive: true,
                }
                break;
              case 'Living World Kota Wisata':
                return {
                  pane: 'pane_XXI_3',
                    shape: 'triangle',
                    radius: 8.000000000000004,
                    opacity: 1,
                    color: 'rgba(35,35,35,1.0)',
                    dashArray: '',
                    lineCap: 'butt',
                    lineJoin: 'miter',
                    weight: 1,
                    fill: true,
                    fillOpacity: 1,
                    fillColor: 'rgba(243,203,81,1.0)',
                    interactive: true,
                }
                break;
              case 'Metropolitan Mall Cibubur':
                return {
                  pane: 'pane_XXI_3',
                    shape: 'triangle',
                    radius: 8.000000000000004,
                    opacity: 1,
                    color: 'rgba(35,35,35,1.0)',
                    dashArray: '',
                    lineCap: 'butt',
                    lineJoin: 'miter',
                    weight: 1,
                    fill: true,
                    fillOpacity: 1,
                    fillColor: 'rgba(242,255,4,1.0)',
                    interactive: true,
                }
                break;
              case 'Ramayana Tajur':
                return {
                  pane: 'pane_XXI_3',
                    shape: 'triangle',
                    radius: 8.000000000000004,
                    opacity: 1,
                    color: 'rgba(35,35,35,1.0)',
                    dashArray: '',
                    lineCap: 'butt',
                    lineJoin: 'miter',
                    weight: 1,
                    fill: true,
                    fillOpacity: 1,
                    fillColor: 'rgba(242,255,4,1.0)',
                    interactive: true,
                }
                break;
              case 'Transmart Bogor':
                return {
                  pane: 'pane_XXI_3',
                    shape: 'triangle',
                    radius: 8.000000000000004,
                    opacity: 1,
                    color: 'rgba(35,35,35,1.0)',
                    dashArray: '',
                    lineCap: 'butt',
                    lineJoin: 'miter',
                    weight: 1,
                    fill: true,
                    fillOpacity: 1,
                    fillColor: 'rgba(242,255,4,1.0)',
                    interactive: true,
                }
                break;
              default:
                return {
                  pane: 'pane_XXI_3',
                    shape: 'triangle',
                    radius: 8.000000000000004,
                    opacity: 1,
                    color: 'rgba(35,35,35,1.0)',
                    dashArray: '',
                    lineCap: 'butt',
                    lineJoin: 'miter',
                    weight: 1,
                    fill: true,
                    fillOpacity: 1,
                    fillColor: 'rgba(255,255,255,1.0)',
                    interactive: true,
                }
                break;
            }
          }
          map.createPane('pane_XXI_3');
          map.getPane('pane_XXI_3').style.zIndex = 403;
          map.getPane('pane_XXI_3').style['mix-blend-mode'] = 'normal';
          var layer_XXI_3 = new L.geoJson(json_XXI_3, {
            attribution: '',
            interactive: true,
            dataVar: 'json_XXI_3',
            layerName: 'layer_XXI_3',
            pane: 'pane_XXI_3',
            onEachFeature: pop_XXI_3,
            pointToLayer: function(feature, latlng) {
              var context = {
                feature: feature,
                variables: {}
              };
              return L.shapeMarker(latlng, style_XXI_3_0(feature));
            },
          });
          bounds_group.addLayer(layer_XXI_3);
          map.addLayer(layer_XXI_3);
          var osmGeocoder = new L.Control.Geocoder({
            collapsed: true,
            position: 'topleft',
            text: 'Search',
            title: 'Testing'
          }).addTo(map);
          document.getElementsByClassName('leaflet-control-geocoder-icon')[0]
            .className += ' fa fa-search';
          document.getElementsByClassName('leaflet-control-geocoder-icon')[0]
            .title += 'Search for a place';
          var baseMaps = {};
          var overlaysTree = [{
              label: 'XXI-<br /><table><tr><td style="text-align: center;"><img src="legend/XXI_3_AEONMallSentulCity0.png" /></td><td>AEON Mall Sentul City</td></tr><tr><td style="text-align: center;"><img src="legend/XXI_3_BogorSquare1.png" /></td><td>Bogor Square</td></tr><tr><td style="text-align: center;"><img src="legend/XXI_3_BogorTradeMall2.png" /></td><td>Bogor Trade Mall</td></tr><tr><td style="text-align: center;"><img src="legend/XXI_3_BotaniSquare3.png" /></td><td>Botani Square</td></tr><tr><td style="text-align: center;"><img src="legend/XXI_3_Boxies123MallBogor4.png" /></td><td>Boxies 123 Mall Bogor</td></tr><tr><td style="text-align: center;"><img src="legend/XXI_3_CibinongCityMall5.png" /></td><td>Cibinong City Mall</td></tr><tr><td style="text-align: center;"><img src="legend/XXI_3_CiplazParung6.png" /></td><td>Ciplaz Parung</td></tr><tr><td style="text-align: center;"><img src="legend/XXI_3_JambuDuaPlaza7.png" /></td><td>Jambu Dua Plaza</td></tr><tr><td style="text-align: center;"><img src="legend/XXI_3_LivingWorldKotaWisata8.png" /></td><td>Living World Kota Wisata</td></tr><tr><td style="text-align: center;"><img src="legend/XXI_3_MetropolitanMallCibubur9.png" /></td><td>Metropolitan Mall Cibubur</td></tr><tr><td style="text-align: center;"><img src="legend/XXI_3_RamayanaTajur10.png" /></td><td>Ramayana Tajur</td></tr><tr><td style="text-align: center;"><img src="legend/XXI_3_TransmartBogor11.png" /></td><td>Transmart Bogor</td></tr><tr><td style="text-align: center;"><img src="legend/XXI_3_12.png" /></td><td></td></tr></table>',
              layer: layer_XXI_3
            },
            {
              label: 'CGV<br /><table><tr><td style="text-align: center;"><img src="legend/CGV_2_VivoSentul0.png" /></td><td>Vivo Sentul</td></tr><tr><td style="text-align: center;"><img src="legend/CGV_2_1.png" /></td><td></td></tr></table>',
              layer: layer_CGV_2
            },
            {
              label: 'Cinepolis<br /><table><tr><td style="text-align: center;"><img src="legend/Cinepolis_1_LippoPlazaEkalosari0.png" /></td><td>Lippo Plaza Ekalosari</td></tr><tr><td style="text-align: center;"><img src="legend/Cinepolis_1_LippoPlazaKebunRayaBogor1.png" /></td><td>Lippo Plaza Kebun Raya Bogor</td></tr><tr><td style="text-align: center;"><img src="legend/Cinepolis_1_2.png" /></td><td></td></tr></table>',
              layer: layer_Cinepolis_1
            },
            {
              label: "OpenStreetMap",
              layer: layer_OpenStreetMap_0
            },
          ]
          var lay = L.control.layers.tree(null, overlaysTree, {
            //namedToggle: true,
            //selectorBack: false,
            //closedSymbol: '&#8862; &#x1f5c0;',
            //openedSymbol: '&#8863; &#x1f5c1;',
            //collapseAll: 'Collapse all',
            //expandAll: 'Expand all',
            collapsed: true,
          });
          lay.addTo(map);
          setBounds();
          map.addControl(new L.Control.Search({
            layer: layer_XXI_3,
            initial: false,
            hideMarkerOnCollapse: true,
            propertyName: 'Nama Mall'
          }));
          document.getElementsByClassName('search-button')[0].className +=
            ' fa fa-binoculars';
        </script>
      </div>
    </div>
  </section>
  <!-- end lokasi section -->

  <!-- footer section -->
  <footer class="footer_section">
    <div class="container">
      <div class="footer-info">
        <p>
          &copy; <span id="displayYear"></span> Distributed By
          <a>Enigma</a>
        </p>
      </div>
    </div>
  </footer>
  <!-- footer section -->

  <!-- jQery -->
  <script src="js/jquery-3.4.1.min.js"></script>
  <!-- popper js -->
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
  </script>
  <!-- bootstrap js -->
  <script src="js/bootstrap.js"></script>
  <!-- owl slider -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js">
  </script>
  <!-- isotope js -->
  <script src="https://unpkg.com/isotope-layout@3.0.4/dist/isotope.pkgd.min.js"></script>
  <!-- nice select -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/js/jquery.nice-select.min.js"></script>
  <!-- custom js -->
  <script src="js/custom.js"></script>
  <!-- Google Map -->
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCh39n5U-4IoWpsVGUHWdqB6puEkhRLdmI&callback=myMap">
  </script>
  <!-- End Google Map -->

</body>

</html>