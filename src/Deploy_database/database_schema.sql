USE match_making_db;

CREATE TABLE
    Roles (
        id_rol INT AUTO_INCREMENT PRIMARY KEY,
        rol VARCHAR(50) NOT NULL,
        descripcion VARCHAR(50) NOT NULL
    );

INSERT INTO
    Roles (rol, descripcion)
VALUES
    ('ADMIN', 'administrador'),
    ('USUARIO', 'usuario');

CREATE TABLE
    TipoEquipo (
        id_tipo_equipo INT AUTO_INCREMENT PRIMARY KEY,
        tipo VARCHAR(20),
        descripcion_corta VARCHAR(20)
    );

INSERT INTO
    TipoEquipo (tipo, descripcion_corta)
VALUES
    ('Masculino', 'masculino'),
    ('Femenino', 'femenino'),
    ('Mixto', 'mixto');

CREATE TABLE
    NivelElo (
        id_nivel_elo INT AUTO_INCREMENT PRIMARY KEY,
        descripcion VARCHAR(20),
        descripcion_corta VARCHAR(20)
    );

INSERT INTO
    NivelElo (descripcion, descripcion_corta)
VALUES
    ('Principiante', 'principiante'),
    ('Amateur', 'amateur'),
    ('Semi profesional', 'semi_profesional'),
    ('Profesional', 'profesional');

CREATE TABLE
    EstadoDesafio (
        id_estado_desafio INT AUTO_INCREMENT PRIMARY KEY,
        descripcion VARCHAR(20),
        descripcion_corta VARCHAR(20)
    );

INSERT INTO
    EstadoDesafio (descripcion, descripcion_corta)
VALUES
    ('Aceptado', 'aceptado'),
    ('Pendiente', 'pendiente'),
    ('Rechazado', 'rechazado');

CREATE TABLE
    EstadoPartido (
        id_estado_partido INT AUTO_INCREMENT PRIMARY KEY,
        descripcion VARCHAR(20),
        descripcion_corta VARCHAR(20)
    );

INSERT INTO
    EstadoPartido (descripcion, descripcion_corta)
VALUES
    ('Pendiente', 'pendiente'),
    ('Cancelado', 'cancelado'),
    ('Jugado', 'jugado'),
    ('No acordado', 'no_acordado');

CREATE TABLE
    Equipo (
        id_equipo INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(100) NOT NULL UNIQUE,
        nombre VARCHAR(30) NOT NULL,
        contrasena VARCHAR(100),
        telefono VARCHAR(20) NOT NULL,
        ubicacion POINT SRID 4326 NOT NULL,
        SPATIAL INDEX (ubicacion),
        lema VARCHAR(200),
        acronimo VARCHAR(5),
        elo_actual INT NOT NULL DEFAULT 800,
        fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
        url_foto_perfil VARCHAR(45),
        id_tipo_equipo INT,
        id_nivel_elo INT,
        id_rol INT,
        FOREIGN KEY (id_tipo_equipo) REFERENCES TipoEquipo (id_tipo_equipo),
        FOREIGN KEY (id_nivel_elo) REFERENCES NivelElo (id_nivel_elo),
        FOREIGN KEY (id_rol) REFERENCES Roles (id_rol)
    );

CREATE TABLE
    Estadisticas (
        id_estadistica INT AUTO_INCREMENT PRIMARY KEY,
        id_equipo INT,
        goles INT DEFAULT 0,
        asistencias INT DEFAULT 0,
        tarjetas_rojas INT DEFAULT 0,
        tarjetas_amarillas INT DEFAULT 0,
        jugados INT DEFAULT 0,
        empatados INT DEFAULT 0,
        perdidos INT DEFAULT 0,
        FOREIGN KEY (id_equipo) REFERENCES Equipo (id_equipo)
    );

CREATE TABLE
    Comentario (
        id_comentario INT AUTO_INCREMENT PRIMARY KEY,
        id_equipo_comentado INT,
        id_equipo_comentador INT,
        comentario TEXT,
        deportividad INT CHECK (
            deportividad >= 0
            AND deportividad <= 5
        ),
        fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_equipo_comentado) REFERENCES Equipo (id_equipo),
        FOREIGN KEY (id_equipo_comentador) REFERENCES Equipo (id_equipo)
    );

CREATE TABLE
    Partido (
        id_partido INT AUTO_INCREMENT PRIMARY KEY,
        fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
        fecha_finalizacion DATETIME,
        id_estado_partido INT NOT NULL,
        finalizado TINYINT (1) DEFAULT 0,
        FOREIGN KEY (id_estado_partido) REFERENCES EstadoPartido (id_estado_partido)
    );

CREATE TABLE
    Desafio (
        id_desafio INT AUTO_INCREMENT PRIMARY KEY,
        id_equipo_desafiante INT,
        id_equipo_desafiado INT,
        fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
        fecha_aceptacion DATETIME,
        id_estado_desafio INT,
        id_partido INT,
        FOREIGN KEY (id_equipo_desafiante) REFERENCES Equipo (id_equipo),
        FOREIGN KEY (id_equipo_desafiado) REFERENCES Equipo (id_equipo),
        FOREIGN KEY (id_estado_desafio) REFERENCES EstadoDesafio (id_estado_desafio),
        FOREIGN KEY (id_partido) REFERENCES Partido (id_partido)
    );

CREATE TABLE
    FormularioPartido (
        id_formulario INT AUTO_INCREMENT PRIMARY KEY,
        id_equipo INT,
        id_partido INT,
        fecha DATETIME,
        total_inscripciones INT DEFAULT 0,
        total_faltas INT DEFAULT 0,
        total_goles INT DEFAULT 0,
        total_amarillas INT DEFAULT 0,
        total_rojas INT DEFAULT 0,
        total_asistencias INT DEFAULT 0,
        total_iteraciones INT DEFAULT 0,
        tipo_formulario ENUM (
            'FORMULARIO_MI_EQUIPO',
            'FORMULARIO_EQUIPO_CONTRARIO'
        ),
        FOREIGN KEY (id_equipo) REFERENCES Equipo (id_equipo),
        FOREIGN KEY (id_partido) REFERENCES Partido (id_partido)
    );

CREATE TABLE
    ResultadoPartido (
        id_resultado INT AUTO_INCREMENT PRIMARY KEY,
        id_partido INT,
        id_equipo_ganador INT,
        id_equipo_perdedor INT,
        goles_equipo_ganador INT,
        goles_equipo_perdedor INT,
        elo_inicial_ganador INT,
        elo_final_ganador INT,
        elo_inicial_perdedor INT,
        elo_final_perdedor INT,
        total_amarillas_ganador INT,
        total_amarillas_perdedor INT,
        total_rojas_ganador INT,
        total_rojas_perdedor INT,
        fecha_jugado DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_partido) REFERENCES Partido (id_partido),
        FOREIGN KEY (id_equipo_ganador) REFERENCES Equipo (id_equipo),
        FOREIGN KEY (id_equipo_perdedor) REFERENCES Equipo (id_equipo)
    );