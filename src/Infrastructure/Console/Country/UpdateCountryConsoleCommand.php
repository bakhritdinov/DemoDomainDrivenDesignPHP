<?php

namespace App\Infrastructure\Console\Country;

use App\Application\CommandBus;
use App\Application\Country\Command\UpdateCountryCommand;
use App\Application\Country\Query\FindAllCountriesQuery;
use App\Application\QueryBus;
use App\Core\Domain\Country\Dto\UpdateCountryDto;
use App\Core\Domain\Country\Entity\Country;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Uid\Uuid;

#[AsCommand(name: 'app:country:update', description: 'Update country console command')]
class UpdateCountryConsoleCommand extends Command
{
    public function __construct(
        public readonly CommandBus $commandBus,
        public readonly QueryBus   $queryBus
    )
    {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        /**
         * Choice country
         */
        $countries = $this->queryBus->handle(new FindAllCountriesQuery());
        $choiceList = new ChoiceQuestion(
            'Please select your country',
            array_map(function (Country $country) {
                return $country->getName() . ' | ' . $country->getId();
            }, $countries),
            null
        );
        $choiceList->setErrorMessage("Selected country %s is invalid.\n");
        $choiceAnswer = explode('|', $helper->ask($input, $output, $choiceList));
        $countryId = Uuid::fromString(trim(end($choiceAnswer)));

        /**
         * Set new name for selected country
         */
        $setNameQuestion = new Question("Set new name (press enter for skip stage) \n", null);
        $setNameQuestion->setValidator(function ($answer) {
            if (strlen($answer) > 250) {
                throw new \RuntimeException(
                    'The name field must be less than 250 characters'
                );
            }
            return $answer;
        });
        $name = $helper->ask($input, $output, $setNameQuestion);

        /**
         * Set new numeric code for selected country
         */
        $setNameQuestion = new Question("Set new numeric code (press enter for skip stage) \n", null);
        $setNameQuestion->setValidator(function ($answer) {
            if (strlen($answer) > 3) {
                throw new \RuntimeException(
                    'The name field must be less than 3 characters'
                );
            }
            return $answer;
        });
        $numericCode = $helper->ask($input, $output, $setNameQuestion);

        /**
         * Set new alpha2 for selected country
         */
        $setNameQuestion = new Question("Set new alpha2 (press enter for skip stage) \n", null);
        $setNameQuestion->setValidator(function ($answer) {
            if (strlen($answer) > 2) {
                throw new \RuntimeException(
                    'The name field must be less than 2 characters'
                );
            }
            return $answer;
        });
        $alpha2 = $helper->ask($input, $output, $setNameQuestion);
        if (!is_null($alpha2)) {
            $alpha2 = strtoupper($alpha2);
        }

        /**
         * Set new alpha3 for selected country
         */
        $setNameQuestion = new Question("Set new alpha3 (press enter for skip stage) \n", null);
        $setNameQuestion->setValidator(function ($answer) {
            if (strlen($answer) > 3) {
                throw new \RuntimeException(
                    'The name field must be less than 3 characters'
                );
            }
            return $answer;
        });
        $alpha3 = $helper->ask($input, $output, $setNameQuestion);
        if (!is_null($alpha3)) {
            $alpha3 = strtoupper($alpha3);
        }

        /**
         * Set status for selected country
         */
        $setIsActiveQuestion = new Question("Set is_active (press enter for skip stage) \n", null);
        $setIsActiveQuestion->setValidator(function ($answer) {
            if (is_null($answer)) {
                return null;
            }

            $answer = filter_var($answer, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if (!is_bool($answer)) {
                throw new \RuntimeException(
                    'The is_active field must contain a boolean value'
                );
            }
            return $answer;
        });
        $isActive = $helper->ask($input, $output, $setIsActiveQuestion);

        $updateCountryDto = new UpdateCountryDto($name, $numericCode, $alpha2, $alpha3, $isActive);
        $this->commandBus->dispatch(new UpdateCountryCommand($countryId, $updateCountryDto));

        $countries = $this->queryBus->handle(new FindAllCountriesQuery());
        $table = new Table($output);
        $table->setStyle('box-double');
        $table
            ->setHeaders(['id', 'numericCode', 'alpha2', 'alpha3', 'name', 'is_active', 'created_at', 'updated_at'])
            ->setRows(array_map(function (Country $country) {
                return [
                    $country->getId()->toRfc4122(),
                    $country->getNumericCode(),
                    $country->getAlpha2(),
                    $country->getAlpha3(),
                    $country->getName(),
                    $country->isActive(),
                    $country->getCreatedAt()->format('Y-m-d H:i:s'),
                    $country->getUpdatedAt()?->format('Y-m-d H:i:s'),
                ];
            }, $countries));

        $table->render();

        if (!is_null($name) || !is_null($numericCode) || !is_null($alpha2) || !is_null($alpha3) || !is_null($isActive)) {
            $output->writeln(sprintf('Country with ID %s successfully updated', $countryId->toRfc4122()));
        } else {
            $output->writeln(sprintf('Country with ID %s not updated', $countryId->toRfc4122()));
        }

        return Command::SUCCESS;
    }
}