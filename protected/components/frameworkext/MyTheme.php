<?php

class MyTheme extends CTheme
{
    public function getViewFile($controller, $viewName)
    {
        $viewPath = $this->getViewPath();
        $moduleViewPath = $viewPath;
        if (($module = $controller->getModule()) !== null)
        {
            $moduleViewPath = $module->getBasePath() . '/themes/' . $this->getName() . '/views/' . $controller->getId();
        }
        return $controller->resolveViewFile($viewName, $moduleViewPath, $moduleViewPath, $moduleViewPath);
    }

    public function getLayoutFile($controller, $layoutName)
    {
        $moduleViewPath = $basePath = $this->getViewPath();
        $module = $controller->getModule();
        if (empty($layoutName))
        {
            while ($module !== null)
            {
                if ($module->layout === false)
                    return false;
                if (!empty($module->layout))
                    break;
                $module = $module->getParentModule();
            }
            if ($module === null)
            {
                $layoutName = Yii::app()->layout;
            }
            else
            {
                $layoutName = $module->layout;
                $moduleViewPath = $module->getBasePath() . '/themes/' . $this->getName() . '/views';
            }
        }
        else if ($module !== null)
        {
            $moduleViewPath = $module->getBasePath() . '/themes/' . $this->getName() . '/views';
        }

        return $controller->resolveViewFile($layoutName, $moduleViewPath . '/layouts', $basePath, $moduleViewPath);
    }

}