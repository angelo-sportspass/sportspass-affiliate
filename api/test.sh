#!/bin/bash
#

if [ -s ~/Sites/sportspass-affiliate/api/test ]
then
    echo 'true';
else
   echo "true" >> ~/Sites/sportspass-affiliate/api/test;
   curl -s 'sportspass-affiliate.dev.local/v1/rakuten/merchant-by-app-status';
fi