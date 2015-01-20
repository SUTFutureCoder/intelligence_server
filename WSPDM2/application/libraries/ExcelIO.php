<?php
require_once 'PHPExcel/IOFactory.php';
/**
 * 
 * Enter description here ...
 * @author Aaron
 *
 */
class ExcelIO
{
	private $fileName;
	private $excelReader = NULL;
	private $objPhpExcel = NULL;
	private $excelWriter = NULL;
	private $valueArray;
	private $tableHeader = array();
	private $fileType = '';
	/**
	 * 
	 * Enter description here ...
	 * @param array $config  
	 * @description $config是用于存储属性的数组，$key=>$value格式进行存储
	 */
	public function __construct($config = array())
	{
		log_message('DEBUG','ExcelIO class Initialized');
		if(!empty($config))
		{
			foreach($config as $key => $value)
			{
				$method = 'set_'.$key;
				if (method_exists($this, $method))
				{
					$this->$method($config[$key]);
				}
				else
				{
					$this->$key = $config[$key];
				}
			}
		}
	}
	/**
	 * 
	 * 读取excel文件时创建Reader
	 * @param string $fileName 要读取的文件的全路径
	 * @param string $fileType Excel2007,Excel2003XML,OOCalc,SYLK,Gnumeric,CSV
	 */
	public function setReader($fileName = '',$fileType = '')
	{
		if(is_file($fileName))
		{
			$this->fileName = $fileName;
		}
		if(!empty($fileType) && checkType($fileType))
		{
			$this->fileType = $fileType;
		}
		//如果不知道文档类型，则使用PHPExcel_IOFactory的createReaderForFile方法创建reader
		if(empty($this->fileType))
		{
			$this->excelReader = PHPExcel_IOFactory::createReaderForFile($this->fileName);
			return true;
		}
		else
		{
			//如果不知道文档类型，则使用PHPExcel_IOFactory的createReader方法创建reader
			$this->excelReader = PHPExcel_IOFactory::createReader($this->fileType);
			return true;
		}
	}
	/**
	 * 
	 * 内部函数，校验用户要创建的文档类型是否有效
	 *@param string $fileType  用户要创建的文件类型
	 */
	private function checkType($fileType)
	{
		$permit = array('Excel2007','Excel2003XML','OOCalc','SYLK','Gnumeric','CSV');
		if(!in_array($fileType,$permit))
		{
			return false;
		}
		return true;
	}
	/**
	 * 
	 * 在生成excel文档时，需要先得到PHPExcel对象
	 */
	public function initWriter()
	{
		//得到PHPExcel对象
		require_once 'PHPExcel.php';
		$objExcel = new PHPExcel();
		$this->objPhpExcel = $objExcel;
	}
	/**
	 * 
	 * 设置Reqder要加载的sheet
	 * @param mixed $sheetName  为要加载的sheet的名称或名称列表
	 */
	public function setSheet($sheetName = '')
	{
		if(!is_object($this->excelReader))
		{
			exit('PHPExcel Reader is not init!');
		}
		if(empty($sheetName))
		{
			return;
		}
		$this->excelReader->setLoadSheetsOnly($sheetName);
	}
	/**
	 * 
	 * 得到某一个sheet的值
	 *@param int $sheetIndex excel表格的sheet索引，如果不传该参数，取文件的当前sheet
	 */
	public function getSheetValues($sheetIndex = NULL)
	{
		$this->objPhpExcel = $this->excelReader->load($this->fileName);
		if(isset($sheetIndex))
		{
			$this->objPhpExcel->setActiveSheetIndex($sheetIndex);
		}
		$curSheet = $this->objPhpExcel->getActiveSheet();
		$this->valueArray = $curSheet->toArray(null,true,true,true);
		return $this->valueArray;
	}
	/**
	 * 
	 * 将数组输出为excel表格
	 *@param array $dataArray 要写入excel的数据，为键值对的二维数组
	 *@param boolean $isOutPut 是否输出到浏览器
	 *@param array $tableHeader 表格的标题头
	 */
	public function outPutExcel($dataArray,$isOutPut = false,$tableHeader = array())
	{
		if(!is_object($this->objPhpExcel))
		{
			exit('output error。');
		}
		$curSheet = &$this->objPhpExcel->getActiveSheet();
		$row = 1;
		if(!empty($tableHeader))
		{
			$this->tableHeader = $tableHeader;
			//第一列为A
			$col = 65;
			foreach($tableHeader as $value)
			{
				$curSheet->setCellValue(chr($col) . '1', $value);
				$col++;
			}
			//如果有表头，则数据需要从第二行开始
			$row++;
		}
		if(!empty($dataArray))
		{
			foreach($dataArray as $oneRow)
			{
				//如果没有表头，把数组键值设为数字键值
				if(empty($tableHeader))
				{
					$oneRow = array_values($oneRow);
				}
				foreach($oneRow as $key=>$value)
				{
					$col = $this->getColByKey($key);
					$curSheet->setCellValue($col . $row, $value);					
				}
				$row++;
			}
		}
		//生成文件,根据生成的PHPExcel,生成writer对象
		$this->excelWriter = PHPExcel_IOFactory::createWriter($this->objPhpExcel,$this->fileType);
		if($isOutPut)
		{
			//header("Content-Type:application/force-download");//强制下载
			header("Content-Type:application/vnd.ms-execl");//文件的mime类型
			header("Content-Type:application/octet-stream");
			//header("Content-Type:application/download");;
			header('Content-Disposition:attachment;filename="'.$this->fileName.'"');
			header("Content-Transfer-Encoding:binary");
			$this->excelWriter->save('php://output');
		}
		else
		{
			$this->excelWriter->save($this->fileName);
		}
	}
	/**
	 * 
	 * 内部函数，用来得到列号
	 *@param mixed $key 数据的键值，用来得到列
	 */
	private function getColByKey($key)
	{
		if(!empty($this->tableHeader))
		{
			$num = array_search($key,$this->tableHeader);
			return chr(65 + $num);
		}
		else
		{
			//如果没有表头，则列为数据的数字键+65，A的asc码为65
			return chr($key + 65);
		}
	}
}