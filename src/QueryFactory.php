<?php

namespace Piwik\ReportingApi;

/**
 * Factory for Piwik reporting API query objects.
 */
class QueryFactory implements QueryFactoryInterface
{

  /**
   * {@inheritdoc}
   */
  public function getQuery()
  {
    $query = new Query();
    return $query;
  }

}
