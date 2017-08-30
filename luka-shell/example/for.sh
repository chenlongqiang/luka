#!/bin/bash
NUMS="1 2 3 4 5 6 7"
for NUM in $NUMS
do
    if [ $NUM -eq 3 ]
    then
        echo "Number is an even number!!"
        break
    fi
    echo "number is: "$NUM
done
