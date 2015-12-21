#!/bin/bash
root_dir="/phpstudy/www/swoole/api_docs/"
api_dir=${root_dir}"dirapi/"
api_doc=${root_dir}"apidoc/"

filelist=`ls ${api_dir}`
for file in $filelist
do
	cat ${api_dir}$file | egrep "function|\*" | egrep -v ';' > ${root_dir}tmp.txt
	sed -i '1 i<pre>' ${root_dir}tmp.txt
	sed -i '$ i</pre>' ${root_dir}tmp.txt
	cat ${root_dir}tmp.txt > ${api_doc}$file	
done

