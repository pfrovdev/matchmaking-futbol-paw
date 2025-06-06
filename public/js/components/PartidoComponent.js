// components/PartidoComponent.js

export default class PartidoComponent {
  /**
   * @param {Array<PartidoDto>} partidos - Array de instancias de PartidoDto.
   * @param {HTMLElement} container - El <ul> donde se inyecta los <li>.
   */
  constructor(partidos, container) {
    this.partidos  = partidos;
    this.container = container;
  }

  updateData(nuevos) {
    this.partidos = nuevos;
    this.render();
  }

  render() {
    this.container.innerHTML = '';

    if (this.partidos.length === 0) {
      const li = document.createElement('li');
      li.textContent = 'No hay partidos pendientes para mostrar.';
      this.container.appendChild(li);
      return;
    }

    this.partidos.forEach(p => {
      // <li> contenedor de la tarjeta
      const li = document.createElement('li');
      li.classList.add('match-item');

      // <div class="nm-card">
      const card = document.createElement('div');
      card.classList.add('nm-card');

      // nm-card-side: imagen del equipo + badge de nivel
      const side = document.createElement('div');
      side.classList.add('nm-card-side');

      const teamImageWrapper = document.createElement('div');
      teamImageWrapper.classList.add('nm-team-image');

      const img = document.createElement('img');
      img.src = p.equipo.urlFotoPerfil || '/icons/defaultTeamIcon.png';
      img.alt = `Logo de ${p.equipo.nombreEquipo}`;
      teamImageWrapper.appendChild(img);

      const badge = document.createElement('span');
      badge.classList.add('nm-level-badge');
      badge.textContent = p.equipo.descripcionElo;
      teamImageWrapper.appendChild(badge);

      side.appendChild(teamImageWrapper);
      card.appendChild(side);

      // nm-card-main: contenido principal
      const mainDiv = document.createElement('div');
      mainDiv.classList.add('nm-card-main');

      // nm-card-header: <h3 class="nm-team-name"> y <div class="nm-team-record">
      const header = document.createElement('div');
      header.classList.add('nm-card-header');

      // <h3 class="nm-team-name">Nombre del equipo</h3>
      const h3 = document.createElement('h3');
      h3.classList.add('nm-team-name');
      h3.textContent = p.equipo.nombreEquipo;
      header.appendChild(h3);

      // <div class="nm-team-record">record</div>
      // Formateamos record como "ganados-perdidos-empates"
      const resultados = p.equipo.resultadosEquipo || {};
      const ganados  = resultados.ganados  ?? 0;
      const perdidos = resultados.perdidos ?? 0;
      const empates  = resultados.empates  ?? 0;
      const recordStr = `${ganados}-${perdidos}-${empates}`;

      const recordDiv = document.createElement('div');
      recordDiv.classList.add('nm-team-record');
      recordDiv.textContent = recordStr;
      header.appendChild(recordDiv);

      mainDiv.appendChild(header);

      // nm-card-body
      const body = document.createElement('div');
      body.classList.add('nm-card-body');

      // Deportividad como íconos
      const deportDiv = document.createElement('div');
      deportDiv.classList.add('nm-sport-icons');
      deportDiv.textContent = 'Deportividad: ';
      for (let i = 1; i <= 5; i++) {
        const span = document.createElement('span');
        span.classList.add('nm-icon');
        span.textContent = '⚽';
        if (i > p.equipo.deportividad) {
          span.style.opacity = '0.4';
          span.style.color = 'grey';
        }
        deportDiv.appendChild(span);
      }
      body.appendChild(deportDiv);

      // <p class="nm-team-motto">Lema del equipo</p>
      const pMotto = document.createElement('p');
      pMotto.classList.add('nm-team-motto');
      pMotto.textContent = p.equipo.lema;
      body.appendChild(pMotto);

      // <a href="profile-link" class="profile-link">ver perfil del equipo</a>
      const aProfile = document.createElement('a');
      aProfile.href = `/equipo/${p.equipo.idEquipo}`;
      aProfile.classList.add('profile-link');
      aProfile.textContent = 'ver perfil del equipo';
      body.appendChild(aProfile);

      mainDiv.appendChild(body);

      // nm-card-actions: botones “Abrir wapp”, “Coordinar resultado”, “Cancelar”
      const actionsDiv = document.createElement('div');
      actionsDiv.classList.add('nm-card-actions');

      // Botón “Abrir wapp”
      const btnWapp = document.createElement('button');
      btnWapp.classList.add('nm-btn-secondary', 'nm-small');
      btnWapp.textContent = 'Abrir wapp';
      btnWapp.dataset.finalizado = p.finalizado ? 'true' : 'false';
      if (p.finalizado) {
        btnWapp.disabled = true;
        btnWapp.classList.add('disabled-link');
      }
      actionsDiv.appendChild(btnWapp);

      // Enlace “Coordinar resultado”
      const aCoordinar = document.createElement('a');
      aCoordinar.classList.add('nm-btn-primary', 'nm-small');
      aCoordinar.textContent = 'Coordinar resultado';
      aCoordinar.href = '/coordinar-resultado?id_partido=' + p.idPartido;
      aCoordinar.dataset.finalizado = p.finalizado ? 'true' : 'false';
      if (p.finalizado) {
        aCoordinar.removeAttribute('href');
        aCoordinar.classList.add('disabled-link');
      }
      actionsDiv.appendChild(aCoordinar);

      // Botón “Cancelar”
      const btnCancelar = document.createElement('button');
      btnCancelar.classList.add('nm-btn-danger', 'nm-small');
      btnCancelar.textContent = 'Cancelar';
      btnCancelar.dataset.finalizado = p.finalizado ? 'true' : 'false';
      if (p.finalizado) {
        btnCancelar.disabled = true;
        btnCancelar.classList.add('disabled-link');
      }
      actionsDiv.appendChild(btnCancelar);

      mainDiv.appendChild(actionsDiv);

      // Insertar todo en la tarjeta
      card.appendChild(mainDiv);
      li.appendChild(card);
      this.container.appendChild(li);
    });
  }
}