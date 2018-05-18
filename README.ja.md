# Ray.QueryModule
[![Code Coverage](https://scrutinizer-ci.com/g/bearsunday/BEAR.QueryRepository/badges/coverage.png?b=1.x)](https://scrutinizer-ci.com/g/bearsunday/BEAR.QueryRepository/?branch=1.x)
[![Code Coverage](https://scrutinizer-ci.com/g/bearsunday/BEAR.QueryRepository/badges/coverage.png?b=1.x)](https://scrutinizer-ci.com/g/bearsunday/BEAR.QueryRepository/?branch=1.x)
[![Build Status](https://travis-ci.org/ray-di/Ray.QueryModule.svg?branch=1.x)](https://travis-ci.org/ray-di/Ray.QueryModule)

[English](README.md)

## 概要

`Ray.QueryModule`はデータベースなど外部メディアへの問い合わせを、インジェクトされる関数オブジェクトで行うようにします。

 * `SqlQueryModule`はDBに特化していています。SQLファイルをそのSQLを実行する単純な関数オブジェクトに変換します。
 * `PhpQueryModule`は汎用のモジュールです。静的な変換では提供できないストレージアクセスをPHPの関数オブジェクトとして提供します。

## モチベーション

 * コードにドメイン層（利用コード）とインフラストラクチャ層（インジェクトされる関数）の明確な境界を持たせることが出来ます。
 * 実行オブジェクトは自動で生成されるので実行のための手続きコードを記述する必要がありません。
 * 利用コードは外部メディアの実態に無関心なので、ストレージを後で変更することができます。平行開発やスタブ化が容易です。


## インストール

### Composerインストール

    $ composer require ray/query-module ^0.1
 
### Moduleインストール

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

### SQLファイル

$sqlDir/**todo_insert.sql**

```sql
INSERT INTO todo (id, title) VALUES (:id, :title)
```

$sqlDir/**todo_item_by_id.sql**

```sql
SELECT * FROM todo WHERE id = :id
```

## 利用

## callableオブジェクトとしてインジェクション

SQLのファイル名によって束縛されたSQL実行関数がインジェクトされます。
例えば以下の例なら、`todo_insert.sql`ファイルが`$createTodo`の実行オブジェクトに変換されインジェクトされます

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
## 単一行と複数行

得られる値が`単一行(Row)`か`複数行(Rowのリスト`)に応じて`RowInterface`か`RowListInterface`を指定することができます。

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
        $todo = ($this->todo)(['id' => $uuid]); // 単一行
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
        $todos = ($this->todos)(); // 複数行
    }
}
```

## メソッドをSQL実行に置き換える

`@AliasQuery`でメソッド全体をSQLの実行に置き換えることができます。

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

メソッド引数とSQLのバインドする変数名が違う時は`templated=true`を指定すると`uri_template`と同じように変数名を変えることができます。

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

単一行の時は`type='row'`を指定します。

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

## デモ

```
php demo/run.php
```

## BEAR.Sunday

 * [Koriym.Ticketsan](https://github.com/koriym/Koriym.TicketSan/blob/master/src/Resource/App/Ticket.php)

