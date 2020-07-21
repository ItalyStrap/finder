<?php
declare(strict_types=1);

namespace ItalyStrap\Tests\Unit;

include_once 'BaseViewFinderTestUnit.php';
class ViewFinderTest extends BaseViewFinderTestUnit
{

	protected function setType() {
		return \ItalyStrap\View\ViewFinder::class;
	}

	protected function setArgs() {
		return null;
	}

	/**
	 * @test
	 */
    public function it_should_be_Instantiable()
    {
		$finder = $this->getInstance();
		$this->assertInstanceOf( \ItalyStrap\View\ViewFinder::class, $finder );
    }
}