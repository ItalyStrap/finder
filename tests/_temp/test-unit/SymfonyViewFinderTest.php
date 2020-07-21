<?php
declare(strict_types=1);

namespace ItalyStrap\Tests\Unit;

use Symfony\Component\Finder\Finder;

// phpcs:disable
include_once 'BaseViewFinderTestUnit.php';
// phpcs:enable
class SymfonyViewFinderTest extends BaseViewFinderTestUnit {


	protected function setType() {
		return \ItalyStrap\View\SymfonyViewFinderAdapter::class;
	}

	protected function setArgs() {
		return $this->make( Finder::class );
	}

	/**
	 * @test
	 */
	public function itShouldBeInstantiable() {
		$finder = $this->getInstance();
		$this->assertInstanceOf( \ItalyStrap\View\SymfonyViewFinderAdapter::class, $finder );
	}
}
