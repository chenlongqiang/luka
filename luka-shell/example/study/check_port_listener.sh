#!/bin/bash
if [ "$1" == "" ]; then
    echo "请输入端口"
    exit 1
fi
if [ $(sudo netstat -anp | grep :"$1 " | wc -l) -eq 0 ]; then 
    echo "该端口未被监听"
else 
    echo "端口在监听:"
    sudo netstat -anp | grep :"$1 "
fi
