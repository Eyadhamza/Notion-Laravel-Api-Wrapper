<?php

use Pi\Notion\Core\Models\NotionDatabase;
use Pi\Notion\Core\Properties\NotionDatabaseTitle;
use Pi\Notion\Core\Properties\NotionRollup;
use Pi\Notion\Core\Properties\NotionPeople;
use Pi\Notion\Core\Properties\NotionMedia;
use Pi\Notion\Core\Properties\NotionEmail;
use Pi\Notion\Core\Properties\NotionNumber;
use Pi\Notion\Core\Properties\NotionPhoneNumber;
use Pi\Notion\Core\Properties\NotionUrl;
use Pi\Notion\Core\Properties\NotionCreatedTime;
use Pi\Notion\Core\Properties\NotionCreatedBy;
use Pi\Notion\Core\Properties\NotionLastEditedTime;
use Pi\Notion\Core\Properties\NotionLastEditedBy;
use Pi\Notion\Core\Properties\NotionCheckbox;
use Pi\Notion\Core\Properties\NotionDatabaseDescription;
use Pi\Notion\Core\Properties\NotionTitle;
use Pi\Notion\Core\Properties\NotionDate;
use Pi\Notion\Core\Properties\NotionFormula;
use Pi\Notion\Core\Properties\NotionRelation;
use Pi\Notion\Core\Properties\NotionSelect;
use Pi\Notion\Core\Query\NotionFilter;
use Pi\Notion\Exceptions\NotionValidationException;
use function PHPUnit\Framework\assertObjectHasProperty;

beforeEach(function () {
    $this->databaseId = 'ae4c13cd00394938b2f7914cb00350f8';
    $this->pageId = '0ef342c64e9f431fb5cf8a9eebea4c92';
});

it('returns database info', function () {
    $database = NotionDatabase::make($this->databaseId)->find();
    assertObjectHasProperty('objectType', $database);
});

it('can create a database object', function () {
    $database = (new NotionDatabase)
        ->setParentPageId('fa4379661ed948d7af52df923177028e')
        ->setTitle(NotionDatabaseTitle::make('Test Database'))
        ->buildProperties([
            NotionTitle::make('Name'),
            NotionSelect::make('Status')->setOptions([
                ['name' => 'A', 'color' => 'red'],
                ['name' => 'B', 'color' => 'green']
            ]),
            NotionDate::make('Date'),
            NotionCheckbox::make('Checkbox'),
            NotionFormula::make('Formula')->setExpression('prop("Name")'),
            NotionRelation::make('Relation')
                ->setDatabaseId($this->databaseId),
            NotionRollup::make('Rollup')
                ->setRollupPropertyName('Name')
                ->setRelationPropertyName('Relation')
                ->setFunction('count'),
            NotionPeople::make('People'),
            NotionMedia::make('Media'),
            NotionEmail::make('Email'),
            NotionNumber::make('Number'),
            NotionPhoneNumber::make('Phone'),
            NotionUrl::make('Url'),
            NotionCreatedTime::make('CreatedTime'),
            NotionCreatedBy::make('CreatedBy'),
            NotionLastEditedTime::make('LastEditedTime'),
            NotionLastEditedBy::make('LastEditedBy'),
        ])->create();

    assertObjectHasProperty('objectType', $database);
});

it('can update a database object', function () {
    $database = NotionDatabase::make($this->databaseId)
        ->setTitle(NotionDatabaseTitle::make('Test Database'))
        ->setDatabaseDescription(NotionDatabaseDescription::make('Test Description'))
        ->buildProperties([
            NotionDate::make('Date'),
            NotionCheckbox::make('Checkbox'),
        ])
        ->update();

    assertObjectHasProperty('objectType', $database);
});

it('throws exception when database not found', function () {
    $id = '632b5fb7e06c4404ae12asdasd065c48280e4asdc';
    $this->expectException(NotionValidationException::class);
    (new NotionDatabase($id))->find();
});

it('throws exception when database not authorized', function () {
    $id = '632b5fb7e06c4404ae12065c48280e4asdc';
    $this->expectException(NotionValidationException::class);
    (new NotionDatabase($id))->find();
});

it('can filter database with one filter', function () {
    $paginated = NotionDatabase::make($this->databaseId)
        ->setFilter(NotionSelect::make('Status')->equals('Reading'))
        ->query();

    expect($paginated->getResults())->toHaveCount(1);
});

it('can filter database with many filters', function () {
    $database = NotionDatabase::make($this->databaseId);

    $paginated = $database->setFilters([
        NotionFilter::groupWithAnd([
            NotionSelect::make('Status')->equals('Reading'),
            NotionTitle::make('Name')->equals('MMMM')
        ])
    ])->query();

    expect($paginated->getResults())->toHaveCount(1);

    $paginated = $database->setFilters([
        NotionFilter::groupWithOr([
            NotionSelect::make('Status')->equals('Reading'),
            NotionTitle::make('Name')->equals('MMMM')
        ])
    ])->query();
    expect($paginated->getResults())->toHaveCount(1);
});

it('can filter database with nested filters', function () {
    $paginated = NotionDatabase::make($this->databaseId)->setFilters([
        NotionFilter::groupWithOr([
            NotionSelect::make('Status')->equals('Reading'),
            NotionFilter::groupWithAnd([
                NotionTitle::make('Name')->contains('MMMM')
            ])
        ]),
    ])->query();
    expect($paginated->getResults())->toHaveCount(1);
});

it('can sort database results', function () {
    $paginated = NotionDatabase::make($this->databaseId)
        ->setSorts([
            NotionTitle::make('Name')->ascending(),
            NotionDate::make('Date')->descending()
        ])
        ->query( 50);

    expect($paginated->getResults())->toHaveCount(50)
        ->and($paginated->hasMore())->toBeTrue();

    $paginated->next();

    expect($paginated->getResults())->toHaveCount(50)
        ->and($paginated->getNextCursor())->toBeString();
});
