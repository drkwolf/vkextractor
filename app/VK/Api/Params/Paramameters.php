<?php
/**
 * Created by PhpStorm.
 * User: drkwolf
 * Date: 10/27/16
 * Time: 4:40 PM
 */

namespace App\VK\Api\Params;


class Paramameters
{
    const MAX_COUNT = 200;


    public $required = [];

    public $others = [];

    public $default = [];


    /**
     * @param Array default
     * @param Array $params
     */
    protected function mergeParameters(Array $default, Array $params) {
        $filtred = array_only($params, array_keys($default));
        $collect = collect($default);
        $params = $collect->merge($filtred);

        return $params->all();
    }

}