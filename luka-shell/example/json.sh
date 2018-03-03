#!/bin/bash
json='{"street":"1600 Amphitheatre Parkway","city":"Mountain View","state":"California","country":"US"}'
echo $json | jq '.'
echo $json | jq '.state'
