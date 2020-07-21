<?php
declare(strict_types=1);

class FinderTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    private $paths = [];
    
    protected function _before()
    {
		$this->paths = [
			'childPath'		=> \codecept_data_dir( 'child' ),
			'parentPath'	=> \codecept_data_dir( 'parent' ),
			'pluginPath'	=> \codecept_data_dir( 'plugin' ),
		];
    }

    protected function _after()
    {
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
	 */
	public function it_should_be_instantiable()
	{
		$finder = $this->getInstance();
	}

	/**
	 * @test
	 */
	public function it_should_find_files()
	{
		$finder = $this->getInstance();
		$finder->in( $this->paths );
		$files = $finder->find( ['config', 'content'] );

		codecept_debug( $files );
	}
}