<?php

namespace Pi\Notion\Tests\Featured;

use Illuminate\Support\Collection;
use Pi\Notion\Filter;
use Pi\Notion\NotionDatabase;

use Pi\Notion\Exceptions\NotionDatabaseException;
use Pi\Notion\NotionPage;
use Pi\Notion\Properties\MultiSelect;
use Pi\Notion\Properties\Select;
use Pi\Notion\Properties\Title;
use Pi\Notion\Query\MultiSelectFilter;
use Pi\Notion\Query\SelectFilter;
use Pi\Notion\Tests\TestCase;
use Pi\Notion\Workspace;

class ManageNotionDatabasesTest extends TestCase
{


    /** @test */
    public function return_database_info()
    {
        $response = (new NotionDatabase)->get('632b5fb7e06c4404ae12065c48280e4c');


        $this->assertObjectHasAttribute('properties', $response);

        $response = (new NotionDatabase)->get('632b5fb7e06c4404ae12065c48280e4c');

        $this->assertObjectHasAttribute('properties', $response);

    }

    /** @test */
    public function throw_exception_database_not_found()
    {
        $id = '632b5fb7e06c4404ae12asdasd065c48280e4asdc';

        $this->expectException(NotionDatabaseException::class);

        $response = (new NotionDatabase($id))->get();


    }

    /** @test */
    public function throw_exception_database_not_authorized()
    {
        $id = '632b5fb7e06c4404ae12065c48280e4asdc';
        $this->expectException(NotionDatabaseException::class);
        (new NotionDatabase($id))->get();


    }



    /** @test */
    public function test_database_can_be_filtered_with_one_filter()
    {

        $database = new NotionDatabase('632b5fb7e06c4404ae12065c48280e4c');

        $response = $database->filter(
            Filter::make('title', 'Name')
                ->apply('contains', 'MMMM')
        )->get();
        $this->assertArrayHasKey('results', $response);


    }

    /** @test */
    public function test_database_can_be_filtered_with_many_filters()
    {

        $database = new NotionDatabase('632b5fb7e06c4404ae12065c48280e4c');

        $response = $database->applyFilters([
            Filter::make('select', 'Status')
                ->apply('equals', 'Reading'),
            Filter::make('multi_select', 'Status2')
                ->apply('contains', 'A'),
            Filter::make('title', 'Name')
                ->apply('contains', 'MMMM'),
        ],'and')->get();

        $this->assertCount('1', $response['results']);
        $response = $database->applyFilters([
            Filter::make('select', 'Status')
                ->apply('equals', 'Reading'),
            Filter::make('multi_select', 'Status2')
                ->apply('contains', 'A'),
            Filter::make('title', 'Name')
                ->apply('contains', 'MMMM'),
        ],'or')->get();

        $this->assertCount('4', $response['results']);

    }

    public function test_database_can_be_filtered_with_many_filters_using_database_api()
    {


        $database
            ->ofSelect('Status1', 'B')
            ->ofMultiSelect('Status2', 'A')
            ->ofTitle('Name', 'A');

        $response = (new NotionDatabase('632b5fb7e06c4404ae12065c48280e4c'))->getContents($filters, filterType: 'and');
        $this->assertObjectHasAttribute('properties', $response);
    }


    /** @test */
    public function i_can_sort_database_results()
    {
        $filters = new Collection();
        $filters->add((new SelectFilter('Status'))->equals('Reading'))
            ->add((new SelectFilter('Publisher'))->equals('NYT'));


        $database = (new NotionDatabase('632b5fb7e06c4404ae12065c48280e4c'))->getContents($filters, filterType: 'and');

        $database->sort([
            Sort::ofType('select')->property('Status1')->ascending(),
            Sort::ofType('multi_select')->property('Status2')->descending(),
            Sort::ofType('title')->property('Status2')->ascending(),
        ]);

        $this->assertObjectHasAttribute('properties', $database);
    }
}
