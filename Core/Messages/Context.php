<?php

namespace Smartbox\Integration\FrameworkBundle\Core\Messages;

use JMS\Serializer\Annotation as JMS;
use Smartbox\CoreBundle\Type\SerializableArray;

/**
 * Class Context.
 */
class Context implements \ArrayAccess
{
    const ORIGINAL_FROM = 'from';
    const ORIGINAL_TIMESTAMP = 'timestamp';
    const FLOWS_VERSION = 'version';
    const TRANSACTION_ID = 'transaction_id';
    const CALLBACK = 'callback';
    const CALLBACK_METHOD = 'callback_method';

    /**
     * @JMS\Type("Smartbox\CoreBundle\Type\SerializableArray")
     * @JMS\Expose
     * @JMS\Groups({"logs"})
     *
     * @var SerializableArray
     */
    protected $values;

    /**
     * @param SerializableArray|array $values
     */
    public function __construct($values = [])
    {
        if ($values instanceof SerializableArray) {
            $this->values = $values;
        } elseif (is_array($values)) {
            $this->values = new SerializableArray($values);
        } else {
            throw new \InvalidArgumentException('Invalid value, expected array or SerializableArray');
        }
    }

    /**
     * Get a value from the context.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return $this->values->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->values[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return @$this->values[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        throw new \Exception('You can not mutate the context once is created');
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        throw new \Exception('You can not mutate the context once is created');
    }

    /**
     * Convert the context to an associative array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->values->toArray();
    }
}
