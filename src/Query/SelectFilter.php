<?php /** @noinspection ALL */


namespace Pi\Notion\Query;


use Illuminate\Support\Collection;
use Pi\Notion\Properties\Select;

class SelectFilter implements Filterable
{
    private $equals;
    private $notEqual;
    private $isNotEmpty;
    private $isEmpty;
    private string $propertyName;

    public function __construct(string $propertyName)
    {

        $this->propertyName = $propertyName;
    }

    public function setPropertyFilter(): array
    {

       return array(
           'property'=> $this->propertyName,
           'select'=>  $this->setFilterConditions()
       );

    }
    public function equals($optionName): self
    {
        $this->equals = $optionName;
        return $this;
    }

    public function notEqual($optionName): self
    {
        $this->notEqual = $optionName;
        return $this;
    }
    public function isNotEmpty(): self
    {
        $this->isNotEmpty = true;
        return $this;
    }

    public function isEmpty(): self
    {
        $this->isEmpty = true;
        return $this;
    }
    public function setFilterConditions(): array|null
    {

        if ($this->equals){
            return ['equals'=> $this->equals];
        }
        if ($this->notEqual){
            return ['does_not_equal'=> $this->notEqual];
        }
        if ($this->isNotEmpty){
            return ['is_not_empty'=> $this->isNotEmpty];
        }
        if ($this->isEmpty){
            return ['is_empty'=> $this->isEmpty];
        }
        return ['equals'=> $this->equals];

    }

}
