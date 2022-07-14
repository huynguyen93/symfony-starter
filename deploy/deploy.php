<?php

namespace Deployer;

require __DIR__. '/vendor/deployer/deployer/recipe/common.php';

set('repository', '...');
set('app_path', '{{release_or_current_path}}/apps/main');
set('bin/console', '{{bin/php}} {{app_path}}/bin/console');

add('shared_files', [
    'apps/main/.env.local',
]);
add('shared_dirs', [
    'apps/main/var/log',
]);

host('...')
    ->set('remote_user', '...')
    ->set('deploy_path', '...');

task('composer:install', function () {
    run('cd {{app_path}} && {{bin/composer}} install --verbose --prefer-dist --no-progress --no-interaction 2>&1');
});

task('composer:dump-env', function () {
    run('cd {{app_path}} && {{bin/composer}} dump-env 2>&1');
});

task('cache:warmup', function () {
    run('{{bin/console}} c:w');
});

task('database:migrate', function () {
    run("{{bin/console}} doctrine:migrations:migrate --no-interaction");
});

task('supervisor:restart', function () {
    run('supervisorctl restart all');
});

task('deploy', [
    'deploy:prepare',
    'composer:install',
    'composer:dump-env',
    'cache:warmup',
    'database:migrate',
    'deploy:publish',
    'supervisor:restart'
]);

after('deploy:failed', 'deploy:unlock');
