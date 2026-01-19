<?php

namespace Ksfraser\Prefs\Tests\Unit;

use Ksfraser\Prefs\Schema\PrefsSchema;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/TestDoubles.php';

class PrefsSchemaTest extends TestCase
{
    public function testApplyDefaultsOnlySetsMissing(): void
    {
        $schema = (new PrefsSchema())
            ->addKey('a', 1, true)
            ->addKey('b', 'x', false);

        $store = new ArrayPrefsStore(['a' => 999]);

        $count = $schema->applyDefaults($store);

        $this->assertSame(1, $count);
        $this->assertSame(999, $store->get('a'));
        $this->assertSame('x', $store->get('b'));
    }
}
