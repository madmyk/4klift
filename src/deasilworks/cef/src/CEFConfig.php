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

use Cassandra\Cluster;
use Cassandra\RetryPolicy;

/**
 * Class CEFConfig.
 */
class CEFConfig
{
    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $keyspace;

    /**
     * @var array
     */
    protected $contactPoints;

    /**
     * @var Cluster\Builder
     */
    protected $clusterBuilder;

    /**
     * @var Cluster
     */
    protected $cluster;

    /**
     * CEFConfig constructor.
     *
     * @SuppressWarnings(StaticAccess)
     * Because DI does not make sense here.
     */
    public function __construct()
    {
        $this->clusterBuilder = \Cassandra::cluster();
    }

    /**
     * @return string
     */
    public function getKeyspace()
    {
        return $this->keyspace;
    }

    /**
     * @param string $keyspace
     *
     * @return CEFConfig
     */
    public function setKeyspace($keyspace)
    {
        $this->keyspace = $keyspace;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return CEFConfig
     */
    public function setUsername($username)
    {
        $this->cluster = null;
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return CEFConfig
     */
    public function setPassword($password)
    {
        $this->cluster = null;
        $this->password = $password;

        return $this;
    }

    /**
     * @return array
     */
    public function getContactPoints()
    {
        $this->cluster = null;

        if (!$this->contactPoints) {
            $this->contactPoints = ['127.0.0.1'];
        }

        return $this->contactPoints;
    }

    /**
     * @param array $contactPoints
     *
     * @return CEFConfig
     */
    public function setContactPoints($contactPoints)
    {
        $this->cluster = null;
        $this->contactPoints = $contactPoints;

        return $this;
    }

    /**
     * @return Cluster
     */
    public function getCluster()
    {
        if ($this->cluster == null) {
            $retryPolicy = new RetryPolicy\DowngradingConsistency();
            $loggedRetry = new RetryPolicy\Logging($retryPolicy);

            /** @var Cluster\Builder $builder */
            $builder = $this->clusterBuilder;
            $builder
                ->withDefaultConsistency(\Cassandra::CONSISTENCY_LOCAL_QUORUM)
                ->withRetryPolicy($loggedRetry)
                ->withTokenAwareRouting(true);

            if ($this->getUsername() && $this->getPassword()) {
                $builder->withCredentials($this->getUsername(), $this->getPassword());
            }

            call_user_func_array([$builder, 'withContactPoints'], $this->getContactPoints());

            /* @var Cluster $cluster */
            $this->cluster = $builder->build();
        }

        return $this->cluster;
    }
}
