<?php
/*-------------------------------------------------------
*
*   LiveStreet Engine Social Networking
*   Copyright © 2008 Mzhelskiy Maxim
*
*--------------------------------------------------------
*
*   Official site: www.livestreet.ru
*   Contact e-mail: rus.engine@gmail.com
*
*   GNU General Public License, version 2:
*   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
---------------------------------------------------------
*/


/**
 * Проверяет запрос послан как ajax или нет
 * Пришлось продублировать здесь, чтобы получить к ней доступ до подключения роутера
 *
 * @return unknown
 */
function isAjaxRequest()
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest';
}

/**
 * функция доступа к GET POST параметрам
 *
 * @param  string $sName
 * @param  mixed  $default
 * @param  string $sType
 * @return mixed
 */
function getRequest($sName, $default=null, $sType=null)
{
    /**
     * Выбираем в каком из суперглобальных искать указанный ключ
     */
    switch (strtolower($sType)) {
        default:
        case null:
            $aStorage = $_REQUEST;
            break;
        case 'get':
            $aStorage = $_GET;
            break;
        case 'post':
            $aStorage = $_POST;
            break;
        case 'cookie':
            $aStorage = $_COOKIE;
            break;
    }

    if (isset($aStorage[$sName])) {
        if (is_string($aStorage[$sName])) {
            return trim($aStorage[$sName]);
        } else {
            return $aStorage[$sName];
        }
    }
    return $default;
}

/**
 * функция доступа к GET POST параметрам, которая значение принудительно приводит к строке
 *
 * @param string $sName
 * @param mixed $default
 * @param string $sType
 *
 * @return string
 */
function getRequestStr($sName, $default=null, $sType=null)
{
    $mData=getRequest($sName, $default, $sType);
    if (!is_array($mData)) {
        return (string)$mData;
    }
    return '';
}

/**
 * Определяет был ли передан указанный параметр методом POST
 *
 * @param  string $sName
 * @return bool
 */
function isPost($sName)
{
    return (getRequest($sName, null, 'post')!==null);
}

/**
 * @param int $iLength Длина
 * @return string Псевдослучайная последовательность символов
 */
function func_generator($iLength=10)
{
    if ($iLength>32) {
        $iLength=32;
    }
    return substr(bin2hex(openssl_random_pseudo_bytes(16)), 0, $iLength);
}

/**
 * htmlspecialchars умеющая обрабатывать массивы
 *
 * @param mixed $data
 * @param int %walkIndex - represents the key/index of the array being recursively htmlspecialchars'ed
 * @return void
 */
function func_htmlspecialchars(&$data, $walkIndex = null)
{
    if (is_string($data)) {
        $data = htmlspecialchars($data);
    } elseif (is_array($data)) {
        array_walk($data, __FUNCTION__);
    }
}

/**
 * stripslashes умеющая обрабатывать массивы
 *
 * @param unknown_type $data
 */
function func_stripslashes(&$data)
{
    if (is_array($data)) {
        foreach ($data as $sKey => $value) {
            if (is_array($value)) {
                func_stripslashes($data[$sKey]);
            } else {
                $data[$sKey]=stripslashes($value);
            }
        }
    } else {
        $data=stripslashes($data);
    }
}

/**
 * Проверяет на корректность значение соглавно правилу
 *
 * @param unknown_type $sValue
 * @param unknown_type $sParam
 * @param unknown_type $iMin
 * @param unknown_type $iMax
 * @return unknown
 */
function func_check($sValue, $sParam, $iMin=1, $iMax=100)
{
    if (is_array($sValue)) {
        return false;
    }
    switch ($sParam) {
        case 'id': if (preg_match("/^\d{".$iMin.','.$iMax."}$/", $sValue)) {
            return true;
        } break;
        case 'float': if (preg_match("/^[\-]?\d+[\.]?\d*$/", $sValue)) {
            return true;
        } break;
        case 'mail': if (preg_match("/^[\da-z\_\-\.\+]+@[\da-z_\-\.]+\.[a-z]{2,16}$/i", $sValue)) {
            return true;
        } break;
        case 'login': if (preg_match("/^[\da-z\_\-]{".$iMin.','.$iMax."}$/i", $sValue)) {
            return true;
        } break;
        case 'md5': if (preg_match("/^[\da-z]{32}$/i", $sValue)) {
            return true;
        } break;
        case 'password': if (mb_strlen($sValue, 'UTF-8')>=$iMin) {
            return true;
        } break;
        case 'text': if (mb_strlen($sValue, 'UTF-8')>=$iMin and mb_strlen($sValue, 'UTF-8')<=$iMax) {
            return true;
        } break;
        default:
            return false;
    }
    return false;
}

/**
 * Определяет IP адрес
 *
 * @return unknown
 */
