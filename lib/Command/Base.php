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

use OCA\TalkSimplePoll\Model\PollMapper;
use OCA\TalkSimplePoll\Model\VoteMapper;
use Symfony\Component\Console\Output\OutputInterface;

class Base extends \OC\Core\Command\Base {

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
