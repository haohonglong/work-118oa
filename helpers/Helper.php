<?php

namespace app\helpers;


class Helper
{
    /**
     * 日期范围字符串转换数组
     * @param $finished
     * @return array|null
     */
    public static function daterangeToArray($finished){
        if(!empty(trim($finished))){
            $finished = explode(' - ',$finished);
            $finished[0] = trim($finished[0]).' 00:00:00';
            $finished[1] = trim($finished[1]).' 23:59:59';
        }else{
            $finished = null;
        }
        return $finished;
    }


    /**
     * @param $fun   callback
     * @param string $name excle 文件名
     */
    public static function exportExcle($fun,$name='')
    {
        $filename=$name.'_'.date('ymd');
        $PHPExcel = new \PHPExcel();
        call_user_func($fun,$PHPExcel,$name);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = \PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }

}