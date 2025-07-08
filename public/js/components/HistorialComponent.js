export default class HistorialComponent {

  constructor(items, container, idEquipoPerfil) {
    this.items = items;
    this.container = container;
    this.idEquipoPerfil = idEquipoPerfil;
  }

  updateData(newItems) {
    this.items = newItems;
    this.render();
  }

  render() {
    this.container.innerHTML = '';

    if (this.items.length === 0) {
      const p = document.createElement('p');
      p.textContent = 'No hay partidos para mostrar.';
      p.style.textAlign = 'center';
      p.style.color = '#ccc';
      p.style.marginTop = '2rem';
      this.container.appendChild(p);
      return;
    }

    this.items.forEach(m => {
      const esEmpate = m.esEmpate;
      const idPerfil = this.idEquipoPerfil;

      const equipoGanadorId = m.resultadoGanador.equipo.id_equipo;
      const equipoPerdedorId = m.resultadoPerdedor.equipo.id_equipo;

      const idPerfilStr = String(idPerfil);
      const ganadorIdStr = String(equipoGanadorId);
      const perdedorIdStr = String(equipoPerdedorId);

      let claseCard = '';

      if (esEmpate) {
        claseCard = 'history-card-draw';
      } else if (idPerfilStr === ganadorIdStr) {
        claseCard = 'history-card-win';
      } else if (idPerfilStr === perdedorIdStr) {
        claseCard = 'history-card-lose';
      } else {
        claseCard = 'history-card-default';
      }
      if (m.soyObservador) {
        claseCard = 'history-card-observer';
      }

      let equipoPerfil, equipoRival;
      if (idPerfilStr === ganadorIdStr) {
        equipoPerfil = m.resultadoGanador;
        equipoRival = m.resultadoPerdedor;
      } else if (idPerfilStr === perdedorIdStr) {
        equipoPerfil = m.resultadoPerdedor;
        equipoRival = m.resultadoGanador;
      } else {
        equipoPerfil = m.resultadoGanador;
        equipoRival = m.resultadoPerdedor;
      }

      const cardDiv = document.createElement('div');
      cardDiv.classList.add('history-card-base', claseCard);

      // --- HEADER ---
      const headerDiv = document.createElement('div');
      headerDiv.classList.add('hc-header');

      const eloChangeSpan = document.createElement('span');
      eloChangeSpan.classList.add('elo-change');
      if (equipoPerfil.eloConseguido >= 0) {
        eloChangeSpan.classList.add('up');
      } else {
        eloChangeSpan.classList.add('down');
      }

      if (m.soyObservador) {
        eloChangeSpan.textContent = 'Observador';
      } else {
        const icon = document.createElement('i');
        icon.classList.add('fas', `fa-arrow-${equipoPerfil.eloConseguido >= 0 ? 'up' : 'down'}`);
        eloChangeSpan.appendChild(icon);
        eloChangeSpan.append(` ${equipoPerfil.eloConseguido >= 0 ? '+' : ''}${equipoPerfil.eloConseguido} ELO`);
      }
      headerDiv.appendChild(eloChangeSpan);

      if (m.soyObservador) {
        const btnLink = document.createElement('a');
        btnLink.href = `/match/${m.id_partido}`;
        btnLink.classList.add('hc-btn-link');
        const eyeIcon = document.createElement('i');
        eyeIcon.classList.add('fas', 'fa-eye');
        btnLink.appendChild(eyeIcon);
        btnLink.append(' Ver partido');
        headerDiv.appendChild(btnLink);
      }

      const matchDateSpan = document.createElement('span');
      matchDateSpan.classList.add('match-date');
      matchDateSpan.textContent = m.getFechaFormateada();
      headerDiv.appendChild(matchDateSpan);

      cardDiv.appendChild(headerDiv);

      const bodyDiv = document.createElement('div');
      bodyDiv.classList.add('hc-body');

      const homeTeamBlock = document.createElement('div');
      homeTeamBlock.classList.add('team-block', 'home');

      const homeTeamImgDiv = document.createElement('div');
      homeTeamImgDiv.classList.add('team-img');
      const homeTeamLink = document.createElement('a');
      homeTeamLink.href = `/dashboard?id=${equipoPerfil.equipo.id_equipo}`;
      homeTeamLink.title = equipoPerfil.equipo.nombre_equipo;
      const homeTeamImg = document.createElement('img');
      homeTeamImg.src = equipoPerfil.equipo.url_foto_perfil || '/icons/defaultTeamIcon.png';
      homeTeamImg.alt = equipoPerfil.equipo.nombre_equipo;
      homeTeamLink.appendChild(homeTeamImg);
      homeTeamImgDiv.appendChild(homeTeamLink);
      homeTeamBlock.appendChild(homeTeamImgDiv);

      const homeTeamInfoDiv = document.createElement('div');
      homeTeamInfoDiv.classList.add('team-info');

      const homeTeamAbbr = document.createElement('span');
      homeTeamAbbr.classList.add('team-abbr');
      homeTeamAbbr.textContent = equipoPerfil.equipo.acronimo;
      homeTeamInfoDiv.appendChild(homeTeamAbbr);

      const homeTarjetasDiv = document.createElement('div');
      homeTarjetasDiv.classList.add('tarjetas');
      if (equipoPerfil.tarjetas_amarillas) {
        const yellowCard = document.createElement('span');
        yellowCard.classList.add('tarjeta', 'yellow');
        yellowCard.textContent = equipoPerfil.tarjetas_amarillas;
        homeTarjetasDiv.appendChild(yellowCard);
      }
      if (equipoPerfil.tarjetas_rojas) {
        const redCard = document.createElement('span');
        redCard.classList.add('tarjeta', 'red');
        redCard.textContent = equipoPerfil.tarjetas_rojas;
        homeTarjetasDiv.appendChild(redCard);
      }
      homeTeamInfoDiv.appendChild(homeTarjetasDiv);
      homeTeamBlock.appendChild(homeTeamInfoDiv);
      bodyDiv.appendChild(homeTeamBlock);

      const scoreDiv = document.createElement('div');
      scoreDiv.classList.add('hc-score');
      scoreDiv.textContent = `${equipoPerfil.goles}-${equipoRival.goles}`;
      bodyDiv.appendChild(scoreDiv);

      const awayTeamBlock = document.createElement('div');
      awayTeamBlock.classList.add('team-block', 'away');

      const awayTeamImgDiv = document.createElement('div');
      awayTeamImgDiv.classList.add('team-img');
      const awayTeamLink = document.createElement('a');
      awayTeamLink.href = `/dashboard?id=${equipoRival.equipo.id_equipo}`;
      awayTeamLink.title = equipoRival.equipo.nombre_equipo;
      const awayTeamImg = document.createElement('img');
      awayTeamImg.src = equipoRival.equipo.url_foto_perfil || '/icons/defaultTeamIcon.png';
      awayTeamImg.alt = equipoRival.equipo.nombre_equipo;
      awayTeamLink.appendChild(awayTeamImg);
      awayTeamImgDiv.appendChild(awayTeamLink);
      awayTeamBlock.appendChild(awayTeamImgDiv);

      const awayTeamInfoDiv = document.createElement('div');
      awayTeamInfoDiv.classList.add('team-info');

      const awayTeamAbbr = document.createElement('span');
      awayTeamAbbr.classList.add('team-abbr');
      awayTeamAbbr.textContent = equipoRival.equipo.acronimo;
      awayTeamInfoDiv.appendChild(awayTeamAbbr);

      const awayTarjetasDiv = document.createElement('div');
      awayTarjetasDiv.classList.add('tarjetas');
      if (equipoRival.tarjetas_amarillas) {
        const yellowCard = document.createElement('span');
        yellowCard.classList.add('tarjeta', 'yellow');
        yellowCard.textContent = equipoRival.tarjetas_amarillas;
        awayTarjetasDiv.appendChild(yellowCard);
      }
      if (equipoRival.tarjetas_rojas) {
        const redCard = document.createElement('span');
        redCard.classList.add('tarjeta', 'red');
        redCard.textContent = equipoRival.tarjetas_rojas;
        awayTarjetasDiv.appendChild(redCard);
      }
      awayTeamInfoDiv.appendChild(awayTarjetasDiv);
      awayTeamBlock.appendChild(awayTeamInfoDiv);
      bodyDiv.appendChild(awayTeamBlock);

      cardDiv.appendChild(bodyDiv);

      this.container.appendChild(cardDiv);
    });
  }
}