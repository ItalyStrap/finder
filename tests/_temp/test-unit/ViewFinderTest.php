<?php
declare(strict_types=1);

namespace ItalyStrap\Tests\Unit;

// phpcs:disable
include_once 'BaseViewFinderTestUnit.php';
// phpcs:enable
class ViewFinderTest extends BaseViewFinderTestUnit {


	protected function setType() {
		return \ItalyStrap\View\ViewFinder::class;
	}

	protected function setArgs() {
		return null;
	}

	/**
	 * @test
	 */
	public function itShouldBeInstantiable() {
		$finder = $this->getInstance();
		$this->assertInstanceOf( \ItalyStrap\View\ViewFinder::class, $finder );
	}
}
