document.addEventListener('DOMContentLoaded', () => {
    let equipoListado = document.querySelector('.lista-equipos');
    const ordenForm = document.getElementById('ordenForm');
    const mapForm = document.getElementById('mapForm');
    const searchForm = document.querySelector('form[action="/search-team"]');

    const clearButton = document.getElementById('clearFilters');

    if (!equipoListado) return;

    const updateHiddenInput = (form, name, value) => {
        let input = form.querySelector(`[name="${name}"]`);
        if (!input) {
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            form.appendChild(input);
        }
        input.value = value;
    };

    const serializeForms = (...forms) => {
        const params = new URLSearchParams();
        forms.forEach(form => {
            if (!form) return;
            new FormData(form).forEach((value, key) => {
                if (value !== '') params.set(key, value);
            });
        });
        return params.toString();
    };

    const updateEquipos = () => {
        const queryString = serializeForms(searchForm, ordenForm, mapForm);
        fetch(`/search-team?${queryString}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(res => res.ok ? res.text() : Promise.reject(res))
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const newEquipos = doc.querySelector('.lista-equipos');
                if (newEquipos && equipoListado) {
                    equipoListado.replaceWith(newEquipos);
                    equipoListado = newEquipos;
                }
            })
            .catch(err => console.error('Error actualizando equipos:', err));
    };

    ordenForm.querySelectorAll('input[type=radio]').forEach(radio => {
        radio.addEventListener('change', () => {
            //Esto no me gusta mucho, si agregammos nuestos filtros o demás
            ['id_nivel_elo', 'lat', 'lng', 'radius_km', 'nombre'].forEach(name => {
                const value =
                    searchForm.querySelector(`[name="${name}"]`)?.value ||
                    mapForm.querySelector(`[name="${name}"]`)?.value ||
                    '';
                updateHiddenInput(ordenForm, name, value);
            });
            updateEquipos();
        });
    });

    document.querySelectorAll('.boton-filtro').forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();
            const id_nivel_elo = btn.value;

            document.querySelectorAll('.boton-filtro').forEach(b => b.classList.remove('activo'));
            btn.classList.add('activo');

            updateHiddenInput(ordenForm, 'id_nivel_elo', id_nivel_elo);
            updateHiddenInput(searchForm, 'id_nivel_elo', id_nivel_elo);

            updateEquipos();
        });

        const currentId = searchForm.querySelector('[name="id_nivel_elo"]')?.value;
        if (btn.value === currentId) {
            btn.classList.add('activo');
        }
    });

    if (mapForm) {
        mapForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const lat = mapForm.querySelector('[name="lat"]').value;
            const lng = mapForm.querySelector('[name="lng"]').value;
            const radius = mapForm.querySelector('[name="radius_km"]').value;

            updateHiddenInput(ordenForm, 'lat', lat);
            updateHiddenInput(ordenForm, 'lng', lng);
            updateHiddenInput(ordenForm, 'radius_km', radius);

            updateHiddenInput(searchForm, 'lat', lat);
            updateHiddenInput(searchForm, 'lng', lng);
            updateHiddenInput(searchForm, 'radius_km', radius);

            updateEquipos();
        });
    }

    if (clearButton) {
    clearButton.addEventListener('click', () => {
        ['nombre', 'id_nivel_elo', 'orden', 'lat', 'lng', 'radius_km'].forEach(name => {
            updateHiddenInput(ordenForm, name, '');
            updateHiddenInput(searchForm, name, '');
            updateHiddenInput(mapForm, name, '');
        });

        const nombreInput = document.querySelector('input[name="nombre"]');
        if (nombreInput) nombreInput.value = '';

        const radiusSlider = document.getElementById('radiusSlider');
        const radiusValue = document.getElementById('radiusValue');

        if (radiusSlider) radiusSlider.value = 1;
        if (radiusValue) radiusValue.textContent = 1.0;

        document.querySelectorAll('.boton-filtro.activo').forEach(btn => {
            btn.classList.remove('activo');
        });

        ordenForm.querySelectorAll('input[type=radio]').forEach(radio => radio.checked = false);

        updateEquipos();
    });
}
});
