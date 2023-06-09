<?php

/**
 * This is used to save feed
 *
 * @since      1.0.0
 * @package    Woo_Feed
 * @subpackage Woo_Feed/includes
 * @author     Ohidul Islam <wahid@webappick.com>
 */
class Woo_Feed_Savefile {

    /**
     * Check if the directory for feed file exist or not and make directory
     *
     * @param $path
     * @return bool
     */
    public function checkDir( $path ) {
        if ( ! file_exists($path) ) {
            return wp_mkdir_p($path);
        }
        return true;
    }

	/**
	 * Save Common Feed file - CSV, TSV, XLS
	 *
	 * @param $path
	 * @param $file
	 * @param $content
	 * @param $info
	 * @param $type
	 *
	 * @return bool
	 */
	public function saveValueFile( $path, $file, $content, $info, $type ) {
		if ( $this->checkDir( $path ) ) {
			/**
			 * @TODO see below
			 * @see Woo_Feed_Savefile::saveFile()
			 */
			if ( file_exists( $file ) ) {
				unlink( $file ); // phpcs:ignore
			}

            $enclosure = $info['enclosure'];
            $eol       = PHP_EOL;

            if ( 'csv' === $type ) {
                $fp = fopen( $file, 'wb' ); // phpcs:ignore

                $delimiter = $info['delimiter'];
            }elseif ( 'tsv' === $type ) {
                $fp = fopen( $file, 'wb' ); // phpcs:ignore
                $delimiter = "\t";

            }elseif ( 'xls' === $type ) {
                $fp = fopen( $file, 'w' ); // phpcs:ignore
                $delimiter = "\t";
            }elseif ( 'xlsx' === $type ) {
	            $fp = fopen( $file, 'w' ); // phpcs:ignore
	            $delimiter = "\t";
            }elseif ( 'json' === $type ) {
                $fp = fopen( $file, 'w' ); // phpcs:ignore

                if ( isset($info['provider']) && 'adform' === $info['provider'] ) {
                    $json_content['products']['product'] = $content;
                }else {
                    $json_content = $content;
                }

                fwrite($fp, json_encode($json_content));
            }


            if ( 'csv' === $type || 'tsv' === $type ) {
                if ( 'trovaprezzi' === $info['provider'] ) {
                    $eol = '<endrecord>' . PHP_EOL;
                }
                if ( count( $content ) ) {
                    foreach ( $content as $fields ) {
                        if ( 'double' == $enclosure ) {
                            fputcsv( $fp, $fields, $delimiter, chr( 34 ) ); // phpcs:ignore
                        } elseif ( 'single' == $enclosure ) {
                            fputcsv( $fp, $fields, $delimiter, chr( 39 ) ); // phpcs:ignore
                        } else {
                            fputs( $fp, implode( $delimiter, $fields ) . $eol ); // phpcs:ignore
                        }
                    }
                }
            }elseif ( 'xls' === $type || 'xlsx' === $type ) {
                $xl_col_value = "";
                if ( count( $content ) ) {
                    foreach ( $content as $key => $fields ) {

                        foreach ( $fields as $value ) {
                            $value = preg_replace('/\\s+/', ' ', $value); //remove double whitespaces
                            $xl_col_value .= $value . $delimiter;
                        }

                        $xl_col_value .= "\n";
                    }
                }

                fwrite($fp, $xl_col_value);
            }


			fclose( $fp ); // phpcs:ignore

			return true;
		} else {
			return false;
		}
	}

	/**
	 * Save XML and TXT File
	 *
	 * @param $path
	 * @param $file
	 * @param $content
	 *
	 * @return bool
	 */
	public function saveFile( $path, $file, $content ) {
		/**
		 * @TODO use WP Filesystem API
		 * @see https://codex.wordpress.org/Filesystem_API
		 * @see http://ottopress.com/2011/tutorial-using-the-wp_filesystem/
		 *
		 * @TODO Check write permission on installation and show admin warning
		 * @see wp_is_writable()
		 */
		if ( $this->checkDir( $path ) ) {
			if ( file_exists( $file ) ) {
				unlink( $file ); // phpcs:ignore
			}
			$fp = fopen( $file, 'w' ); // phpcs:ignore
			fwrite( $fp, $content ); // phpcs:ignore
			fclose( $fp ); // phpcs:ignore

			return true;
		} else {
			return false;
		}
	}
}
