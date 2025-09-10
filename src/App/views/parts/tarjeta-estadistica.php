<h3 class="seccion-titulo">Estadísticas del Equipo</h3>

<div class="resumen-destacado">
    <div class="dato-destacado">
        <span class="dato-destacado-valor" id="tasa-victoria-destacada">0%</span>
        <span class="dato-destacado-etiqueta">Tasa de Victoria</span>
    </div>
    <div class="dato-destacado">
        <span class="dato-destacado-valor" id="partidos-destacados">0</span>
        <span class="dato-destacado-etiqueta">Partidos Jugados</span>
    </div>
    <div class="dato-destacado">
        <span class="dato-destacado-valor" id="goles-destacados">0</span>
        <span class="dato-destacado-etiqueta">Goles a favor</span>
    </div>
    <div class="dato-destacado">
        <span class="dato-destacado-valor" id="elo-destacado">0</span>
        <span class="dato-destacado-etiqueta">ELO Actual</span>
    </div>
</div>

<div class="analisis-rendimiento">
    <div class="metricas-rendimiento">
        <div class="fila-metrica">
            <span class="etiqueta-metrica">Goles a Favor</span>
            <span class="valor-metrica" id="metrica-goles-favor">0</span>
        </div>
        <div class="fila-metrica">
            <span class="etiqueta-metrica">Goles en Contra</span>
            <span class="valor-metrica neutro" id="metrica-goles-contra">0</span>
        </div>
        <div class="fila-metrica">
            <span class="etiqueta-metrica">Diferencia de Gol</span>
            <span class="valor-metrica" id="metrica-diferencia-gol">0</span>
        </div>
        <div class="fila-metrica">
            <span class="etiqueta-metrica">Asistencias</span>
            <span class="valor-metrica" id="metrica-asistencias">0</span>
        </div>
        <div class="fila-metrica">
            <span class="etiqueta-metrica">Tarjetas Amarillas</span>
            <span class="valor-metrica" id="metrica-amarillas">0</span>
        </div>
        <div class="fila-metrica">
            <span class="etiqueta-metrica">Tarjetas Rojas</span>
            <span class="valor-metrica" id="metrica-rojas">0</span>
        </div>
        <div class="fila-metrica">
            <span class="etiqueta-metrica">Racha Actual</span>
            <span class="valor-metrica" id="metrica-racha-actual">0</span>
        </div>
        <div class="fila-metrica">
            <span class="etiqueta-metrica">Racha más Larga</span>
            <span class="valor-metrica" id="metrica-racha-maslarga">0</span>
        </div>
    </div>
    <div class="forma-reciente">
        <h4 class="titulo-forma">Últimos 5 Partidos</h4>
        <div id="resultados-recientes">
            <span class="sin-datos">Sin partidos</span>
        </div>
    </div>
</div>

<div class="pestanas-nav">
    <button class="pestana-boton activo" data-tab="resumen">Resumen</button>
    <button class="pestana-boton" data-tab="rendimiento">Rendimiento</button>
    <button class="pestana-boton" data-tab="promedios">Promedios</button>
</div>

<div class="pestanas-contenido">
    <div id="resumen" class="panel-pestana activo">
        <div class="grafico-contenedor">
            <canvas id="grafico-resumen"></canvas>
        </div>
        <div class="grafico-insights compacto">
            <div class="tarjeta-insight">
                <span class="insight-valor" id="insight-ganados">0</span>
                <span class="insight-etiqueta">Ganados</span>
            </div>
            <div class="tarjeta-insight">
                <span class="insight-valor" id="insight-empatados">0</span>
                <span class="insight-etiqueta">Empatados</span>
            </div>
            <div class="tarjeta-insight">
                <span class="insight-valor" id="insight-perdidos">0</span>
                <span class="insight-etiqueta">Perdidos</span>
            </div>
        </div>
    </div>

    <div id="rendimiento" class="panel-pestana">
        <div class="grafico-contenedor">
            <canvas id="grafico-rendimiento"></canvas>
        </div>
        <div class="grafico-insights compacto">
            <div class="tarjeta-insight">
                <span class="insight-valor" id="insight-elo-maximo">0</span>
                <span class="insight-etiqueta">ELO Máximo</span>
            </div>
            <div class="tarjeta-insight">
                <span class="insight-valor" id="insight-elo-actual">0</span>
                <span class="insight-etiqueta">ELO Actual</span>
            </div>
            <div class="tarjeta-insight">
                <span class="insight-valor" id="insight-elo-progreso">0</span>
                <span class="insight-etiqueta">Progreso</span>
            </div>
        </div>
    </div>

    <div id="promedios" class="panel-pestana">
        <div class="grafico-contenedor">
            <canvas id="grafico-promedios"></canvas>
        </div>
        <div class="grafico-insights compacto">
            <div class="tarjeta-insight">
                <span class="insight-valor" id="promedio-goles-valor">0</span>
                <span class="insight-etiqueta">Promedio Goles</span>
            </div>
            <div class="tarjeta-insight">
                <span class="insight-valor" id="promedio-asistencias-valor">0</span>
                <span class="insight-etiqueta">Promedio Asist.</span>
            </div>
            <div class="tarjeta-insight">
                <span class="insight-valor" id="promedio-amarillas-valor">0</span>
                <span class="insight-etiqueta">Promedio Amar.</span>
            </div>
            <div class="tarjeta-insight">
                <span class="insight-valor" id="promedio-golescontra-valor">0</span>
                <span class="insight-etiqueta">Promedio Goles En Contra</span>
            </div>
            <div class="tarjeta-insight">
                <span class="insight-valor" id="promedio-rojas-valor">0</span>
                <span class="insight-etiqueta">Promedio Rojas</span>
            </div>
        </div>
    </div>
</div>