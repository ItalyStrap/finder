<?php
declare(strict_types=1);

namespace ItalyStrap\Finder;

use SplFileInfo;

interface SearchFileStrategy {

	/**
	 * @param array<string> $file_names The name of the files to search
	 * 									Example:
	 * 									[
	 * 										'file-part1.php',
	 * 										'file-part2.php',
	 * 										'file1.php',
	 * 										'file2.php',
	 * 									]
	 * @param array<string> $dirs A list of full path directory to search on
	 * @return string|SplFileInfo the first file founded if the file is readable
	 */
	public function first( array $file_names, array $dirs );

	/**
	 * @param array<string> $file_names The name of the files to search
	 * 									Example:
	 * 									[
	 * 										'file-part1.php',
	 * 										'file-part2.php',
	 * 										'file1.php',
	 * 										'file2.php',
	 * 									]
	 * @param array<string> $dirs A list of full path directory to search on
	 * @return array<string> Return a list of the real path of all files founded
	 */
	public function all( array $file_names, array $dirs ): array;
}
