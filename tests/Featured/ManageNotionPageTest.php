<?php

namespace Pi\Notion\Tests\Featured;

use Illuminate\Support\Collection;
use Pi\Notion\NotionBlock;
use Pi\Notion\NotionDatabase;
use Pi\Notion\NotionPage;
use Pi\Notion\NotionProperty;
use Pi\Notion\Tests\TestCase;

class ManageNotionPageTest extends TestCase
{
    private string $notionPageId = 'b4f8e429038744ca9c8d5afa93ea2edd';
    private string $notionDatabaseId = '632b5fb7e06c4404ae12065c48280e4c';

    /** @test */
    public function it_should_return_page_info()
    {
        $this->withoutExceptionHandling();

        $page = new NotionPage('b4f8e429038744ca9c8d5afa93ea2edd');

        $this->assertObjectHasAttribute('objectType', $page);


    }
    /** @test */
    public function it_should_return_page_info_with_blocks()
    {
        $this->withoutExceptionHandling();

        $page = new NotionPage('b4f8e429038744ca9c8d5afa93ea2edd');

        $this->assertObjectHasAttribute('objectType', $page);


    }
    /** @test */
    public function i_can_create_a_page_object()
    {
        $page = new NotionPage();
        $page->setDatabaseId($this->notionDatabaseId);
        $page->create();

        $this->assertObjectHasAttribute('objectType', $page);

    }
    /** @test */
    public function it_can_delete_a_page_object()
    {
        $page = (new NotionPage('ec9df16fa65f4eef96776ee41ee3d4d4'))
            ->delete();
        $this->assertObjectHasAttribute('objectType', $page);

    }
    /** @test */
    public function it_should_add_properties_to_created_page_using_page_class()
    {

        $page = new NotionPage();
        $page->setDatabaseId($this->notionDatabaseId);

        $page
            ->title('Name','Eyad Hamza')
            ->select('Status', 'A')
            ->multiSelect('Status1', ['A', 'B'])
            ->date('Date', [
                'start' => "2020-12-08T12:00:00Z",
                'end' => "2020-12-08T12:00:00Z",
            ])->email('Email', 'eyad@outlook.com')
            ->phone('Phone', '123456789')
            ->url('Url', 'https://www.google.com')
            ->create();


        $this->assertCount(7, $page->getProperties());

        $this->assertObjectHasAttribute('properties', $page);
    }

    /** @test */
    public function it_should_update_properties_to_created_page_using_page_class()
    {
        $page = new NotionPage('b4f8e429038744ca9c8d5afa93ea2edd');
        $response = $page
            ->select('Status', 'In Progress')
            ->update();

        $this->assertObjectHasAttribute('properties', $page);
    }
    /** @test */
    public function it_should_add_properties_to_created_page()
    {
        $page = new NotionPage();
        $page->setDatabaseId($this->notionDatabaseId);

         $page->setProperties([
            NotionProperty::title('Name', 'Eyad Hamza'),
            NotionProperty::multiSelect('Status1', ['A', 'B']),
            NotionProperty::select('Status', 'A'),
            NotionProperty::date('Date', [
                'start' => "2020-12-08T12:00:00Z",
                'end' => "2020-12-08T12:00:00Z",
            ]),
            NotionProperty::url('Url','https://developers.notion.com'),
            NotionProperty::email('Email','Eyadhamza0@outlook.com'),
            NotionProperty::phone()->setValues('0123456789')
        ])->create();
        $this->assertCount(7, $page->getProperties());
        $this->assertObjectHasAttribute('properties', $page);
    }

    /** @test */
    public function it_can_add_content_blocks_to_created_pages()
    {
        $page = (new NotionPage);
        $page->setDatabaseId($this->notionDatabaseId);

        $page
            ->title('Name','Eyad Hamza')
            ->multiSelect('Status1', ['A', 'B']);

        $page->setBlocks([
            NotionBlock::headingOne('Heading 1'),
            NotionBlock::headingTwo('Heading 2'),
            NotionBlock::headingThree('Heading 3'),
            NotionBlock::numberedList('Numbered List'),
            NotionBlock::bulletedList('Bullet List'),
        ]);

        $page->create();
        $this->assertCount(2, $page->getProperties());
        $this->assertCount(5, $page->getBlocks());
        $this->assertObjectHasAttribute('properties', $page);


    }
    /** @test */
    public function it_can_add_nested_content_blocks_to_created_pages()
    {
        $page = (new NotionPage);
        $page->setDatabaseId($this->notionDatabaseId);

        $page
            ->title('Name','Eyad Hamza')
            ->multiSelect('Status1', ['A', 'B']);

        $page->setBlocks([
            NotionBlock::paragraph('asdasdasd')
                ->color('red')
                ->addChildren([
                    NotionBlock::headingTwo('Heading 2')
                        ->color('blue')
                        ->contentLink('https://www.google.com'),
                    NotionBlock::headingThree('Heading 3'),
                    NotionBlock::numberedList('Numbered List'),
                    NotionBlock::bulletedList('Bullet List'),
                ]),
            NotionBlock::headingTwo('Heading 2'),
            NotionBlock::headingThree('Heading 3'),
            NotionBlock::numberedList('Numbered List'),
            NotionBlock::bulletedList('Bullet List'),
        ]);

        $this->assertCount(2, $page->getProperties());
        $this->assertCount(5, $page->getBlocks());
        $this->assertObjectHasAttribute('properties', $page);


    }
    /** @test */
    public function it_can_add_content_blocks_to_created_pages_using_page_class()
    {
        $page = new NotionPage;
        $page->setDatabaseId($this->notionDatabaseId);

        $page
            ->title('Name','Eyad Hamza')
            ->multiSelect('Status1', ['A', 'B'])
            ->headingOne('Heading 1')
            ->headingTwo('Heading 2')
            ->headingThree('Heading 3')
            ->numberedList('Numbered List')
            ->bulletedList('Bullet List')
            ->create();
        $this->assertCount(2, $page->getProperties());
        $this->assertCount(5, $page->getBlocks());
        $this->assertObjectHasAttribute('properties', $page);


    }
    /** @test */
    public function it_returns_search_result()
    {
        $response = (new NotionPage)
            ->search('Eyad');
        $this->assertInstanceOf(Collection::class, $response);
    }


}
