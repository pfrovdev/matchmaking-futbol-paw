document.addEventListener('DOMContentLoaded', function () {
  // 1) Inicializar mapa
  const map = L.map('map').setView([-34.57, -59.11], 13);
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>'
  }).addTo(map);

  // 2) Geocoder
  const geocoder = L.Control.geocoder({
    collapsed: false,
    placeholder: 'Buscá tu zona...',
    defaultMarkGeocode: false
  }).addTo(map);

  const gcContainer = geocoder._container;
  const inputs = gcContainer.querySelectorAll('input[type="search"]');
  const gcInput = Array.from(inputs).find(i => i.offsetParent !== null);

  const teamZoneHiddenInput = document.getElementById('team-zone-hidden');
  if (teamZoneHiddenInput && gcInput) {
    if (teamZoneHiddenInput.value) {
      gcInput.value = teamZoneHiddenInput.value;
    }

    gcInput.addEventListener('input', () => {
      teamZoneHiddenInput.value = gcInput.value;
    });
  }

  const teamZoneValue = document.getElementById('map').dataset.teamZone;
  if (teamZoneValue) gcInput.value = teamZoneValue;

  // 3) Inputs lat/lng y slider
  const latInput = document.getElementById('lat');
  const lngInput = document.getElementById('lng');
  const slider = document.getElementById('radiusSlider');
  const output = document.getElementById('radiusValue');

  if (slider && output) {
    output.textContent = parseFloat(slider.value).toFixed(1);
  }

  // 4) Marker y círculo
  let marker = null;
  let circle = null;

  function updateLatLng(lat, lng) {
    if (latInput && lngInput) {
      latInput.value = lat.toFixed(6);
      lngInput.value = lng.toFixed(6);
    }
  }

  function reverseGeocode(lat, lng) {
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=10&addressdetails=1`)
      .then(r => r.json())
      .then(data => {
        if (data.display_name) {
          gcInput.value = data.display_name;
        }
      })
      .catch(console.error);
  }

  function updateCircle(position) {
    const radius = parseFloat(slider?.value || 0) * 1000;
    if (!circle) {
      circle = L.circle(position, { radius }).addTo(map);
    } else {
      circle.setLatLng(position);
      circle.setRadius(radius);
    }
  }

  function placeMarker(lat, lng) {
    const position = L.latLng(lat, lng);
    if (!marker) {
      marker = L.marker(position, { draggable: true }).addTo(map);
      marker.on('dragend', function (e) {
        const pos = e.target.getLatLng();
        updateLatLng(pos.lat, pos.lng);
        updateCircle(pos);
        reverseGeocode(pos.lat, pos.lng);
      });
    } else {
      marker.setLatLng(position);
    }
    updateLatLng(lat, lng);
    updateCircle(position);
    reverseGeocode(lat, lng);
  }

  map.on('click', function (e) {
    placeMarker(e.latlng.lat, e.latlng.lng);
  });

  geocoder.on('markgeocode', function (e) {
    const c = e.geocode.center;
    map.setView(c, 15);
    placeMarker(c.lat, c.lng);
    gcInput.value = e.geocode.name;
  });

  if (slider) {
    slider.oninput = function () {
      output.textContent = parseFloat(this.value).toFixed(1);
      if (circle) circle.setRadius(parseFloat(this.value) * 1000);
    };
  }
});
