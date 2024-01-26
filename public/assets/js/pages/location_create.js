/*
 * Mapbox init and interaction scripts.
*/
const marker = new mapboxgl.Marker()
let arr = []
let route = {}
mapboxgl.accessToken = 'pk.eyJ1Ijoicm9hZHNpZGUiLCJhIjoiY2toYjJ6OGh1MGhoNzJ4cWt2ZzVsNWhrayJ9.RWhXc_F2z_CuuP1OjewYVA';
let map = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/mapbox/streets-v11',
    center: [-100.85519230275258, 39.18901932172545],
    zoom: 4
});
    
// Setting path
map.on('load', function () {
    map.addSource('route', {
        'type': 'geojson',
        'data': {
            'type': 'Feature',
            'properties': {},
            'geometry': {
                'type': 'LineString',
                'coordinates': arr
            }
        }
    });
    map.addLayer({
        'id': 'route',
        'type': 'line',
        'source': 'route',
        'layout': {
            'line-join': 'round',
            'line-cap': 'round'
        },
        'paint': {
            'line-color': '#888',
            'line-width': 5
        }
    });
    route = map.getSource('route')
});

map.on('click', addMarker);
document.querySelector('#saveRoute').addEventListener('click', saveRoute)
document.querySelector('#clearRoute').addEventListener('click', clearRoute)

function addMarker(e){
    if (marker !== undefined) marker.remove()
    
    marker
        .setLngLat(e.lngLat)
        .addTo(map);
    console.log(e.lngLat)

    arr.push([e.lngLat.lng, e.lngLat.lat])
    
    route.setData({
        "type": "FeatureCollection",
        "features": [{
            "type": "Feature",
            "properties": {  },
            "geometry": {
                "type": "LineString",
                "coordinates": arr
            }
        }]
    })
}

function saveRoute(e) {
    e.preventDefault()
    
    var settings = {
        "url": `${location.origin}/api/locations/add`,
        "method": "POST",
        "timeout": 0,
        "headers": {
            "Authorization": "Bearer 73caf808859ff572754db4c94cdb463a550b1c6f",
            "Content-Type": "application/x-www-form-urlencoded"
        },
        "data": {
            "loc_p_title": "new route",
            "loc_p_cors_start": JSON.stringify(arr[0]),
            "loc_p_cors_finish": JSON.stringify(arr[arr.length - 1]),
            "loc_p_cors_all": JSON.stringify(arr)
        }
    };
      
    $.ajax(settings).done(function (response) {
        console.log(response);
    });
}

function clearRoute(e) {
    e.preventDefault()
    arr = []

    if (marker !== undefined) marker.remove()
    
    route.setData({
        "type": "FeatureCollection",
        "features": [{
            "type": "Feature",
            "properties": {  },
            "geometry": {
                "type": "LineString",
                "coordinates": []
            }
        }]
    })
}