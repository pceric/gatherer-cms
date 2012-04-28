<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsFunction
 */

/**
 * Smarty {html_toggle} function plugin
 *
 * File:       function.html_toggle.php<br>
 * Type:       function<br>
 * Name:       html_toggle<br>
 * Date:       05.Apr.2012<br>
 * Purpose:    Prints out a checkbox input toggle<br>
 * Examples:
 * <pre>
 * {html_toggle value=$ids output=$names}
 * {html_toggle value=$ids name='box' separator='<br>' output=$names}
 * {html_toggle value=$ids checked=$checked separator='<br>' output=$names}
 * </pre>
 * @author     Eric Hokanson
 * @version    1.0
 * @param array $params parameters
 * Input:<br>
 *           - name       (optional) - string default "checkbox"
 *           - value      (optional) - value if checked
 *           - checked    (optional) - array default not set
 *           - separator  (optional) - ie <br> or &nbsp;
 *           - output     (optional) - the output next to each checkbox
 *           - assign     (optional) - assign the output as an array to this variable
 * @param object $template template object
 * @return string
 * @uses smarty_function_escape_special_chars()
 */
function smarty_function_html_toggle($params, $template)
{
    require_once(SMARTY_PLUGINS_DIR . 'shared.escape_special_chars.php');

    $name = 'checkbox';
    $value = 1;
    $selected = null;
    $separator = '';
    $labels = true;
    $output = null;

    $extra = '';

    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'name':
            case 'value':
            case 'separator':
            case 'output':
                $$_key = $_val;
                break;

            case 'labels':
                $$_key = (bool)$_val;
                break;

            case 'checked':
            case 'selected':
                $selected = array_map('strval', array_values((array)$_val));
                break;

            case 'assign':
                break;

            default:
                if(!is_array($_val)) {
                    $extra .= ' '.$_key.'="'.smarty_function_escape_special_chars($_val).'"';
                } else {
                    trigger_error("html_toggle: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
                }
                break;
        }
    }

    settype($selected, 'array');
    $_html_result = array();

    $_html_result[] = smarty_function_html_toggle_output($name, $value, $output, $selected, $extra, $separator, $labels);

    if(!empty($params['assign'])) {
        $template->assign($params['assign'], $_html_result);
    } else {
        return implode("\n",$_html_result);
    }
}

function smarty_function_html_toggle_output($name, $value, $output, $selected, $extra, $separator, $labels) {
    $_output = '';
    if ($labels) $_output .= '<label>';
    $_output .= '<input type="checkbox" name="'
        . smarty_function_escape_special_chars($name) . '" value="'
        . smarty_function_escape_special_chars($value) . '"';

    if (in_array((string)$value, $selected)) {
        $_output .= ' checked="checked"';
    }
    $_output .= $extra . ' />' . $output;
    if ($labels) $_output .= '</label>';
    $_output .=  $separator;

    return $_output;
}

?>
