#!/bin/bash
#

# change path to application <path>/<to>/<app>/check-banner
# change domain

if [ -s /var/www/sportspass-affiliate/api/check-banner ]
then
    echo 'true';
else
   echo "true" >> /var/www/sportspass-affiliate/api/check-banner;
   curl -s 'affiliate.sportsnomads.com.au/v1/rakuten/merchant-banner-links';
fi