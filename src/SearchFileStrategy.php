<?php
declare(strict_types=1);

namespace ItalyStrap\Finder;

use SplFileInfo;

interface SearchFileStrategy extends \IteratorAggregate, \Countable {

	/**
	 * @return SplFileInfo|string the first file founded if the file is readable
	 */
	public function firstFile();

	/**
	 * @param array<string> $dirs
	 * @return void
	 */
	public function in( array $dirs );

	/**
	 * @param array<string> $names
	 * @return void
	 */
	public function names( array $names );
}
