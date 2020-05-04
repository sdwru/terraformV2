<?php

/*
 * This file is part of the TerraformV2 library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TerraformV2\Api;

use TerraformV2\Entity\OauthClient as OauthClientEntity;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 * @author Graham Campbell <graham@alt-three.com>
 */
class OauthClient extends AbstractApi
{
    /**
     * @return OauthClientEntity
     */
    public function getAll($organization)
    {
        // Special characters"[" and "]" in page[size] and page[number] need to be presented as URL % encoded so "%5B" and "%5D"
        // Since "%" is also a special character it needs to be escaped with another "%" to prevent interpreting.  So "%%5B" and "%%5D"
        $vars = $this->adapter->get(sprintf('%s/organizations/%s/oauth-clients', $this->endpoint, $organization));

        $vars = json_decode($vars);

        return array_map(function ($var) {
            return new OauthClientEntity($var);
        }, $vars->data);
    }
    
    /**
     * @param string $name
     *
     * @throws HttpException
     *
     * @return OauthClientEntity
     */
    public function getByName($organization, $name)
    {
        $var = $this->adapter->get(sprintf('%s/organizations/%s/workspaces/%s', $this->endpoint, $organization, $name));

        $var = json_decode($var);

        return new WorkspaceEntity($var);
    }
    
    /**
     * @param int $id
     *
     * @throws HttpException
     *
     * @return OauthClientEntity
     */
    public function getById($id)
    {
        $var = $this->adapter->get(sprintf('%s/oauth-clients/%d', $this->endpoint, $id));

        $var = json_decode($var);

        return new OauthClientEntity($var);
    }
    
}