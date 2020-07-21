<?php
declare(strict_types=1);

namespace ItalyStrap\Finder;

/**
 * Class AbstractFinder
 * @package ItalyStrap\Finder
 */
abstract class AbstractFinder implements FinderInterface, \Countable
{
	use FinderTrait;
}