<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\Finder\FileInfoFactory;
use ItalyStrap\Finder\Finder;
use ItalyStrap\Finder\FinderInterface;
use ItalyStrap\Finder\SearchFilesHierarchy;
use ItalyStrap\Finder\SearchFileStrategy;

class FinderTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
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

	protected function _before()
    {
    	$this->search_files = $this->prophesize( SearchFileStrategy::class );
    }

    protected function _after()
    {
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
