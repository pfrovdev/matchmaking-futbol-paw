var map = L.map("map").setView([-34.57, -59.11], 13);
L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
  maxZoom: 19,
  attribution: "&copy; OpenStreetMap contributors",
}).addTo(map);

var marker, circle;
var slider = document.getElementById("radiusSlider");
var output = document.getElementById("radiusValue");
output.textContent = slider.value;

function updateLatLngFields(lat, lng) {
  document.getElementById("lat").value = lat.toFixed(6);
  document.getElementById("lng").value = lng.toFixed(6);
}

function placeMarker(e) {
  var lat = e.latlng.lat,
    lng = e.latlng.lng;
  if (!marker) {
    marker = L.marker([lat, lng], { draggable: true }).addTo(map);
    marker.on("dragend", function (ev) {
      var pos = ev.target.getLatLng();
      updateLatLngFields(pos.lat, pos.lng);
      updateCircle(pos);
    });
  } else {
    marker.setLatLng([lat, lng]);
  }
  updateLatLngFields(lat, lng);
  updateCircle(e);
}

function updateCircle(e) {
  var m = parseFloat(slider.value) * 1000;
  if (!circle) {
    circle = L.circle(e.latlng, { radius: m }).addTo(map);
  } else {
    circle.setLatLng(e.latlng);
    circle.setRadius(m);
  }
}

map.on("click", placeMarker);
slider.oninput = function () {
  output.textContent = parseFloat(this.value).toFixed(1);
  if (circle) circle.setRadius(parseFloat(this.value) * 1000);
};
