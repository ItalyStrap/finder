<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
*/
class UnitTester extends \Codeception\Actor
{
    use _generated\UnitTesterActions;

    /**
     * Define custom actions here
     */


	const PLUGIN_PATH = 'pluginPath';
	const CHILD_PATH = 'childPath';
	const PARENT_PATH = 'parentPath';

	public function fixturesPaths(): array {
		return [
			self::PLUGIN_PATH	=> codecept_data_dir( 'fixtures/plugin' ),
			self::CHILD_PATH	=> codecept_data_dir( 'fixtures/child' ),
			self::PARENT_PATH	=> codecept_data_dir( 'fixtures/parent' ),
		];
	}
}
