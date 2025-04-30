USE match_making_db;

CREATE TABLE EstadoPartido (
    id_estado_partido INT AUTO_INCREMENT PRIMARY KEY,
    estado VARCHAR(20),
    descripcion_corta VARCHAR(20)
);

INSERT INTO EstadoPartido (estado, descripcion_corta) VALUES
('Pendiente', 'pendiente'),
('Cancelado', 'cancelado'),
('Jugado', 'jugado'),
('No acordado', 'no_acordado');

CREATE TABLE EstadoIteracion (
    id_estado_iteracion INT AUTO_INCREMENT PRIMARY KEY,
    estado VARCHAR(30),
    descripcion_corta VARCHAR(20)
);

INSERT INTO EstadoIteracion (estado, descripcion_corta) VALUES
('Esperadon rival', 'esperando_rival'),
('Iteracion fallida', 'iteracion_fallida'),
('Resultado generado', 'resultado_geneardo'),
('Penalizados', 'penalizados');

CREATE TABLE TipoEquipo (
    id_tipo_equipo INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(20),
    descripcion_corta VARCHAR(20)
);

INSERT INTO TipoEquipo (tipo, descripcion_corta) VALUES
('Masculino', 'masculino'), ('Femenino', 'femenino'), ('Mixto', 'mixto');

CREATE TABLE EstadoDesafio (
    id_estado_desafio INT AUTO_INCREMENT PRIMARY KEY,
    estado VARCHAR(20),
    descripcion_corta VARCHAR(20)
);

INSERT INTO EstadoDesafio (estado, descripcion_corta) VALUES
('Aceptado', 'aceptado'), ('Pendiente', 'pendiente'), ('Rechazado', 'rechazado');


CREATE TABLE Equipo (
    id_equipo INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nombre_equipo VARCHAR(255) NOT NULL,
    acronimo VARCHAR(10),
    descripcion_lema VARCHAR(255),
    telefono VARCHAR(20),
    geolocalizacion POINT,
    id_tipo_equipo INT,
    FOREIGN KEY (id_tipo_equipo) REFERENCES TipoEquipo(id_tipo_equipo)
);

CREATE TABLE Desafio (
    id_desafio INT AUTO_INCREMENT PRIMARY KEY,
    equipo_desafiante_id INT NOT NULL,
    equipo_desafiado_id INT NOT NULL,
    fecha_creacion DATE NOT NULL,
    fecha_aceptado DATE DEFAULT NULL,
    id_estado_desafio INT DEFAULT 1,
    elo_win INT DEFAULT 0,
    elo_lose INT DEFAULT 0,
    elo_draw INT DEFAULT 0,
    FOREIGN KEY (equipo_desafiante_id) REFERENCES Equipo(id_equipo),
    FOREIGN KEY (equipo_desafiado_id) REFERENCES Equipo(id_equipo),
    FOREIGN KEY (id_estado_desafio) REFERENCES EstadoDesafio(id_estado_desafio)
);

CREATE TABLE Partido (
    id_partido INT AUTO_INCREMENT PRIMARY KEY,
    fecha_creacion DATE NOT NULL,
    fecha_jugado DATE DEFAULT NULL,
    iteracion_maxima INT DEFAULT 5,
    ultima_iteracion INT DEFAULT 0,
    id_estado_partido INT NOT NULL,
    FOREIGN KEY (id_estado_partido) REFERENCES EstadoPartido(id_estado_partido)
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