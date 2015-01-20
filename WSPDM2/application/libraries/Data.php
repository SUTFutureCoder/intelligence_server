<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 数据传输
 * 
 * 
 *
 * @copyright  版权所有(C) 2014-2014 沈阳工业大学ACM实验室 沈阳工业大学网络管理中心 *Chen
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt   GPL3.0 License
 * @version    2.0
 * @link       http://acm.sut.edu.cn/
 * @since      File available since Release 2.0
*/
class Data{
    
    /**    
     *  @Purpose:    
     *  输出数据   
     *  @Method Name:
     *  Out($pipe = NULL, $aim = NULL, $code = NULL, $describe = NULL, $err_id = NULL)
     *  @Parameter: 
     *  $pipe 传输媒介，例如：iframe
     *  $aim  目标地址
     *  $code 状态码
     *  $describe 状态描述
     *  $data_or_err_id 出错前端表单id，用于智能聚焦.兼容数据传输功能
     *  @Return: 
     *  
    */
    public function Out($pipe = NULL, $aim = NULL, $code = NULL, $describe = NULL, $data_or_err_id = NULL){
        echo json_encode(array(
            '0' => $pipe,
            '1' => $aim,
            '2' => $code,
            '3' => $describe,
            '4' => $data_or_err_id
        ));
        exit();        
    }
    
    /**    
     *  @Purpose:    
     *  输出数据 (未json_encode)  
     *  @Method Name:
     *  OutUncode($pipe = NULL, $aim = NULL, $code = NULL, $describe = NULL, $err_id = NULL)
     *  @Parameter: 
     *  $pipe 传输媒介，例如：group

     *  $aim  目标地址
     *  $code 状态码
     *  $describe 状态描述
     *  $data_or_err_id 出错前端表单id，用于智能聚焦.兼容数据传输功能
     *  @Return: 
     *  
    */
    public function OutUncode($pipe = NULL, $aim = NULL, $code = NULL, $describe = NULL, $data_or_err_id = NULL){
        echo array(
            '0' => $pipe,
            '1' => $aim,
            '2' => $code,
            '3' => $describe,
            '4' => $data_or_err_id
        );
        exit();        
    }
            
}