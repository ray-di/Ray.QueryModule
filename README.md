# Ray.QueryModule
[![Code Coverage](https://scrutinizer-ci.com/g/bearsunday/BEAR.QueryRepository/badges/coverage.png?b=1.x)](https://scrutinizer-ci.com/g/bearsunday/BEAR.QueryRepository/?branch=1.x)
[![Code Coverage](https://scrutinizer-ci.com/g/bearsunday/BEAR.QueryRepository/badges/coverage.png?b=1.x)](https://scrutinizer-ci.com/g/bearsunday/BEAR.QueryRepository/?branch=1.x)
[![Build Status](https://travis-ci.org/ray-di/Ray.QueryModule.svg?branch=1.x)](https://travis-ci.org/ray-di/Ray.QueryModule)

[Japanese](README.ja.md)

## Overview

`Ray.QueryModule` makes a query to an external media such as a database or Web API with a function object to be injected.

 * `SqlQueryModule` is DB specialized. Convert the SQL file to a function object that executes that SQL.
 * `PhpQueryModule` is a generic module. It provides storage access which can not be provided by static conversion by PHP function object.


## Motivation

 * You can have a clear boundary between domain layer (usage code) and infrastructure layer (injected function) in code.
 * Execution objects are generated automatically so you do not need to write procedural code for execution.
 * Since usage codes are indifferent to the actual state of external media, storage can be changed later. Easy parallel development and stabbing.

## Installation

### Composer install

    $ composer require ray/query-module ^0.1
 
### Module install

```php
use Ray\Di\AbstractModule;
use Ray\Query\SqlQueryModule;

class AppModule extends AbstractModule
{
    protected function configure()
    {
        $this->install(new SqlQueryModule($sqlDir));
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

## Usage

## Inject callable object

A callable object injected into the constructor. Those object was made in specified sql with `@Named` binding.
For example in the following example, the `todo_insert.sql` file is converted and injected into the `$createTodo` execution object nd injected it into constructor.

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

Entire method invocation can be override with callable object in specified with `@AliasQuery`.

```php
class Foo
{
    /**
     * @AliasQuery(id="todo_item_by_id")
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
     * @AliasQuery(id="todo_item_by_id?id={a}", templated=true)
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
     * @AliasQuery(id="ticket_item_by_id", type="row")
     */
    public function onGet(string $id) : ResourceObject
    {
    }
}
```

## Demo

```
php demo/run.php
```

## BEAR.Sunday example

 * [Koriym.Ticketsan](https://github.com/koriym/Koriym.TicketSan/blob/master/src/Resource/App/Ticket.php)

