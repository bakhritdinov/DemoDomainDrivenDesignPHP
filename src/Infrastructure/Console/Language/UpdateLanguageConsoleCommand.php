<?php

namespace App\Infrastructure\Console\Language;

use App\Application\CommandBus;
use App\Application\Language\Command\UpdateLanguageCommand;
use App\Application\Language\Query\FindLanguageQuery;
use App\Application\QueryBus;
use App\Core\Domain\Language\Dto\UpdateLanguageDto;
use App\Core\Domain\Language\Entity\Language;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Uid\Uuid;

#[AsCommand(
    name: 'app:language:update',
    description: 'Update language.',
    hidden: false
)]
class UpdateLanguageConsoleCommand extends Command
{
    public function __construct(
        public CommandBus $commandBus,
        public QueryBus   $queryBus
    )
    {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        /**
         * Select language
         */
        $languages = $this->queryBus->handle(new FindLanguageQuery);
        $choiceList = new ChoiceQuestion(
            'Выберите язык из найденных результатов',
            array_map(function (Language $language) {
                return $language->getName() . ' | ' . $language->getId()->toRfc4122();
            }, $languages)
        );
        $choiceList->setErrorMessage("Выбранный язык %s недействителен..\n");
        $choiceAnswer = explode('|', $helper->ask($input, $output, $choiceList));
        $languageId = Uuid::fromString(trim(end($choiceAnswer)));


        /**
         * Set new name for selected language
         */
        $setNameQuestion = new Question("Установите новое имя (нажмите Enter, чтобы пропустить этап) \n", null);
        $setNameQuestion->setValidator(function ($answer) {
            if (strlen($answer) > 250) {
                throw new \RuntimeException(
                    'Поле имени должно быть меньше 250 символов.'
                );
            }
            return $answer;
        });
        $name = $helper->ask($input, $output, $setNameQuestion);

        /**
         * Set status for selected language
         */
        $setIsActiveQuestion = new Question("Установите is_active (нажмите Enter, чтобы пропустить этап) \n", null);
        $setIsActiveQuestion->setValidator(function ($answer) {
            if (is_null($answer)) {
                return null;
            }

            $answer = filter_var($answer, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if (!is_bool($answer)) {
                throw new \RuntimeException(
                    'Поле is_active должно содержать логическое значение.'
                );
            }
            return $answer;
        });
        $isActive = $helper->ask($input, $output, $setIsActiveQuestion);


        $this->commandBus->dispatch(new UpdateLanguageCommand(
            $languageId,
            UpdateLanguageDto::fromArray(compact('name', 'isActive')))
        );

        $languages = $this->queryBus->handle(new FindLanguageQuery);
        $table = new Table($output);
        $table->setStyle('box-double');
        $table
            ->setHeaders(['id', 'name', 'code', 'is_active', 'created_at', 'updated_at'])
            ->setRows(array_map(function (Language $language) {
                return [
                    $language->getId()->toRfc4122(),
                    $language->getName(),
                    $language->getCode(),
                    $language->isActive(),
                    $language->getCreatedAt()->format('Y-m-d H:i:s'),
                    $language->getUpdatedAt()?->format('Y-m-d H:i:s'),
                ];
            }, $languages));

        $table->render();


        return Command::SUCCESS;
    }
}