<?php
class A {
    public static function who() {
        echo __CLASS__ . '?';
    }
    public static function testThis() {
//        $this->who();
    }
    public static function testSelf() {
        self::who();
    }
    public static function testStatic() {
        static::who();
    }
}

class B extends A {
    public static function who() {
        echo __CLASS__ . '!';
    }

    public static function testThis() {
//        $this->who();
    }
    public static function testParent() {
        parent::who();
    }
}

A::testThis();  // A
echo "<br>";
A::testSelf();  // A
echo "<br>";
A::testStatic();// A
echo "<br>";

B::testThis();  // B
echo "<br>";
B::testSelf();  // A?
// self это псевдоним класса в котором он прописан
echo "<br>";
B::testStatic();// B!
// позднее статическое связывание
// вызывает поздний вызов
echo "<br>";

B::testParent();// A
echo "<br>";
A::testStatic();// B!