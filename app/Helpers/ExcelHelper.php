<?php
/*
 * Reading Po (Purchase Order File) Of Excel
 * */
if (!function_exists('readExcelPoFile')) {
    /**
     * @param $FileUploadPath
     * @return mixed
     * @throws PHPExcel_Exception
     * @throws PHPExcel_Reader_Exception
     */
    function readExcelPoFile($uploadFilePathToRead)
    {

        try {
            $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($uploadFilePathToRead);
            $objReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
            $objPhpSpreadSheet = $objReader->load($uploadFilePathToRead);
        } catch (Exception $e) {
            die('Error loading file "' . pathinfo($uploadFilePathToRead, PATHINFO_BASENAME)
                . '": ' . $e->getMessage());
        }

        //  Get worksheet dimensions
        $sheet = $objPhpSpreadSheet->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $headings = $sheet->rangeToArray('A1:' . $highestColumn . 1, NULL, TRUE, FALSE);

        $result = [];
        for ($row = 2; $row <= $highestRow; $row++){
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
            $rowData[0] = array_combine(spaceToUnderscore($headings[0]), $rowData[0]);

            array_push($result, $rowData[0]);
        }

        if(File::exists($uploadFilePathToRead)) {
            File::delete($uploadFilePathToRead);
        }

        unset($objPhpSpreadSheet);
        unset($uploadFilePathToRead);

        return $result;










//        $data = array();
//        $objPhpSpreadSheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($uploadFilePathToRead);
//        //get only the Cell Collection
//        $cellCollection = $objPhpSpreadSheet->getActiveSheet()->getCoordinates();
//        //extract to a PHP readable array format
//        foreach ($cellCollection as $cell) {
//            $column = $objPhpSpreadSheet->getActiveSheet()->getCell($cell)->getColumn();
//            $row = $objPhpSpreadSheet->getActiveSheet()->getCell($cell)->getRow();
//            $dataValue = $objPhpSpreadSheet->getActiveSheet()->getCell($cell)->getValue();
//            //The header will/should be in row 1 only. of course, this can be modified to suit your need.
//            if ($row == 1) {
//                $header[$row][$column] = $dataValue;
//            } else {
//                $arrData[$row][$column] = $dataValue;
//            }
//        }

        if(File::exists($uploadFilePathToRead)) {
            File::delete($uploadFilePathToRead);
        }
        unset($objPhpSpreadSheet);
        unset($uploadFilePathToRead);

        //send the data in an array format
        if (isset($header) && $arrData){
            $data['header'] = $header;
            $data['values'] = $arrData;
        }

        return $data;

    }
}

if(!function_exists('getDataFromExcelFile')) {
    /**
     * @param $fileName
     * @return array
     */
    function getDataFromExcelFile($request, $dirToUploadFile)
    {
        $uploadPath = public_path('uploads/'.$dirToUploadFile.'/');
        $fileName = $request->getClientOriginalName();
        $uploadFilePathToRead = $uploadPath . $fileName;

        $request->move($uploadPath, $fileName);

        try {
            $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($uploadFilePathToRead);
            $objReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
            $objReader->setReadDataOnly(true);
            $objPhpSpreadSheet = $objReader->load($uploadFilePathToRead);

        } catch (Exception $e) {
            die('Error loading file "' . pathinfo($uploadFilePathToRead, PATHINFO_BASENAME)
                . '": ' . $e->getMessage());
        }

        $sheet = $objPhpSpreadSheet->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $keys = array();
        $results = array();

        for ($row = 2; $row <= $highestRow; $row++) {
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);

            if ($row === 2) {
                $keys = $rowData[0];
            } else {
                $record = array();
                foreach ($rowData[0] as $pos => $value) {
                    $record[remOf(redundant(redundantAll(spaceToUnderscore(percentageToNull(dashToNull(removeLeftParantesis(removeRightParantesis(remQuestionMark(removeSlashnAndr($keys))))))))
                    ))[$pos]] = $value;
                }

                $results[] = $record;
            }
        }
        if(File::exists($uploadFilePathToRead)) {
            File::delete($uploadFilePathToRead);
        }
        unset($objPhpSpreadSheet);
        unset($uploadFilePathToRead);
        return $results;
    }
}

if(!function_exists('uploadCsvFile')) {

    /**
     * @param $value
     */
    function uploadCsvFile($request, $dirToUploadFile)
    {
        $uploadPath = public_path('uploads/'.$dirToUploadFile.'/');
        $fileName = $request->getClientOriginalName();
        $uploadFilePathToRead = $uploadPath . $fileName;

        $request->move($uploadPath, $fileName);

        if(File::exists($uploadFilePathToRead)) {

            return $uploadFilePathToRead;
        }

        return false;

    }

}