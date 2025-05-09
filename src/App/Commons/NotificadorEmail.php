<?php

namespace Paw\App\Commons;

use Exception;
use Paw\App\Models\Equipo;
use Paw\App\Models\Desafio;
use PHPMailer\PHPMailer\PHPMailer;

class NotificadorEmail implements Notificador {

    protected string $viewsDir = __DIR__ . '/../views/';
    private string $aceptarDesafioTemplate = 'mail-desafio-aceptado.html';
    private string $rechazarDesafioTemplate = 'mail-desafio-rechazado.html';

    public function enviarNotificacionDesafioAceptado(Equipo $equipoDesafiado, Equipo $equipoDesafiante, Desafio $desafioCreado): void {
        $this->enviarEmail(
            $equipoDesafiante->fields['email']?? throw new Exception("Email destinatario no puede ser nulo id_equipo: {$equipoDesafiante->fields['id_equipo']}"),
            "{$equipoDesafiado->fields['nombre']} aceptó tu desafío",
            $this->aceptarDesafioTemplate,
            [
                'teamName' => $equipoDesafiado->fields['nombre'],
                'link' => 'https://wa.me/' . $equipoDesafiante->fields['telefono'],
            ]
        );
    }

    public function enviarNotificacionDesafioRechazado(Equipo $equipoDesafiado, Equipo $equipoDesafiante, Desafio $desafioRechazado): void {
        $this->enviarEmail(
            $equipoDesafiante->fields['email']?? throw new Exception("Email destinatario no puede ser nulo id_equipo: {$equipoDesafiante->fields['id_equipo']}"),
            "{$equipoDesafiado->fields['nombre']} rechazó tu desafío",
            $this->rechazarDesafioTemplate,
            [
                'teamName' => $equipoDesafiado->fields['nombre'],
                'link' => getenv('JWT_APP_URL') . '/search-team',
            ]
        );
    }

    private function enviarEmail(string $destinatario, string $asunto, string $templateFile, array $vars): void {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = getenv('MAIL_HOST');
            $mail->SMTPAuth = true;
            $mail->Username = getenv('MAIL_USERNAME');
            $mail->Password = getenv('MAIL_PASSWORD');
            $mail->SMTPSecure = getenv('MAIL_ENCRYPTION');
            $mail->Port = (int) getenv('MAIL_PORT');

            $mail->setFrom(getenv('MAIL_FROM_ADDRESS'), getenv('MAIL_FROM_NAME'));
            $mail->addAddress($destinatario);

            $mail->isHTML(true);
            $mail->Subject = htmlspecialchars($asunto);
            $mail->Body = $this->renderTemplate($templateFile, $vars);

            $mail->send();
        } catch (Exception $e) {
            throw new Exception("Error al enviar correo: {$mail->ErrorInfo} — Excepción: " . $e->getMessage());
        }
    }

    private function renderTemplate(string $file, array $vars) : string {
        $html = file_get_contents($this->viewsDir . $file);
        foreach ($vars as $key => $value) {
            $html = str_replace("{{{$key}}}", htmlspecialchars($value), $html);
        }
        return $html;
    }

}


?>