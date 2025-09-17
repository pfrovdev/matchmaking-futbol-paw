<?php

namespace Paw\App\Commons;

use Exception;
use Paw\App\Models\Comentario;
use Paw\App\Models\Equipo;
use Paw\App\Models\Desafio;
use Paw\App\Models\FormularioPartido;
use PHPMailer\PHPMailer\PHPMailer;

class NotificadorEmail implements Notificador
{

    protected string $viewsDir = __DIR__ . '/../views/';
    private string $aceptarDesafioTemplate = 'mail-desafio-aceptado.html';
    private string $rechazarDesafioTemplate = 'mail-desafio-rechazado.html';
    private string $crearDesafioTemplate = 'mail-desafio-creado.html';
    private string $finalizarPartidoTemplate = 'mail-partido-finalizado.html';
    private string $cancelarPartidoTemplate = 'mail-partido-cancelado.html';

    public function enviarNotificacionDesafioAceptado(Equipo $equipoDesafiado, Equipo $equipoDesafiante, Desafio $desafioCreado): void
    {
        $this->enviarEmail(
            $equipoDesafiante->fields['email'] ?? throw new Exception("Email destinatario no puede ser nulo id_equipo: {$equipoDesafiante->fields['id_equipo']}"),
            "{$equipoDesafiado->fields['nombre']} aceptó tu desafío",
            $this->aceptarDesafioTemplate,
            [
                'teamName' => $equipoDesafiante->fields['nombre'],
                'teamRivalName' => $equipoDesafiado->fields['nombre'],
                'link' => 'https://wa.me/' . $equipoDesafiado->fields['telefono'],
            ]
        );
    }

    public function enviarNotificacionDesafioRechazado(Equipo $equipoDesafiado, Equipo $equipoDesafiante, Desafio $desafioRechazado): void
    {
        $this->enviarEmail(
            $equipoDesafiante->fields['email'] ?? throw new Exception("Email destinatario no puede ser nulo id_equipo: {$equipoDesafiante->fields['id_equipo']}"),
            "{$equipoDesafiado->fields['nombre']} rechazó tu desafío",
            $this->rechazarDesafioTemplate,
            [
                'teamName' => $equipoDesafiado->fields['nombre'],
                'link' => getenv('JWT_APP_URL') . '/search-team',
            ]
        );
    }


    public function enviarNotificacionPartidoCancelado(Equipo $equipoQueCancela, Equipo $equipoDebeSerNotificado): void
    {
        $this->enviarEmail(
            $equipoDebeSerNotificado->fields['email'] ?? throw new Exception("Email destinatario no puede ser nulo id_equipo: {$equipoDebeSerNotificado->fields['id_equipo']}"),
            "{$equipoQueCancela->fields['nombre']} canceló el partido",
            $this->cancelarPartidoTemplate,
            [
                'teamName' => $equipoDebeSerNotificado->fields['nombre'],
                'teamNameCencelador' => $equipoQueCancela->fields['nombre'],
                'link' => getenv('JWT_APP_URL') . '/search-team',
            ]
        );
    }


    public function enviarNotificacionDesafioCreado(Equipo $equipoDesafiado, Equipo $equipoDesafiante): void
    {
        $this->enviarEmail(
            $equipoDesafiado->fields['email'] ?? throw new Exception("Email destinatario no puede ser nulo id_equipo: {$equipoDesafiante->fields['id_equipo']}"),
            "{$equipoDesafiante->fields['nombre']} te acaba de desafiar",
            $this->crearDesafioTemplate,
            [
                'teamNameDesafiante' => $equipoDesafiante->fields['nombre'],
                'teamNameDesafiado' => $equipoDesafiado->fields['nombre'],
                'link' => getenv('JWT_APP_URL') . '/dashboard',
            ]
        );
    }

    public function enviarNotificacionPartidoFinalizado(
        Equipo $equipoLocal,
        Equipo $equipoVisitante,
        FormularioPartido $formularioLocal,
        FormularioPartido $formularioVisitante
    ): void {
        $vars = [
            'equipoLocal' => $equipoLocal->getNombre(),
            'equipoVisitante' => $equipoVisitante->getNombre(),
            'golesLocal' => $formularioLocal->getTotalGoles(),
            'asistenciasLocal' => $formularioLocal->getTotalAsistencias(),
            'amarillasLocal' => $formularioLocal->getTotalAmarillas(),
            'rojasLocal' => $formularioLocal->getTotalRojas(),
            'golesVisitante' => $formularioVisitante->getTotalGoles(),
            'asistenciasVisitante' => $formularioVisitante->getTotalAsistencias(),
            'amarillasVisitante' => $formularioVisitante->getTotalAmarillas(),
            'rojasVisitante' => $formularioVisitante->getTotalRojas(),
            'detalleLink' => getenv('JWT_APP_URL') . '/partidos/detalle?id_partido=' . $formularioLocal->getIdPartido(),
        ];
        $subject = sprintf(
            "Partido finalizado: %s %d - %d %s",
            $equipoLocal->getAcronimo(),
            $vars['golesLocal'],
            $vars['golesVisitante'],
            $equipoVisitante->getAcronimo()
        );
        $this->enviarEmail(
            $equipoLocal->getEmail() ?? throw new Exception(
                "Email del equipo local no puede ser nulo (id_equipo: {$equipoLocal->getIdEquipo()})"
            ),
            $subject,
            $this->finalizarPartidoTemplate,
            $vars
        );
        $this->enviarEmail(
            $equipoVisitante->getEmail() ?? throw new Exception(
                "Email del equipo visitante no puede ser nulo (id_equipo: {$equipoVisitante->getIdEquipo()})"
            ),
            $subject,
            $this->finalizarPartidoTemplate,
            $vars
        );
    }

