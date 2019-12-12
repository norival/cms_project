<?php

namespace App\Tests\Content;

use App\Content\Page;
use PHPUnit\Framework\TestCase;

class PageTest extends TestCase
{
    public function testNew()
    {
        $page = new Page();

        $this->assertInstanceOf(Page::class, $page);
        $this->assertObjectHasAttribute('title', $page);
        $this->assertObjectHasAttribute('content', $page);
    }
}
