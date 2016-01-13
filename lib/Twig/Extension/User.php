<?php

/*
 * This file is part of Twig.
 *
 * (c) 2012 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Twig_Extension_User extends Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('avatar', 'avatar', array('is_safe' => array('all'))),
            new Twig_SimpleFilter('userName', 'userName', array('is_safe' => array('all'))),
        );
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'user';
    }
}

function avatar($userId)
{
    GLOBAl $db;
    $filter["userId"] = MySQL::SQLValue($userId,"number");
    $db->SelectRows("stenden_users", $filter);
    $row = $db->Row();
    return $row->userImagePath;
}

function userName($userId)
{
    GLOBAl $db;
    $filter["userId"] = MySQL::SQLValue($userId,"number");
    $db->SelectRows("stenden_users", $filter);
    $row = $db->Row();
    return $row->userName;
}
