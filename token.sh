#!/bin/bash

TOKEN="CAAWbKCxg5jABABcFx5BthZCjxxekXh02l1OSypPZBMEcSyB2fhqYpp0qZCZCHH9HwWzZBMMFsLtYTeFPwOBQh4ZBZBuRciX0tgh8o3xZAeiOxyg89zt2uZBwKrBIJsUrlmZChQAfZAVdf5gw2cJUAOCm9CoQK8FKHhcdQRVlBUVHhsGFqCPwvSyi6zBNLYINo8UsxJmrt0jNGnQXTRVp6d4Tr7AyYyjpBTfQPAZD"
CLIENT="1577971729098288"
SECRET="7c1dc93c8dc5a311e680161093c24f61"

curl "https://graph.facebook.com/oauth/access_token?client_id=$CLIENT&client_secret=$SECRET&grant_type=fb_exchange_token&fb_exchange_token=$TOKEN"

echo ""

