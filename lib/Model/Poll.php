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
 * @method void setQuestion(string $question)
 * @method string getQuestion()
 * @method void setOptions(string $options)
 * @method string getOptions()
 * @method void setUserId(string $userId)
 * @method string getUserId()
 * @method void setToken(string $token)
 * @method string getToken()
 * @method void setStatus(int $status)
 * @method int getStatus()
 */
class Poll extends Entity {

	public const STATUS_OPEN = 0;
	public const STATUS_CLOSED = 1;

	/** @var string */
	protected $question;

	/** @var string */
	protected $options;

	/** @var string */
	protected $userId;

	/** @var string */
	protected $token;

	/** @var int */
	protected $status;

	public function __construct() {
		$this->addType('question', 'string');
		$this->addType('options', 'string');
		$this->addType('userId', 'string');
		$this->addType('token', 'string');
		$this->addType('status', 'int');
	}

	/**
	 * @return array
	 */
	public function asArray(): array {
		return [
			'id' => $this->getId(),
			'question' => $this->getQuestion(),
			'options' => $this->getOptions(),
			'userId' => $this->getUserId(),
			'token' => $this->getToken(),
			'status' => $this->getStatus(),
		];
	}
}
