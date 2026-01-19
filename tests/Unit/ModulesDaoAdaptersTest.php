<?php

namespace Ksfraser\Prefs\Tests\Unit;

use Ksfraser\Prefs\Stores\ModulesDaoKeyValueCodecAdapter;
use Ksfraser\Prefs\Stores\ModulesDaoKeyValueStoreAdapter;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/TestDoubles.php';

class ModulesDaoAdaptersTest extends TestCase
{
    public function testPlainAdapterPassesThrough(): void
    {
        $dao = new ArrayDaoStore(['a' => '1']);
        $prefs = new ModulesDaoKeyValueStoreAdapter($dao);

        $this->assertTrue($prefs->has('a'));
        $this->assertSame('1', $prefs->get('a'));

        $prefs->set('b', '2');
        $this->assertSame('2', $prefs->get('b'));

        $prefs->delete('a');
        $this->assertFalse($prefs->has('a'));
    }

    public function testCodecAdapterEncodesAndDecodes(): void
    {
        $dao = new ArrayDaoStore();
        $prefs = new ModulesDaoKeyValueCodecAdapter($dao);

        $prefs->set('arr', ['x' => 1]);
        $this->assertSame(['x' => 1], $prefs->get('arr'));

        $this->assertSame(['arr' => ['x' => 1]], $prefs->all());
    }
}
