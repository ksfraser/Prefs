<?php

namespace Ksfraser\Prefs\Tests\Unit;

use Ksfraser\Prefs\Sync\PrefsSynchronizer;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/TestDoubles.php';

class PrefsSynchronizerTest extends TestCase
{
    public function testSyncWithExplicitKeysCopiesValues(): void
    {
        $from = new ArrayPrefsStore(['a' => 1, 'b' => 2]);
        $to = new ArrayPrefsStore();

        $count = (new PrefsSynchronizer())->sync($from, $to, ['a', 'b']);

        $this->assertSame(2, $count);
        $this->assertSame(1, $to->get('a'));
        $this->assertSame(2, $to->get('b'));
    }

    public function testSyncWithPrefixUsesAll(): void
    {
        $from = new ArrayPrefsStore(['p.a' => 1, 'x.b' => 2]);
        $to = new ArrayPrefsStore();

        $count = (new PrefsSynchronizer())->sync($from, $to, null, 'p.');

        $this->assertSame(1, $count);
        $this->assertTrue($to->has('p.a'));
        $this->assertFalse($to->has('x.b'));
    }

    public function testDeleteKeysDeletesExistingOnly(): void
    {
        $store = new ArrayPrefsStore(['a' => 1, 'b' => 2]);

        $count = (new PrefsSynchronizer())->deleteKeys($store, ['a', 'nope', 'b']);

        $this->assertSame(2, $count);
        $this->assertFalse($store->has('a'));
        $this->assertFalse($store->has('b'));
    }
}
