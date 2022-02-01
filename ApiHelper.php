<?php

class LogoGenerator {
    public static function claimData($id, $session_id, $image_type, $logo_left, $logo_right, $logo_right_second, $claim_left, $claim_right, $color) {
        return [
            $id,
            $session_id,
            $image_type,
            $logo_left,
            $logo_right,
            $logo_right_second,
            $claim_left,
            $claim_right,
            $color
        ];
    }

    public static function logoData($id, $session_id, $image_type, $logo_left, $logo_right, $logo_right_second, $color) {
        return [
            $id,
            $session_id,
            $image_type,
            $logo_left,
            $logo_right,
            $logo_right_second,
            $color
        ];
    }

    public static function sessionData($session_id, $time) {
        return [
            $session_id,
            $time
        ];
    }
}

class ApiHelper
{
    private $csv_filename_session = 'csv/session_entries.csv';
    private $csv_filename_logo = 'csv/logo_entries.csv';
    private $csv_filename_claim = 'csv/claim_entries.csv';
    private $csv_separator = ';';
    // timeout for the api: 15 minutes (900 seconds)
    private $timeout = 900;

    /**
     * Generate a random string of 0-9a-zA-Z
     *
     * @param int $length
     * @return string
     */
    private function generateRandomString($length = 12) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Get Session Timestamps
     * ----------------------
     * - Get all session timestamps
     * - Gett line count of the csv-file with the sessions
     *
     * @return array
     *  [
     *      line_count => x,
     *      timestamps => [192384932, 129384928, ...]
     *  ]
     */
    private function getSessionTimestamps() {
        $handle = fopen('../' . $this->csv_filename_session, "r");
        $timestamps = [];
        $lineCount = 0;

        while(!feof($handle)){
            $line = fgets($handle);
            if ($lineCount > 0) {
                // split at ";" or "\n" (end of $line).
                $lineContent = preg_split("/;|\\n/", $line);
                if (isset($lineContent[1]) && $lineContent[1]) {
                    $timestamps[] = intval($lineContent[1]);
                }
            }
            $lineCount++;
        }

        fclose($handle);

        return [
            'line_count' => $lineCount,
            'timestamps' => $timestamps
        ];
    }

    private function countFileLines($filePath) {
        $lineCount = 0;
        $handle = fopen($filePath, "r");
        while(!feof($handle)){
            $line = fgets($handle);
            $lineCount++;
        }

        fclose($handle);
        return $lineCount;
    }

    private function countImageTypes($filename) {
        $fp = fopen('../' . $filename, "r");
        $data = [
            'count_svg' => 0,
            'count_jpg' => 0,
            'count_png' => 0
        ];

        // first line: header data
        fgetcsv($fp, 0, $this->csv_separator);

        while (($line = fgetcsv($fp, 0, $this->csv_separator)) !== FALSE) {
            // update image_type counter
            $data['count_' . $line[2]] += 1;
        }
        fclose($fp);

        return $data;
    }

    /**
     * Get record length
     * Problem: Not all session-numbers have the same length.
     * Look at the last line of the file, read it to find out how long one line is exactly.
     * @see https://stackoverflow.com/a/24483261/2241151
     */
    private function getRecordLength($file_path) {
        $line = '';

        $f = fopen($file_path, 'r');
        $cursor = -1;

        fseek($f, $cursor, SEEK_END);
        $char = fgetc($f);

        /**
         * Trim trailing newline chars of the file
         */
        while ($char === "\n" || $char === "\r") {
            fseek($f, $cursor--, SEEK_END);
            $char = fgetc($f);
        }

        /**
         * Read until the start of file or first newline char
         */
        while ($char !== false && $char !== "\n" && $char !== "\r") {
            /**
             * Prepend the new char
             */
            $line = $char . $line;
            fseek($f, $cursor--, SEEK_END);
            $char = fgetc($f);
        }

        return strlen($line);
    }

    /**
     * Search in CSV for a special $value.
     *
     * @param $file string          filename & path of the csv file
     * @param $value string         search-value which should be found
     * @param $position integer     position of the value
     * @return array|false
     *   array: full csv line
     *   false: $value is not available.
     */
    private function searchInCsv($file, $value, $position = 0) {
        $fp = fopen($file, "r");
        $recordLength = $this->getRecordLength($file) + 1;
        $fileSize = filesize($file);
        $cursor = $fileSize;
        fseek($fp, $cursor);
        while ($cursor >= 0)
        {
            $record = fgetcsv($fp, 0, $this->csv_separator);

            if ($record && isset($record[$position])) {
                if ($record[$position] == $value) {
                    return $record;
                }
            }

            $cursor = $cursor - $recordLength;
            fseek($fp, $cursor);
        }
        fclose($fp);
        return false;
    }

