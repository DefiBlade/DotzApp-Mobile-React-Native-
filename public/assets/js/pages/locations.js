/*
 * Mapbox init and interaction scripts.
*/
mapboxgl.accessToken = 'pk.eyJ1Ijoicm9hZHNpZGUiLCJhIjoiY2toYjJ6OGh1MGhoNzJ4cWt2ZzVsNWhrayJ9.RWhXc_F2z_CuuP1OjewYVA';
const mapContainers = document.querySelectorAll('.map-container')

mapContainers.forEach(mapping)
initMap(null, mapContainers[0])

function mapping(mapContainer) {
    // Create map instance on accordeon content shown
    $(mapContainer.parentElement.parentElement.parentElement).on('shown.bs.collapse', mapContainer, initMap)
}

function initMap(event = null, elem = null) {
    let mapContainer = event != null ? event.data : elem
    let cors, center
    pageData.locs.forEach(e=>{
        if (e.loc_id === mapContainer.dataset.loc_id) {
            cors = JSON.parse(e.loc_cors_all)
            center = cors[Math.floor(cors.length / 2)]
        }
    })
console.log(mapContainer)
    // Initiating map obj
    let map = new mapboxgl.Map({
        container: mapContainer,
        style: 'mapbox://styles/mapbox/streets-v11',
        center: center,
        zoom: 14
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
                    'coordinates': cors
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
    });
    
    $(mapContainer.parentElement.parentElement.parentElement).on('hidden.bs.collapse', map, ev=>{ev.data.remove()})
}