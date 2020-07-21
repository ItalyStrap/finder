<?php
declare(strict_types=1);

namespace ItalyStrap\Tests\Unit;

use ItalyStrap\View\Exceptions\ViewNotFoundException;

abstract class BaseViewFinderTestUnit extends \Codeception\Test\Unit
{
	/**
	 * @var \UnitTester
	 */
	protected $tester;

	protected $paths = [];

	protected function _before()
	{
		$this->paths = [
			'childPath'		=> \codecept_data_dir( 'fixtures/child' ),
			'parentPath'	=> \codecept_data_dir( 'fixtures/parent' ),
			'pluginPath'	=> \codecept_data_dir( 'fixtures/plugin' ),
		];
	}

	protected function _after()
	{
	}

//	abstract protected function getType();

	protected function getInstance() {

		$type = $this->setType();

		$finder = new $type( $this->setArgs() );
		$this->assertInstanceOf( \ItalyStrap\View\ViewFinderInterface::class, $finder );
		$this->assertInstanceOf( \ItalyStrap\View\AbstractViewFinder::class, $finder );
		$this->assertInstanceOf( \ItalyStrap\Finder\AbstractFinder::class, $finder );
		$this->assertInstanceOf( \Countable::class, $finder );
		return $finder;
	}

	/**
	 * @test
	 */
	public function itShouldFindContentNoneFileInParentDirectory() {
		$finder = $this->getInstance();
		// Search in single directory
		$finder->in( $this->paths[ 'parentPath' ] );

		$full_path_to_file = $finder->find( ['content', 'none', 'test'] );

		$this->assertFileExists( $full_path_to_file, '' );
		$this->assertFileIsReadable( $full_path_to_file, '' );

		$expected = \str_replace( '/', '\\', $this->paths[ 'parentPath' ] . '\content-none.php'  );
		$actual = \str_replace( '/', '\\', $full_path_to_file  );
		$this->assertStringContainsString( $expected, $actual, '' );
	}

	/**
	 * @test
	 */
	public function itShouldFindFileTestInPluginDirectory() {
		$finder = $this->getInstance();
		// New search in another directory with the same $finder object
		$finder->in( $this->paths[ 'pluginPath' ] );

		$full_path_to_file = $finder->find( ['test'] );

		$this->assertFileExists( $full_path_to_file );
		$this->assertFileIsReadable( $full_path_to_file );

		$expected = \str_replace( '/', '\\', $this->paths[ 'pluginPath' ] . '\test.php'  );
		$actual = \str_replace( '/', '\\', $full_path_to_file  );
		$this->assertStringContainsString( $expected, $actual, '' );
	}

	/**
	 * @test
	 */
	public function itShouldFallbackToTestFileInPluginDirectoryIfNoFilesAreFoundInParentAndChildDirectory() {
		$finder = $this->getInstance();

		$finder->in( $this->paths );

		$full_path_to_file = $finder->find( ['test'] );

		$this->assertFileExists( $full_path_to_file );
		$this->assertFileIsReadable( $full_path_to_file );

		$expected = \str_replace( '/', '\\', $this->paths[ 'pluginPath' ] . '\test.php'  );
		$actual = \str_replace( '/', '\\', $full_path_to_file  );
		$this->assertStringContainsString( $expected, $actual, '' );
	}

	/**
	 * @test
	 */
	public function itShouldFindContentFileAndTakeITFromChildInsteadOfParentAndPlugin() {
		$finder = $this->getInstance();

		$finder->in( $this->paths );

		$full_path_to_file = $finder->find( ['content'] );

		$this->assertFileExists( $full_path_to_file );
		$this->assertFileIsReadable( $full_path_to_file );

		$expected = \str_replace( '/', '\\', $this->paths[ 'childPath' ] . '\content.php'  );
		$actual = \str_replace( '/', '\\', $full_path_to_file  );
		$this->assertStringContainsString( $expected, $actual, '' );
	}

	/**
	 * @test
	 */
	public function itShouldFindPartialFileIndex() {
		$finder = $this->getInstance();

		$finder->in( $this->paths );

		$full_path_to_file = $finder->find( ['parts\subparts/index', 'jhlkjn'] );

		$this->assertFileExists( $full_path_to_file );
		$this->assertFileIsReadable( $full_path_to_file );

		$expected = \str_replace( '/', '\\', $this->paths[ 'childPath' ] . '\parts\subparts\index.php'  );
		$actual = \str_replace( '/', '\\', $full_path_to_file  );
		$this->assertStringContainsString( $expected, $actual, '' );
	}

	/**
	 * @test
	 */
	public function itShouldViewFinderSearchInManyDirectories() {
		$finderAdapter = $this->getInstance();

		/**
		 * Criteria
		 * Search in child > parent > plugin path because they are
		 * in this order in $this->paths field
		 * File to search content-none.php {['content', 'none']}
		 * child false
		 * parent true
		 * plugin false
		 */
		$finderAdapter->in( $this->paths );
		$realPath = $finderAdapter->find( ['content', 'none'] );

		$realPath = \str_replace('/', '\\', $realPath );

		$this->assertStringNotContainsString( '_data\fixtures\child', $realPath );
		$this->assertStringContainsString( '_data\fixtures\parent', $realPath );
		$this->assertStringNotContainsString( '_data\fixtures\plugin', $realPath );

	}

	/**
	 * @test
	 */
	public function itShouldThrownExceptionIfNoFilesAreFound() {
		$this->expectException( ViewNotFoundException::class );
		$finder = $this->getInstance();
		$finder->in( $this->paths );
		$full_path_to_file = $finder->find( ['no-file'] );
	}
}