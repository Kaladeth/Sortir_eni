
function init(){
    const parcThabor = {
        lat: 48.114384,
        lng:-1.669494
    }
    const zoomlevel = 7;

    const map = L.map('map').setView([parcThabor.lat, parcThabor.lng], zoomlevel);

    const mainLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    });

    mainLayer.adTo(map);

}
