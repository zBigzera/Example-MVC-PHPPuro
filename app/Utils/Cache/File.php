<?php

namespace App\Utils\Cache;

class File{

    /**
     * Método responsável por retornar o caminho até o arquivo de cache
     * @param string $hash
     * @return string
     */
    private static function getFilePath($hash){
        //diretório de cache
        $cacheDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'PHPMVC';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }

        //retorna o caminho até o arquivo
        return $cacheDir . DIRECTORY_SEPARATOR.$hash;
    }

    /**
     * Método responsável por guardar informações no cahce
     * @param string $hash
     * @param mixed $content
     * @return boolean
     */
    private static function storageCache($hash, $content){
        //Serializa o retorno
        $serialize = serialize($content);

        //obtém o caminho até o arquivo de cahce

        $cacheFile = self::getFilePath($hash);


        //grava as informações no arquivo
        return file_put_contents($cacheFile, $serialize);
    }

    /**
     * Método responsável por retornar o conteúdo gravado no cache
     * @param string $hash
     * @param integer $expiration
     * @return mixed
     */
    private static function getContentCache($hash, $expiration){
        //obtém o caminho do arquivo

        $cacheFile = self::getFilePath($hash);

        //verifica a existência do arquivo

        if(!file_exists($cacheFile)){
            return false;
        }

        //valida a expiração do cache
        $createTime = filectime($cacheFile);

        $diffTime = time() - $createTime;

        if($diffTime > $expiration){
            return false;
        }

        $serialize = file_get_contents($cacheFile);
        
        //retorna o dado real
        return unserialize($serialize);
    }


    /**
     * Método responsável por obter uma informação do cache
     * @param string $hash
     * @param integer $expiration
     * @param \Closure $function
     * @return mixed
     */
    public static function getCache($hash, $expiration, $function){
        //verifica o conteúdo gravado
        if($content = self::getContentCache($hash, $expiration)){
            return $content;
        }

        $content = $function();

        //grava o retorno no cache
        self::storageCache($hash, $content);

        //retorna o conteúdo
        return $content;

    }

}