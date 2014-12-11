#!/bin/bash

TOKEN="CAAWbKCxg5jABALX99NTAAtfjKIPct4gtebooe6dXO8WT2mtJ5Jj3iIIPHwPv1jsteXc9qJznw8mTlEZBKaWrk9QbSiD5xng8PmLLmedNjSMxyt0dA0D5kVZBW1ZCdx0elpU2lzWx2lcluhDPINuazAM177i0esovMm2z677N1Bvg1tQSYwWUTYDntOpB90g2XVylAdLDFFZCGg3gZCkpT"
CLIENT="1577971729098288"
SECRET="7c1dc93c8dc5a311e680161093c24f61"

curl "https://graph.facebook.com/oauth/access_token?client_id=$CLIENT&client_secret=$SECRET&grant_type=fb_exchange_token&fb_exchange_token=$TOKEN"

echo ""

