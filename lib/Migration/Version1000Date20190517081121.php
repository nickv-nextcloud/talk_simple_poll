<?php

declare(strict_types=1);

namespace OCA\TalkSimplePoll\Migration;

use Closure;
use Doctrine\DBAL\Types\Type;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

/**
 * Auto-generated migration step: Please modify to your needs!
 */
class Version1000Date20190517081121 extends SimpleMigrationStep {

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options): ?ISchemaWrapper {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		if (!$schema->hasTable('tsp_polls')) {
			$table = $schema->createTable('tsp_polls');

			$table->addColumn('id', Type::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 20,
			]);
			$table->addColumn('question', Type::STRING, [
				'notnull' => true,
				'length' => 255,
			]);
			$table->addColumn('options', Type::STRING, [
				'notnull' => true,
				'length' => 1024,
			]);
			$table->addColumn('user_id', Type::STRING, [
				'notnull' => true,
				'length' => 64,
			]);
			$table->addColumn('token', Type::STRING, [
				'notnull' => true,
				'length' => 32,
			]);
			$table->addColumn('status', Type::INTEGER, [
				'notnull' => true,
				'length' => 6,
				'default' => 0,
			]);

			$table->setPrimaryKey(['id']);
		}

		if (!$schema->hasTable('tsp_votes')) {
			$table = $schema->createTable('tsp_votes');

			$table->addColumn('id', Type::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 20,
			]);
			$table->addColumn('poll_id', Type::INTEGER, [
				'notnull' => true,
				'length' => 20,
			]);
			$table->addColumn('user_id', Type::STRING, [
				'notnull' => true,
				'length' => 64,
			]);
			$table->addColumn('option_id', Type::INTEGER, [
				'notnull' => true,
				'length' => 6,
				'default' => 1,
			]);

			$table->setPrimaryKey(['id']);
			$table->addUniqueIndex(['poll_id', 'user_id']);
		}

		return $schema;
	}

}
