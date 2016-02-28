# humble-session

[![Latest Version](https://img.shields.io/github/release/humblephp/humble-session.svg)](https://github.com/humblephp/humble-session/releases)
[![Software License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE.md)
[![Build Status](https://api.travis-ci.org/humblephp/humble-session.svg?branch=master)](https://travis-ci.org/humblephp/humble-session)

HUMBLE Session

## Install

Via Composer

``` bash
$ composer require humble/session
```

## Usage

Start PHP Session.
```
\Humble\Session\Session::start();
```

Start PHP Session with custom cookie params.
```
\Humble\Session\Session::start(array('lifetime' => 3600));
```

Create table for Pdo Session.
```
CREATE TABLE `sessions` (
  `id` char(26) NOT NULL,
  `time` int(10) unsigned NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

Start Pdo Session.
```
$handler = new \Humble\Session\PdoSessionHandler($pdo);
\Humble\Session\Session::start(array('lifetime' => 3600), $handler);
```

Get Pdo Session Handler with custom settings.
```
$handler = new \Humble\Session\PdoSessionHandler($pdo, [
    'tableName' => 'sessions',
    'idField' => 'id',
    'timeField' => 'time',
    'dataField' => 'data',
]);
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