    private function getLatestId($file) {
        $file = new SplFileObject($file, 'r');
        $file->seek(PHP_INT_MAX);
        return $file->key();
    }

    /**
     * Check if the csv_filename_session-file exists already.
     *
     * @return bool
     */
    private function checkIfCsvExists() {
        return file_exists($this->csv_filename_session);
    }

    /**
     * Prepare all the necessary CSV-Files at the beginning of saving all image entries.
     */
    private function prepareCsvFile() {
        $csv_header_logo = LogoGenerator::logoData('id', 'session_id', 'image_type', 'logo_left', 'logo_right', 'logo_right_second', 'color');
        $csv_header_claim = LogoGenerator::claimData('id', 'session_id', 'image_type', 'logo_left', 'logo_right', 'logo_right_second', 'claim_left', 'claim_right', 'color');
        $csv_header_session = LogoGenerator::sessionData('session_id', 'time');

        if (!file_exists('csv')) {
            mkdir('csv', 0755, true);
        }

        $file = new SplFileObject($this->csv_filename_session, 'a');
        $file->fputcsv($csv_header_session, $this->csv_separator);
        $file = null;

        $file = new SplFileObject($this->csv_filename_logo, 'a');
        $file->fputcsv($csv_header_logo, $this->csv_separator);
        $file = null;

        $file = new SplFileObject($this->csv_filename_claim, 'a');
        $file->fputcsv($csv_header_claim, $this->csv_separator);
        $file = null;
    }

    private function generateId() {
        session_start();
        $session_id = session_id();

        if (strlen($session_id) < 1) {
            $session_id = $this->generateRandomString(32);
        }

        return $session_id . '-' . $this->generateRandomString(8);
    }

    /**
     * Save the id to the sessions-file.
     *
     * @param $id
     */
    private function saveId($id) {
        if (!$this->checkIfCsvExists()) {
            $this->prepareCsvFile();
        }
        $time = time();
        $entry = LogoGenerator::sessionData(
            $id,
            $time
        );
        $file = new SplFileObject($this->csv_filename_session, 'a');
        $file->fputcsv($entry, $this->csv_separator);
        $file = null;
    }

    /**
     * Get a new session-id to save to the browser of the current user.
     * @return string
     */
    function getNewSessionId() {
        $id = $this->generateId();
        $this->saveId($id);

        return $id;
    }

    /**
     * Check if session id is valid:
     *   - $id is in the entries
     *   - $timeout is not yet reached
     *
     * @param $id
     * @return bool
     */
    function checkSessionId($id) {
        // check if the record is found in the csv-file
        if (!$record = $this->searchInCsv($this->csv_filename_session, $id)) {
            return false;
        }

        // check if the entry is too old for a new entry in the csv-files.
        if ($record[1] + $this->timeout < time()) {
            return false;
        }

        return true;
    }

    function saveDataLogo($data) {
        $id = $this->getLatestId($this->csv_filename_logo);

        $logoData = LogoGenerator::logoData(
            $id,
            $data['session_id'],
            $data['image_type'],
            $data['logo_left'],
            $data['logo_right'],
            $data['logo_right_second'],
            $data['color']
        );

        $file = new SplFileObject($this->csv_filename_logo, 'a');
        $file->fputcsv($logoData, $this->csv_separator);
        $file = null;
    }

    function saveDataClaim($data) {
        $id = $this->getLatestId($this->csv_filename_claim);

        $claimData = LogoGenerator::claimData(
            $id,
            $data['session_id'],
            $data['image_type'],
            $data['logo_left'],
            $data['logo_right'],
            $data['logo_right_second'],
            $data['claim_left'],
            $data['claim_right'],
            $data['color']
        );

        $file = new SplFileObject($this->csv_filename_claim, 'a');
        $file->fputcsv($claimData, $this->csv_separator);
        $file = null;
    }

    function adminGetVisitors() {
        $sessionData = $this->getSessionTimestamps();
        $sessionIdCounter = $sessionData['line_count'] - 2;
        $claimCounter = $this->countFileLines('../' . $this->csv_filename_claim) - 2;
        $logoCounter = $this->countFileLines('../' . $this->csv_filename_logo) - 2;
        return [
            'session' => $sessionIdCounter,
            'claim' => $claimCounter,
            'logo' => $logoCounter,
            'timestamps' => $sessionData['timestamps'],
        ];
    }

    function adminGetLogoData() {
        $imageTypeLogo = $this->countImageTypes($this->csv_filename_logo);
        $imageTypeClaim = $this->countImageTypes($this->csv_filename_claim);

        return [
            'image_type_logo' => $imageTypeLogo,
            'image_type_claim' => $imageTypeClaim,
        ];
    }
}
