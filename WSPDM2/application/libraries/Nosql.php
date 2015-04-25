<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 用于nosql的操作
 * 
 * 
 *
 * @copyright  版权所有(C) 2014-2015 沈阳工业大学ACM实验室 沈阳工业大学网络管理中心 *Chen
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt   GPL3.0 License
 * @version    
 * @link       https://github.com/SUTFutureCoder/
*/
class Nosql{
    
    /**    
     *  @Purpose:    
     *  判断是否为nosql
     *  
     *  @Method Name:
     *  CheckNosql($db_type)
     *  @Parameter: 
     *  $db_type    数据库类型
     * 
     *  @Return: 
     *  0   非nosql数据库
     *  1   nosql数据库
     * 
    */
    public function CheckNosql($db_type){
        return in_array($db_type, array(
            'MongoDB'
        ));
    }
}