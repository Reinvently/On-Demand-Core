Create new project
==================

Create project folder
---------------------

    mkdir project
    cd project

Download Yii
------------

    composer create-project --prefer-dist yiisoft/yii2-app-basic ./

Update composer.json
------------

    "repositories": [
            {
                "type": "vcs",
                "url": "git@github.com:Reinvently/On-Demand-Core.git"
            }
    ],
    "require": {
            "reinvently/on-demand/core": "dev-master"
    }

Composer
--------

    composer update








Deploy existing project
=======================

Create project folder
---------------------

    mkdir project
    cd project

Composer
--------

    composer self-update
    composer global require "fxp/composer-asset-plugin:1.0.0-beta3"
    composer update

Permissions
-----------

    chmod 777 runtime/ web/assets

Update composer.json
------------

    "repositories": [
        ...,
        {
            "type": "vcs",
            "url": "git@github.com:Reinvently/On-Demand-Core.git"
        }
    ],
    "require": {
        ...,    
        "reinvently/on-demand/core": "dev-master"
    }

Composer
--------

    composer update