function func_getIp()
{
    /*
    Временная правка для поддержки совместимости с ipv6
    Поддержка ipv6 запланированна в рамках https://gitea.everypony.ru/everypony/tabun/issues/200

    // Если запускаем через консоль, то REMOTE_ADDR не определен
    return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
    */
    if (isset($_SERVER['REMOTE_ADDR'])) {
        if (strpos($_SERVER['REMOTE_ADDR'], ':') !== false) {
            return substr($_SERVER['REMOTE_ADDR'], -15);
        } else {
            return $_SERVER['REMOTE_ADDR'];
	}
    } else {
        return '127.0.0.1';
    }
}


/**
 * Заменяет стандартную header('Location: *');
 *
 * @param unknown_type $sLocation
 */
function func_header_location($sLocation)
{
    $sProtocol=isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
    header("{$sProtocol} 301 Moved Permanently");
    header('Location: '.$sLocation);
    exit();
}

/**
 * Создаёт каталог по полному пути
 *
 * @param string $sBasePath
 * @param string $sNewDir
 */
function func_mkdir($sBasePath, $sNewDir)
{
    $sDirToCheck = rtrim($sBasePath, '/') . '/' . $sNewDir;
    if (!is_dir($sDirToCheck)) {
        mkdir($sDirToCheck, 0755, true);
    }
}

/**
 * Рекурсивное удаление директории (со всем содержимым)
 *
 * @param  string $sPath
 * @return bool
 */
function func_rmdir($sPath)
{
    if (!is_dir($sPath)) {
        return true;
    }
    $sPath = rtrim($sPath, '/').'/';

    if ($aFiles = glob($sPath.'*', GLOB_MARK)) {
        foreach ($aFiles as $sFile) {
            if (is_dir($sFile)) {
                func_rmdir($sFile);
            } else {
                @unlink($sFile);
            }
        }
    }
    if (is_dir($sPath)) {
        @rmdir($sPath);
    }
}

/**
 * Возвращает обрезанный текст по заданное число слов
 *
 * @param unknown_type $sText
 * @param unknown_type $iCountWords
 */
function func_text_words($sText, $iCountWords)
{
    $aWords = preg_split('#[\s\r\n]+#um', $sText);
    if ($iCountWords < count($aWords)) {
        $aWords = array_slice($aWords, 0, $iCountWords);
    }
    return join(' ', $aWords);
}

/**
 * Изменяет элементы массива
 *
 * @param unknown_type $array
 * @param unknown_type $sBefore
 * @param unknown_type $sAfter
 * @return array
 */
function func_array_change_value($array, $sBefore='', $sAfter='')
{
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $array[$key]=func_change_array_value($value, $sBefore, $sAfter);
        } elseif (!is_object($value)) {
            $array[$key]=$sBefore.$array[$key].$sAfter;
        }
    }
    return $array;
}

/**
 * Меняет числовые ключи массива на их значения
 *
 * @param unknown_type $arr
 * @param unknown_type $sDefValue
 */
function func_array_simpleflip(&$arr, $sDefValue=1)
{
    foreach ($arr as $key => $value) {
        if (is_int($key) and is_string($value)) {
            unset($arr[$key]);
            $arr[$value]=$sDefValue;
        }
    }
}

function func_build_cache_keys($array, $sBefore='', $sAfter='')
{
    $aRes=array();
    foreach ($array as $key => $value) {
        $aRes[$value]=$sBefore.$value.$sAfter;
    }
    return $aRes;
}

function func_array_sort_by_keys($array, $aKeys)
{
    $aResult=array();
    foreach ($aKeys as $iKey) {
        if (isset($array[$iKey])) {
            $aResult[$iKey]=$array[$iKey];
        }
    }
    return $aResult;
}

/**
 * Сливает два ассоциативных массива
 *
 * @param unknown_type $aArr1
 * @param unknown_type $aArr2
 * @return unknown
 */
function func_array_merge_assoc($aArr1, $aArr2)
{
    $aRes=$aArr1;
    foreach ($aArr2 as $k2 => $v2) {
        $bIsKeyInt=false;
        if (is_array($v2)) {
            foreach ($v2 as $k => $v) {
                if (is_int($k)) {
                    $bIsKeyInt=true;
                    break;
                }
            }
        }
        if (is_array($v2) and !$bIsKeyInt and isset($aArr1[$k2])) {
            $aRes[$k2]=func_array_merge_assoc($aArr1[$k2], $v2);
        } else {
            $aRes[$k2]=$v2;
        }
    }
    return $aRes;
}

if (!function_exists('array_fill_keys')) {
    function array_fill_keys($aArr, $values)
    {
        if (!is_array($aArr)) {
            $aArr=array($aArr);
        }
        $aArrOut=array();
        foreach ($aArr as $key => $value) {
            $aArrOut[$value] = $values;
        }
        return $aArrOut;
    }
}

