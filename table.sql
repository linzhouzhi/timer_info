create database swoole;

CREATE TABLE  ip_list  (
   id  int(10) NOT NULL AUTO_INCREMENT,
   ip  varchar(16) NOT NULL DEFAULT '',
  PRIMARY KEY ( id )
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=gbk;

CREATE TABLE  call_url  (
   id  int(11) NOT NULL AUTO_INCREMENT,
   url  varchar(255) NOT NULL DEFAULT '',
   exe_time  int(11) NOT NULL DEFAULT '0',
   status  tinyint(2) NOT NULL COMMENT '-1 表示重试后还失败，1表示已经执行过，0还没执行',
   type  tinyint(2) NOT NULL DEFAULT '0' COMMENT '0 执行一次不执行了，1执行类似定时任务时间到了就要执行',
  PRIMARY KEY ( id )
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=gbk;
