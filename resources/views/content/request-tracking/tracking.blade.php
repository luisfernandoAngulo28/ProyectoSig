<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('assets/img/favicon/favicon-57.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('assets/img/favicon/favicon-72.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('assets/img/favicon/favicon-114.png') }}">
    <title>Andre</title>
    <style type="text/css">
        html {
            height: 100%
        }

        body {
            height: 100%;
            margin: 0;
            padding: 0
        }

        #map_canvas {
            height: 100%
        }

        #my-data {
            display: 'none'
        }
    </style>
</head>

<body>
    <input id="my-data" value="{{ $items }}" style="display: none"></input>
    <p style="display: none" id="url">{{ config('services.node_server.host') }}</p>
    <input id="data" value="{{ $token }}" style="display: none"></input>
    <div id="map_canvas" style="width:100%; height:100%"></div>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('solunes.google_maps_key') }}&libraries=places"></script>

    </script>
    <script>
        class GoogleMaps {
            constructor(lat, lng) {
                const myOptions = {
                    center: new google.maps.LatLng(lat, lng),
                    zoom: 8,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                };
                this.map = new google.maps.Map(document.getElementById('map_canvas'), myOptions);
                this.map = new google.maps.Map(document.getElementById('map_canvas'), myOptions);
                this.markerTrackeo = null
            }

            setMarket(lat, lng, title) {
                const marker = new google.maps.Marker({
                    position: new google.maps.LatLng(lat, lng),
                    map: this.map,
                    title,
                });
            }

            setMarketTrackeo(lat, lng, title) {
                if (this.markerTrackeo) this.markerTrackeo.setMap(null);

                this.markerTrackeo = new google.maps.Marker({
                    position: new google.maps.LatLng(lat, lng),
                    map: this.map,
                    title,
                });
            }

            currentUbication() {
                infoWindow = new google.maps.InfoWindow();

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const pos = {
                                lat: position.coords.latitude,
                                lng: position.coords.longitude,
                            };

                            infoWindow.setPosition(pos);
                            infoWindow.setContent('Location found.');
                            infoWindow.open(this.map);
                            this.map.setCenter(pos);
                        },
                        () => {
                            handleLocationError(true, infoWindow, this.map.getCenter());
                        }
                    );
                } else {
                    // Browser doesn't support Geolocation
                    this.handleLocationError(false, infoWindow, this.map.getCenter());
                }
            }

            handleLocationError(browserHasGeolocation, infoWindow, pos) {
                infoWindow.setPosition(pos);
                infoWindow.setContent(
                    browserHasGeolocation ?
                    'Error: The Geolocation service failed.' :
                    "Error: Your browser doesn't support geolocation."
                );
                infoWindow.open(map);
            }

            drawPath(waypoints) {

                const waypointTransform = waypoints.map((point) => {
                    return {
                        location: new google.maps.LatLng(point.latitude, point.longitude),
                        stopover: true
                    }
                })

                const directionsService = new google.maps.DirectionsService();
                directionsService.route({
                        origin: {
                            lat: Number(waypoints[0].latitude),
                            lng: Number(waypoints[0].longitude)
                        },
                        destination: {
                            lat: Number(waypoints[waypoints.length - 1].latitude),
                            lng: Number(waypoints[waypoints.length - 1].longitude),

                        },
                        waypoints: waypointTransform,
                        travelMode: 'DRIVING',
                    },
                    (response, status) => {
                        if (status === 'OK') {
                            new google.maps.DirectionsRenderer({
                                suppressMarkers: true,
                                directions: response,
                                map: this.map,
                            });
                        }
                    }
                );
            }
        }
    </script>
    <script type="module">
            import {io} from 'https://cdn.socket.io/4.3.2/socket.io.esm.min.js';
          
            document.addEventListener('DOMContentLoaded', function () {
                const data = JSON.parse(document.getElementById('my-data').value)

                const googleMaps = new GoogleMaps(
                        Number(data.request_waypoints[0].latitude),
                        Number(data.request_waypoints[data.request_waypoints.length-1].latitude)
                );
                // SET MARKERTS
                data.request_waypoints.map((point)=>{
                    googleMaps.setMarket(Number(point.latitude), Number(point.longitude), 'titulo');
                    return null;
                })
                // DRAW ROUTE
                googleMaps.drawPath(data.request_waypoints);

                const token = document.getElementById('data').value
                const url = document.getElementById('url').innerText

                console.log("SERVER NODE: ",url)
                
                const socket = io(url, {
                    extraHeaders: { "authentication": token }
                });
                socket.on('connect', (data) => {
                    console.log('Conectado: ', data);
                    socket.on('ON_CHANGE_LAT_LONG_DRIVER', (data) => {
                        console.log("emite ?:",data )
                        googleMaps.setMarketTrackeo(data.lat, data.lng, '');
                    });
                    
                });
                socket.on('disconnect', (data) => {
                    console.log('Desconectado: ', data);
                });
            });

    </script>
</body>

</html>
