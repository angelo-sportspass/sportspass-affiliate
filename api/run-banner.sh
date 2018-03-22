#!/bin/bash
#

# change path to application <path>/<to>/<app>/check-banner
# change domain

if [ -s ~/Sites/sportspass-affiliate/api/check-banner ]
then
    echo 'true';
else
   echo "true" >> ~/Sites/sportspass-affiliate/api/check-banner;
   curl -s 'sportspass-affiliate.dev.local/v1/rakuten/merchant-banner-links';
fi