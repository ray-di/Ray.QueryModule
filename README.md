# Ray.QueryModule

## Overview

`Ray.QueryModule` makes a query to an external media such as a database or Web API with a function object to be injected.

 * `SqlQueryModule` is DB specialized. Convert the SQL file to a function object that executes that SQL.
 * `PhpQueryModule` is a generic module. It provides storage access which can not be provided by static conversion by PHP function object.


### motivation

 * You can have a clear boundary between domain layer (usage code) and infrastructure layer (injected function) in code.
 * Since usage codes are indifferent to the actual state of external media, storage can be changed later.
 * Easy parallel development and stabbing.

## 概要

`Ray.QueryModule`はデータベースなど外部メディアへの問い合わせを、インジェクトされる関数オブジェクトで行うようにします。

 * `SqlQueryModule`はDBに特化していています。SQLファイルをそのSQLを実行する関数オブジェクトに変換します。
 * `PhpQueryModule`は汎用のモジュールです。静的な変換では提供できないストレージアクセスをPHPの関数オブジェクトとして提供します。

### 利点

 * コードにドメイン層（利用コード）とインフラストラクチャ層（インジェクトされる関数）の明確な境界を持たせることが出来ます。
 * 利用コードは外部メディアの実態に無関心なので、ストレージを後で変更することができます。
 * 平行開発やスタブ化が容易です。
 

## Installation

### Composer install

    $ composer require ray/query-module 1.x-dev
 
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
## Item or List

You can speciafy expected return value type is eihter `item` or `list` with `ItemInterface` or `ListInterface`. 
`ItemInterface` is handy to specify SQL which return single row.

```php
use Ray\Query\ItemInterface;

class Todo
{
    /**
     * @Named("todo_item_by_id")
     */
    public function __construct(ItemInterface $todo)
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
use Ray\Query\ListInterface;

class Todos
{
    /**
     * @Named("todos")
     */
    public function __construct(ListInterface $todos)
    {
        $this->todos = $todos;
    }
    
    public function get(string $uuid)
    {
        $todos = ($this->todos)(['id' => $uuid]); // multiple row data
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

Specify `type='item'` when single row result is expected to return.

```php
class FooItem
{
    /**
     * @AliasQuery(id="ticket_item_by_id", type="item")
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

