A Simple Iterator library

### Installation

Since this package is not hosted on packagist, you must add it manaually to your composer.json

```json
"repositories":[
    {
    "type": "vcs",
    "url": "git@github.com:robtesch/iterable-paginator.git"
    }
],
"require": {
    //other dependencies
    "robtesch/iterable-paginator": "dev-master"
}
```

After this, run:

```bash
composer update
```

### Usage

This library aims to provide a simple pagination for iterables including arrays and Traversable objects

Basic usage:

```php
use Robtesch\IterablePaginator\Paginator;

$arrayToPaginate = range(1,100);
$perPage = 15;
$page = 1;

//You can pass the page you want either as a 4th parameter to the paginator like this
$pagination = (new Paginator($arrayToPaginate, $perPage, $page))->paginate();
//outputs [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15]

//Or as a parameter to the paginate method, like this
$pagination = (new Paginator($arrayToPaginate, $perPage))->paginate($page);
//outputs [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15]
```

To get an array representation of the paginator, you can also use the "toArray" method on the object. This might be useful when returning the results as part of an API response,
because it shows the user more contextual information about where they are in the total result set.

```php
use Robtesch\IterablePaginator\Paginator;

...

$animals = [
    'chimp',
    'turtle',
    'chicken',
    'pig',
    'dog',
    'frog',
    'ostrich',
    'cow',
    'elephant',
    'giraffe'
];

$perPage = 3;
$page = 2;

$pagination = (new Paginator($animals, $perPage, $page))->toArray();

//outputs
//[
//    "current_page" => 2,
//    "data"         => ['pig', 'dog', 'frog'],
//    "from"         => 4,
//    "to"           => 6,
//    "last_page"    => 4,
//    "per_page"     => 3,
//    "total"        => 10,
//];

```

### Testing

```bash
phpunit tests
```
