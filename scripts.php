<?php
declare(strict_types=1);

return function ($io): \Generator {
    $version = $io->choice(
        'Select PHP Version',
        [
            '8.3',
            '8.2',
        ],
        0
    );
    $version = 'php' . $version;

    $host = $io->ask('Host', 'mager-symfony.com');

    yield "sed -i 's/{\$input.host}/{$host}/g' mager.yaml";
    yield "sed -i 's/{\$input.host}/{$host}/g' mager.dev.yaml";

    yield "sed -i 's/{\$input.php-version}/{$version}/g' Dockerfile";

    if (file_exists('./composer.json')) {
        try {
            yield 'composer require runtime/frankenphp-symfony';
            yield "composer config --json extra.symfony.docker 'true'";
        } catch (\Exception) {
            $composerCommand = <<<SCRIPT
            composer require runtime/frankenphp-symfony
            composer config --json extra.symfony.docker 'true'
            SCRIPT;
            $io->warning('Composer not installed yet, please run this following command manually');
            $io->writeln($composerCommand);
        }
    }
};
