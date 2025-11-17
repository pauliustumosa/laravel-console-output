<?php

namespace Tumosa\LaravelConsoleOutput;

use Illuminate\Support\Str;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput as SymfonyConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleOutput
{
    private ?SymfonyConsoleOutput $output = null;

    /**
     * The default verbosity of output commands.
     */
    protected int $verbosity = OutputInterface::VERBOSITY_NORMAL;

    /**
     * The mapping between human readable verbosity levels and Symfony's OutputInterface.
     */
    protected array $verbosityMap = [
        'v' => OutputInterface::VERBOSITY_VERBOSE,
        'vv' => OutputInterface::VERBOSITY_VERY_VERBOSE,
        'vvv' => OutputInterface::VERBOSITY_DEBUG,
        'quiet' => OutputInterface::VERBOSITY_QUIET,
        'normal' => OutputInterface::VERBOSITY_NORMAL,
    ];

    public function write(string $message, string $style = null, $verbosity = null): void
    {
        if (self::isConsoleAvailable()) {
            $this->line($message, $style, $verbosity);
        }
    }

    public function info($string, $verbosity = null): void
    {
        $this->write($string, 'info', $verbosity);
    }

    public function warn(string $string, $verbosity = null): void
    {
        if (! $this->output->getFormatter()->hasStyle('warning')) {
            $this->output->getFormatter()->setStyle('warning', new OutputFormatterStyle('yellow'));
        }

        $this->write($string, 'warning', $verbosity);
    }

    public function error(string $string, $verbosity = null): void
    {
        $this->write($string, 'error', $verbosity);
    }

    public function alert(string $string, $verbosity = null): void
    {
        $length = Str::length(strip_tags($string)) + 12;

        $this->comment(str_repeat('*', $length), $verbosity);
        $this->comment('*     '.$string.'     *', $verbosity);
        $this->comment(str_repeat('*', $length), $verbosity);

        $this->comment('', $verbosity);
    }

    public function comment(string $string, $verbosity = null): void
    {
        $this->write($string, 'comment', $verbosity);
    }

    public function createProgressBar(int $max = 0): ProgressBar
    {
        $progressBar = new ProgressBar($this->output, $max);

        if ('\\' !== \DIRECTORY_SEPARATOR || 'Hyper' === getenv('TERM_PROGRAM')) {
            $progressBar->setEmptyBarCharacter('░'); // light shade character \u2591
            $progressBar->setProgressCharacter('');
            $progressBar->setBarCharacter('▓'); // dark shade character \u2593
        }

        return $progressBar;
    }

    private function isConsoleAvailable(): bool
    {
        if ($this->output !== null) {
            return true;
        }

        // Detect TTY
        if (!\function_exists('posix_isatty') || !posix_isatty(STDOUT)) {
            return false;
        }

        // Create ConsoleOutput
        $this->output = new SymfonyConsoleOutput();
        return true;
    }

    private function line($string, string $style = null, $verbosity = null): void
    {
        $styled = $style ? "<$style>$string</$style>" : $string;

        $this->output->writeln($styled, $this->parseVerbosity($verbosity));
    }

    private function parseVerbosity($level = null)
    {
        if (isset($this->verbosityMap[$level])) {
            $level = $this->verbosityMap[$level];
        } elseif (! is_int($level)) {
            $level = $this->verbosity;
        }

        return $level;
    }
}
