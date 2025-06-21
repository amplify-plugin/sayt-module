<?php

namespace Amplify\System\Sayt\Classes;

use Amplify\System\Sayt\Interfaces\IResultRow;
use Amplify\System\Support\Money;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use JsonException;
use Traversable;

// Contains product data in columns. Used to display products.

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @property string|null $Product_Id
 * @property string|null $Product_Type
 * @property string|null $Product_Code
 * @property string|null $Product_Image
 * @property string|null $Product_Name
 * @property string|null $Status
 * @property string|null $Brand_Name
 * @property string|null $GTIN
 * @property string|null $HasImage
 * @property string|null $Product_Description
 * @property string|null $Product_Slug
 * @property string|null $Manufacturer
 * @property string|null $MPN
 * @property string|null $Price
 * @property string|null $Msrp
 * @property string|null $Days_Published
 * @property string|null $Sku_Id
 * @property string|null $Sku_ProductCode
 * @property string|null $Sku_ProductImage
 * @property string|null $Sku_Name
 * @property string|null $Sku_Status
 * @property string|null $EAScore
 * @property string|null $EASource
 * @property string|null $EAWeight
 * @property string|null $EARules
 * @property string|null $Sku_Count
 * @property array $Sku_List
 * @property string|null $MinPrice
 * @property string|null $MaxPrice
 */
class ItemRow implements \ArrayAccess, \IteratorAggregate, \JsonSerializable, Arrayable, IResultRow, Jsonable
{
    private $m_items;

    private $m_ea_raw;

    private $m_casts = [
        'Product_Id' => 'integer',
        'Product_Type' => 'string',
        'Product_Code' => 'string',
        'Product_Image' => 'string',
        'Product_Name' => 'string',
        'Status' => 'string',
        'Brand_Name' => 'string',
        'GTIN' => 'string',
        'HasImage' => 'bool',
        'Product_Description' => 'string',
        'Product_Slug' => 'string',
        'Manufacturer' => 'string',
        'MPN' => 'string',
        'Price' => 'money',
        'Msrp' => 'string',
        'Days_Published' => 'string',
        'Sku_Id' => 'string',
        'Sku_ProductCode' => 'string',
        'Sku_ProductImage' => 'string',
        'Sku_Name' => 'string',
        'Sku_Status' => 'string',
        'EAScore' => 'string',
        'EASource' => 'string',
        'EAWeight' => 'integer',
        'EARules' => 'string',
        'Sku_Count' => 'integer',
        'Sku_List' => 'array',
        'MinPrice' => 'money',
        'MaxPrice' => 'money',
    ];

    // Creates the ItemRow
    public function __construct($desc, $item)
    {
        $this->m_ea_raw = $item;

        $this->m_items = [];

        foreach ($desc as $dd) {

            $attribute = $dd->getTagName();

            $this->{$attribute} = match ($attribute) {
                'Product_Name' => $this->sanitizeProductName($item->{$attribute}),
                'Sku_List' => json_decode($item->{$attribute}, true),
                default => $item->{$attribute} ?? ''
            };
        }
    }

    private function sanitizeProductName($value): string
    {
        $value = trim(trim($value), "\'\"");

        foreach (Config::get('amplify.sayt.sanitize_product_name_callbacks', []) as $callback) {
            $value = call_user_func($callback, $value);
        }

        return $value;
    }

    public function __debugInfo(): ?array
    {
        return $this->m_items;
    }

    public function __isset($name)
    {
        return $this->offsetExists($name);
    }

    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }

    public function __unset($name)
    {
        $this->offsetUnset($name);
    }

    public function getEAResponse()
    {
        return $this->m_ea_raw;
    }

    // Returns the data contained in a specific column
    public function getCellData($attribute)
    {
        return $this->m_items[$attribute];
    }

    // Returns the amount of columns contained within the row
    public function size()
    {
        return count($this->m_items);
    }

    /**
     * Specify data which should be serialized to JSON
     */
    public function jsonSerialize(): mixed
    {
        return $this->m_items;
    }

    /**
     * Retrieve an external iterator
     *
     * @return Traversable An instance of an object implementing
     */
    public function getIterator(): Traversable
    {
        yield from $this->m_items;
    }

    /**
     * Whether a offset exists
     *
     * @return bool true on success or false on failure.
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->m_items[$offset]);
    }

    /**
     * Offset to retrieve
     *
     * @link https://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param  mixed  $offset  <p>
     *                         The offset to retrieve.
     *                         </p>
     * @return TValue Can return all value types.
     */
    public function offsetGet(mixed $offset): mixed
    {
        if (array_key_exists($offset, $this->m_items)) {
            return $this->m_items[$offset];
        } else {
            return '';
        }
    }

    /**
     * Offset to set
     *
     * @param  TKey  $offset  The offset to assign the value to.
     * @param  TValue  $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->m_items[$offset] = $value;

        $this->resolveCasts();
    }

    public function resolveCasts(): void
    {
        foreach ($this->m_casts as $field => $cast) {
            if (array_key_exists($field, $this->m_items) && gettype($this->m_items[$field]) !== $cast) {
                $this->m_items[$field] = $this->convertValue($cast, $this->m_items[$field]);
            }
        }
    }

    private function convertValue($type, $value): mixed
    {
        if ($value == null) {
            return null;
        }

        switch ($type) {
            case 'boolean':
            case 'bool' :
                if (! is_bool($value)) {
                    return in_array($value, ['false', 'FALSE', '0', 0], true);
                }

                return $value;

            case 'float' :
            case 'double' :
            case 'decimal' :
                return (float) $value;

            case 'integer' :
                return (int) $value;

            case 'array' :
                if (json_decode($value, true) === null) {
                    return Arr::wrap($value);
                }

                return json_decode($value, true, 512, JSON_THROW_ON_ERROR);

            case 'datetime' :
            case 'date':
                return Carbon::parse($value);

            case 'money' :
                return Money::parse($value);

            case 'string' :
                return (string) $value;

            default:
                return $value;
        }
    }

    /**
     * Offset to unset
     *
     * @param  TKey  $offset
     */
    public function offsetUnset(mixed $offset): void
    {
        if (array_key_exists($offset, $this->m_items)) {
            unset($this->m_items[$offset]);
        }
    }

    /**
     * Get the instance as an array.
     *
     * @return array<TKey, TValue>
     */
    public function toArray()
    {
        return $this->m_items;
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     *
     * @throws JsonException
     */
    public function toJson($options = 0)
    {
        $json = json_encode($this->m_items, $options);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonException(json_last_error_msg());
        }

        return $json;
    }

    /**
     * @throws JsonException
     */
    public function __toString()
    {
        return $this->toJson();
    }
}
