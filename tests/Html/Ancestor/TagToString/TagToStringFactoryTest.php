<?php declare(strict_types=1);

namespace DaisyDiff\Html\Ancestor\TagToString;

use DaisyDiff\Html\Ancestor\TagChangeSemantic;
use DaisyDiff\Html\Dom\TagNode;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * TagToStringFactory Tests.
 */
class TagToStringFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $factory = new TagToStringFactory();

        $root  = new TagNode(null, 'html');
        $link  = new TagNode(null, 'a');
        $image = new TagNode(null, 'img');

        $this->assertEquals('Html page', $factory->create($root)->getDescription());
        $this->assertEquals('Link', $factory->create($link)->getDescription());
        $this->assertEquals('Image', $factory->create($image)->getDescription());
    }

    public function testGetChangeSemantic(): void
    {
        $factory = new TagToStringFactory();

        $refMethod = new ReflectionMethod($factory, 'getChangeSemantic');
        $refMethod->setAccessible(true);

        $this->assertEquals(TagChangeSemantic::MOVED, $refMethod->invoke($factory, 'table'));
        $this->assertEquals(TagChangeSemantic::STYLE, $refMethod->invoke($factory, 'big'));
        $this->assertEquals(TagChangeSemantic::UNKNOWN, $refMethod->invoke($factory, 'unknown'));
    }
}