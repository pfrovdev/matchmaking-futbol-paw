USE match_making_db;

CREATE TABLE EstadoPartido (
    estado VARCHAR(20) PRIMARY KEY
);

INSERT INTO EstadoPartido (estado) VALUES
('PENDIENTE'),
('CANCELADO'),
('JUGADO'),
('NO_ACORDADO');

CREATE TABLE EstadoIteracion (
    estado VARCHAR(30) PRIMARY KEY
);

INSERT INTO EstadoIteracion (estado) VALUES
('ESPERANDO_RIVAL'),
('ITERACION_FALLIDA'),
('RESULTADO_GENERADO'),
('PENALIZADOS');

CREATE TABLE TipoEquipo (
    tipo VARCHAR(20) PRIMARY KEY
);

INSERT INTO TipoEquipo (tipo) VALUES
('MASCULINO'), ('FEMENINO'), ('MIXTO');

CREATE TABLE EstadoDesafio (
    estado VARCHAR(20) PRIMARY KEY
);

INSERT INTO EstadoDesafio (estado) VALUES
('ACEPTADO'), ('PENDIENTE'), ('RECHAZADO');


CREATE TABLE Equipo (
    id_equipo INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nombre_equipo VARCHAR(255) NOT NULL,
    acronimo VARCHAR(10),
    descripcion_lema VARCHAR(255),
    telefono VARCHAR(20),
    geolocalizacion POINT,
    tipo_equipo VARCHAR(20),
    FOREIGN KEY (tipo_equipo) REFERENCES TipoEquipo(tipo)
);

CREATE TABLE Desafio (
    id_desafio INT AUTO_INCREMENT PRIMARY KEY,
    equipo_desafiante_id INT NOT NULL,
    equipo_desafiado_id INT NOT NULL,
    fecha_creacion DATE NOT NULL,
    fecha_aceptado DATE DEFAULT NULL,
    estado_desafio VARCHAR(20) DEFAULT 'PENDIENTE',
    elo_win INT DEFAULT 0,
    elo_lose INT DEFAULT 0,
    elo_draw INT DEFAULT 0,
    FOREIGN KEY (equipo_desafiante_id) REFERENCES Equipo(id_equipo),
    FOREIGN KEY (equipo_desafiado_id) REFERENCES Equipo(id_equipo),
    FOREIGN KEY (estado_desafio) REFERENCES EstadoDesafio(estado)
);

CREATE TABLE Partido (
    id_partido INT AUTO_INCREMENT PRIMARY KEY,
    fecha_creacion DATE NOT NULL,
    fecha_jugado DATE DEFAULT NULL,
    iteracion_maxima INT DEFAULT 5,
    ultima_iteracion INT DEFAULT 0,
    estado_partido VARCHAR(20),
    FOREIGN KEY (estado_partido) REFERENCES EstadoPartido(estado)
);

CREATE TABLE FormularioPartido (
    id_formulario INT AUTO_INCREMENT PRIMARY KEY,
    partido_id INT NOT NULL,
    iteracion INT NOT NULL,
    fecha DATE NOT NULL,
    total_asistencias INT DEFAULT 0,
    total_tarjetas_amarillas INT DEFAULT 0,
    total_tarjetas_rojas INT DEFAULT 0,
    total_goles INT DEFAULT 0,
    equipo_propietario ENUM('MI_EQUIPO', 'EQUIPO_CONTRARIO') NOT NULL,
    FOREIGN KEY (partido_id) REFERENCES Partido(id_partido)
);

CREATE TABLE ResultadoPartido (
    id_resultado INT AUTO_INCREMENT PRIMARY KEY,
    partido_id INT NOT NULL,
    fecha_de_acuerdo DATE NOT NULL,
    formulario_mi_equipo_id INT NOT NULL,
    formulario_equipo_contrario_id INT NOT NULL,
    FOREIGN KEY (partido_id) REFERENCES Partido(id_partido),
    FOREIGN KEY (formulario_mi_equipo_id) REFERENCES FormularioPartido(id_formulario),
    FOREIGN KEY (formulario_equipo_contrario_id) REFERENCES FormularioPartido(id_formulario)
);

CREATE TABLE HistorialPartidos (
    equipo_id INT NOT NULL,
    resultado_partido_id INT NOT NULL,
    PRIMARY KEY (equipo_id, resultado_partido_id),
    FOREIGN KEY (equipo_id) REFERENCES Equipo(id_equipo),
    FOREIGN KEY (resultado_partido_id) REFERENCES ResultadoPartido(id_resultado)
);

CREATE TABLE ELO (
    id_elo INT AUTO_INCREMENT PRIMARY KEY,
    equipo_id INT NOT NULL,
    valor INT DEFAULT 800,
    FOREIGN KEY (equipo_id) REFERENCES Equipo(id_equipo)
);

CREATE TABLE Comentario (
    id_comentario INT AUTO_INCREMENT PRIMARY KEY,
    equipo_id INT NOT NULL,
    deportividad INT NOT NULL,
    comentario TEXT,
    FOREIGN KEY (equipo_id) REFERENCES Equipo(id_equipo)
);