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
     * Search in CSV for a special $value.
     *
     * @param $file string          filename & path of the csv file
     * @param $value string         search-value which should be found
     * @param $position integer     position of the value
     * @return array|false
     *   array: full csv line
     *   false: $value is not available.
     */
    private function searchInCsv($file, $value, $position = 0, $recordLength = 47) {
        $fp = fopen($file, "r");
        $cursor = filesize($file);
        fseek($fp, $cursor);
        while ($cursor >= 0)
        {
            $record = fgetcsv($fp, 0, $this->csv_separator);
            if ($record) {
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
            $session_id = $this->generateRandomString(26);
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
}
