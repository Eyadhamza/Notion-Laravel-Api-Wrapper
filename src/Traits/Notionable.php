<?php

namespace Pi\Notion\Traits;

use LogicException;
use Pi\Notion\Core\Builders\NotionBlockBuilder;
use Pi\Notion\Core\Content\NotionPropertyContentFactory;
use Pi\Notion\Core\Models\NotionBlock;
use Pi\Notion\Core\Models\NotionPage;
use Pi\Notion\Core\Properties\BaseNotionProperty;

trait Notionable
{
    protected array $notionMap = [];
    protected ?NotionBlockBuilder $blockBuilder = null;

    public abstract function mapToNotion(): array;

    public function saveToNotion(string $pageId = null): NotionPage
    {
        $this->blockBuilder = NotionBlockBuilder::make();

        $this->notionMap = $this->mapToNotion();

        $this->setBlockBuilder();

        $this->validateHasNotionDatabaseId();

        $this->notionMap = $this->buildNotionProperties();

        if ($pageId) {
            if ($this->blockBuilder->getBlocks()->isNotEmpty()){
                NotionBlock::make($pageId)
                    ->setBlockBuilder($this->blockBuilder)
                    ->createChildren();
            }
            return NotionPage::make($pageId)
                ->setProperties($this->notionMap)
                ->update();
        }

        return NotionPage::make()
            ->setDatabaseId($this->notionDatabaseId)
            ->setProperties($this->notionMap)
            ->setBlockBuilder($this->blockBuilder)
            ->create();
    }

    public function getNotionDatabaseId(): string
    {
        return $this->notionDatabaseId;
    }

    abstract public function getAttributes();

    public function validateHasNotionDatabaseId(): self
    {
        if (!isset($this->notionDatabaseId)) {
            throw new LogicException('Notionable class must have a notionDatabaseId property');
        }

        return $this;
    }

    public function buildNotionProperties(): array
    {
        return collect($this->getAttributes())->map(function ($value, $key) {
            if (array_key_exists($key, $this->notionMap)) {
                /** @var BaseNotionProperty $property */
                $property = $this->notionMap[$key];

                if (!$property->hasValue()) {
                    $property->setValue($value);
                }

                if ($property->shouldBeBuilt()) {
                    $property->buildContent();
                }

                return $property;
            }
        })->filter()->toArray();
    }

    protected function setBlockBuilder(): void
    {

    }

    public function deleteFromNotion(string $id): self
    {
        NotionPage::make($id)->delete();

        return $this;
    }
}
