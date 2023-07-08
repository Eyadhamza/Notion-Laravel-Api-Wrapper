<?php

namespace Pi\Notion\Core\NotionProperty;

use Pi\Notion\Core\Enums\NotionPropertyTypeEnum;
use Pi\Notion\Core\Models\NotionUser;
use Pi\Notion\Core\NotionValue\NotionBlockContent;
use Pi\Notion\Core\NotionValue\NotionEmptyValue;

class NotionLastEditedBy extends BaseNotionProperty
{
    protected function buildValue(): NotionBlockContent
    {
        return NotionEmptyValue::make()->type('last_edited_by');
    }

    protected function buildFromResponse(array $response): BaseNotionProperty
    {
        if (empty($response['last_edited_by'])) {
            return $this;
        }
        $this->lastEditedBy = NotionUser::fromResponse($response['last_edited_by']);
        return $this;
    }

    public function setType(): BaseNotionProperty
    {
        $this->type = NotionPropertyTypeEnum::LAST_EDITED_BY;

        return $this;
    }
}

