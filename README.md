# Push server for HromadskeTV app

Sends push notifications to the HromadskeTV apps.

[![Build Status](https://travis-ci.org/mcfedr/hrompushserver.svg?branch=master)](https://travis-ci.org/mcfedr/hrompushserver)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/c05e89cb-3dc5-4571-a2e0-c981ecb6986e/mini.png)](https://insight.sensiolabs.com/projects/c05e89cb-3dc5-4571-a2e0-c981ecb6986e)

## Updating a server

    git pull
    composer install

You will be pleased to know that cache:clear doesn't quite live up to your hopes

    rm -rf app/cache/prod/*

I know its called clear, but actually it focuses more on creating.

    ./app/console cache:clear --env=prod

Clears APC cache

    sudo service apache2 restart
    
Restart the daemons

    sudo supervisorctl reload
