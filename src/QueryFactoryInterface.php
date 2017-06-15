<?php

namespace Piwik\ReportingApi;

/**
 * Interface for classes that provide Piwik reporting API query objects.
 */
interface QueryFactoryInterface
{

    /**
     * Returns a query object for the given Piwik API method.
     *
     * @param string $method
     *   The name of the method for which to return a query object, in the format
     *   'ModuleName.methodName'.
     *
     * @return \Piwik\ReportingApi\Query
     *   The query object.
     */
    public function getQuery($method);
}
