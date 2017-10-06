<?php

namespace Tests\Framework\Database;


use Framework\Database\Query;
use Tests\DatabaseTestCase;

class QueryTest extends DatabaseTestCase
{
    public function testSimpleQuery()
    {
        $query = (new Query())->from('posts')->select('name');
        $this->assertEquals('SELECT name FROM posts', (string)$query);
    }

    public function testWithWhere()
    {
        $query = (new Query())
            ->from('posts', 'p')
            ->where('a = :a OR b = :b', "c = :c");
        $query2 = (new Query())
            ->from('posts', 'p')
            ->where('a = :a OR b = :b', "c = :c")
            ->where("c = :c");
        $this->assertEquals('SELECT * FROM posts AS p WHERE (a = :a OR b = :b) AND (c = :c)', (string)$query);
        $this->assertEquals('SELECT * FROM posts AS p WHERE (a = :a OR b = :b) AND (c = :c)', (string)$query2);
    }

    public function testFetchAll()
    {
        $tests = (new Query($this->getPdo()))
            ->from('test', 'p')
            ->count();

        $tests2 = (new Query($this->getPdo()))
            ->from('test', 'p')
            ->where('p.id < :number')
            ->params([
                "number" => 2
            ])
            ->count();
        $this->assertEquals(3, $tests);
        $this->assertEquals(1, $tests2);
    }

    public function testHydrateEntity()
    {
        $tests = (new Query($this->getPdo()))
            ->from('posts', 'p')
            ->into(Demo::class)
            ->all();
        $this->assertEquals('demo', substr($tests[0]->getSlug(), -4));
    }


    public function testLazyHydrate()
    {
        $tests = (new Query($this->getPdo()))
            ->from('test', 'p')
            ->into(Demo::class)
            ->all();
        $test1 = $tests[0];
        $test2 = $tests[0];
        $this->assertSame($test1, $test2);
    }

    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $pdo = $this->getPdo();
        $pdo->exec("CREATE TABLE IF NOT EXISTS test (
            id INTEGER PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(255)
          )");
        $pdo->exec("INSERT INTO test (name) VALUES (\"ok\")");
        $pdo->exec("INSERT INTO test (name) VALUES (\"ok\")");
        $pdo->exec("INSERT INTO test (name) VALUES (\"ok\")");
    }

    public function tearDown()
    {
        $pdo = $this->getPdo();
        $pdo->exec("DROP TABLE IF EXISTS test ");
        parent::tearDown(); // TODO: Change the autogenerated stub
    }
}