<?php

//适配器模式
interface IDatabase {
    //基础函数
    //数据库连接
    function connect($db_type, $host, $user, $passwd);
    //执行简单查询(不返回结果的查询 INSERT/UPDATE/DELETE)
    function exec($sql);
    //执行select查询
    function query($sql, $row_count);
    //关闭连接
    function close();
      
    //拓展
    //获取数据库基础信息
    //可以尝试使用memcache
    function getDbBasic();    
    //获取表数据
    function getTableData();
}
