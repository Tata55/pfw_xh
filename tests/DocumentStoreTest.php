<?php

/**
 * The plugin framework
 */
namespace Pfw;

use org\bovigo\vfs\vfsStream;

class DocumentStoreTest extends \PHPUnit_Framework_TestCase
{
    const BASENAME = 'foo.txt';

    const CONTENTS = 'some text';

    private $root;

    private $subject;

    private $filename;

    public function setUp()
    {
        $this->root = vfsStream::setup();
        $this->subject = new DocumentStore($this->root->url());
        $this->filename = $this->root->url() . '/' . self::BASENAME;
    }

    public function testInsert()
    {
        $document = new Document(self::CONTENTS);
        $this->assertTrue($this->subject->insert(self::BASENAME, $document));

        $this->assertFileExists($this->filename);
    }

    public function testCanNotInsertExisting()
    {
        file_put_contents($this->filename, self::CONTENTS);
        $document = new Document('another text');
        $this->assertFalse(
            @$this->subject->insert(self::BASENAME, $document)
        );
        $this->assertEquals(self::CONTENTS, file_get_contents($this->filename));
    }

    public function testFind()
    {
        file_put_contents($this->filename, self::CONTENTS);
        $document = $this->subject->find(self::BASENAME);
        $this->assertEquals(self::CONTENTS, $document->contents());
    }

    public function testFindNonExisting()
    {
        $this->assertFalse(
            @$this->subject->find(self::BASENAME)
        );
    }

    public function testUpdate()
    {
        file_put_contents($this->filename, self::CONTENTS);
        $document = $this->subject->find(self::BASENAME);
        $document = new Document('another text', $document->token());
        $this->assertTrue(
            $this->subject->update(self::BASENAME, $document)
        );
        $this->assertEquals('another text', file_get_contents($this->filename));
    }

    public function testCantUpdateNonExisting()
    {
        $document = new Document(self::CONTENTS);
        $this->assertFalse(
            @$this->subject->update(self::BASENAME, $document)
        );
    }

    public function testUpdateFailsDueToOfflineConcurrency()
    {
        file_put_contents($this->filename, self::CONTENTS);
        $document = $this->subject->find(self::BASENAME);
        file_put_contents($this->filename, 'another text');
        $this->assertFalse(
            $this->subject->update(self::BASENAME, $document)
        );
        $this->assertEquals('another text', file_get_contents($this->filename));
    }

    public function testDelete()
    {
        file_put_contents($this->filename, self::CONTENTS);
        $document = $this->subject->find(self::BASENAME);
        $this->assertTrue(
            $this->subject->delete(self::BASENAME, $document)
        );
        $this->assertFileNotExists($this->filename);
    }

    public function testCantDeleteNonExisting()
    {
        $document = new Document(self::CONTENTS);
        $this->assertFalse(
            @$this->subject->delete(self::BASENAME, $document)
        );
    }

    public function testDeleteFailsDueToOfflineConcurrency()
    {
        file_put_contents($this->filename, self::CONTENTS);
        $document = $this->subject->find(self::BASENAME);
        file_put_contents($this->filename, 'another text');
        $this->assertFalse(
            $this->subject->delete(self::BASENAME, $document)
        );
        $this->assertFileExists($this->filename);
    }
}
