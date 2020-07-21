<?php
declare(strict_types=1);

namespace ItalyStrap\Tests\Unit;

use Symfony\Component\Finder\Finder;

include_once 'BaseViewFinderTestUnit.php';
class SymfonyViewFinderTest extends BaseViewFinderTestUnit
{

	protected function setType() {
		return \ItalyStrap\View\SymfonyViewFinderAdapter::class;
	}

	protected function setArgs() {
		return $this->make( Finder::class );
	}

	/**
	 * @test
	 */
	public function it_should_be_Instantiable()
	{
		$finder = $this->getInstance();
		$this->assertInstanceOf( \ItalyStrap\View\SymfonyViewFinderAdapter::class, $finder );
	}
}