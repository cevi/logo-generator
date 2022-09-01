<?php

class CsvHelper
{
    private $csv_filename_size = 'csv/svg_sizes.csv';
    private $csv_separator = ';';

    /**
     * Constructor
     *
     * - prepare the used csv files
     */
    public function __construct() {
        $this->prepareCsvFile();
    }

    /**
     * Prepare all the necessary CSV-Files at the beginning of saving all image entries.
     */
    private function prepareCsvFile() {
        if (!file_exists('csv')) {
            mkdir('csv', 0755, true);
        }

        if (!file_exists($this->csv_filename_size)) {
            $file = new SplFileObject($this->csv_filename_size, 'a');
            $file->fputcsv(['name', 'width'], $this->csv_separator);
            $file = null;
        }
    }

    /**
     * Search in CSV for a special $value.
     *
     * @param $file string          filename & path of the csv file
     * @param $value string         search-value which should be found
     * @param $position integer     position of the value in the line-array
     * @return array|false
     *   array: full csv line
     *   false: $value is not available.
     */
    private function searchInCsv($file, $value, $position = 0) {
        $fp = fopen($file, "r");
        while (($record = fgetcsv($fp, null, $this->csv_separator)) !== false) {
            if ($record && isset($record[$position])) {
                if ($record[$position] == $value) {
                    return $record;
                }
            }
        }
        fclose($fp);
        return false;
    }

    /**
     * Get the size entry of the "$name" in our csv.
     *
     * @param $name
     * @return array|false
     */
    public function getSizeEntry($name) {
        return $this->searchInCsv($this->csv_filename_size, $name);
    }

    /**
     * Save a new size ($width) of the $name.
     *
     * @param $name
     * @param $width
     */
    public function saveSizeEntry($name, $width) {
        $file = new SplFileObject($this->csv_filename_size, 'a');
        $file->fputcsv([$name, $width], $this->csv_separator);
        $file = null;
    }
}
