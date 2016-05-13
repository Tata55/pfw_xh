<?php

/**
 * Routes
 *
 * @copyright 2016 Christoph M. Becker
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 */

namespace Pfw;

/**
 * Routes
 *
 * Routes are mappings of query string patterns to controller names.
 * When they are going to be resolved, the patterns are traversed from first
 * to last and matched against the current query string. As soon as a pattern
 * matches, its respective controller is instantiated, and the appropriate
 * action is invoked.
 *
 * A pattern is structured like query string. Each parameter name is
 * checked for existence in the current query string, and if a parameter value
 * is present in the pattern, it has to match too. A special feature of the
 * patterns is that a question mark may be prepended to any parameter,
 * which signals that this parameter has to be the first query parameter.
 * This is useful to add handling for certain CMSimple_XH pages (typically
 * special pages dynamically generated by the plugin).
 */
class Route
{
    /**
     * The plugin
     *
     * @var Plugin
     */
    private $plugin;

    /**
     * The map of query string patterns to controller names
     *
     * @var array
     */
    private $map;

    /**
     * Constructs an instance
     *
     * @param Plugin $plugin
     * @param array  $map
     */
    public function __construct(Plugin $plugin, array $map)
    {
        $this->plugin = $plugin;
        $this->map = $map;
    }

    /**
     * Resolves a route
     *
     * The first query pattern that matches causes the respective controller
     * to be instantiated. If any $args are given (what happens when the route
     * is a user function route) these are passed as additional arguments to
     * the constructor.
     *
     * Then the controller's dispatcher is looked up and if there is a
     * respective query parameter, its value is used to determine the action
     * to invoke; otherwise the index action will be invoked. Either way,
     * any parameters of the determined action will be automagically assigned
     * from the query parameter which has the name of the argument prefixed
     * by the plugin name. If this query parameter is not set, null is assigned.
     *
     * @param array $args
     *
     * @return void
     */
    public function resolve(array $args = null)
    {
        foreach ($this->map as $pattern => $controllerName) {
            if ($this->match($pattern)) {
                $controller = $this->createController($controllerName, $args);
                $actionName = $this->getActionNameOf($controller);
                return $this->invokeAction($controller, $actionName);
            }
        }
    }

    /**
     * Returns whether a given pattern matches the current query string
     *
     * @param string $pattern
     *
     * @return bool
     */
    private function match($pattern)
    {
        global $su;

        parse_str($pattern, $params);
        foreach ($params as $name => $value) {
            if (strpos($name, '?') === 0) {
                if (substr($name, 1) != $su) {
                    return false;
                }
            } elseif (!isset($_GET[$name]) || ($value && $value != $_GET[$name])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Creates a controller and returns it
     *
     * @param string $controllerName
     * @param array  $args
     *
     * @return Controller
     */
    private function createController($controllerName, array $args = null)
    {
        if (isset($args)) {
            $params = $args;
            array_unshift($params, $this->plugin);
        } else {
            $params = array($this->plugin);
        }
        $class = new \ReflectionClass($controllerName);
        return $class->newInstanceArgs($params);
    }

    /**
     * Returns the name of the action method to be invoked
     *
     * @param Controller $controller
     *
     * @return string
     */
    private function getActionNameOf(Controller $controller)
    {
        $dispatcher = $controller->getDispatcher();
        if (isset($dispatcher) && isset($_GET[$dispatcher])) {
            $name = $_GET[$dispatcher];
        } else {
            $name = 'index';
        }
        return "{$name}Action";
    }

    /**
     * Invokes the action on the controller
     *
     * @param Controller $controller
     * @param string     $methodName
     *
     * @return void
     */
    private function invokeAction(Controller $controller, $methodName)
    {
        $method = new \ReflectionMethod($controller, $methodName);
        $params = array();
        foreach ($method->getParameters() as $param) {
            $name = $this->plugin->name . '_' . $param->getName();
            if (isset($_GET[$name])) {
                $params[] = $_GET[$name];
            } else {
                $params[] = null;
            }
        }
        $method->invokeArgs($controller, $params);
    }
}
