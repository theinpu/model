<?php
/**
 * User: anubis
 * Date: 24.01.14
 * Time: 13:51
 */

namespace bc\model;

interface IBuilder {

    /**
     * метода для красоты IBuilder::create()->build();
     * @return IBuilder
     */
    public static function create();

    /**
     * @return Model
     */
    public function build();

} 