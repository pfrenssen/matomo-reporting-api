# Matomo Reporting API

(former Piwik Reporting API)

This library allows to retrieve data from the [Matomo](https://matomo.org) open
source web analytics platform using the [Matomo Reporting
API](https://developer.matomo.org/api-reference/reporting-api) using PHP.

## Usage

```
<?php

use Matomo\ReportingApi\QueryFactory;

require getcwd() . '/vendor/autoload.php';

// The URL of the Matomo server. *Always use HTTPS!*
$matomo_url = 'https://my.matomo.server';

// The user authentication token. You can get it in the web interface of the
// Matomo server at Administration > Platform > API.
$token = 'e0357ffdf830ca8be8af4151b564b53d';

// Instantiate the query factory. This class helps to quickly generate
// different query objects with reusable default parameters.
$query_factory = QueryFactory::create($matomo_url);

// Set some default parameters such as the site ID and user authentication
// token. 
$query_factory
    ->set('idSite', 1)
    ->set('token_auth', $token);

// Example: retrieve the version of the Matomo server.
$query = $query_factory->getQuery('API.getMatomoVersion');
$response = $query->execute()->getResponse();
$matomo_version = $response->value;

echo "Server is running Matomo $matomo_version.\n\n";

// Example: retrieve browser usage statistics for the past week.
$response = $query_factory->getQuery('DevicesDetection.getBrowsers')
  ->setParameter('date', 'today')
  ->setParameter('period', 'week')
  ->execute()
  ->getResponse();

foreach ($response as $browser) {
  echo "Browser: {$browser->label}\n";
  echo "Total visits: {$browser->nb_visits}\n";
  echo "Bounce count: {$browser->bounce_count}\n";
  echo "Daily unique visitors: {$browser->sum_daily_nb_uniq_visitors}\n\n";
}
```

For the full list of available methods, see the [Matomo API
reference](https://developer.matomo.org/api-reference/reporting-api).

## Security

The Matomo Reporting API uses a user authentication token to access the API. The
API is very powerful and allows to view, edit and delete most data, even add
and remove users. This means that *the user authentication token should be kept
as secret as your username and password*.

Since the token is sent over the network *it is crucial that all network
traffic to the Matomo API is encrypted*. You can use an unencrypted HTTP URL
during testing, but for a production server *it is highly recommended to use
HTTPS* or it will be possible for an attacker to steal your credentials and
lock you out of your Matomo server.
