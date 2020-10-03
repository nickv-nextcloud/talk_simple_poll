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

class Poll extends Base {

	protected function configure(): void {
		$this
			->setName('talk:poll')
			->setDescription('Simple polls for Nextcloud Talk')
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
			->setHelp('/poll - Simple polls for Nextcloud Talk

Start a poll by sending a message with /poll and your question in the first line
and each possible answer in a follow up line, e.g.:
    /poll When should we leave?
    1pm
    2pm
    3pm
    4pm

To close a running poll send a message with:
    /poll close')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$payload = $input->getArgument('payload');

		try {
			$poll = $this->pollMapper->findByToken($input->getArgument('token'));
		} catch (DoesNotExistException $e) {
			$poll = null;
		}

		if (($payload === 'close' || $payload === 'end') && $poll instanceof \OCA\TalkSimplePoll\Model\Poll) {
			if (!empty($poll->getUserId()) && $poll->getUserId() !== $input->getArgument('userId')) {
				$output->writeln('The poll can only be closed by the author.');
				return;
			}

			$poll->setStatus(\OCA\TalkSimplePoll\Model\Poll::STATUS_CLOSED);
			$this->pollMapper->update($poll);

			$this->showPoll($output, $poll);
			return;
		}

		if ($payload === 'show' && $poll instanceof \OCA\TalkSimplePoll\Model\Poll) {
			$this->showPoll($output, $poll);
			return;
		}

		if (!$poll instanceof \OCA\TalkSimplePoll\Model\Poll || $poll->getStatus() === \OCA\TalkSimplePoll\Model\Poll::STATUS_CLOSED) {

			$options = explode("\n", $payload);
			$numOptions = count($options);

			if (strlen($payload) > (750 - ($numOptions * 50))) {
				$output->writeln('The question and answer are too long.');
				return;
			}

			if (count($options) < 3) {
				$output->writeln('You need to provide one question and at least two answers');
				return;
			}

			$question = array_shift($options);
			$poll = new \OCA\TalkSimplePoll\Model\Poll();
			$poll->setToken($input->getArgument('token'));
			$poll->setUserId($input->getArgument('userId'));
			$poll->setQuestion($question);
			$poll->setOptions(json_encode($options));
			$this->pollMapper->insert($poll);

			$this->showPoll($output, $poll);
			return;
		}

		$output->writeln('A poll is already running.');
		$output->writeln('');
		$this->showPoll($output, $poll);
	}
}
