# PHP CS fixer

We install cs fixer separately as per the docs.

It does however check it's requirements against the root composer.json in ../../api. php-cs-fixer at the time of writing does not support symfony 6. Used [this workaround](https://github.com/FriendsOfPHP/PHP-CS-Fixer/pull/6095#issuecomment-982999742) to get it installed.

This pull request should introduce support, but its not fully resolved yet it seems: https://github.com/FriendsOfPHP/PHP-CS-Fixer/pull/6095

We also need to set the env var `PHP_CS_FIXER_IGNORE_ENV` to true in the docker-compose file otherwise php-cs-fixer will not run as we're on too high a version of php.