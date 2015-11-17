<?php

return [
    'api_url'            => getenv('ORDERCLOUD_API_URL'),
    'organisation_id'    => getenv('ORDERCLOUD_ORGANISATION_ID'),
    'organisation_code'  => getenv('ORDERCLOUD_ORGANISATION_CODE'),
    'username'           => getenv('ORDERCLOUD_USERNAME'),
    'password'           => getenv('ORDERCLOUD_PASSWORD'),
    'client_secret'      => getenv('ORDERCLOUD_CLIENT_SECRET'),
    'organisation_token' => getenv('ORDERCLOUD_ORGANISATION_TOKEN'),
    'logging'            => getenv('ORDERCLOUD_LOGGING_ENABLED') == 'true',
];
