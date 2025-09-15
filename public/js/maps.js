document.addEventListener('DOMContentLoaded', function () {

  function initMap(containerId, options = {}) {
    const mapContainer = document.getElementById(containerId);
    if (!mapContainer) return null;

    // === 1) Inicializar mapa ===
    const map = L.map(containerId).setView(
      options.initialView || [-34.57, -59.11],
      options.initialZoom || 13
    );
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>'
    }).addTo(map);

    // === 2) Geocoder ===
    const geocoder = L.Control.geocoder({
      collapsed: false,
      placeholder: 'Buscá tu zona...',
      defaultMarkGeocode: false
    }).addTo(map);

    const gcContainer = geocoder._container;
    const inputs = gcContainer.querySelectorAll('input[type="search"]');
    const gcInput = Array.from(inputs).find(i => i.offsetParent !== null);

    const teamZoneHiddenInput = document.getElementById(options.zoneInputId || 'team-zone-hidden');
    if (teamZoneHiddenInput && gcInput) {
      if (teamZoneHiddenInput.value) {
        gcInput.value = teamZoneHiddenInput.value;
      }
      gcInput.addEventListener('input', () => {
        teamZoneHiddenInput.value = gcInput.value;
      });
    }

    const teamZoneValue = mapContainer.dataset.teamZone;
    if (teamZoneValue) gcInput.value = teamZoneValue;

    // === 3) Inputs lat/lng y slider ===
    const latInput = document.getElementById(options.latInputId || 'lat');
    const lngInput = document.getElementById(options.lngInputId || 'lng');
    const slider = document.getElementById(options.sliderId || 'radiusSlider');
    const output = document.getElementById(options.outputId || 'radiusValue');

    if (slider && output) {
      output.textContent = parseFloat(slider.value).toFixed(1);
    }

    // === 4) Marker y círculo ===
    let marker = null;
    let circle = null;

    function clearMap() {
      if (marker) {
        map.removeLayer(marker);
        marker = null;
      }
      if (circle) {
        map.removeLayer(circle);
        circle = null;
      }
      if (latInput) latInput.value = 0;
      if (lngInput) lngInput.value = 0;
      if (slider) {
        slider.value = 1.0;
        if (output) output.textContent = "1.0";
      }
    }

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
          if (data.display_name && gcInput) {
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
      if (gcInput) gcInput.value = e.geocode.name;
    });

    if (slider) {
      slider.oninput = function () {
        output.textContent = parseFloat(this.value).toFixed(1);
        if (circle) circle.setRadius(parseFloat(this.value) * 1000);
      };
    }

    return { map, clearMap };
  }

  // Inicializar mapa desktop/tablet
  const mapDesktop = initMap('map', {
    latInputId: 'latDesktop',
    lngInputId: 'lngDesktop',
    sliderId: 'radiusSliderDesktop',
    outputId: 'radiusValueDesktop',
    zoneInputId: 'team-zone-hidden'
  });

  // Inicializar mapa mobile
  const mapMobile = initMap('map-mobile', {
    latInputId: 'latMobile',
    lngInputId: 'lngMobile',
    sliderId: 'radiusSliderMobile',
    outputId: 'radiusValueMobile',
    zoneInputId: 'team-zone-hidden-mobile'
  });

  window.mapDesktop = mapDesktop;
  window.mapMobile = mapMobile;
});
