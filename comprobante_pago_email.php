<?php

class ComprobantePagoEmailException extends RuntimeException {}

/** Envío solicitado por el colaborador; nunca cambia de empresa como fallback. */
class ComprobantePagoEmail
{
    public static function remitente($numeroEvento)
    {
        $correos = array(
            'EPC' => 'epconvenciones@epconvenciones.com.mx',
            'INN' => 'innovacc@innovacc.com.mx',
            'EVE' => 'eventos520@eventos520.com.mx',
        );
        $prefijo = strtoupper(substr(trim((string)$numeroEvento), 0, 3));
        return isset($correos[$prefijo]) ? $correos[$prefijo] : null;
    }

    public static function token()
    {
        if (empty($_SESSION['csrf_comprobante_pago'])) {
            if (function_exists('random_bytes')) {
                $bytes = random_bytes(32);
            } else {
                // PHP 5.6: OpenSSL debe confirmar que el generador es seguro.
                $seguro = false;
                $bytes = function_exists('openssl_random_pseudo_bytes')
                    ? openssl_random_pseudo_bytes(32, $seguro) : false;
                if ($bytes === false || !$seguro || strlen($bytes) !== 32) {
                    throw new ComprobantePagoEmailException('No se pudo generar el token seguro del formulario. Revisa OpenSSL en PHP.');
                }
            }
            $_SESSION['csrf_comprobante_pago'] = bin2hex($bytes);
        }
        return $_SESSION['csrf_comprobante_pago'];
    }

    /** Devuelve destinatarios únicos; rechaza toda la lista si uno es inválido. */
    public static function destinatarios($email)
    {
        if (!is_string($email) || trim($email) === '') {
            return array();
        }
        $destinatarios = array();
        foreach (explode(',', $email) as $direccion) {
            $direccion = trim($direccion);
            if (!filter_var($direccion, FILTER_VALIDATE_EMAIL)) {
                throw new ComprobantePagoEmailException('Escribe correos válidos separados por comas para el envío del comprobante de pago.');
            }
            $clave = strtolower($direccion);
            if (!isset($destinatarios[$clave])) {
                $destinatarios[$clave] = $direccion;
            }
        }
        return array_values($destinatarios);
    }

    public static function validarEmail($email)
    {
        if ($email === null) {
            return true;
        }
        if (!is_string($email)) {
            return false;
        }
        try {
            self::destinatarios($email);
            return true;
        } catch (ComprobantePagoEmailException $error) {
            return false;
        }
    }

    public static function enviar($registro, $documentos, $smtp, $directorio, $mail)
    {
        $destinatarios = self::destinatarios(isset($registro['email_enviacomprobante']) ? $registro['email_enviacomprobante'] : '');
        if (!$destinatarios) {
            throw new ComprobantePagoEmailException('El registro no tiene correos válidos para el comprobante.');
        }
        $evento = trim((string)$registro['NUMERO_EVENTO']);
        $remitente = self::remitente($evento);
        if ($remitente === null) {
            throw new ComprobantePagoEmailException('El evento no pertenece a EPC, INN o EVE.');
        }
        if (empty($smtp['Host']) || empty($smtp['Username']) || empty($smtp['Passwordd']) ||
            empty($smtp['Port']) || empty($smtp['SMTPSecure']) ||
            strcasecmp(trim(isset($smtp['setFrom1']) ? $smtp['setFrom1'] : ''), $remitente) !== 0) {
            throw new ComprobantePagoEmailException('Falta la configuración SMTP de '.$remitente.'.');
        }
        $base = realpath($directorio);
        $adjuntos = array();
        foreach ($documentos as $documento) {
            $nombre = trim((string)$documento['CONPROBANTE_TRANSFERENCIA']);
            if ($nombre === '') {
                continue;
            }
            $ruta = $base === false ? false : realpath($base.DIRECTORY_SEPARATOR.$nombre);
            if ($ruta === false || strpos($ruta, $base.DIRECTORY_SEPARATOR) !== 0 ||
                !is_file($ruta) || !is_readable($ruta)) {
                throw new ComprobantePagoEmailException('No se puede leer uno de los comprobantes guardados.');
            }
            $adjuntos[$ruta] = basename($ruta);
        }
        if (!$adjuntos) {
            throw new ComprobantePagoEmailException('Carga un comprobante de transferencia antes de enviarlo.');
        }
        $mail->clearAllRecipients();
        $mail->clearAttachments();
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->Timeout = 20;
        $mail->SMTPDebug = 0;
        $mail->Host = $smtp['Host'];
        $mail->Username = $smtp['Username'];
        $mail->Password = $smtp['Passwordd'];
        $mail->SMTPSecure = $smtp['SMTPSecure'];
        $mail->Port = $smtp['Port'];
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($remitente, isset($smtp['setFrom2']) ? $smtp['setFrom2'] : $remitente);
        foreach ($destinatarios as $email) {
            if (!$mail->addAddress($email)) {
                throw new ComprobantePagoEmailException('No se pudo agregar uno de los destinatarios del comprobante.');
            }
        }
        foreach ($adjuntos as $ruta => $nombre) {
            if (!$mail->addAttachment($ruta, $nombre)) {
                throw new ComprobantePagoEmailException('No se pudo adjuntar el comprobante de transferencia.');
            }
        }
        $mail->isHTML(false);
        $mail->Subject = 'Comprobante de pago - Evento '.str_replace(array("\r", "\n"), '', $evento);
        $mail->Body = 'Adjuntamos el comprobante de transferencia correspondiente al evento '.$evento.'.';
        if (!$mail->send()) {
            throw new ComprobantePagoEmailException('El servidor SMTP no aceptó el envío del comprobante.');
        }
    }

