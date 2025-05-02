USE match_making_db;

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
    EstadoIteracion (
        id_estado_iteracion INT AUTO_INCREMENT PRIMARY KEY,
        estado VARCHAR(30),
        descripcion_corta VARCHAR(20)
    );

INSERT INTO
    EstadoIteracion (estado, descripcion_corta)
VALUES
    ('Esperadon rival', 'esperando_rival'),
    ('Iteracion fallida', 'iteracion_fallida'),
    ('Resultado generado', 'resultado_geneardo'),
    ('Penalizados', 'penalizados');

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
        email VARCHAR(255) NOT NULL UNIQUE,
        nombre VARCHAR(100) NOT NULL,
        descripcion TEXT,
        id_tipo_equipo INT,
        id_nivel_elo INT,
        elo_actual INT,
        fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_tipo_equipo) REFERENCES TipoEquipo (id_tipo_equipo),
        FOREIGN KEY (id_nivel_elo) REFERENCES NivelElo (id_nivel_elo)
    );

CREATE TABLE
    Comentario (
        id_comentario INT AUTO_INCREMENT PRIMARY KEY,
        equipo_comentado_id INT,
        equipo_comentador_id INT,
        comentario TEXT,
        deportividad INT constraint check (deportividad >= 0 and deportividad <= 5),
        fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (equipo_comentado_id) REFERENCES Equipo (id_equipo),
        FOREIGN KEY (equipo_comentador_id) REFERENCES Equipo (id_equipo)
    );

CREATE TABLE
    Partido (
        id_partido INT AUTO_INCREMENT PRIMARY KEY,
        fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
        finalizado TINYINT (1) DEFAULT 0,
        fecha_finalizacion DATETIME
    );

CREATE TABLE
    Desafio (
        id_desafio INT AUTO_INCREMENT PRIMARY KEY,
        equipo_desafiante_id INT,
        equipo_desafiado_id INT,
        fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
        fecha_aceptacion DATETIME,
        id_estado_desafio INT,
        id_partido INT,
        FOREIGN KEY (equipo_desafiante_id) REFERENCES Equipo (id_equipo),
        FOREIGN KEY (equipo_desafiado_id) REFERENCES Equipo (id_equipo),
        FOREIGN KEY (id_estado_desafio) REFERENCES EstadoDesafio (id_estado_desafio),
        FOREIGN KEY (id_partido) REFERENCES Partido (id_partido)
    );

CREATE TABLE
    FormularioPartido (
        id_formulario INT AUTO_INCREMENT PRIMARY KEY,
        equipo_id INT,
        partido_id INT,
        fecha DATETIME,
        id_estado_partido INT,
        total_inscripciones INT DEFAULT 0,
        total_faltas INT DEFAULT 0,
        total_goles INT DEFAULT 0,
        tipo_formulario ENUM (
            'FORMULARIO_MI_EQUIPO',
            'FORMULARIO_EQUIPO_CONTRARIO'
        ),
        FOREIGN KEY (equipo_id) REFERENCES Equipo (id_equipo),
        FOREIGN KEY (partido_id) REFERENCES Partido (id_partido),
        FOREIGN KEY (id_estado_partido) REFERENCES EstadoPartido (id_estado_partido)
    );

CREATE TABLE
    ResultadoPartido (
        id_resultado INT AUTO_INCREMENT PRIMARY KEY,
        partido_id INT,
        equipo_ganador_id INT,
        equipo_perdedor_id INT,
        goles_equipo_ganador INT,
        goles_equipo_perdedor INT,
        elo_inicial_ganador INT,
        elo_final_ganador INT,
        elo_inicial_perdedor INT,
        elo_final_perdedor INT,
        FOREIGN KEY (partido_id) REFERENCES Partido (id_partido),
        FOREIGN KEY (equipo_ganador_id) REFERENCES Equipo (id_equipo),
        FOREIGN KEY (equipo_perdedor_id) REFERENCES Equipo (id_equipo)
    );