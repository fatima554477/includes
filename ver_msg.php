<?php
/** Lee mensajes RFC 5322/MIME sin ejecutar el HTML ni guardar sus adjuntos. */
class EmlReader
{
    public static function read($raw)
    {
        $result = array('subject' => '', 'sender' => '', 'recipients' => array(),
            'text' => '', 'html' => '', 'attachments' => array());
        list($headers) = self::split($raw);
        $result['subject'] = self::decodeHeader(isset($headers['subject']) ? $headers['subject'] : '');
        $result['sender'] = self::decodeHeader(isset($headers['from']) ? $headers['from'] : '');
        foreach (array('to', 'cc') as $key) {
            if (!empty($headers[$key])) {
                $result['recipients'][] = self::decodeHeader($headers[$key]);
            }
        }
        self::part($raw, $result, 0);
        return $result;
    }

    private static function split($raw)
    {
        $parts = preg_split('/\r?\n\r?\n/', $raw, 2);
        $headers = array();
        $head = preg_replace('/\r?\n[ \t]+/', ' ', $parts[0]);
        foreach (preg_split('/\r?\n/', $head) as $line) {
            if (strpos($line, ':') === false) { continue; }
            list($name, $value) = explode(':', $line, 2);
            $name = strtolower(trim($name));
            $headers[$name] = isset($headers[$name]) ? $headers[$name].', '.trim($value) : trim($value);
        }
        return array($headers, isset($parts[1]) ? $parts[1] : '');
    }

    private static function decodeHeader($value)
    {
        return mb_decode_mimeheader($value);
    }

    private static function parameter($header, $name)
    {
        // RFC 2231: parámetros extendidos y continuaciones (filename*0*, etc.).
        $params = array();
        preg_match_all('/;\s*([a-z0-9_*\-]+)\s*=\s*("(?:[^"\\\\]|\\\\.)*"|[^;\s]*)/i', $header, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $value = $match[2];
            if (substr($value, 0, 1) === '"') {
                $value = preg_replace('/\\\\(.)/s', '$1', substr($value, 1, -1));
            }
            $params[strtolower($match[1])] = $value;
        }
        $value = '';
        $encoded = false;
        if (isset($params[$name.'*'])) {
            $value = $params[$name.'*'];
            $encoded = true;
        } elseif (isset($params[$name.'*0']) || isset($params[$name.'*0*'])) {
            for ($i = 0; ; $i++) {
                if (isset($params[$name.'*'.$i.'*'])) {
                    $value .= rawurldecode($params[$name.'*'.$i.'*']);
                    $encoded = true;
                } elseif (isset($params[$name.'*'.$i])) {
                    $value .= $params[$name.'*'.$i];
                } else { break; }
            }
            if ($encoded && preg_match("/^([^']*)'[^']*'(.*)$/s", $value, $m)) {
                return self::utf8($m[2], $m[1]);
            }
            return self::decodeHeader($value);
        } elseif (isset($params[$name])) {
            return self::decodeHeader($params[$name]);
        }
        if ($encoded && preg_match("/^([^']*)'[^']*'(.*)$/s", $value, $m)) {
            return self::utf8(rawurldecode($m[2]), $m[1]);
        }
        return $encoded ? rawurldecode($value) : '';
    }

    private static function utf8($value, $charset)
    {
        if ($charset === '') { $charset = 'UTF-8'; }
        // Valida el charset antes de convertir, también en PHP 5.6.
        $valid = false;
        foreach (mb_list_encodings() as $encoding) {
            if (in_array(strtolower($encoding), array('base64', 'uuencode', 'html-entities', 'quoted-printable'), true)) { continue; }
            if (strcasecmp($charset, $encoding) === 0) { $valid = true; break; }
            foreach (mb_encoding_aliases($encoding) as $alias) {
                if (strcasecmp($charset, $alias) === 0) { $valid = true; break 2; }
            }
        }
        return mb_convert_encoding($value, 'UTF-8', $valid ? $charset : 'UTF-8');
    }

