<?php

namespace Tools;

class Storage
{
	const folder = '__Protected';

	public static function save($_file, $_folderName = '', $_fileName = null)
	{
		$file = PrjDir . '/' . self::folder . '/' . $_folderName . '/' . $_fileName;
		if (!file_exists(PrjDir . '/' . self::folder . '/' . $_folderName))
			mkdir(PrjDir . '/' . self::folder . '/' . $_folderName, 0777, true);
		if (move_uploaded_file($_file, $file))
			return $file;
		else
			return false;
	}

	public static function open($_file, $_fromRoot = false, $_return = false)
	{
		$file = $_file;
		if (!$_fromRoot)
			$file = PrjDir . '/' . self::folder . '/' . $_file;

		if (file_exists($file)) {
			if ($_return === false) {
				header('Content-type: ' . mime_content_type($file));
				header('Content-Disposition: attachment; filename="' . basename($file) . '"');
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				header('Content-Length: ' . filesize($file));

				readfile($file);
			} else {
				return [
					'base64' => base64_encode(file_get_contents($file)),
					'mime' => mime_content_type($file)
				];
			}
		} else {
			if ($_return === true)
				return false;
			else
				\Tools\Response::errorHandle(404);
		}
		exit(0);
	}

	public static function remove($_file)
	{
		if (empty($_file))
			return false;
		return @unlink($_file);
	}
}
