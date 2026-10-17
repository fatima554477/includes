<?php


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
		$value = rtrim($value, "\0");
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