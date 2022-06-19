<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\Finder\FileInfoFactory;
use ItalyStrap\Finder\Finder;
use ItalyStrap\Finder\FinderInterface;
use ItalyStrap\Finder\FilesHierarchyIterator;
use ItalyStrap\Finder\SearchFileStrategy;
use Prophecy\Prophet;

class FinderTest extends \Codeception\Test\Unit {

	/**
	 * @var \UnitTester
	 */
	protected $tester;

	/**
	 * @var Prophet
	 */
	private $prophet;

	/**
	 * @var \Prophecy\Prophecy\ObjectProphecy
	 */
	private $search_files;

	/**
	 * @return SearchFileStrategy
	 */
	public function getSearchFiles(): SearchFileStrategy {
		return $this->search_files->reveal();
	}

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
		$this->prophet = new Prophet;
		$this->search_files = $this->prophet->prophesize( SearchFileStrategy::class );
	}

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
	}

	private function getInstance() {
		$sut = new Finder( $this->getSearchFiles() );
		$this->assertInstanceOf( FinderInterface::class, $sut );
		$this->assertInstanceOf( Finder::class, $sut );
		return $sut;
	}

	/**
	 * @test
	 */
	public function itShouldBeInstantiable() {
		$sut = $this->getInstance();
	}
}
