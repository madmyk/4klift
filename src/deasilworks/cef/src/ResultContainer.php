<?php

/*
 * MIT License
 *
 * Copyright (c) 2017 Deasil Works, Inc
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace deasilworks\CEF;

use JMS\Serializer\Annotation as JMS;

/**
 * Class ResultContainer.
 */
class ResultContainer extends EntityCollection
{
    /**
     * Class name of values.
     *
     * @JMS\Exclude()
     *
     * @var string
     */
    protected $valueClass = EntityModel::class;

    /**
     * @JMS\Exclude
     *
     * @var string
     */
    private $statement;

    /**
     * @JMS\Exclude
     *
     * @var array
     */
    private $arguments;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * ResultContainer constructor.
     *
     * Result containers are used by Entity Managers.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param array $arguments
     *
     * @return ResultContainer
     */
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatement()
    {
        return $this->statement;
    }

    /**
     * @param string $statement
     *
     * @return ResultContainer
     */
    public function setStatement($statement)
    {
        $this->statement = $statement;

        return $this;
    }

    /**
     * Creates all entities from an array at once.
     * These are an array of entries that need to be converted to models.
     *
     * @param array $results
     *
     * @return ResultContainer
     */
    public function setResults($results)
    {
        // hydrate model
        foreach ($results as $entry) {
            /** @var EntityModel $model */
            $model = $this->getModel();
            $model->setEntityManager($this->getEntityManager());

            // check for json
            if (array_key_exists('[json]', $entry)) {
                $entry = json_decode($entry['[json]']);
            }

            $model = $this->populate($entry, $model);

            $this->collection[] = $model;
        }

        $this->setCount(count($this->collection));

        return $this;
    }

    /**
     * This calls __set for each property of the model object created
     * above in the setResults call. See CEFData.php for what happens next.
     *
     * @param $entity
     * @param $model
     *
     * @return mixed
     */
    private function populate($entity, $model)
    {
        if ($entity && (is_array($entity) || is_object($entity))) {
            foreach ($entity as $name => $value) {
                $model->$name = $value;
            }
        }

        return $model;
    }
}
