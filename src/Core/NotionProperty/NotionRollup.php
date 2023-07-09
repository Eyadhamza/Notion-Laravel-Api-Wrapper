<?php

namespace Pi\Notion\Core\NotionProperty;

use Illuminate\Http\Resources\MissingValue;
use Pi\Notion\Core\BlockContent\NotionArrayValue;
use Pi\Notion\Core\BlockContent\NotionContent;
use Pi\Notion\Enums\NotionPropertyTypeEnum;

class NotionRollup extends BaseNotionProperty
{
    public string $rollupType;
    public ?int $rollupNumber = null;
    private ?string $relationPropertyName = null;
    private ?string $relationPropertyId = null;
    private ?string $rollupPropertyName = null;
    private ?string $rollupPropertyId = null;
    private ?string $rollupFunction = null;

    protected function buildValue(): NotionContent
    {
        return NotionArrayValue::make([
            'relation_property_name' => $this->relationPropertyName ?? new MissingValue(),
            'relation_property_id' => $this->relationPropertyId ?? new MissingValue(),
            'rollup_property_name' => $this->rollupPropertyName ?? new MissingValue(),
            'rollup_property_id' => $this->rollupPropertyId ?? new MissingValue(),
            'function' => $this->rollupFunction ?? new MissingValue(),
        ])->setValueType('rollup');
    }

    public function setType(): BaseNotionProperty
    {
        $this->type = NotionPropertyTypeEnum::ROLLUP;

        return $this;
    }

    protected function buildFromResponse(array $response): self
    {
        if (empty($response['rollup'])) {
            return $this;
        }
        $this->rollupType = $response['rollup']['type'];
        $this->rollupNumber = $response['rollup']['number'];
        $this->rollupFunction = $response['rollup']['function'];

        return $this;
    }

    public function setRelationPropertyName(?string $relationPropertyName): NotionRollup
    {
        $this->relationPropertyName = $relationPropertyName;
        return $this;
    }

    public function setRelationPropertyId(?string $relationPropertyId): NotionRollup
    {
        $this->relationPropertyId = $relationPropertyId;
        return $this;
    }

    public function setRollupPropertyName(?string $rollupPropertyName): NotionRollup
    {
        $this->rollupPropertyName = $rollupPropertyName;
        return $this;
    }

    public function setRollupPropertyId(?string $rollupPropertyId): NotionRollup
    {
        $this->rollupPropertyId = $rollupPropertyId;
        return $this;
    }

    public function setFunction(?string $function): NotionRollup
    {
        $this->rollupFunction = $function;
        return $this;
    }


}

