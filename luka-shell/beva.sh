#!/bin/bash
#我的第一波shell爬虫

until ((false))
do

last_id=$(cat ./last_id.txt)
num=`expr $last_id + 1`

json=$(curl "http://g.beva.com/mp3/action/get?dl=dl&t=r&l=$num" -H 'Cookie: PHPSESSID=jhls14tq39upbskh6kac8im0d6; bdshare_firstime=1490090045937; BAIDU_SSP_lcr=https://www.baidu.com/link?url=X0rur1Hw2KjuTMhRbCakrnOA4Mzmwos44KHpy5t3Rdu&wd=&eqid=fb45508f00021ae80000000458d0f839; nickname=luka-chen; bvss=hw0lF3eGL3rDNiLBg5OIP0FHAV6oVxgkNYgfhLndT5digJ76xCYibL9BjfFxCctP; __utmt=1; __utma=107580317.1353326513.1490090046.1490090046.1490102710.2; __utmb=107580317.81.8.1490103822693; __utmc=107580317; __utmz=107580317.1490102710.2.2.utmcsr=g.beva.com|utmccn=(referral)|utmcmd=referral|utmcct=/mp3/album/10094.html; Hm_lvt_c49fb18e261578db7ad1a165fd09734e=1490090046,1490090060; Hm_lpvt_c49fb18e261578db7ad1a165fd09734e=1490104907' -H 'Accept-Encoding: gzip, deflate, sdch' -H 'Accept-Language: zh-CN,zh;q=0.8' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36' -H 'Accept: application/json, text/javascript, */*; q=0.01' -H "Referer: http://g.beva.com/mp3/${num}.html" -H 'X-Requested-With: XMLHttpRequest' -H 'Connection: keep-alive' --compressed)

title=$(curl "http://g.beva.com/mp3/$num.html" | grep '<title>.*<\/title>')
title=${title/<title>/}
title=${title/<\/title>/}
title=${title/[0-9]*:/}

len=${#json}
a=50
if [ $len -lt $a ]
then
    end=$(cat ./end.txt)
    end=`expr $end + 1`

    b=50
    if [ $end -gt $b ]
    then
        echo "try $b, to the end"
        break
    fi

    echo $num > last_id.txt
    echo $end > end.txt
else
    mp3_url=$(echo $json | jq '.data[0].url')
    mp3_url=$(echo $mp3_url | sed 's/\"//g')
    mp3_url='http://ss.beva.cn'$mp3_url
    html_url=$(echo $json | jq '.data[0].download')
    html_url=$(echo $html_url | sed 's/\"//g')
    name=$(echo $json | jq '.data[0].name')
    name=$(echo $name | sed 's/\"//g')
    beva_id=$(echo $json | jq '.data[0].id')
    beva_id=$(echo $beva_id | sed 's/\"//g')

    sql="insert into erge.beva (beva_id,name,type,mp3_url,html_url) values ( '$beva_id', '$name', '$title', '$mp3_url', '$html_url');"
    echo $num > last_id.txt
    mysql -uvagrant -pvagrant -e "$sql"
fi

done
echo '爬完了!'
