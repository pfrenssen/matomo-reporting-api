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
   * @return \Piwik\ReportingApi\Query
   *   The query object.
   */
  public function getQuery();

}
