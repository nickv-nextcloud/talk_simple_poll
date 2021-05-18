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

namespace OCA\TalkSimplePoll\Command;

use OCP\AppFramework\Db\DoesNotExistException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Vote extends Base {

	protected function configure(): void {
		$this
			->setName('talk:poll:vote')
			->setDescription('Vote on a simple poll')
			->addArgument(
				'token',
				InputArgument::REQUIRED
			)
			->addArgument(
				'userId',
				InputArgument::REQUIRED
			)
			->addArgument(
				'payload',
				InputArgument::REQUIRED
			)
			->setHelp('/vote - Vote for an option of "Simple polls for Nextcloud Talk"

To vote for option 2, send a message with:
    /vote 2')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output): int {
		$payload = $input->getArgument('payload');

		try {
			$poll = $this->pollMapper->findByToken($input->getArgument('token'));
		} catch (DoesNotExistException $e) {
			$poll = null;
		}

		if (!$poll instanceof \OCA\TalkSimplePoll\Model\Poll) {
			$output->writeln('There is currently no poll running.');
			return 0;
		}

		if ($poll->getStatus() === \OCA\TalkSimplePoll\Model\Poll::STATUS_CLOSED) {
			$output->writeln('There is currently no poll running.');
			return 0;
		}

		if ($input->getArgument('userId') === '') {
			$output->writeln('Guests can\'t vote.');
			return 0;
		}

		$options = json_decode($poll->getOptions(), true);
		$optionId = $payload - 1;
		if ($optionId < 0 || $optionId >= count($options)) {
			$output->writeln('Invalid option');
			return 0;
		}

		try {
			$vote = $this->voteMapper->findByPollForUser($poll, $input->getArgument('userId'));
			$vote->setOptionId($optionId);
			$this->voteMapper->update($vote);
		}catch (DoesNotExistException $e) {
			$vote = new \OCA\TalkSimplePoll\Model\Vote();
			$vote->setUserId($input->getArgument('userId'));
			$vote->setPollId($poll->getId());
			$vote->setOptionId($optionId);
			$this->voteMapper->insert($vote);
		}

		$this->showPoll($output, $poll);
		return 0;
	}
}
