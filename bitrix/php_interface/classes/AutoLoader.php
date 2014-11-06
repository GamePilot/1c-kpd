<?php
/* Автолоадер всех файлов из текущей папки, где находится этот файл.
Автолоадер не проверяет принадлежность файла к классу, загружает все!
Файлы видов могут лежать в view текущей папки, но не в корне этой папки. */
$handle = opendir(__DIR__);
while ($subfile = readdir($handle))
{
	if ($subfile == '.' or $subfile == '..')
		continue;
	if (is_file(__DIR__."/".$subfile))
		require_once __DIR__."/".$subfile;
}
@closedir($handle);
