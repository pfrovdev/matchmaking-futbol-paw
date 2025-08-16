export default class DesafioComponent {
  /**
   * @param {Array<DesafioDto>} desafios - Array de objetos DesafioDto.
   * @param {HTMLElement} container - El <ul> donde se inyectan los <li>.
   */
  constructor(desafios, container) {
    this.desafios = desafios;
    this.container = container;
  }

  updateData(nuevos) {
    this.desafios = nuevos;
    this.render();
  }

  render() {
    this.container.innerHTML = '';

    if (this.desafios.length === 0) {
      const li = document.createElement('li');
      li.textContent = 'No hay desafíos para mostrar.';
      this.container.appendChild(li);
      return;
    }

    this.desafios.forEach(d => {
      const li = document.createElement('li');
      li.classList.add('challenge-item');

      const card = document.createElement('div');
      card.classList.add('challenge-card');

      // -------------------------------------------------------
      // card-side: imagen del equipo + badge de nivel
      // -------------------------------------------------------
      
      const side = document.createElement('div');
      side.classList.add('card-side');

      const figure = document.createElement('figure');
      figure.classList.add('team-image');

      const img = document.createElement('img');
      img.src = d.equipoDesafiante.urlFotoPerfil || '/icons/defaultTeamIcon.png';
      img.alt = `Escudo del equipo ${d.equipoDesafiante.nombreEquipo}`;
      figure.appendChild(img);

      const figcaption = document.createElement('figcaption');
      figcaption.classList.add('team-rank');
      figcaption.textContent = d.equipoDesafiante.descripcionElo;

      const eloData = levelsEloMap.find(
        level => level.descripcion === d.equipoDesafiante.descripcionElo
      );

      if (eloData) {
        const gradient = `linear-gradient(90deg, ${eloData.color_inicio}, ${eloData.color_fin})`;
        figcaption.style.background = gradient;
      } else {
        figcaption.style.background = 'gray';
      }

      figure.appendChild(figcaption);

      side.appendChild(figure);
      card.appendChild(side);

      // -------------------------------------------------------
      // card-main: contenido principal de la tarjeta
      // -------------------------------------------------------
      const mainDiv = document.createElement('div');
      mainDiv.classList.add('card-main');

      // Header: nombre del equipo + “Elo”
      const header = document.createElement('div');
      header.classList.add('card-header');

      const h3 = document.createElement('h3');
      h3.classList.add('team-name');
      h3.textContent = `${d.equipoDesafiante.nombreEquipo} (${d.equipoDesafiante.acronimo})`;
      header.appendChild(h3);

      const eloDiv = document.createElement('div');
      eloDiv.classList.add('team-record');
      eloDiv.textContent = `Elo: ${d.equipoDesafiante.eloActual}`;
      header.appendChild(eloDiv);

      mainDiv.appendChild(header);

      // -------------------------------------------------------
      // Body: deportividad (⚽), lema, record, link a perfil
      // -------------------------------------------------------
      const body = document.createElement('div');
      body.classList.add('card-body');

      // Deportividad como iconos
      const deportDiv = document.createElement('div');
      deportDiv.classList.add('sport-icons');
      deportDiv.textContent = 'Deportividad: ';
      for (let i = 1; i <= 5; i++) {
        const span = document.createElement('span');
        span.classList.add('icon');
        span.textContent = '⚽';
        if (i > d.equipoDesafiante.deportividad) {
          span.style.opacity = '0.4';
          span.style.color = 'grey';
        }
        deportDiv.appendChild(span);
      }
      body.appendChild(deportDiv);

      // Lema
      const pMotto = document.createElement('p');
      pMotto.classList.add('team-motto');
      pMotto.textContent = d.equipoDesafiante.lema;
      body.appendChild(pMotto);

      // Record W/L/D
      if (
        d.equipoDesafiante.resultadosEquipo &&
        typeof d.equipoDesafiante.resultadosEquipo === 'object' &&
        ('ganados' in d.equipoDesafiante.resultadosEquipo ||
         'perdidos' in d.equipoDesafiante.resultadosEquipo ||
         'empates' in d.equipoDesafiante.resultadosEquipo)
      ) {
        const {
          ganados = 0,
          perdidos = 0,
          empates = 0
        } = d.equipoDesafiante.resultadosEquipo;

        const recordP = document.createElement('p');
        recordP.classList.add('team-record');
        recordP.textContent = `W/L/D: ${ganados}-${perdidos}-${empates}`;
        body.appendChild(recordP);
      }

      // Link al perfil del equipo
      const aProfile = document.createElement('a');
      aProfile.href = `/dashboard?id=${d.equipoDesafiante.idEquipo}`;
      aProfile.classList.add('profile-link');
      aProfile.textContent = 'ver perfil del equipo';
      body.appendChild(aProfile);

      mainDiv.appendChild(body);

      // -------------------------------------------------------
      // card-actions: formularios “Aceptar” / “Rechazar”
      // -------------------------------------------------------
      const actionsDiv = document.createElement('div');
      actionsDiv.classList.add('card-actions');

      // Formulario ACEPTAR
      const formAccept = document.createElement('form');
      formAccept.action = '/accept-desafio';
      formAccept.method = 'POST';

      const inputEquipoAccept = document.createElement('input');
      inputEquipoAccept.type = 'hidden';
      inputEquipoAccept.name = 'id_equipo';
      inputEquipoAccept.value = d.equipoDesafiante.idEquipo;
      formAccept.appendChild(inputEquipoAccept);

      const inputDesafioAccept = document.createElement('input');
      inputDesafioAccept.type = 'hidden';
      inputDesafioAccept.name = 'id_desafio';
      inputDesafioAccept.value = d.idDesafio;
      formAccept.appendChild(inputDesafioAccept);

      const btnAccept = document.createElement('button');
      btnAccept.type = 'submit';
      btnAccept.classList.add('btn', 'btn-accept');
      
      const btnText = document.createElement('span');
      btnText.classList.add('btn-text');
      btnText.textContent = 'Aceptar desafío';
      btnAccept.appendChild(btnText);
      
      const spinner = document.createElement('span');
      spinner.classList.add('spinner');
      spinner.style.display = 'none';
      btnAccept.appendChild(spinner);

      formAccept.appendChild(btnAccept);
      formAccept.classList.add('form-accept');

      formAccept.addEventListener('submit', function () {
          activarSpinner(btnAccept);
      });

      actionsDiv.appendChild(formAccept);

      // Formulario RECHAZAR
      const formReject = document.createElement('form');
      formReject.action = '/reject-desafio';
      formReject.method = 'POST';
      formReject.style.display = 'inline';

      const methodOverride = document.createElement('input');
      methodOverride.type = 'hidden';
      methodOverride.name = '_method';
      methodOverride.value = 'DELETE';
      formReject.appendChild(methodOverride);

      const inputEquipoReject = document.createElement('input');
      inputEquipoReject.type = 'hidden';
      inputEquipoReject.name = 'id_equipo';
      inputEquipoReject.value = d.equipoDesafiante.idEquipo;
      formReject.appendChild(inputEquipoReject);

      const inputDesafioReject = document.createElement('input');
      inputDesafioReject.type = 'hidden';
      inputDesafioReject.name = 'id_desafio';
      inputDesafioReject.value = d.idDesafio;
      formReject.appendChild(inputDesafioReject);

      const btnReject = document.createElement('button');
      btnReject.type = 'submit';
      btnReject.classList.add('btn', 'btn-reject');

      const btnRejectText = document.createElement('span');
      btnRejectText.classList.add('btn-text');
      btnRejectText.textContent = 'Rechazar desafío';
      btnReject.appendChild(btnRejectText);
      
      const spinnerRechazar = document.createElement('span');
      spinnerRechazar.classList.add('spinner');
      spinnerRechazar.style.display = 'none';
      btnReject.appendChild(spinnerRechazar);

      formReject.appendChild(btnReject);
      actionsDiv.appendChild(formReject);

      formReject.addEventListener('submit', function () {
          activarSpinner(btnReject);
      });

      mainDiv.appendChild(actionsDiv);

      // -------------------------------------------------------
      card.appendChild(mainDiv);
      li.appendChild(card);
      this.container.appendChild(li);
    });
  }
}