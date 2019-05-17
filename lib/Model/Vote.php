<?php
declare(strict_types=1);
/**
 * @copyright Copyright (c) 2019 Joas Schilling <coding@schilljs.com>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\TalkSimplePoll\Model;

use OCP\AppFramework\Db\Entity;

/**
 * @method void setUserId(string $userId)
 * @method string getUserId()
 * @method void setPollId(int $pollId)
 * @method int getPollId()
 * @method void setOptionId(int $optionId)
 * @method int getOptionId()
 */
class Vote extends Entity {

	/** @var string */
	protected $userId;

	/** @var int */
	protected $pollId;

	/** @var int */
	protected $optionId;

	public function __construct() {
		$this->addType('userId', 'string');
		$this->addType('pollId', 'int');
		$this->addType('optionId', 'int');
	}

	/**
	 * @return array
	 */
	public function asArray(): array {
		return [
			'id' => $this->getId(),
			'userId' => $this->getUserId(),
			'pollId' => $this->getPollId(),
			'optionId' => $this->getOptionId(),
		];
	}
}
