<?php

namespace wbarcovsky\yii2\request_docs\helpers;

use Yii;
use Exception;

/**
 * Вычисление вызываемых частей системы на основании URL
 *
 * Класс позволяет опираясь на настройки \yii\web\UrlManager заданные в приложении вычислять вызываемые части (контроллер
 * и действие) на основании заданного URL.
 * **Внимание!** Из за высокого зацепления внутри Yii2 невозмоно получить изолированный экземпляр класса \yii\web\Application.
 * Поэтому вызов getRoute() приводит к переинициализации базовых компонентов и неожиданному поведению приложения вплоть
 * до полной поломки. Для исключения этой ситуации получение имени класса контроллера и метода нужно делать через вызов
 * wbarcovsky\yii2\request_docs\commands\UrlResolverController::actionRoute(). Не используйте данный помошник в своем
 * коде если не понимаете, что делайте и зачем!
 */
class UrlResolver
{
    /**
     * Разершить адрес $url в контроллер/действие.
     *
     * На основании заданного $url вернет связанный с ним контроллер и имя метода связанного с указанным действием (явно
     * заданными либо по умолчанию).
     *
     * @param string $url
     * @param string $httpMethod
     * @return array[\yii\web\Controller, string] Вернет массив в котором первый элемент объект контроллера, второй имя метода действия
     * @throws InvalidConfigException
     * @throws \Exception
     */
    public static function getRoute($url, $httpMethod = 'GET')
    {
        $webConfig = include Yii::getAlias('@app/config/web.php');
        // Поднимаем \yii\web\Application, т.к. в нем urlManager определяеющий роутинг
        $webApp = new \yii\web\Application($webConfig);
        $request = self::getRequest($url);
        $parseResult = $webApp->urlManager->parseRequest($request);
        if ($parseResult === false) {
            throw new Exception("Не удалось разобрать URL «{$url}»");
        }
        $route = $parseResult[0];
        /** @var \yii\web\Controller $controller */
        list($controller, $actionName) = $webApp->createController($route);
        if (empty($actionName)) {
            $actionName = $controller->defaultAction;
        }
        $actionMethodName = 'action' . ucfirst($actionName);
        if (!$controller->hasMethod($actionMethodName)) {
            throw new Exception('В классе контроллера ' . get_class($controller) . ' отсутствует действие ' . $actionMethodName);
        }
        return [$controller, $actionMethodName];
    }

    /**
     * Вычисляет pathInfo который следует задать для yii\web\Request (частичная копия с resolvePathInfo метода).
     * @see \yii\web\Request::resolvePathInfo()
     * @param string $url
     * @return string
     */
    protected static function getPathInfo($url)
    {
        if (($pos = strpos($url, '?')) !== false) {
            $url = substr($url, 0, $pos);
        }
        $pathInfo = urldecode($url);
        // try to encode in UTF8 if not so
        // http://w3.org/International/questions/qa-forms-utf-8.html
        if (!preg_match('%^(?:
            [\x09\x0A\x0D\x20-\x7E]              # ASCII
            | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
            | \xE0[\xA0-\xBF][\x80-\xBF]         # excluding overlongs
            | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
            | \xED[\x80-\x9F][\x80-\xBF]         # excluding surrogates
            | \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
            | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
            | \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
            )*$%xs', $pathInfo)
        ) {
            $pathInfo = utf8_encode($pathInfo);
        }
        if (substr($pathInfo, 0, 1) === '/') {
            $pathInfo = substr($pathInfo, 1);
        }
        return $pathInfo;
    }

    /**
     * Вернет объект HTTP запроса связанный с заданным адресом $url.
     * @param string $url
     * @param string $httpMethod
     * @return \yii\web\Request
     */
    protected static function getRequest($url, $httpMethod = 'GET')
    {
        $webConfig = include Yii::getAlias('@app/config/web.php');
        $methodParamName = '_routeDetection';
        $webConfig['components']['request']['methodParam'] = $methodParamName;
        if (!isset($_POST)) {
            $_POST = [];
        }
        $_POST[$methodParamName] = $httpMethod;
        /** @var \yii\web\Request $request */
        $request = Yii::createObject($webConfig['components']['request']);
        $request->setUrl($url);
        $pathInfo = self::getPathInfo($url);
        $request->setPathInfo($pathInfo);
        $queryParams = [];
        parse_str(parse_url($url, PHP_URL_QUERY), $queryParams);
        $request->setQueryParams($queryParams);

        return $request;
    }
}
