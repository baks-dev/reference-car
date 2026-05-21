# BaksDev Reference Car

[![Version](https://img.shields.io/badge/version-7.4.5-blue)](https://github.com/baks-dev/reference-car/releases)
![php 8.4+](https://img.shields.io/badge/php-min%208.4-red.svg)
[![packagist](https://img.shields.io/badge/packagist-green)](https://packagist.org/packages/baks-dev/reference-car)

Библиотека автомобилей (Бренды, модели, параметры)

## Установка

``` bash
$ composer require baks-dev/reference-car
```

## Настройки

Для парсинга необходимо установить пакет Symfony Panther.

``` bash
$ composer require --dev symfony/panther
```

Также будет необходимо установить веб-драйвера:

``` bash
$ composer require --dev dbrekelmans/bdi

$ vendor/bin/bdi detect drivers
```

Как альтернативный вариант, это можно сделать с помощью пакетного менеджера:

``` bash
# Ubuntu
$ apt-get install chromium-chromedriver firefox-geckodriver

# MacOS, using Homebrew
$ brew install chromedriver geckodriver

# Windows, using Chocolatey
$ choco install chromedriver selenium-gecko-driver
```

## Журнал изменений ![Changelog](https://img.shields.io/badge/changelog-yellow)

О том, что изменилось за последнее время, обратитесь к [CHANGELOG](CHANGELOG.md) за дополнительной информацией.

## Лицензия ![License](https://img.shields.io/badge/MIT-green)

The MIT License (MIT). Обратитесь к [Файлу лицензии](LICENSE.md) за дополнительной информацией.
