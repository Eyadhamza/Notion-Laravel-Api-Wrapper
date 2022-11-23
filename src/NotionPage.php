<?php


namespace Pi\Notion;


use Illuminate\Support\Collection;
use Pi\Notion\Traits\HandleBlocks;
use Pi\Notion\Traits\HandleProperties;
use Pi\Notion\Traits\RetrieveResource;
use Pi\Notion\Traits\ThrowsExceptions;

class NotionPage extends NotionObject
{
    use ThrowsExceptions;
    use HandleProperties;
    use HandleBlocks;

    private NotionDatabase $notionDatabase;
    protected Collection $blocks;
    protected Collection $properties;


    public function __construct($id = '')
    {
        $this->id = $id;
        $this->blocks = new Collection();
        $this->properties = new Collection();

    }
    public function get()
    {
        $response = prepareHttp()->get($this->getUrl());

        $this->throwExceptions($response);

        $page = $this->build($response->json())
            ->buildProperties($response->json()['properties']);
        dd($page);
        return $this;
    }

    public function create(): self
    {
        $response = prepareHttp()
            ->post(Workspace::PAGE_URL, [
                'parent' => array('database_id' => $this->notionDatabase->getDatabaseId()),
                'properties' => Property::mapsProperties($this),
                'children' => Block::mapsBlocksToPage($this)
            ]);
        return  $this;
    }

    public function update(): self
    {
        $response = prepareHttp()
            ->patch($this->getUrl(), [
                'properties' => Property::mapsProperties($this),
            ]);

        return  $this;
    }

    public function delete(): self
    {
        $response = prepareHttp()
            ->delete(Workspace::BLOCK_URL . $this->id);
        return  $this;
    }
    public function search(string $pageTitle)
    {

        $response = prepareHttp()
            ->post(Workspace::SEARCH_PAGE_URL, ['query' => $pageTitle]);

        return $this;

    }

    public function setDatabaseId(string $notionDatabaseId): void
    {
        $this->notionDatabase = new NotionDatabase($notionDatabaseId);
    }

    private function getUrl(): string
    {
        return Workspace::PAGE_URL . $this->id;
    }


}
