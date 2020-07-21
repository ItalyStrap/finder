<?php
declare(strict_types=1);

namespace ItalyStrap\View;

class CallbackViewFinder extends AbstractViewFinder
{
	/**
	 * @var callable
	 */
	private $finder;

	/**
	 * CallbackConfigFinder constructor.
	 * @param callable $callable
	 */
	public function __construct( callable $callable ) {
		$this->finder = $callable;
	}

	/**
	 * @inheritDoc
	 */
	protected function filter( array $files ): string {
		$callback = $this->finder;
		return $callback( $files, $this->dirs );
	}
}