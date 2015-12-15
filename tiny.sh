#!/bin/bash

M=`/bin/date +%m`
#数据库链接配置
HOSTNAME="127.0.0.1"
PORT="3306"
USERNAME="root"
PASSWORD="root"
DBNAME="test"
#图片读取路径
dir="/home/lzz/imgs/"
#压缩后图片保存位置
tiny="/home/lzz/imgs_tiny/"
#每个月压缩最大次数
COUNT=50

#读取今日新添加的文件
`/usr/bin/find $dir -name '*.*g' -ctime -1 | /usr/bin/awk -F $dir '{print $NF}' > cname.txt`
#`find $dir -name '*.*g' -ctime -1 -printf "%f\n" > cname.txt`
#比较今日新添加的文件和昨天压缩的文件，获取没有压缩过的文件
`/usr/bin/diff -y --left-column cname.txt tmp.txt | /bin/grep  "<" | /usr/bin/awk '{print $1}' | /bin/grep 'g$' > t.txt`
`/bin/cp t.txt tmp.txt`
#读取需要压缩的文件
list=`/bin/cat /phpstudy/www/swoole/tinyshell/tmp.txt`

#查询可以使用的app key
sql="select app_key,times,month from tinypng"
/phpstudy/mysql/bin/mysql -h${HOSTNAME}  -P${PORT}  -u${USERNAME} -p${PASSWORD} ${DBNAME} -e "${sql}" | while read app_key times month
do
	if [ "$app_key" != "app_key" ]
	then
        #如果时间已经是下一个月那么要更新times=0
	if [ "$month" != "$M" ]
	then
        	sql="update tinypng set times=0,month='$M'"
	        /phpstudy/mysql/bin/mysql -h${HOSTNAME}  -P${PORT}  -u${USERNAME} -p${PASSWORD} ${DBNAME} -e "${sql}"
		times=0
	fi
	#不能超过定义的数
	if [ $times -lt $COUNT ]
	then
	        count=$times
		for file_name in $list
		do
			if [ $count -lt $COUNT ]
			then
				file=$dir$file_name
				#调用压缩文件接口
				echo "curl --insecure --retry 4 --connect-timeout 10 -m 300 --user api:$app_key --data-binary @$file -i http://"
				url_con=`/usr/bin/curl --insecure --retry 4 --connect-timeout 10 -m 300 --user api:$app_key --data-binary @$file -i https://api.tinify.com/shrink`
				#获取压缩文件的地址
				tmp=${url_con#*\"url\":\"}
				url=${tmp%\"\}\}*}
				echo "url:$url"
				#建立相应的文件夹
				tiny_file=$tiny$file_name
				/bin/mkdir -p ${tiny_file%/*} | /bin/grep -v "[\.][pj][np]g$"
				#下载文件
				echo "wget -t 4 -T 10 -c --no-check-certificate $url -O $tiny_file"
				`/usr/bin/wget -t 4 -T 10 -c --no-check-certificate $url -O $tiny_file --user-agent="Mozilla/5.0 (X11;U;Linux i686;en-US;rv:1.9.0.3) Geco/2008092416 Firefox/3.0.3"`
				if [ $(stat -c %s $tiny_file) -eq 0 ];then
					`/usr/bin/wget -t 1 -T 10 -c --no-check-certificate $url -O $tiny_file`
				fi
				count=`expr $count + 1`
				#更新该接口的使用次数
				sql="update tinypng set times=times+1 where app_key='$app_key'"
				/phpstudy/mysql/bin/mysql -h${HOSTNAME}  -P${PORT}  -u${USERNAME} -p${PASSWORD} ${DBNAME} -e "${sql}"
			else
			break;
			fi
		done
	fi
	
	fi
done

#判断哪些文件压缩后为空，说明该文有错就删除
/bin/cat tmp.txt | while read tmp_file
do
	file=$tiny$tmp_file
	if [ $(stat -c %s $file) -eq 0 ];then
		/bin/rm -f $file
	fi
done

#将压缩后的文件拷贝到目标文件夹中，如果目标文件夹的文件比较新则不替换
`/bin/cp -ur $tiny* $dir`
#将tiny_imgs 中的文件删除
`/bin/rm -rf $tiny*`
