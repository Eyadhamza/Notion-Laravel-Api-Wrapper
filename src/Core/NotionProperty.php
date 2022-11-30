<?php


namespace Pi\Notion\Core;


use Illuminate\Support\Collection;
use Pi\Notion\PropertyType;
use Pi\Notion\Traits\CreatePropertyTypes;
use stdClass;

class NotionProperty
{
    use CreatePropertyTypes;

    private string $type;
    private string $name;
    private ?string $id;
    private array|string|null $values;
    private array|string|null $options;

    public function __construct(string $type, string $name)
    {
        $this->type = $type;
        $this->name = $name;
    }

    public static function make(string $type, string $name): NotionProperty
    {
        return new self($type, $name);
    }

    public static function mapsProperties(NotionDatabase|NotionPage $object)
    {
        return $object->getProperties()->mapToAssoc(function (NotionProperty $property) {
            return
                array(
                    $property->name, array($property->getType() => empty($property->getValues()) ? $property->getOptions() : $property->getValues())
                );
        });
    }

    public static function buildProperty(string $name, array $body): NotionProperty
    {
        $property = NotionProperty::make($body['type'], $name);
        $property->id = $body['id'];

        $property->options = $body[$body['type']]['options'] ?? [];
        return $property;
    }

    public static function buildList(mixed $response): Collection
    {
        $list = new Collection();
        collect($response['results'])->each(function ($item) use ($list) {
            $list->add(self::buildProperty($item['type'], $item));
        });
        return $list;
    }

    public function setValues(array|string $values): self
    {
        $this->values = $values;
        return $this;
    }

    public function setMultipleValues(array $values): self
    {
        $this->values = collect($values)->map(function ($optionName) {
            return ['name' => $optionName];
        })->toArray();
        return $this;
    }

    public function getValues(): array|string
    {
        if (!isset($this->values)) {
            return [];
        }
        if (is_array($this->values)) {
            return $this->isNested() ? array($this->values) : array($this->values)[0];
        }
        return $this->values;
    }

    private function isNested(): bool
    {
        return in_array($this->type, [
            PropertyType::TITLE,
            PropertyType::RICH_TEXT
        ]);
    }

    public function isPaginated(): bool
    {
        return in_array($this->type, [
            PropertyType::TITLE,
            PropertyType::RICH_TEXT,
            PropertyType::RELATION,
            PropertyType::PEOPLE,
        ]);
    }

    public function getOptions(): array|stdClass
    {
        if (!isset($this->options)) {
            return new stdClass();
        }
        if (is_array($this->options)) {
            return [
                'options' => $this->options
            ];
        }
        return array($this->options);
    }

    public function setOptions(array|string $options): self
    {
        $this->options = $options;
        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function ofName(string $name): bool
    {
        return $this->name == $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
