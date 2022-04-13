<?php

namespace WP_Smart_Image_Resize\Utilities;

use Exception;

class Backup
{
    const BACKUP_DIR = 'wp_sir_backups';

    /**
     * @param $file
     * @return bool
     * @throws Exception
     */
    public function create( $file )
    {
        if ( !file_exists( $file ) ) {
            throw new Exception( "Файл не найден: $file" );
        }

        $backupFile = $this->getBackupFile($file);
      
        if ( !wp_mkdir_p( dirname( $backupFile ) ) ) {
            throw new Exception( "Не удается создать папку резервного копирования, проверьте права доступа к папке uploads." );
        }

        if ( !copy( $file, $backupFile ) ) {
            throw new Exception( "Невозможно создать резервную копию, проверьте файл error_log для отладки." );
        }

        return true;

    }

    public function exists( $file )
    {
        return file_exists( $this->getBackupFile( $file ) );
    }
    
    public function getBackupFile($file){
        return $this->getUploadsDirectory() . trailingslashit( self::BACKUP_DIR ) . $this->getRelativePath( $file );
    }
    
    public function delete( $file )
    {
        $backupFile = $this->getBackupFile($file);

        if ( file_exists( $backupFile ) ) {
            unlink( $backupFile );
        }
    }

    /**
     * @param $file
     * @return bool
     * @throws Exception
     */
    function restore( $file )
    {
        $backupFile = $this->getBackupFile($file);

        if ( !file_exists( $backupFile ) ) {
            throw new Exception( 'Не найдена резервная копия для: ' . $file );
        }

        if ( !copy( $backupFile, $file ) ) {
            throw new Exception( "Невозможно восстановить файл: [$file], проверьте файл error_log для отладки." );
        }

        unlink( $backupFile );
        
        // TODO: remove folder if empty.
        
        return true;
    }

    function clear()
    {
        // Safety check.
        if( empty( trim( self::BACKUP_DIR ) ) ){
            return;
        }

        File::rrmdir( $this->getUploadsDirectory() . self::BACKUP_DIR );
    }

    private function getRelativePath( $file )
    {
        return str_replace( $this->getUploadsDirectory(), '', $file );
    }

    private function getUploadsDirectory()
    {
        return trailingslashit( wp_get_upload_dir()[ 'basedir' ] );
    }

}