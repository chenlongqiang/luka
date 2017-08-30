#!/bin/bash
sql="insert into erge.beva (beva_id,name,type,mp3_url,html_url) values ( '1', a, 'b', 'c', 'd');"
mysql -uvagrant -pvagrant -e "$sql"
