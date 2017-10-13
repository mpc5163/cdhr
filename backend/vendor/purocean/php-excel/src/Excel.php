<?php

namespace YExcel;

use \PHPExcel_IOFactory as IOFactory;
use \PHPExcel_Cell as Cell;
use \PHPExcel as PHPExcel;

/**
* 导入导出
*/
class Excel
{
    /**
     * 从 Excel 获取所有行
     *
     * @param string   $file          xlsx 文件路径
     * @param int|null $highestColumn 列数，为 null 时候自动检测
     * @param array    $skipRows      跳过的行，默认跳过第一行（表头）
     * @param bool     $skipBlankRow  是否跳过空白行，默认为 true
     *
     * @return generator 可遍历的生成器
     */
    public static function get($file, $highestColumn = null, $skipRows = [1], $skipBlankRow = true)
    {

        $objReader = IOFactory::createReader('Excel2007');
        $objPHPExcel = $objReader->load($file);
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        is_null($highestColumn) and $highestColumn = Cell::columnIndexFromString($sheet->getHighestColumn());

        for ($row = 1; $row <= $highestRow; ++$row) {
            if (in_array($row, $skipRows)) {
                continue;
            }

            $rowData = [];
            for ($col = 0; $col < $highestColumn; $col++) {
                $value = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($col, $row)->getValue();
                $rowData[] = is_null($value) ? '' : $value;
            }

            if ($skipBlankRow) {
                if (!array_filter($rowData)) {
                    continue;
                }
            }

            yield $rowData;
        }
    }

    /**
     * 把数据写入一个文件
     *
     * @param string          $file    文件名
     * @param array|generator $data    数据，可以被 foreach 遍历的数据，数组或者生成器
     * @param string          $tplFile 模板文件，以哪个模板填写数据，如果不提供则生成空白 xlsx 文件
     * @param int             $skipRow 跳过表头的行数，默认为 1
     */
    public static function put($file, $data, $tplFile = null, $skipRow = 1)
    {
        if ($tplFile) {
            if (file_exists($tplFile)) {
                $objReader = IOFactory::createReader('Excel2007');
                $objPHPExcel = $objReader->load($tplFile);
            } else {
                throw new \Exception("File `{$tplFile}` not exists");
            }
        } else {
            $objPHPExcel = new PHPExcel();
        }

        $objPHPExcel->setActiveSheetIndex(0);
        $objSheet=$objPHPExcel->getActiveSheet();
        $objSheet->setTitle('export');

        $rowNum = 1;
        foreach ($data as $row) {
            $colNum = 0;
            foreach ($row as $val) {
                $objSheet->setCellValueByColumnAndRow(
                    $colNum,
                    $rowNum + $skipRow,
                    $val
                );
                ++$colNum;
            }
            ++$rowNum;
        }

        IOFactory::createWriter($objPHPExcel, 'Excel2007')->save($file);
    }
}
