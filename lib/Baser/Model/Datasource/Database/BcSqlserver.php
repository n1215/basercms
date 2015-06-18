<?php
/**
 * MySQL DBO拡張
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model.Datasource.Database
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('Sqlserver', 'Model/Datasource/Database');

class BcSqlserver extends Sqlserver {

    /**
     * Gets the database encoding
     *
     * @return string The database encoding
     */
    public function getEncoding() {
        return 'utf8';
    }


    /**
     * Returns a quoted and escaped string of $data for use in an SQL statement.
     *
     * @param string $data String to be prepared for use in an SQL statement
     * @param string $column The column datatype into which this data will be inserted.
     * @return string Quoted and escaped data
     */
    public function value($data, $column = null) {

        switch ($column) {
            case 'date':
            case 'datetime':
            case 'timestamp':
            case 'time':
                // postgresql の場合、0000-00-00 00:00:00 を指定すると範囲外エラーとなる為
                if ($data === '0000-00-00 00:00:00') {
                    return "'" . date('Y-m-d H:i:s', 0) . "'";
                }
                return parent::value($data, $column);
            break;
            default:
                return parent::value($data, $column);
        }
    }

}
