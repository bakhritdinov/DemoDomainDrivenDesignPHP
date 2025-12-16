<?php

namespace App\Infrastructure\Console\Language;

use App\Application\CommandBus;
use App\Application\Language\Command\CreateLanguageCommand;
use App\Core\Domain\Language\Dto\CreateLanguageDto;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;


#[AsCommand(
    name: 'app:language:create',
    description: 'Creates a new language.',
    hidden: false
)]
class CreateLanguageConsoleCommand extends Command
{
    public function __construct(public CommandBus $commandBus)
    {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        /**
         * Set name
         */
        $nameQuestion = new Question("Введите название \n");
        $nameQuestion->setValidator(function ($answer) {
            if (is_null($answer) || strlen($answer) > 250) {
                throw new \RuntimeException(
                    'Поле name является обязательным и должно содержать менее 250 символов.'
                );
            }

            return $answer;
        });
        $name = $helper->ask($input, $output, $nameQuestion);

        /**
         * Set code
         */
        $codeQuestion = new Question("Введите код \n");
        $codeQuestion->setValidator(function ($answer) {
            if (is_null($answer) || strlen($answer) > 250) {
                throw new \RuntimeException(
                    'Поле code является обязательным и должно содержать менее 250 символов.'
                );
            }

            return $answer;
        });
        $code = $helper->ask($input, $output, $codeQuestion);

        try {
            $this->commandBus->dispatch(new CreateLanguageCommand(
                CreateLanguageDto::fromArray(compact('name', 'code'))
            ));

        } catch (\Exception $exception) {
            $output->writeln($exception->getMessage());
            die;
        }

        $output->writeln(sprintf('Язык с кодом %s успешно добавлен', $code));

        return Command::SUCCESS;
    }
}