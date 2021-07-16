# Ray.QueryModule

[![codecov](https://codecov.io/gh/ray-di/Ray.QueryModule/branch/1.x/graph/badge.svg?token=60G2MFDOBR)](https://codecov.io/gh/ray-di/Ray.QueryModule)
[![Type Coverage](https://shepherd.dev/github/ray-di/Ray.QueryModule/coverage.svg)](https://shepherd.dev/github/ray-di/Ray.QueryModule)
![Continuous Integration](https://github.com/ray-di/Ray.QueryModule/workflows/Continuous%20Integration/badge.svg)

[Japanese](README.ja.md)

## Overview

`Ray.QueryModule` makes a query to an external media such as a database or Web API with a function object to be injected.

 * `SqlQueryModule` is for DB. Convert the SQL file to a simple function object that executes that SQL.
 * `WebQueryModule` is for the Web API. Convert the URI and method set into a simple function object that Web requests to that URI.
 * `PhpQueryModule` is a generic module. It provides storage access which can not be provided by static conversion by PHP function object.


## Motivation

 * You can have a clear boundary between domain layer (usage code) and infrastructure layer (injected function) in code.
 * Execution objects are generated automatically so you do not need to write procedural code for execution.
 * Since usage codes are indifferent to the actual state of external media, storage can be changed later. Easy parallel development and stabbing.

## Installation

### Composer install

    $ composer require ray/query-module
 
### Module install

```php
use Ray\Di\AbstractModule;
use Ray\Query\SqlQueryModule;

class AppModule extends AbstractModule
{
    protected function configure()
    {
        // SqlQueryModule install
        $this->install(new SqlQueryModule($sqlDir));

        // WebQueryModule install
        $webQueryConfig = [
            'post_todo' => ['POST', 'https://httpbin.org/todo'], // bind-name => [method, uri]
            'get_todo' => ['GET', 'https://httpbin.org/todo']
        ];
        $guzzleConfig = []; // @see http://docs.guzzlephp.org/en/stable/request-options.html
        $this->install(new WebQueryModule($webQueryConfig, $guzzleConfig));
    }
}
```

### SQL files

$sqlDir/**todo_insert.sql**

```sql
INSERT INTO todo (id, title) VALUES (:id, :title)
```

$sqlDir/**todo_item_by_id.sql**

```sql
SELECT * FROM todo WHERE id = :id
```

## Convert SQL to SQL invocation object


A callable object injected into the constructor. Those object was made in specified sql with `@Named` binding.

```php
class Todo
{
    /**
     * @var callable
     */
    private $createTodo;
    
    /**
     * @var callable
     */
    private $todo;
    
    /**
     * @Named("createTodo=todo_insert, todo=todo_item_by_id")
     */
    public function __construct(
        callable $createTodo,
        callable $todo
    ){
        $this->createTodo = $createTodo;
        $this->todo = $todo;
    }
    
    public function get(string $uuid)
    {
        return ($this->todo)(['id' => $uuid]);
    }

    public function create(string $uuid, string $title)
    {
        ($this->createTodo)([
            'id' => $uuid,
            'title' => $title
        ]);
    }
}
```
## Row or RowList

You can specify expected return value type is either `Row` or `RowList` with `RowInterface` or `RowListInterface`. 
`RowInterface` is handy to specify SQL which return single row.

```php
use Ray\Query\RowInterface;

class Todo
{
    /**
     * @Named("todo_item_by_id")
     */
    public function __construct(RowInterface $todo)
    {
        $this->todo = $todo;
    }
    
    public function get(string $uuid)
    {
        $todo = ($this->todo)(['id' => $uuid]); // single row data
    }
}
```

```php
use Ray\Query\RowListInterface;

class Todos
{
    /**
     * @Named("todos")
     */
    public function __construct(RowListInterface $todos)
    {
        $this->todos = $todos;
    }
    
    public function get(string $uuid)
    {
        $todos = ($this->todos)(); // multiple row data
    }
}
```

## Override the method with callable object

Entire method invocation can be override with callable object in specified with `@Query`.

```php
class Foo
{
    /**
     * @Query(id="todo_item_by_id")
     */
    public function get(string $id)
    {
    }
}
```

When parameter name is different method arguments and Query object arguments, uri_template style expression can solve it.

```php
class FooTempalted
{
    /**
     * @Query(id="todo_item_by_id?id={a}", templated=true)
     */
    public function get(string $a)
    {
    }
}
```

Specify `type='row'` when single row result is expected to return.

```php
class FooRow
{
    /**
     * @Query(id="ticket_item_by_id", type="row")
     */
    public function onGet(string $id) : ResourceObject
    {
    }
}
```

If there is no SELECT result, it returns `404 Not Found`.

## Convert URI to Web request object

With `WebQueryModule`, it converts the URI bound in the configuration into an invocation object for web access and injects it.
In the following example, an invocation object of `$createTodo` which makes` POST` request to `https://httpbin.org/todo` is injected as `$createTodo`.

```php
use Ray\Di\AbstractModule;
use Ray\Query\SqlQueryModule;

class AppModule extends AbstractModule
{
    protected function configure()
    {
        // WebQueryModuleインストール
        $webQueryConfig = [
            'todo_post' => ['POST', 'https://httpbin.org/todo'],
            'todo_get' => ['GET', 'https://httpbin.org/todo']
        ];
        $guzzleConfig = [];
        $this->install(new WebQueryModule($webQueryConfig, $guzzleConfig));
    }
}
```

The usage code is the same as for `SqlQueryModule`.


```php
/**
 * @Named("createTodo=todo_post, todo=todo_get")
 */
public function __construct(
    callable $createTodo,
    callable $todo
){
    $this->createTodo = $createTodo;
    $this->todo = $todo;
}
```

```php
// POST
($this->createTodo)([
    'id' => $uuid,
    'title' => $title
]);

// GET
($this->todo)(['id' => $uuid]);
```

The usage code of `@Query` does not change either.

## Bind to PHP class

If other dependencies are needed, we bind to PHP class and use dependency as a service.

```php
class CreateTodo implements QueryInterface
{
    private $pdo;
    private $builder;

    public function __construct(PdoInterface $pdo, QueryBuilderInferface $builder)
    {
        $this->pdo = $pdo;
        $this->builder = $builder;
    }

    public function __invoke(array $query)
    {
        // Query execution using $pdo and $builder
        return $result;
    }
}
```

Bind to `callable`.

```php
$this->bind('')->annotatedWith('cretate_todo')->to(CreateTodo::class); // callableはインターフェイスなし
```

The usage codes are the same. The usage code of `@Query` does not change either.

## ISO8601 DateTime Module

Convert the specified column name value to the [ISO8601](https://www.iso.org/iso-8601-date-and-time-format.html) format. In PHP, it is a format defined by constants of [DateTime::ATOM](https://www.php.net/manual/en/class.datetime.php#datetime.constants.atom).
Install date column names as an array and pass it as an argument to `Iso8601FormatModule`.

```php
$this->install(new Iso8601FormatModule(['created_at', 'updated_at']));
```

## SQL file name log

The SQL file name can be appended to the SQL statement as a comment. This is useful for query logging.

```php
use Ray\Query\SqlFileName;
use Ray\Query\SqlQueryModule;

$this->install(new SqlQueryModule(__DIR__ . '/Fake/sql', null, new SqlFileName()));
````

Execute SQL

```sql
/* todo_item_by_id.sql */ SELECT * FROM todo WHERE id = :id
````

## Demo

```
php demo/run.php
```

## BEAR.Sunday example

 * [Koriym.Ticketsan](https://github.com/koriym/Koriym.TicketSan/blob/master/src/Resource/App/Ticket.php)

