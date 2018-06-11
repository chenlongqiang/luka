#!bin/bash
case $1 in
    "hello")
        echo 'hello, how are you?'
        ;;
    "")
        echo 'must input!'
        ;;
    *)
        echo 'default'
        ;;
esac
