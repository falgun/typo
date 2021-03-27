

# Typo

Type safe Query builder that tries to reduce typo.

## Install
 *Please note that PHP 8.0 or higher is required.*

Via Composer

``` bash
$ composer require falgunphp/typo
```
## Limitations
- Only supports MySQL/MariaDB.
- Currently has very tiny subset of SQL features available.
- Not stable for real usage.

## Usage

Before starting using this library, please note that:
- Typo tries to be a **Database First** ORM/Query Builder. You can't design DB schema with it. Schema related meta datas will be generated from existing database.
- It tries to stay as close as possible to SQL syntax structure. As a result, code will look too verbose.

To generate Metadatas for your DB, run (after installation):
```bash
vendor/bin/generate-typo-meta
```
Above script will ask for db credentials and metadata directory (where it will store files).
Currently, Metadata classes have default namespace defined as `App\DB`. You can change it to suit your need but keep in mind re-running the generator script will **overwrite** your changes.

Once metadatas are generated, we can start using Typo to build queries.

```php
<?php
use Falgun\Kuery\Kuery;
use App\DB\Metas\UsersMeta;
use Falgun\Typo\Query\Builder;
use Falgun\Kuery\Configuration;
use Falgun\Kuery\Connection\MySqlConnection;

$confArray = [
      'host' => 'localhost',
      'user' => 'username',
      'password' => 'password',
      'database' => 'falgun'
  ];

// build configuration class
$configuration = Configuration::fromArray($confArray);

// build connection class
$connection = new MySqlConnection($configuration);

// attemp to connect
$connection->connect();

//create the query builder
$builder = new Builder(new Kuery($connection));

//load user meta, this is auto generated class
$userMeta = UsersMeta::new();

/**************
* Select Query
**************/
$users = $builder
    ->select(
        $userMeta->id(),
        $userMeta->name()->as('full_name'),
        $userMeta->username(),
    )
    ->from($userMeta->table())
    ->where($userMeta->id()->eq(12))
    ->orWhere($userMeta->id()->eq(13))
    ->orderBy($userMeta->id())
    ->limit(0, 100)
    ->fetch();

// This one will run below SQL
SELECT users.id, users.name as full_name, users.username
FROM users
WHERE users.id = ? OR users.id = ?
ORDER BY users.id ASC
LIMIT 0, 100

/**************
* Insert Query
**************/
$userID = $builder->insertInto(
    $userMeta->table(),
    $userMeta->name(),
    $userMeta->username()
    )
    ->values('New User', 'NewUser')
    ->execute();

// It will run below SQL
INSERT INTO users (users.name, users.username) VALUES (?, ?)

/**************
* Update Query
**************/
$builder->update($userMeta->table())
    ->set($userMeta->name(), 'Updated Name')
    ->set($userMeta->username(), 'UpdatedName')
    ->where($userMeta->id()->eq(10))
    ->execute();

// It will run below SQL
UPDATE users
SET users.name = ?, users.username = ?
WHERE users.id = ?

/**************
* Delete Query
**************/
$qb->delete($userMeta->table())
    ->where($userMeta->id()->eq(5))
    ->execute();

// It will run below SQL
DELETE FROM users
WHERE users.id = ?
```

## License
This software is distributed under the [LGPL 3.0](http://www.gnu.org/licenses/lgpl-3.0.html) or later license. Please read [LICENSE](LICENSE) for information on the software availability and distribution.

