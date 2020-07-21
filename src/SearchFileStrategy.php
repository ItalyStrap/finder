<?php
declare(strict_types=1);

namespace ItalyStrap\Finder;

interface SearchFileStrategy extends \IteratorAggregate, \Countable {

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
