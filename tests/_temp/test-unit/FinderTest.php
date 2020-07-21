<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

class FinderTest extends \Codeception\Test\Unit {

	/**
	 * @var \UnitTester
	 */
	protected $tester;

	private $paths = [];

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
		$this->paths = [
			'childPath'		=> \codecept_data_dir( 'child' ),
			'parentPath'	=> \codecept_data_dir( 'parent' ),
			'pluginPath'	=> \codecept_data_dir( 'plugin' ),
		];
	}

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
	}

	private function getInstance() {
		$finder = new class extends \ItalyStrap\Finder\AbstractFinder {

			public function find( $slugs, $extension = 'php' ) {
				// TODO: Implement find() method.
			}
			protected function filter( array $files ) {
				// TODO: Implement filter() method.
			}
		};
		$this->assertInstanceOf( ItalyStrap\Finder\FinderInterface::class, $finder );
		$this->assertInstanceOf( ItalyStrap\Finder\AbstractFinder::class, $finder );
		$this->assertInstanceOf( \Countable::class, $finder );
//		$this->
		return $finder;
	}

	/**
	 * @test
	 * phpcs:ignore -- Method from Codeception
	 */
	public function itShouldBeInstantiable() {
		$finder = $this->getInstance();
	}

	/**
	 * @test
	 * phpcs:ignore -- Method from Codeception
	 */
	public function itSshouldFindFiles() {
		$finder = $this->getInstance();
		$finder->in( $this->paths );
		$files = $finder->find( ['config', 'content'] );

		codecept_debug( $files );
	}
}