    public function enviarNotificacionNuevaIteracion(Equipo $equipoLocal, Equipo $equipoVisitante, int $iteracion, int $idPartido)
    {
        $this->enviarEmail(
            $equipoVisitante->getEmail() ?? throw new Exception("Email no puede ser nulo"),
            "{$equipoLocal->getNombre()} subió iteración #{$iteracion}",
            'mail-nueva-iteracion.html',
            [
                'equipoLocal' => $equipoLocal->getNombre(),
                'equipoVisitante' => $equipoVisitante->getNombre(),
                'iteracion' => $iteracion,
                'link' => getenv('JWT_APP_URL') . "/partidos/detalle?id_partido={$idPartido}"
            ]
        );
    }

    public function enviarNotificacionPartidoNoAcordado(Equipo $equipoLocal, Equipo $equipoVisitante): void
    {
        $this->enviarEmail(
            $equipoLocal->getEmail() ?? throw new Exception("Email no puede ser nulo"),
            'Partido no acordado',
            'mail-partido-no-acordado.html',
            [
                'equipoLocal' => $equipoLocal->getNombre(),
                'equipoVisitante' => $equipoVisitante->getNombre(),
            ]
        );
        $this->enviarEmail(
            $equipoVisitante->getEmail() ?? throw new Exception("Email no puede ser nulo"),
            'Partido no acordado',
            'mail-partido-no-acordado.html',
            [
                'equipoLocal' => $equipoVisitante->getNombre(),
                'equipoVisitante' => $equipoLocal->getNombre(),
            ]
        );
    }

    public function enviarNotificacionComentarioEquipo(Equipo $equipo, Equipo $equipoComentador, Comentario $comentario): void
    {
        $deportividad = (int) $comentario->getDeportividad();

        $deportividadHTML = '<span style="color:green;">' . str_repeat('⚽', $deportividad) . '</span>';
        $deportividadHTML .= '<span style="color:lightgray;">' . str_repeat('⚽', 5 - $deportividad) . '</span>';

        $comentarioSanitizado = nl2br(htmlspecialchars($comentario->getComentario(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));

        $this->enviarEmail(
            $equipo->fields['email'] ?? throw new Exception("Email destinatario no puede ser nulo id_equipo: {$equipo->fields['id_equipo']}"),
            "Nuevo comentario de {$equipoComentador->fields['nombre']}",
            'mail-equipo-comentado.html',
            [
                'teamName' => $equipo->fields['nombre'],
                'commentingTeamName' => $equipoComentador->fields['nombre'],
                'deportividad' => $deportividadHTML,
                'comentario' => $comentarioSanitizado,
                'link' => getenv('JWT_APP_URL') . '/dashboard',
            ]
        );
    }

    private function enviarEmail(string $destinatario, string $asunto, string $templateFile, array $vars): void
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = getenv('MAIL_HOST');
            $mail->SMTPAuth = true;
            $mail->Username = getenv('MAIL_USERNAME');
            $mail->Password = getenv('MAIL_PASSWORD');
            $mail->SMTPSecure = getenv('MAIL_ENCRYPTION');
            $mail->Port = (int) getenv('MAIL_PORT');

            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';

            $mail->setFrom(getenv('MAIL_FROM_ADDRESS'), getenv('MAIL_FROM_NAME'));
            $mail->addAddress($destinatario);

            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body = $this->renderTemplate($templateFile, $vars);

            $mail->send();
        } catch (Exception $e) {
            throw new Exception("Error al enviar correo: {$mail->ErrorInfo} — Excepción: " . $e->getMessage());
        }
    }

    private function renderTemplate(string $file, array $vars): string
    {
        $html = file_get_contents($this->viewsDir . $file);
        foreach ($vars as $key => $value) {
            $html = str_replace("{{{$key}}}", htmlspecialchars($value), $html);
        }
        return $html;
    }
}