    private static function part($raw, &$result, $depth)
    {
        if ($depth > 30) { throw new RuntimeException('El mensaje tiene demasiados niveles MIME.'); }
        list($headers, $body) = self::split($raw);
        $typeHeader = isset($headers['content-type']) ? $headers['content-type'] : 'text/plain';
        $type = strtolower(trim(explode(';', $typeHeader, 2)[0]));
        $disposition = isset($headers['content-disposition']) ? $headers['content-disposition'] : '';
        $filename = self::parameter($disposition, 'filename');
        if ($filename === '') { $filename = self::parameter($typeHeader, 'name'); }
        if ($filename !== '' || strtolower(trim(explode(';', $disposition, 2)[0])) === 'attachment') {
            $result['attachments'][] = $filename !== '' ? $filename : '(sin nombre)';
            return;
        }
        if (strpos($type, 'multipart/') === 0) {
            $boundary = self::parameter($typeHeader, 'boundary');
            if ($boundary === '') { throw new RuntimeException('Falta el separador MIME.'); }
            $lines = preg_split('/\r?\n/', $body);
            $buffer = null;
            foreach ($lines as $line) {
                $marker = rtrim($line, " \t");
                if ($marker === '--'.$boundary || $marker === '--'.$boundary.'--') {
                    if ($buffer !== null) { self::part(implode("\r\n", $buffer), $result, $depth + 1); }
                    if ($marker === '--'.$boundary.'--') { return; }
                    $buffer = array();
                } elseif ($buffer !== null) { $buffer[] = $line; }
            }
            if ($buffer !== null) { self::part(implode("\r\n", $buffer), $result, $depth + 1); }
            return;
        }
        $encoding = isset($headers['content-transfer-encoding']) ? strtolower(trim($headers['content-transfer-encoding'])) : '';
        if ($encoding === 'base64') {
            $body = base64_decode(preg_replace('/\s+/', '', $body), true);
            if ($body === false) { throw new RuntimeException('Contenido base64 no válido.'); }
        } elseif ($encoding === 'quoted-printable') { $body = quoted_printable_decode($body); }
        if ($type === 'text/plain' || $type === 'text/html') {
            $key = $type === 'text/html' ? 'html' : 'text';
            $body = self::utf8($body, self::parameter($typeHeader, 'charset'));
            $result[$key] .= ($result[$key] !== '' ? "\n" : '').$body;
        }
    }
}

class MsgReader
{
	private $data;
	private $sectorSize;
	private $miniSectorSize;
	private $miniCutoff;
	private $fat = array();
	private $miniFat = array();
	private $miniStream = '';

	public function __construct($path)
	{
		$this->data = @file_get_contents($path);
		if ($this->data === false || substr($this->data, 0, 8) !== "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1") {
			throw new RuntimeException('El archivo no tiene un formato MSG válido.');
		}
		if (strlen($this->data) < 512 || !in_array($this->u16(30), array(9, 12), true) || $this->u16(32) !== 6) { throw new RuntimeException('Cabecera MSG no válida.'); }
		$this->sectorSize = 1 << $this->u16(30);
		$this->miniSectorSize = 1 << $this->u16(32);
		$this->miniCutoff = $this->u32(56);
		$this->loadFat();
	}

