create database swoole;

create table ip_list(
id int(10) not null auto_increment primary key,
ip varchar(16) not null default ''
);

create table call_url(
id int(11) not null auto_increment primary key,
url varchar(255) not null default '',
exe_time int(11) not null default 0
);
