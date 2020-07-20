<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\Finder\FileInfoFactory;
use ItalyStrap\Finder\Finder;
use ItalyStrap\Finder\FinderFactory;
use ItalyStrap\Finder\FinderInterface;
use ItalyStrap\Finder\FilesHierarchyIterator;
use UnitTester;

class FinderFactoryTest extends Unit {

	/**
	 * @var UnitTester
	 */
	protected $tester;

	// phpcs:ignore -- Method from Codeception
    protected function _before() {
	}

	// phpcs:ignore -- Method from Codeception
    protected function _after() {
	}

	/**
	 * @test
	 */
	public function instanceOk() {
		$sut = new FinderFactory();
		$this->assertInstanceOf(
			FinderInterface::class,
			$sut->make(),
			''
		);
	}
}
