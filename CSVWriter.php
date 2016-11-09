<?php
class CsvWriter
{
   # public $filename;
    public $fp;
    public $delimeter = ';';
    public $limit;
    public $pointer = 0;

    function __construct()
    {
       # $this->filename = 'csv/' . $filename . '.csv';
        $this->fp = fopen('php://output', 'w');
        fputs($this->fp, "\xEF\xBB\xBF");
    }

    function __destruct()
    {
        fclose($this->fp);
    }

    function setHeaders($headers)
    {
        fputcsv($this->fp, $headers, $this->delimeter);
    }

    /**
     * @param [] $row
     */
    function insertLine($row)
    {
        fputcsv($this->fp, $row, $this->delimeter);
        $this->pointer++;
    }
}