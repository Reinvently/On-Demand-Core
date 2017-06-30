/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

var map = {
    map: null,
    init: function () {
        var centerCoords = {lat: 0, lng: 0};
        var mapDiv = document.getElementById('map');
        this.map = new google.maps.Map(mapDiv, {
            center: centerCoords,
            zoom: 2
        });
    },
    addMarker: function (lat, lng) {
        var marker = new google.maps.Marker({
            position: {lat: parseFloat(lat), lng: parseFloat(lng)},
            map: this.map,
            title: 'Hello World!'
        });
    }
};

function loadLocations() {
    $.ajax({
        url: '/api/locations',
        headers: {'Authorization': 'Bearer ' + authToken},
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.data) {
                $.each(response.data, function (key, marker) {
                    map.addMarker(marker.latitude, marker.longitude);
                });
            }
        }
    });
}

window.onload = function () {
    //alert('11');
    //initMap();
    map.init();
    //console.log(map.map)
    loadLocations();
}