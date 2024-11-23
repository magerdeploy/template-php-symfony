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

    $image = $io->ask('Image Name', 'username/your-image');
    $host = $io->ask('Host', 'mager-symfony.com');

    yield <<<CMD
        sed -i 's/{\$input.image}/{$image}/g' mager.yaml
        sed -i 's/{\$input.host}/{$host}/g' mager.yaml
        sed -i 's/{\$input.host}/{$host}/g' mager.dev.yaml
        sed -i 's/{\$input.php-version}/{$version}/g' Dockerfile"
    CMD;

    $composerCommand = <<<SCRIPT
        composer require runtime/frankenphp-symfony
        composer config --json extra.symfony.docker 'true'
    SCRIPT;
    if (file_exists('./composer.json')) {
        try {
            yield $composerCommand;
        } catch (\Exception) {
            $io->warning('Composer not installed yet, please run this following command manually');
            $io->writeln($composerCommand);
        }
    }
};
