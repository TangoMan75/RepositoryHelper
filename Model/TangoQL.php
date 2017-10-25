<?php

namespace TangoMan\RepositoryHelper\Model;

/**
 * Class TangoQL
 *
 * @author  Matthias Morin <matthias.morin@gmail.com>
 * @package TangoMan\RepositoryHelper\Model
 */
class TangoQL
{
    /**
     * @var array
     */
    private $dictionary = [
        'a' => 'andWhere',
        'b' => 'boolean',
        'c' => 'count',
        'd' => 'dateTime',
        'e' => 'exactMatch',
        'j' => 'join',
        'l' => 'like',
        'n' => 'notNull',
        'o' => 'orWhere',
        'p' => 'property',
        'r' => 'orderBy',
        's' => 'simpleArray',
        't' => 'distinct',
        'u' => 'sum',
    ];

    /**
     * User switches
     *
     * @var array
     */
    private $switches;

    /**
     * Two modes available: order/search
     *
     * @var string
     */
    private $mode;

    /**
     * Two operators available: andWhere/orWhere
     *
     * @var string
     */
    private $operator;

    /**
     * Should query contain join
     *
     * @var boolean
     */
    private $join;

    /**
     * Target entity
     *
     * @var string
     */
    private $entity;

    /**
     * target entity property
     *
     * @var string
     */
    private $property;

    /**
     * @var string
     */
    private $action;

    /**
     * @var string
     */
    private $parameters;

    /**
     * @param string $entity
     */
    public function __construct($entity)
    {
        $this->entity = $entity;
        $this->join = false;
        $this->mode = 'search';
        $this->operator = 'andWhere';
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     *
     * @return TangoQL
     */
    public function setMode($mode)
    {
        // when mode is orderBy, default action is orderBy property (alphabetical)
        // and default property is id
        if ($mode == 'r' || $mode == 'orderBy') {
            if (!$this->action) {
                $this->action = 'property';
            }
            if (!$this->property) {
                $this->property = 'id';
            }
        }

        $this->mode = $mode;

        return $this;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param string $operator
     *
     * @return TangoQL
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * @return bool
     */
    public function isJoin()
    {
        return $this->join;
    }

    /**
     * @param bool $join
     *
     * @return TangoQL
     */
    public function setJoin($join)
    {
        $this->join = $join;

        return $this;
    }

    /**
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param string $entity
     *
     * @return TangoQL
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * @param string $property
     *
     * @return TangoQL
     */
    public function setProperty($property)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     *
     * @return TangoQL
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return string
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param string $parameters
     *
     * @return TangoQL
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        $parameters = explode('-', $parameters);

        switch (count($parameters)) {
            // One parameter only is property
            case 1:
                if ($parameters[0]) {
                    $this->property = $parameters[0];
                }
                break;

            // Two parameters are either "(mode/action) + property" or "entity + property (+ join)"
            case 2:
                $switches = $this->parseSwitches($parameters[0]);

                // We have switches
                if ($switches) {
                    $mode = $this->parseMode($switches);
                    if ($mode) {
                        $this->mode = $mode;
                    }

                    $action = $this->parseAction($switches);
                    if ($action) {
                        $this->action = $action;
                    }
                } else {
                    // No switches then we have "entity + property (+ join)"
                    $this->entity = $parameters[0];
                }

                $this->property = $parameters[1];
                break;

            // Three parameters are "(mode/action) + entity + property (+join)"
            case 3:
                $switches = $this->parseSwitches($parameters[0]);

                if ($switches) {
                    $mode = $this->parseMode($switches);
                    if ($mode) {
                        $this->mode = $mode;
                    }

                    $action = $this->parseAction($switches);
                    if ($action) {
                        $this->action = $action;
                    }
                }

                $this->entity = $parameters[1];
                $this->property = $parameters[2];
                break;
        }

        // join is true when given entity different from current entity
        if ($this->entity != $this->getTableName()) {
            $this->join = true;
        }

        // join is true when order and count mode
        if ($this->mode == 'r' && $this->action == 'c') {
            $this->join = true;
        }

        return $this;
    }

    /**
     * Switches are one character long codes
     *
     * @param $string
     *
     * @return array|bool
     */
    private function parseSwitches($string)
    {
        $switches = str_split(strtolower($string), 1);

//        // No more than 3 switches allowed (join, mode, action)
//        if (count($switches) > 3) {
//            return false;
//        }

        // Only valid switch group allowed
        if (count(array_diff($switches, array_keys($this->switches))) > 0) {
            return false;
        }

        return $this->translateSwitches($switches);
    }

    /**
     * @param array $switches
     *
     * @return array
     */
    private function translateSwitches($switches)
    {
        $result = [];
        foreach ($switches as $switch) {
            if (key_exists($switch, $this->dictionary)) {
                $result[] = $this->dictionary[$switch];
            }
        }

        return $result;
    }

    /**
     * @param array $switches
     *
     * @return string|null
     */
    private function parseMode($switches)
    {
        if (in_array('andWhere', $switches)) {
            return 'andWere';
        }

        if (in_array('orWhere', $switches)) {
            return 'orWhere';
        }

        if (in_array('orderBy', $switches)) {
            return 'orderBy';
        }

        return null;
    }

    /**
     * @param array $switches
     *
     * @return null|string
     */
    private function parseAction($switches)
    {
        // I left here possibility to have several actions
        $remove = [
            'andWere',
            'orWhere',
            'orderBy',
        ];

        $action = array_diff($switches, $remove);

        if (count($action) === 0) {
            return null;
        }

        return implode($action);
    }
}
