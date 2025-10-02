document.addEventListener("DOMContentLoaded", () => {
  let equipoListado = document.querySelector(".lista-equipos");
  if (!equipoListado) return;

  const forms = {
    search: Array.from(document.querySelectorAll('[data-form="search"]')),
    orden: Array.from(document.querySelectorAll('[data-form="orden"]')),
    map: Array.from(document.querySelectorAll('[data-form="map"], [data-form="map-mobile"]')),
  };

  const clearButton = document.getElementById("clearFilters");
  const isMobile = () => window.innerWidth <= 768;

  const updateHiddenInput = (formList, name, value) => {
    if (!formList) return;
    formList.forEach((form) => {
      if (!form) return;
      let input = form.querySelector(`[name="${name}"]`);
      if (!input) {
        input = document.createElement("input");
        input.type = "hidden";
        input.name = name;
        form.appendChild(input);
      }
      input.value = value;
    });
  };

    const serializeForms = (formsObj) => {
        const params = new URLSearchParams();

        Object.values(formsObj).forEach((formList) => {
        formList.forEach((form) => {
            if (!form) return;
            new FormData(form).forEach((value, key) => {
            if (value === "") return;
            if (params.has(key) && ["id_nivel_elo"].includes(key)) {
                params.append(key, value);
            } else {
                params.set(key, value);
            }
            });
        });
        });

        return params.toString();
    };

    const updateEquipos = () => {
        const queryString = serializeForms(forms);
        fetch(`/search-team?${queryString}`, {
        headers: { "X-Requested-With": "XMLHttpRequest" },
        })
        .then((res) => (res.ok ? res.text() : Promise.reject(res)))
        .then((html) => {
            const doc = new DOMParser().parseFromString(html, "text/html");
            const newEquipos = doc.querySelector(".lista-equipos");
            if (newEquipos) {
            equipoListado.replaceWith(newEquipos);
            equipoListado = newEquipos;
            }

        })
        .catch((err) => console.error("Error actualizando equipos:", err));
    };

    document.querySelectorAll('[data-form="orden"] input[name="orden"]').forEach((radio) => {
        radio.addEventListener("change", () => {
            document.querySelectorAll('[data-form="orden"]').forEach((form) => {
                form.querySelectorAll('input[name="orden"]').forEach((r) => {
                r.checked = r.value === radio.value;
                });
            });

            updateHiddenInput(forms.search, "orden", radio.value);
            updateHiddenInput(forms.map, "orden", radio.value);

            updateEquipos();
            if (isMobile()) {
                window.closeFiltersModal();
            }
        });
    });

    document.querySelectorAll(".boton-filtro").forEach((btn) => {
        btn.addEventListener("click", (e) => {
            e.preventDefault();
            const id_nivel_elo = btn.value;

            document.querySelectorAll(".boton-filtro").forEach((b) => b.classList.remove("activo"));
            btn.classList.add("activo");

            updateHiddenInput(forms.search, "id_nivel_elo", id_nivel_elo);
            updateEquipos();
            if (isMobile()) {
                window.closeFiltersModal();
            }
        });
        
        const currentId = forms.search
            .map((f) => f.querySelector('[name="id_nivel_elo"]')?.value)
            .find(Boolean);
        if (btn.value === currentId) btn.classList.add("activo");
        
    });

    forms.map.forEach((mapForm) => {
        mapForm?.addEventListener("submit", (e) => {
            e.preventDefault();
            const lat = mapForm.querySelector('[name="lat"]')?.value || "";
            const lng = mapForm.querySelector('[name="lng"]')?.value || "";
            const radius = mapForm.querySelector('[name="radius_km"]')?.value || "";

            updateHiddenInput(forms.search, "lat", lat);
            updateHiddenInput(forms.search, "lng", lng);
            updateHiddenInput(forms.search, "radius_km", radius);

            updateHiddenInput(forms.map, "lat", lat);
            updateHiddenInput(forms.map, "lng", lng);
            updateHiddenInput(forms.map, "radius_km", radius);

            updateEquipos();
            if (isMobile()) {
                window.closeFiltersModal();
            }
        });
    });

    document.querySelectorAll('input[name="radius_km"]').forEach((slider) => {
        slider.addEventListener("input", (e) => {
            const value = e.target.value;
            const span = slider.closest(".input-group")?.querySelector("#radiusValue");
            if (span) span.textContent = value;
            
        });
    });

    forms.search.forEach((form) => {
        const btnBuscar = form.querySelector('button[type="submit"]');
        btnBuscar?.addEventListener("click", (e) => {
            e.preventDefault();
            const nombre = form.querySelector('[name="nombre"]')?.value || "";
            updateHiddenInput(forms.search, "nombre", nombre);
            updateEquipos();
            if (isMobile()) window.closeFiltersModal();
        });
    });

    clearButton?.addEventListener("click", () => {
        document.querySelectorAll('input[name="nombre"]').forEach((input) => (input.value = ""));

        ["id_nivel_elo", "orden", "lat", "lng", "radius_km", "nombre"].forEach((name) => {
            let defaultValue = "";
            if (name === "orden") defaultValue = "desc";
            if (name === "lat" || name === "lng") defaultValue = 0;
            if (name === "radius_km") defaultValue = 1.0;

            updateHiddenInput(forms.search, name, defaultValue);
            updateHiddenInput(forms.map, name, defaultValue);
        });

        document.querySelectorAll(".boton-filtro.activo").forEach((btn) => btn.classList.remove("activo"));

        document.querySelectorAll('[data-form="orden"]').forEach((ordenForm) => {
            ordenForm.querySelectorAll('input[name="orden"]').forEach((radio) => {
            radio.checked = radio.value === "desc";
            });
        });

        document.querySelectorAll('input[name="radius_km"]').forEach((s) => (s.value = 1.0));
        document.querySelectorAll('#radiusValueDesktop, #radiusValueMobile').forEach((v) => (v.textContent = "1.0"));

        window.mapDesktop?.clearMap();
        window.mapMobile?.clearMap();

        updateEquipos();
    });

});
