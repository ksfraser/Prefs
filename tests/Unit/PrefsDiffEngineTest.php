<?php

namespace Ksfraser\Prefs\Tests\Unit;

use Ksfraser\Prefs\Schema\PrefsSchema;
use Ksfraser\Prefs\Sync\PrefsDiffEngine;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/TestDoubles.php';

class PrefsDiffEngineTest extends TestCase
{
    public function testDiffDetectsOnlyInAndDifferentAndSame(): void
    {
        $from = new ArrayPrefsStore([
            'a' => 1,
            'b' => 'same',
            'c' => 'from',
        ]);
        $to = new ArrayPrefsStore([
            'b' => 'same',
            'c' => 'to',
            'd' => true,
        ]);

        $schema = (new PrefsSchema())
            ->addKey('a', null, true)
            ->addKey('d', null, true)
            ->addKey('e', 'default', true);

        $diff = (new PrefsDiffEngine())->diff($from, $to, null, null, $schema);

        $this->assertArrayHasKey('a', $diff['onlyInFrom']);
        $this->assertArrayHasKey('d', $diff['onlyInTo']);
        $this->assertArrayHasKey('c', $diff['different']);
        $this->assertArrayHasKey('b', $diff['same']);

        $this->assertContains('e', $diff['missingRequiredInFrom']);
        $this->assertContains('e', $diff['missingRequiredInTo']);
        $this->assertNotContains('a', $diff['missingRequiredInFrom']);
        $this->assertNotContains('d', $diff['missingRequiredInTo']);
    }

    public function testDiffRespectsPrefix(): void
    {
        $from = new ArrayPrefsStore([
            'p.x' => 1,
            'q.y' => 2,
        ]);
        $to = new ArrayPrefsStore([
            'p.x' => 1,
            'p.z' => 3,
        ]);

        $diff = (new PrefsDiffEngine())->diff($from, $to, null, 'p.', null);

        $this->assertArrayNotHasKey('q.y', $diff['onlyInFrom']);
        $this->assertArrayHasKey('p.z', $diff['onlyInTo']);
        $this->assertArrayHasKey('p.x', $diff['same']);
    }
}