	public function read()
	{
		$directory = $this->readChain($this->u32(48), $this->fat, $this->sectorSize, false);
		$entries = array();
		for ($offset = 0; $offset + 128 <= strlen($directory); $offset += 128) {
			$entry = substr($directory, $offset, 128);
			$nameLength = $this->value16($entry, 64);
			$type = ord($entry[66]);
			if ($nameLength < 2 || ($type !== 2 && $type !== 5)) { continue; }
			$name = $this->utf16(substr($entry, 0, $nameLength - 2));
			$entries[] = array('name' => $name, 'type' => $type,
				'start' => $this->value32($entry, 116), 'size' => $this->value32($entry, 120));
		}

		foreach ($entries as $entry) {
			if ($entry['type'] === 5) {
				$this->miniStream = $this->readChain($entry['start'], $this->fat, $this->sectorSize, false, $entry['size']);
				break;
			}
		}
		$miniStart = $this->u32(60);
		if (!$this->isEnd($miniStart)) {
			$rawMiniFat = $this->readChain($miniStart, $this->fat, $this->sectorSize, false);
			$this->miniFat = array_values(unpack('V*', $rawMiniFat));
		}

		$result = array('subject' => '', 'sender_name' => '', 'sender_email' => '',
			'to' => '', 'body' => '', 'html' => '', 'attachments' => array());
		foreach ($entries as $entry) {
			if ($entry['type'] !== 2 || strpos($entry['name'], '__substg1.0_') !== 0) { continue; }
			$value = $this->stream($entry);
			$tag = strtoupper(substr($entry['name'], 12));
			$text = $this->propertyText($value, substr($tag, -4));
			if (strpos($tag, '0037') === 0) { $result['subject'] = $text; }
			elseif (strpos($tag, '0C1A') === 0) { $result['sender_name'] = $text; }
			elseif (strpos($tag, '5D01') === 0 || strpos($tag, '0C1F') === 0) { $result['sender_email'] = $text; }
			elseif (strpos($tag, '0E04') === 0) { $result['to'] = $text; }
			elseif (strpos($tag, '1000') === 0) { $result['body'] = $text; }
			elseif (strpos($tag, '1013') === 0) { $result['html'] = $this->propertyText($value, substr($tag, -4)); }
			elseif (strpos($tag, '3707') === 0 || strpos($tag, '3704') === 0) {
				if ($text !== '' && !in_array($text, $result['attachments'], true)) { $result['attachments'][] = $text; }
			}
		}
		return $result;
	}

	private function loadFat()
	{
		$fatSectors = array();
		for ($i = 0; $i < 109; $i++) {
			$sid = $this->u32(76 + ($i * 4));
			if (!$this->isEnd($sid)) { $fatSectors[] = $sid; }
		}
		$difat = $this->u32(68);
		$difatCount = $this->u32(72);
		for ($n = 0; $n < $difatCount && !$this->isEnd($difat); $n++) {
			$sector = $this->sector($difat);
			$count = ($this->sectorSize / 4) - 1;
			for ($i = 0; $i < $count; $i++) {
				$sid = $this->value32($sector, $i * 4);
				if (!$this->isEnd($sid)) { $fatSectors[] = $sid; }
			}
			$difat = $this->value32($sector, $this->sectorSize - 4);
		}
		foreach ($fatSectors as $sid) {
			$this->fat = array_merge($this->fat, array_values(unpack('V*', $this->sector($sid))));
		}
	}

	private function stream($entry)
	{
		if ($entry['size'] < $this->miniCutoff && $this->miniStream !== '' && $this->miniFat) {
			return $this->readChain($entry['start'], $this->miniFat, $this->miniSectorSize, true, $entry['size']);
		}
		return $this->readChain($entry['start'], $this->fat, $this->sectorSize, false, $entry['size']);
	}

	private function readChain($start, $fat, $size, $mini, $limit = null)
	{
		$output = '';
		$sid = $start;
		$seen = array();
		while (!$this->isEnd($sid) && isset($fat[$sid]) && !isset($seen[$sid])) {
			$seen[$sid] = true;
			$output .= $mini ? substr($this->miniStream, $sid * $size, $size) : $this->sector($sid);
			$sid = $fat[$sid];
		}
		return $limit === null ? $output : substr($output, 0, $limit);
	}

	private function sector($sid) { return substr($this->data, ($sid + 1) * $this->sectorSize, $this->sectorSize); }
	private function u16($offset) { return $this->value16($this->data, $offset); }
	private function u32($offset) { return $this->value32($this->data, $offset); }
	private function value16($data, $offset) { $v = unpack('v', substr($data, $offset, 2)); return $v[1]; }
	private function value32($data, $offset) { $v = unpack('V', substr($data, $offset, 4)); return $v[1]; }
	private function isEnd($sid) { return $sid >= 0xFFFFFFFA; }

	private function propertyText($value, $type)
	{
		return $type === '001F' ? $this->utf16($value) : $this->cleanString($value);
	}

	private function utf16($value)
	{
		while (strlen($value) >= 2 && substr($value, -2) === "\0\0") { $value = substr($value, 0, -2); }
		if (function_exists('mb_convert_encoding')) { return $this->cleanString(mb_convert_encoding($value, 'UTF-8', 'UTF-16LE')); }
		if (function_exists('iconv')) { return $this->cleanString(iconv('UTF-16LE', 'UTF-8//IGNORE', $value)); }
		return $this->cleanString(str_replace("\0", '', $value));
	}

