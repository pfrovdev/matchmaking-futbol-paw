<?php
namespace Paw\App\Models;

use Exception;
use Paw\Core\AbstractModel;
use Paw\Core\Exceptions\InvalidValueFormatException;

class Equipo extends AbstractModel
{
    public $table = "Equipo";
    public $fields = [
        "id_equipo"       => null,
        "email"           => null,
        "nombre"          => null,
        "contrasena"      => null,
        "telefono"        => null,
        "ubicacion"       => null,
        "lema"            => null,
        "acronimo"        => null,
        "elo_actual"      => null,
        "fecha_creacion"  => null,
        "url_foto_perfil" => null,
        "id_tipo_equipo"  => null,
        "id_nivel_elo"    => null,
        "id_rol"          => null,
    ];

    public function setIdEquipo(int $id)
    {
        if ($id <= 0) {
            throw new InvalidValueFormatException("ID de equipo no válido");
        }
        $this->fields["id_equipo"] = $id;
    }

    public function setEmail(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidValueFormatException("Formato de email no válido");
        }
        $this->fields["email"] = $email;
    }

    public function setNombre(string $nombre)
    {
        if (empty($nombre) || strlen($nombre) > 30) {
            throw new InvalidValueFormatException("Nombre debe tener entre 1 y 30 caracteres");
        }
        $this->fields["nombre"] = $nombre;
    }

    public function setContrasena(string $contrasena)
    {
        if (empty($contrasena)) {
            throw new InvalidValueFormatException("La contraseña es obligatoria");
        }
        $this->fields["contrasena"] = $contrasena;
    }

    public function setTelefono(string $telefono)
    {
        if (empty($telefono)) {
            throw new InvalidValueFormatException("El teléfono es obligatorio");
        }
        $this->fields["telefono"] = $telefono;
    }

    public function setUbicacion($ubicacion)
    {
        if (empty($ubicacion)) {
            throw new InvalidValueFormatException("La ubicación es obligatoria");
        }
        $this->fields["ubicacion"] = $ubicacion;
    }

    public function setLema(string $lema)
    {
        if (strlen($lema) > 200) {
            throw new InvalidValueFormatException("El lema no puede superar 200 caracteres");
        }
        $this->fields["lema"] = $lema;
    }

    public function setAcronimo(string $acronimo)
    {
        if (empty($acronimo) || strlen($acronimo) > 5) {
            throw new InvalidValueFormatException("El acrónimo debe tener hasta 5 caracteres");
        }
        $this->fields["acronimo"] = $acronimo;
    }

    public function setEloActual(int $elo)
    {
        if ($elo < 0) {
            throw new InvalidValueFormatException("Elo actual no válido");
        }
        $this->fields["elo_actual"] = $elo;
    }

    public function setFechaCreacion(string $fecha)
    {
        // Se espera formato YYYY-MM-DD HH:MM:SS
        if (!strtotime($fecha)) {
            throw new InvalidValueFormatException("Formato de fecha no válido");
        }
        $this->fields["fecha_creacion"] = $fecha;
    }

    public function setUrlFotoPerfil(?string $url)
    {
        if($url){
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                throw new InvalidValueFormatException("URL de foto de perfil no válida");
            }
        }
        $this->fields["url_foto_perfil"] = $url;
    }

    public function setIdTipoEquipo(int $id)
    {
        if ($id <= 0) {
            throw new InvalidValueFormatException("ID de tipo de equipo no válido");
        }
        $this->fields["id_tipo_equipo"] = $id;
    }

    public function setIdNivelElo(int $id)
    {
        if ($id <= 0) {
            throw new InvalidValueFormatException("ID de nivel elo no válido");
        }
        $this->fields["id_nivel_elo"] = $id;
    }

    public function setIdRol(int $id)
    {
        if ($id <= 0) {
            throw new InvalidValueFormatException("ID de rol no válido");
        }
        $this->fields["id_rol"] = $id;
    }

    public function set(array $values)
    {
        foreach (array_keys($this->fields) as $field) {
            if (!array_key_exists($field, $values)) {
                continue;
            }
            $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
            if (method_exists($this, $method)) {
                $this->$method($values[$field]);
            }
        }
    }

    public function __get($name)
    {
        return $this->fields[$name] ?? null;
    }

    public function select(array $params)
    {
        $qb = $this->getQueryBuilder();
        return $qb->select($this->table, $params);
    }

    public function saveNewTeam(array $params): ?string
    {
        $qb = $this->getQueryBuilder();
        return $qb->insert($this->table, $params);
    }

    public function selectLike(array $params): array
    {
        $qb = $this->getQueryBuilder();
        return $qb->selectLike($this->table, $params);
    }

    public function getTeams(array $selectParams, 
                                string $orderBy = 'id_nivel_elo', 
                                string $direction = 'DESC'): array {
        $qb = $this->getQueryBuilder();
        $nombre = $selectParams['nombre'] ?? null;
        $id_nivel_elo = $selectParams['id_nivel_elo'] ?? null;
        $miEquipo = $selectParams['miEquipo'] ?? null;

        $filtros = [];
    
        if ($nombre) $filtros['nombre'] = $nombre;
        if ($id_nivel_elo) $filtros['id_nivel_elo'] = $id_nivel_elo;
        if ($miEquipo && isset($miEquipo->id_equipo)) {
            $filtros['id_equipo !='] = $miEquipo->id_equipo;
        }

        if (!empty($filtros)) {
            $todosLosEquipos = $qb->selectLike(
                $this->table,
                $filtros,
                $orderBy,
                $direction
            );
        } else {
            $todosLosEquipos = $qb->select(
                $this->table,
                [],
                $orderBy,
                $direction
            );
        }
    
        return $this->setDeportividadEloDescripcion($todosLosEquipos);
    }
    

    public function setTeams(array $todosLosEquipos){
        $teams = [];
        foreach ($todosLosEquipos as $row) {
            
            $this->model->set($row);
            $teams[] = $this->model;
        }
        return $teams;
    }

    public function setDeportividadEloDescripcion(array $todosLosEquipos){
        $qb = $this->getQueryBuilder();
        $nivelElo = new NivelElo();
        $comentarioModel = new Comentario();
        $comentarios = $qb->select(
            $comentarioModel->table,
            [],
            null,
            null,
            null,
            null
        );
        $deportividadPorEquipo = [];

        foreach ($comentarios as $comentario) {
            $id = $comentario['id_equipo_comentado'];
            if (!isset($deportividadPorEquipo[$id])) {
                $deportividadPorEquipo[$id] = ['total' => 0, 'cantidad' => 0];
            }
            $deportividadPorEquipo[$id]['total'] += (float)$comentario['deportividad'];
            $deportividadPorEquipo[$id]['cantidad']++;
        }
        foreach ($todosLosEquipos as &$equipo) {
            $idEquipo = $equipo['id_equipo'];
            $nivelEloData = $qb->select(
                $nivelElo->table, 
                ['id_nivel_elo' => $equipo['id_nivel_elo']]
            );
            $equipo['nivel_elo_descripcion'] = $nivelEloData[0]['descripcion'] ?? 'Sin nivel';
            if (isset($deportividadPorEquipo[$idEquipo])) {
                $total = $deportividadPorEquipo[$idEquipo]['total'];
                $cantidad = $deportividadPorEquipo[$idEquipo]['cantidad'];
                $equipo['deportividad'] = round($total / $cantidad, 2);
            } else {
                $equipo['deportividad'] = null;
            }
        }
        return $todosLosEquipos;
    }
    
    public function getComentarios(int $page = 1, int $perPage = 5, ?string $orderBy = 'fecha_creacion', ?string $direction = 'DESC'): array
    {
        $comentarioModel = new Comentario();
        $qb = $this->getQueryBuilder();
    
        $offset = ($page - 1) * $perPage;
        $data = $qb->select(
            $comentarioModel->table,
            ['id_equipo_comentado' => $this->fields['id_equipo']],
            $orderBy,
            $direction,
            $perPage,
            $offset
        );
    
        $comentarios = [];
    
        foreach ($data as $row) {
            $comentario = new Comentario($qb);
            $comentario->set($row);
            $comentarios[] = $comentario;
        }
    
        return $comentarios;
    }
    
    public function promediarDeportividad(): float {
        $qb = $this->getQueryBuilder();
        $comentarioModel = new Comentario();
    
        $data = $qb->select(
            $comentarioModel->table, 
            ['id_equipo_comentado' => $this->fields['id_equipo']]
        );
    
        if (count($data) === 0) {
            return 0.0;
        }
    
        $total = 0;
        foreach ($data as $row) {
            $comentario = new Comentario();
            $comentario->set($row);
            $total += $comentario->fields['deportividad'];
        }
    
        return $total / count($data);
    }

    public function getDesafiosPendientes(int $page = 1, int $perPage = 5, string $orderBy = 'fecha_creacion', string $direction = 'DESC'): array {
        $desafioModel = new Desafio();
        $qb = $this->getQueryBuilder();
        $estadoDesafio = new EstadoDesafio($qb);
        $idPendiente = $estadoDesafio->select(["descripcion_corta" => "pendiente"])[0]['id_estado_desafio'];
        $offset = ($page - 1) * $perPage;
        $data = $qb->select(
            $desafioModel->table, 
            ['id_equipo_desafiado' => $this->fields['id_equipo'], 'id_estado_desafio' => $idPendiente],
            $orderBy, 
            $direction, 
            $perPage, 
            $offset
        );

        $result = [];
        foreach ($data as $row) {
            $desafio = new Desafio($qb);
            $desafio->set($row);
            $result[] = $desafio;
        }

        return $result;
    }

    public function getNivelElo(): string {
        $qb = $this->getQueryBuilder();
        $nivelElo = new NivelElo();
        
        $data = $qb->select(
            $nivelElo->table, 
            ['id_nivel_elo' => $this->fields['id_nivel_elo']]
        );
        
        if (empty($data)) {
            throw new Exception("Nivel Elo no encontrado para el equipo");
        }
        
        $nivelElo->set($data[0]);
        return (string)($nivelElo->fields['descripcion'] ?? "Sin descripción");
    }

    public function insertarDesafio($miEquipo, $equipoDesafiado){
        $qb = $this->getQueryBuilder();
        $model = new Desafio($qb);
        $params = [
            "id_equipo_desafiante" => $miEquipo,
            "id_equipo_desafiado" => $equipoDesafiado,
            "fecha_creacion" => null,
            "fecha_aceptacion" => null,
            "id_estado_desafio" => $this->getEstadoDesafioInicial(),
            "id_partido" => null,
        ];

        $insertedId = $model->saveNewTeam($params);
        return $insertedId;
    }

    public function getEquipo($id_equipo): Equipo{
        $qb = $this->getQueryBuilder();
        $equipo = new Equipo($qb);
        $newEquipo = $qb->select(
            $equipo->table,
            ['id_equipo' => $id_equipo]
        );
        $equipo->set($newEquipo[0]);
        return $equipo;
    }

    public function getEstadoDesafioInicial(){
        $qb = $this->getQueryBuilder();
        $modeloDesafio = new EstadoDesafio($qb);
        $estadoDesafio = $qb->select(
            $modeloDesafio->table,
            ['descripcion_corta' => 'pendiente']
        );
        return $estadoDesafio[0]['id_estado_desafio'];
    }

    public function getHistorialPartidos(int $page = 1, int $perPage = 5, string $orderBy = 'fecha_jugado', string $direction = 'DESC'): array{
        $qb = $this->getQueryBuilder();
        $model = new ResultadoPartido($qb);
        $equipoId = $this->fields['id_equipo'];

        $rowsPerdidos = $qb->select(
            $model->table,
            ['id_equipo_perdedor' => $equipoId],
            $orderBy,
            $direction
        );
        $rowsGanados  = $qb->select(
            $model->table,
            ['id_equipo_ganador'  => $equipoId],
            $orderBy,
            $direction
        );
        $allRows = array_merge($rowsPerdidos, $rowsGanados);
        usort($allRows, function (array $a, array $b) use ($orderBy, $direction) {
            $dtA = new \DateTime($a[$orderBy]);
            $dtB = new \DateTime($b[$orderBy]);
            if ($dtA == $dtB) {
                return 0;
            }
            $cmp = ($dtA < $dtB) ? -1 : 1;
            return $direction === 'DESC' ? -$cmp : $cmp;
        });

        $offset = ($page - 1) * $perPage;
        $slice  = array_slice($allRows, $offset, $perPage);

        $result = [];
        foreach ($slice as $row) {
            $rp = new ResultadoPartido($qb);
            $rp->set($row);
            $result[] = $rp;
        }
        return $result;
    }

    public function getTipoEquipo(): string {
        $qb = $this->getQueryBuilder();
        $tipoEquipoModel = new TipoEquipo();
        
        $data = $qb->select(
            $tipoEquipoModel->table, 
            ['id_tipo_equipo' => $this->fields['id_tipo_equipo']]
        );

        if (empty($data)) {
            throw new Exception("Nivel Elo no encontrado para el equipo");
        }
        
        $tipoEquipoModel->set($data[0]);

        return (string)($tipoEquipoModel->fields['tipo'] ?? "Sin descripción");
    }

    public function contieneHistorial(): bool{
        return count($this->getHistorialPartidos(1,1)) > 0;
    }

    public function aceptarDesafio(int $desafioId): Desafio{
        $qb = $this->getQueryBuilder();
        $desafioModel = new Desafio($qb);
        $data = $qb->select(
            $desafioModel->table,
            ['id_desafio' => $desafioId]
        );
        if (empty($data)) {
            throw new Exception("Desafio no encontrado");
        }
        $desafioModel->set($data[0]);
        $desafioModel->aceptar();
        return $desafioModel;
    }

    public function rechazarDesafio(int $desafioId): Desafio{
        $qb = $this->getQueryBuilder();
        $desafioModel = new Desafio($qb);
        $data = $qb->select(
            $desafioModel->table,
            ['id_desafio' => $desafioId]
        );
        if (empty($data)) {
            throw new Exception("Desafio no encontrado");
        }
        $desafioModel->set($data[0]);
        $desafioModel->rechazar();
        return $desafioModel;
    }

    public function __toString(): string
    {
        $output = [];
        foreach ($this->fields as $k => $v) {
            if ($v !== null) {
                $output[] = "$k: $v";
            }
        }
        return implode(", ", $output);
    }
    
}