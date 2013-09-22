<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Dashboard | Lima Security Car</title>
    <!--jquery-->
    <link href="assets/jquery/css/jquery-ui-1.8.16.custom.css" rel="stylesheet">
    <!--bootstrap-->
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!--app-->
    <link href="assets/app/css/style.css" rel="stylesheet">
</head>
<body>
    <!--Navigation-->
    <div class="navbar navbar-inverse navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container-fluid">
                <a class="brand" href="index.html">
                    <h2 style="font-family: Verdana; color: #fff; font-weight: normal;">Lima Security Car</h2>
                    </a>                
                <ul id="secondary-nav" class="nav pull-right">
                    <li><a href="#"><i class="icon-user icon-white"></i>&nbsp;<?=$user['name'] ?></a></li>
                    <li><a href="/logout"><i class="icon-off icon-white"></i>&nbsp;logout</a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <!--Container-->
    <div class="container-fluid">
        
        <!--Dashboad-->    
        <h2>Bienvenido, <?=$user['name'] ?></h2>
        <p>Información Vehícular de todos los medios de transportes que consultaste:</p>
        <hr />
        <ul class="nav nav-tabs" id="typeDriver">
            <li class="active"><a href="#encontradas">Encontradas</a></li>
            <li class=""><a href="#noencontradas">No encontradas</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="encontradas">
				Encontradas
			</div>
			<div class="tab-pane active" id="noencontradas">
				No encontradas
			</div>
		</div>
        <table id="dataDriver" class="table">
            <thead>
               <tr>
                    <td><b>Fecha y Hora</b></td>
                    <td><b>Placa</b></td>
                    <td><b>Ubicación</b></td>
                    <td><b>Marca</b></td>
                    <td><b>Modelo</b></td>
                    <td><b>Puertas</b></td>
                    <td><b>Capacidad</b></td>
                    <td><b>Foto</b></td>
                </tr>
            </thead>
            <tbody>
				<?php foreach ($drivers as $d) : ?>
				<tr>
                    <td><?=$d->date ?></td>
                    <td><?=$d->num_placa ?></td>
                    <td><a target="_blank" href="https://maps.google.com/maps?q=<?=$d->lat_placa ?>,<?=$d->long_placa ?>&z=14">Ver</a></td>
                    <td><?=$d->nombre_marca ?></td>
                    <td><?=$d->nombre_modelo ?></td>
                    <td><?=$d->numero_puerta ?></td>
                    <td><?=$d->capacidad_pasajeros ?></td>
                    <td><?php if (isset($d->path_placa)) : ?><a href="<?=$d->path_placa ?>" target="_blank">Foto</a><?php endif; ?></td>
                </tr>
				<?php endforeach; ?>
            </tbody>
        </table>
<br /><br /><br /><br />
        <hr />
        <p>Mapa con todas tus ubicaciones:</p>
        <div id="map-canvas" style="width: 100%; height: 550px"></div>
		<input id="lastId" type="hidden" value="<?=$drivers[0]->id ?>" />
    </div>
    <!--jquery-->
    <script src="assets/jquery/js/jquery-1.7.2.min.js"></script>
    <!--bootstrap-->
    <script src="assets/bootstrap/js/bootstrap-transition.js"></script>
    <script src="assets/bootstrap/js/bootstrap-alert.js"></script>
    <script src="assets/bootstrap/js/bootstrap-modal.js"></script>
    <script src="assets/bootstrap/js/bootstrap-dropdown.js"></script>
    <script src="assets/bootstrap/js/bootstrap-scrollspy.js"></script>
    <script src="assets/bootstrap/js/bootstrap-tab.js"></script>
    <script src="assets/bootstrap/js/bootstrap-tooltip.js"></script>
    <script src="assets/bootstrap/js/bootstrap-popover.js"></script>
    <script src="assets/bootstrap/js/bootstrap-button.js"></script>
    <script src="assets/bootstrap/js/bootstrap-collapse.js"></script>
    <script src="assets/bootstrap/js/bootstrap-carousel.js"></script>
    <script src="assets/bootstrap/js/bootstrap-typeahead.js"></script>
    <!--jquery ui-->
    <script src="assets/jquery/js/jquery.easing.min.js"></script>
    <script src="assets/jquery/js/jquery-ui-1.8.16.custom.min.js"></script>
    <!--app-->
    <script src="assets/app/js/script.js"></script>
    <!--jquery init-->
    <script>
        $('document').ready(function () {
            Dashboard.init();
			$('#typeDriver a').click(function (e) {
                e.preventDefault();
                $(this).tab('show');
            })
            setInterval(function() {
				$.ajax({
					url: 'welcome/get_new_drives',
					type: 'get',
					data: {'lastId': $('#lastId').val()},
					dataType: 'json',
					success: function(response) {
						var lastId = 0
						if (response[0]) {
							lastId = response[0].id;
						}
						$.each(response, function( index, value ) {
							$('#dataDriver > tbody').prepend('<tr id="driver'+value.id+'" style="background-color: yellow;"><td>'+value.date+'</td><td>'+value.num_placa+'</td><td><a target="_blank" href="https://maps.google.com/maps?q='+value.lat_placa+','+value.long_placa+'&amp;z=14">Ver</a></td><td></td><td></td><td></td><td></td><td><a href="'+value.path_placa+'" target="_blank">Foto</a></td></tr>');
						});
						if (lastId != 0) {
							$('#lastId').val(lastId);
						}
				   }
				})
			}, 5000);
        });

        //Live Tooltip
        $('body').tooltip({
            selector: '[rel=tooltip]'
        });

        function ShowHelp() {
            window.open('help.html', '', 'height=600,width=800,scrollbars=1,resizable=1');
        }
    </script>

    <script>
            var locations = <?=$positions ?>;
                
            var map;

            var gmaps = {
                initialize : function() {
                  var mapOptions = {
                    zoom: 13,
                    center: new google.maps.LatLng(locations[0][1],locations[0][2]),
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                  };
                  map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
                  
                    var infowindow = new google.maps.InfoWindow();

                    var marker, i;

                    for (i = 0; i < locations.length; i++) {  
                      marker = new google.maps.Marker({
                        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                        map: map
                      });

                      google.maps.event.addListener(marker, 'click', (function(marker, i) {
                        return function() {
                          infowindow.setContent(locations[i][0]);
                          infowindow.open(map, marker);
                        }
                      })(marker, i));
                    }           
                  
                },
                placeMarker : function(lat, lng, map, mensaje) {

                }
            };
            
            function loadScript() {
                var script = document.createElement('script');
                script.type = 'text/javascript';
                script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true&language=es&' +
                    'callback=gmaps.initialize';
                document.body.appendChild(script);
            }
            window.onload = loadScript;


       </script>
</body>
</html>
