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

use OC\Core\Command\Base;
use OCA\TalkSimplePoll\Model\PollMapper;
use OCA\TalkSimplePoll\Model\Vote;
use OCA\TalkSimplePoll\Model\VoteMapper;
use OCP\AppFramework\Db\DoesNotExistException;
use function Sabre\Event\Loop\instance;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Poll extends Base {

	/** @var PollMapper */
	protected $pollMapper;
	/** @var VoteMapper */
	protected $voteMapper;

	public function __construct(PollMapper $pollMapper,
								VoteMapper $voteMapper) {
		parent::__construct();

		$this->pollMapper = $pollMapper;
		$this->voteMapper = $voteMapper;
	}

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
				'action',
				InputArgument::REQUIRED
			)
			->addArgument(
				'payload',
				InputArgument::REQUIRED
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$action = $input->getArgument('action');
		$payload = $input->getArgument('payload');

		try {
			$poll = $this->pollMapper->findByToken($input->getArgument('token'));
		} catch (DoesNotExistException $e) {
			$poll = null;
		}

		if ($action === 'poll') {
			if (($payload === 'close' || $payload === 'end') && $poll instanceof \OCA\TalkSimplePoll\Model\Poll) {
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
			return;
		}

		if (!$poll instanceof \OCA\TalkSimplePoll\Model\Poll) {
			$output->writeln('There is currently no poll running.');
			return;
		}

		if ($poll->getStatus() === \OCA\TalkSimplePoll\Model\Poll::STATUS_CLOSED) {
			$output->writeln('There is currently no poll running.');
			return;
		}

		if ($input->getArgument('userId') === '') {
			$output->writeln('Guests can\'t vote.');
			return;
		}

		$options = json_decode($poll->getOptions(), true);
		$optionId = $payload - 1;
		if ($optionId < 0 || $optionId >= count($options)) {
			$output->writeln('Invalid option');
			return;
		}

		try {
			$vote = $this->voteMapper->findByPollForUser($poll, $input->getArgument('userId'));
			$vote->setOptionId($optionId);
			$this->voteMapper->update($vote);
		}catch (DoesNotExistException $e) {
			$vote = new Vote();
			$vote->setUserId($input->getArgument('userId'));
			$vote->setPollId($poll->getId());
			$vote->setOptionId($optionId);
			$this->voteMapper->insert($vote);
		}

		$this->showPoll($output, $poll);
	}

	public function showPoll(OutputInterface $output, \OCA\TalkSimplePoll\Model\Poll $poll): void {
		$output->write($poll->getQuestion());
		$options = json_decode($poll->getOptions(), true);

		$votes = $this->voteMapper->findByPoll($poll);
		$totalVotes = count($votes);

		$output->writeln(' (' . $totalVotes . ' votes)');
		$output->writeln('');

		if ($poll->getStatus() === \OCA\TalkSimplePoll\Model\Poll::STATUS_OPEN) {
			foreach ($options as $key => $option) {
				$output->writeln('/vote ' . ($key+1) . ' - ' . $option);
			}


			$output->writeln('');
			$output->writeln('/poll close - Close the voting and show results');
			return;
		}

		$result = [];
		foreach ($votes as $vote) {
			$result[$vote->getOptionId()] = $result[$vote->getOptionId()] ?? 0;
			$result[$vote->getOptionId()]++;
		}

		$winnerVotes = max($result);

		foreach ($options as $key => $option) {
			$votes = $result[$key] ?? 0;
			$quota = $totalVotes === 0 ? 0 : ($votes / $totalVotes);
			$chars = (int) ($quota * 20);

			$output->write(($key+1) . '. ' . $option . ': ' . $votes . ' votes,');
			$output->write(' ' . round($quota * 100, 1) . '%');
			$output->writeln((($votes === $winnerVotes)?' 🏆':''));
			$output->write(str_repeat('█', $chars) );
			$output->writeln(str_repeat('░', 20 - $chars));
		}
	}
}