    public static function enviarGuardado($conn, $id, $directorio, $emailFormulario)
    {
        try {
            $id = intval($id);
            $consulta = mysqli_query($conn, "SELECT NUMERO_EVENTO, email_enviacomprobante FROM 02SUBETUFACTURA WHERE id = $id LIMIT 1");
            $registro = $consulta ? mysqli_fetch_assoc($consulta) : null;
            if (!$registro) {
                throw new ComprobantePagoEmailException('No se pudo consultar el registro guardado.');
            }
            if (!is_string($emailFormulario) || trim($emailFormulario) !== trim((string)$registro['email_enviacomprobante'])) {
                throw new ComprobantePagoEmailException('Guarda el email antes de enviar el comprobante.');
            }
            $remitente = self::remitente($registro['NUMERO_EVENTO']);
            if ($remitente === null) {
                throw new ComprobantePagoEmailException('El evento no pertenece a EPC, INN o EVE.');
            }
            $correoSQL = mysqli_real_escape_string($conn, $remitente);
            $consultaSMTP = mysqli_query($conn, "SELECT Host, Username, Passwordd, SMTPSecure, Port, setFrom1, setFrom2 FROM 03datossmtp WHERE LOWER(TRIM(setFrom1)) = '$correoSQL' LIMIT 1");
            $smtp = $consultaSMTP ? mysqli_fetch_assoc($consultaSMTP) : array();
            $consultaDocs = mysqli_query($conn, "SELECT CONPROBANTE_TRANSFERENCIA FROM 02SUBETUFACTURADOCTOS WHERE idTemporal = '$id' AND CONPROBANTE_TRANSFERENCIA IS NOT NULL AND CONPROBANTE_TRANSFERENCIA <> '' ORDER BY id");
            if (!$consultaDocs) {
                throw new ComprobantePagoEmailException('No se pudieron consultar los comprobantes guardados.');
            }
            $documentos = array();
            while ($doc = mysqli_fetch_assoc($consultaDocs)) {
                $documentos[] = $doc;
            }
            self::enviar($registro, $documentos, $smtp ?: array(), $directorio, new herramientas());
            return array('ok' => true, 'mensaje' => 'Comprobante enviado a '.trim($registro['email_enviacomprobante']).'.');
        } catch (Exception $error) {
            // PHP 5.6 no tiene Throwable: sus excepciones se capturan aquí.
            $mensaje = $error instanceof ComprobantePagoEmailException ? $error->getMessage() : 'No se pudo enviar el correo. Revisa la configuración SMTP de la empresa.';
            return array('ok' => false, 'mensaje' => $mensaje);
        } catch (Throwable $error) {
            // No mostrar respuestas SMTP, contraseñas ni detalles internos de la conexión.
            $mensaje = $error instanceof ComprobantePagoEmailException ? $error->getMessage() : 'No se pudo enviar el correo. Revisa la configuración SMTP de la empresa.';
            return array('ok' => false, 'mensaje' => $mensaje);
        }
    }
}