if (!function_exists('array_intersect_key')) {
    function array_intersect_key($isec, $keys)
    {
        $argc = func_num_args();
        if ($argc > 2) {
            for ($i = 1; !empty($isec) && $i < $argc; $i++) {
                $arr = func_get_arg($i);
                foreach (array_keys($isec) as $key) {
                    if (!isset($arr[$key])) {
                        unset($isec[$key]);
                    }
                }
            }
            return $isec;
        } else {
            $res = array();
            foreach (array_keys($isec) as $key) {
                if (isset($keys[$key])) {
                    $res[$key] = $isec[$key];
                }
            }
            return $res;
        }
    }
}

if (!function_exists('class_alias')) {
    function class_alias($original, $alias)
    {
        if (!class_exists($original)) {
            return false;
        }
        eval('abstract class ' . $alias . ' extends ' . $original . ' {}');
        return true;
    }
}


function func_underscore($sStr)
{
    return strtolower(preg_replace('/([^A-Z])([A-Z])/', "$1_$2", $sStr));
}

function func_camelize($sStr)
{
    $aParts = explode('_', $sStr);
    $sCamelized = '';
    foreach ($aParts as $sPart) {
        $sCamelized .= ucfirst($sPart);
    }
    return $sCamelized;
}


function func_list_plugins($bAll = false)
{
    $sPluginsDir = Config::Get('path.root.server').'/plugins';
    $sPluginsListFile = $sPluginsDir.'/'.Config::Get('sys.plugins.activation_file');
    $aPlugin = array();
    if ($bAll) {
        $aPluginRaw = array();
        $aPaths = glob("$sPluginsDir/*", GLOB_ONLYDIR);
        if ($aPaths) {
            foreach ($aPaths as $sPath) {
                $aPluginRaw[] = basename($sPath);
            }
        }
    } else {
        if ($aPluginRaw = @file($sPluginsListFile)) {
            $aPluginRaw = array_map('trim', $aPluginRaw);
            $aPluginRaw = array_unique($aPluginRaw);
        }
    }
    if ($aPluginRaw) {
        foreach ($aPluginRaw as $sPlugin) {
            $sPluginXML = "$sPluginsDir/$sPlugin/plugin.xml";
            if (is_file($sPluginXML)) {
                $aPlugin[] = $sPlugin;
            }
        }
    }
    return $aPlugin;
}

function func_convert_entity_to_array(Entity $oEntity, $aMethods = null, $sPrefix = '')
{
    if (!is_array($aMethods)) {
        $aMethods=get_class_methods($oEntity);
    }
    $aEntity=array();
    foreach ($aMethods as $sMethod) {
        if (!preg_match('#^get([a-z][a-z\d]*)$#i', $sMethod, $aMatch)) {
            continue;
        }
        $sProp=strtolower(preg_replace('#([a-z])([A-Z])#', '$1_$2', $aMatch[1]));
        $mValue=call_user_func(array($oEntity,$sMethod));
        $aEntity[$sPrefix.$sProp]=$mValue;
    }
    return $aEntity;
}

/**
 * Строит логарифмическое облако - расчитывает значение size в зависимости от count
 * У объектов в коллекции обязательно должны быть методы getCount() и setSize()
 *
 * @param array $aCollection	Список тегов
 * @param int $iMinSize	Минимальный размер
 * @param int $iMaxSize	Максимальный размер
 * @return array
 */
function func_make_cloud($aCollection, $iMinSize=1, $iMaxSize=10)
{
    if (count($aCollection)) {
        $iSizeRange=$iMaxSize-$iMinSize;

        $iMin=10000;
        $iMax=0;
        foreach ($aCollection as $oObject) {
            if ($iMax<$oObject->getCount()) {
                $iMax=$oObject->getCount();
            }
            if ($iMin>$oObject->getCount()) {
                $iMin=$oObject->getCount();
            }
        }
        $iMinCount=log($iMin+1);
        $iMaxCount=log($iMax+1);
        $iCountRange=$iMaxCount-$iMinCount;
        if ($iCountRange==0) {
            $iCountRange=1;
        }
        foreach ($aCollection as $oObject) {
            $iTagSize=$iMinSize+(log($oObject->getCount()+1)-$iMinCount)*($iSizeRange/$iCountRange);
            $oObject->setSize(round($iTagSize));
        }
    }
    return $aCollection;
}

/**
 * Преобразует спец символы в html последовательнось, поведение
 * аналогично htmlspecialchars, кроме преобразования амперсанта "&"
 *
 * @param string $data
 * @return string
 */
function func_urlspecialchars($data)
{
    if (is_string($data)) {
        $aTable = get_html_translation_table();
        unset($aTable['&']);
        return strtr($data, $aTable);
    }
}