	private function cleanString($value)
	{
		$value = trim((string) $value, "\0 \t\r\n");
		if (!preg_match('//u', $value) && function_exists('iconv')) { $value = iconv('Windows-1252', 'UTF-8//IGNORE', $value); }
		return $value;
	}
}
// Visor de correos MSG y EML para adjunto_cierre
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['idem']) || $_SESSION['idem'] === '') {
	http_response_code(403);
	exit('TU SESIÓN HA TERMINADO');
}


// Solo el nombre del archivo, sin rutas (evita ../)
$archivo = isset($_GET['archivo']) && is_string($_GET['archivo']) ? basename($_GET['archivo']) : '';
$extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
if ($archivo === '' || !in_array($extension, array('msg', 'eml'), true)) {
	http_response_code(400);
	exit('VISOR MSG/EML v2: ARCHIVO NO VÁLIDO. Nombre recibido: '.htmlspecialchars($archivo, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'. Extensión: '.htmlspecialchars($extension, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'. Se requiere ?archivo=nombre.eml o nombre.msg.');
}

$ruta = __DIR__.'/includes/archivos/'.$archivo;
if (!is_file($ruta)) {
	http_response_code(404);
	exit('ARCHIVO NO ENCONTRADO');
}

function h($texto) {
	return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

$error = '';
$asunto = $remitente = $cuerpoTexto = $cuerpoHtml = '';
$destinatarios = array();
$adjuntos = array();

try {
	if ($extension === 'eml') {
		$contenido = file_get_contents($ruta);
		if ($contenido === false) { throw new RuntimeException('No se pudo abrir el correo.'); }
		$correo = EmlReader::read($contenido);
		$asunto = $correo['subject'];
		$remitente = $correo['sender'];
		$destinatarios = $correo['recipients'];
		$cuerpoTexto = $correo['text'];
		$cuerpoHtml = $correo['html'];
		$adjuntos = $correo['attachments'];
	} else {
		$lector = new MsgReader($ruta);
		$correo = $lector->read();
		$asunto = $correo['subject'];
		$remitente = trim($correo['sender_name'].($correo['sender_email'] !== '' ? ' <'.$correo['sender_email'].'>' : ''));
		$destinatarios = $correo['to'] !== '' ? array($correo['to']) : array();
		$cuerpoTexto = $correo['body'];
		$cuerpoHtml = $correo['html'];
		$adjuntos = $correo['attachments'];
	}
} catch (Exception $e) {
	$error = 'No fue posible leer el archivo .'.$extension.': '.$e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Ver correo — <?php echo h($archivo); ?></title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
	<div class="card shadow-sm">
		<div class="card-header"><strong>CORREO (.<?php echo h($extension); ?>)</strong></div>
		<div class="card-body">
		<?php if ($error !== '') { ?>
			<div class="alert alert-danger"><?php echo h($error); ?></div>
		<?php } else { ?>
			<p class="mb-1"><strong>ASUNTO:</strong> <?php echo h($asunto); ?></p>
			<p class="mb-1"><strong>DE:</strong> <?php echo h($remitente); ?></p>
			<p class="mb-1"><strong>PARA:</strong> <?php echo h(implode('; ', $destinatarios)); ?></p>
			<?php if (count($adjuntos) > 0) { ?>
				<p class="mb-1"><strong>ADJUNTOS:</strong> <?php echo h(implode(', ', $adjuntos)); ?></p>
			<?php } ?>
			<hr>
			<?php if (trim($cuerpoHtml) !== '') { ?>
				<!-- sandbox sin permisos: no ejecuta scripts del correo -->
				<iframe sandbox="" srcdoc="<?php echo h($cuerpoHtml); ?>" style="width:100%;height:600px;border:1px solid #dee2e6;background:#fff;"></iframe>
			<?php } else { ?>
				<div style="white-space:pre-wrap;"><?php echo h($cuerpoTexto); ?></div>
			<?php } ?>
		<?php } ?>
		</div>
	</div>
</div>
</body>
</html>